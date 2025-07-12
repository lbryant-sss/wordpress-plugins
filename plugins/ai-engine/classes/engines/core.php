<?php

class Meow_MWAI_Engines_Core {
  protected $core = null;
  public $env = null;
  public $envId = null;
  public $envType = null;

  // Streaming
  protected $streamCallback = null;
  protected $streamTemporaryBuffer = '';
  protected $streamBuffer = '';
  protected $streamHeaders = [];
  protected $streamContent = '';

  // Debug mode for stream events
  protected $currentDebugMode = false;
  protected $currentQuery = null;
  protected $emittedFunctionResults = [];

  public function __construct( $core, $env ) {
    $this->core = $core;
    $this->env = $env;
    $this->envId = isset( $env['id'] ) ? $env['id'] : null;
    $this->envType = isset( $env['type'] ) ? $env['type'] : null;
  }

  /**
   * Reset all request-specific state variables.
   * This should be called at the start of each new request to prevent
   * state leakage between requests.
   */
  protected function reset_request_state() {
    // Reset streaming state
    $this->streamCallback = null;
    $this->streamTemporaryBuffer = '';
    $this->streamBuffer = '';
    $this->streamHeaders = [];
    $this->streamContent = '';
    
    // Reset debug/event state
    $this->currentDebugMode = false;
    $this->currentQuery = null;
    $this->emittedFunctionResults = [];
  }

  public function run( $query, $streamCallback = null, $maxDepth = 5 ) {

    // Apply filter to allow overriding maxDepth (only on first call)
    if ( !isset( $query->_maxDepthConfigured ) ) {
      $maxDepth = apply_filters( 'mwai_function_call_max_depth', $maxDepth, $query );
      $query->_maxDepthConfigured = $maxDepth;
    }

    // Check if queries debug is enabled
    $queries_debug = $this->core->get_option( 'queries_debug_mode' );

    // Log query start if debug is enabled
    if ( $queries_debug ) {
      // We'll let the individual engines log the actual HTTP requests/responses
      // Just log a simple start marker here
      error_log( '[AI Engine Queries Debug] ========================================' );
      $query_type = get_class( $query );
      error_log( '[AI Engine Queries Debug] Starting ' . $query_type . ' to ' . ( $query->model ?? 'unknown model' ) );
    }

    // Check if the query is allowed.
    $limits = $this->core->get_option( 'limits' );
    $allowed = apply_filters( 'mwai_ai_allowed', true, $query, $limits );
    if ( $allowed !== true ) {
      $message = is_string( $allowed ) ? $allowed : 'Unauthorized query.';
      throw new Exception( $message );
    }

    // Important as it makes sure everything is consolidated in the query and the engine.
    $this->final_checks( $query );

    // Run the query
    $reply = null;
    if ( $query instanceof Meow_MWAI_Query_Text || $query instanceof Meow_MWAI_Query_Feedback ) {
      $reply = $this->run_completion_query( $query, $streamCallback );
    }
    else if ( $query instanceof Meow_MWAI_Query_Assistant || $query instanceof Meow_MWAI_Query_AssistFeedback ) {
      $reply = $this->run_assistant_query( $query, $streamCallback );
      if ( $reply === null ) {
        throw new Exception( 'Assistants are not supported in this version of AI Engine.' );
      }
    }
    else if ( $query instanceof Meow_MWAI_Query_Embed ) {
      $reply = $this->run_embedding_query( $query );
    }
    else if ( $query instanceof Meow_MWAI_Query_EditImage ) {
      $reply = $this->run_editimage_query( $query );
    }
    else if ( $query instanceof Meow_MWAI_Query_Image ) {
      $reply = $this->run_image_query( $query );
    }
    else if ( $query instanceof Meow_MWAI_Query_Transcribe ) {
      $reply = $this->run_transcribe_query( $query );
    }
    else {
      throw new Exception( 'Unknown query type.' );
    }

    // Allow to modify the reply before it is sent.
    $reply = apply_filters( 'mwai_ai_reply', $reply, $query );

    // Log query completion if debug is enabled
    if ( $queries_debug && empty( $reply->needFeedbacks ) ) {
      // For embedding queries, just log the dimensions count
      if ( $query instanceof Meow_MWAI_Query_Embed && !empty( $reply->result ) && is_array( $reply->result ) ) {
        error_log( '[AI Engine Queries Debug] Embedding completed with ' . count( $reply->result ) . ' dimensions' );
      }
      else {
        error_log( '[AI Engine Queries Debug] Query completed' );
      }
      error_log( '[AI Engine Queries Debug] ========================================' );
    }

    // Function Call Handling - This is where the magic happens!
    // When the AI model requests function calls, we execute them and send results back
    if ( !empty( $reply->needFeedbacks ) ) {
      
      // Debug: Log how many needFeedbacks we have
      if ( $queries_debug ) {
        error_log( '[AI Engine Queries Debug] Core: Processing ' . count( $reply->needFeedbacks ) . ' needFeedbacks' );
        foreach ( $reply->needFeedbacks as $idx => $feedback ) {
          error_log( '[AI Engine Queries Debug] Core: needFeedback[' . $idx . ']: name=' . $feedback['name'] . ', toolId=' . ( $feedback['toolId'] ?? 'none' ) );
        }
      }
      

      // Prevent infinite loops - each function call reduces maxDepth by 1
      if ( $maxDepth <= 0 ) {
        // Build call stack for better debugging
        $callStack = [];
        foreach ( $reply->needFeedbacks as $feedback ) {
          $callStack[] = $feedback['name'] ?? 'unknown';
        }

        throw Meow_MWAI_FunctionCallException::loop_detected(
          $query->_maxDepthConfigured ?? 5, // Use configured max depth
          $callStack
        );
      }

      // Create a feedback query if we're not already in one
      // This wraps the original query with function execution results
      if ( !( $query instanceof Meow_MWAI_Query_AssistFeedback ) && !( $query instanceof Meow_MWAI_Query_Feedback ) ) {
        $queryClass = $query instanceof Meow_MWAI_Query_Assistant ?
        Meow_MWAI_Query_AssistFeedback::class : Meow_MWAI_Query_Feedback::class;
        // Note: $reply->query contains the original query that produced this reply
        $query = new $queryClass( $reply, $reply->query );
      }

      // Validate that all function calls have proper function definitions
      foreach ( $reply->needFeedbacks as $needFeedback ) {
        if ( !isset( $needFeedback['function'] ) ) {
          $functionName = $needFeedback['name'] ?? 'unknown';
          $availableFunctions = array_map( function( $f ) { return $f->name; }, $query->functions );
          
          throw new Exception( sprintf(
            "Function '%s' not found in query functions. Available functions: %s",
            $functionName,
            implode( ', ', $availableFunctions )
          ) );
        }
      }

      // Group function calls by their source message to maintain proper context
      // This ensures related function calls are processed together
      $feedback_blocks = [];
      foreach ( $reply->needFeedbacks as $needFeedback ) {
        $rawMessageKey = md5( serialize( $needFeedback['rawMessage'] ) );

        // Initialize the feedback block for this rawMessage if it hasn't been initialized yet
        if ( !isset( $feedback_blocks[$rawMessageKey] ) ) {
          $feedback_blocks[$rawMessageKey] = [
            'rawMessage' => $needFeedback['rawMessage'],
            'feedbacks' => []
          ];
        }

        // Get the value related to this feedback (usually, a function call)
        $value = apply_filters( 'mwai_ai_feedback', null, $needFeedback, $reply );

        if ( $value === null ) {
          // Check if the function handler exists
          if ( !has_filter( 'mwai_ai_feedback' ) ) {
            Meow_MWAI_Logging::error(
              Meow_MWAI_FunctionCallException::missing_function_handler(
                $needFeedback['name']
              )->getMessage()
            );
          }
          else {
            Meow_MWAI_Logging::warn( "The returned value for '{$needFeedback['name']}' was null." );
          }
          $value = '[NO VALUE RETURNED - DO NOT SHOW THIS]';
        }

        // Emit "Got result" event and log for debugging
        if ( $this->currentDebugMode ) {
          // Format the result preview
          $resultPreview = is_array( $value ) ? json_encode( $value ) : (string) $value;
          if ( strlen( $resultPreview ) > 100 ) {
            $resultPreview = substr( $resultPreview, 0, 100 ) . '...';
          }

          // Log the function result for debugging
          Meow_MWAI_Logging::log( "Function '{$needFeedback['name']}' returned: " . $resultPreview );
          
          // Emit function result event if we have a callback
          if ( !empty( $streamCallback ) ) {
            // Load event helper if not already loaded
            if ( !class_exists( 'Meow_MWAI_Event' ) ) {
              require_once MWAI_PATH . '/classes/event.php';
            }
            
            $functionName = $needFeedback['name'];
            
            $event = Meow_MWAI_Event::function_result( $functionName )
              ->set_metadata( 'result', $resultPreview )
              ->set_metadata( 'tool_id', $needFeedback['toolId'] ?? null );
            call_user_func( $streamCallback, $event );
          }
        }

        // Add the feedback information to the appropriate feedback block
        $feedback_blocks[$rawMessageKey]['feedbacks'][] = [
          'request' => $needFeedback, // TODO: Meow_MWAI_Feedback_Request
          'reply' => [ 'value' => $value ] // TODO: Meow_MWAI_Feedback_Reply
        ];
      }

      $query->clear_feedback_blocks();
      foreach ( $feedback_blocks as $feedback_block ) {
        $query->add_feedback_block( $feedback_block );
      }

      // Log feedback query if debug is enabled
      if ( $queries_debug ) {
        error_log( '[AI Engine Queries Debug] Processing feedback query with ' . count( $feedback_blocks ) . ' feedback blocks' );
      }

      // Run the feedback query
      $reply = $this->run( $query, $streamCallback, $maxDepth - 1 );
    }

    return $reply;
  }

  public function retrieve_model_info( $model ) {
    $models = $this->get_models();
    foreach ( $models as $currentModel ) {
      if ( $currentModel['model'] === $model ) {
        return $currentModel;
      }
    }
    return false;
  }

  public function final_checks( Meow_MWAI_Query_Base $query ) {
    $query->final_checks();
    //$found = false;

    // Check if the model is available, except if it's an assistant
    if ( !( $query instanceof Meow_MWAI_Query_Assistant ) ) {
      // TODO: Avoid checking on the finetuned models for now.
      if ( substr( $query->model, 0, 3 ) === 'ft:' ) {
        return;
      }
      $model_info = $this->retrieve_model_info( $query->model );
      if ( $model_info === false ) {
        throw new Exception( "AI Engine: The model '{$query->model}' is not available." );
      }
      if ( isset( $model_info['mode'] ) ) {
        $query->mode = $model_info['mode'];
      }
    }
  }

  // Streamline the messages:
  // - Concatenate consecutive model messages into a single message for the model role
  // - Make sure the first message is a user message
  // - Make sure the last message is a user message
  protected function streamline_messages( $messages, $systemRole = 'assistant', $messageType = 'content' ) {
    $processedMessages = [];
    $lastRole = '';
    $concatenatedText = '';

    // Determine the way to access message content based on messageType
    $getContent = function ( $message ) use ( $messageType ) {
      if ( $messageType == 'parts' ) {
        return $message['parts'][0]['text'];
      }
      else { // Default to 'content'
        return $message['content'];
      }
    };

    // Set content to a message depending on the messageType
    $setContent = function ( &$message, $content ) use ( $messageType ) {
      if ( $messageType == 'parts' ) {
        $message['parts'] = [['text' => $content]];
      }
      else { // Default to 'content'
        $message['content'] = $content;
      }
    };

    // Concatenate consecutive model messages into a single message for the model role
    foreach ( $messages as $message ) {
      if ( $message['role'] == $systemRole ) {
        if ( $lastRole == $systemRole ) {
          $concatenatedText .= "\n" . $getContent( $message );
        }
        else {
          if ( $concatenatedText !== '' ) {
            $newMessage = [ 'role' => $systemRole ];
            $setContent( $newMessage, $concatenatedText );
            $processedMessages[] = $newMessage;
          }
          $concatenatedText = $getContent( $message );
        }
      }
      else {
        if ( $lastRole == $systemRole ) {
          $newMessage = [ 'role' => $systemRole ];
          $setContent( $newMessage, $concatenatedText );
          $processedMessages[] = $newMessage;
          $concatenatedText = '';
        }
        $processedMessages[] = $message;
      }
      $lastRole = $message['role'];
    }
    if ( $lastRole == $systemRole && $concatenatedText !== '' ) {
      $newMessage = [ 'role' => $systemRole ];
      $setContent( $newMessage, $concatenatedText );
      $processedMessages[] = $newMessage;
    }

    // Make sure the last message is a user message, if not, throw an exception
    if ( end( $processedMessages )['role'] !== 'user' ) {
      throw new Exception( 'The last message must be a user message.' );
    }

    // Make sure the first message is a user message, if not, add an empty user message
    if ( $processedMessages[0]['role'] !== 'user' ) {
      $newMessage = [ 'role' => 'user' ];
      $setContent( $newMessage, '' );
      array_unshift( $processedMessages, $newMessage );
    }

    return $processedMessages;
  }

  // Check for a JSON-formatted error in the data, and throw an exception if it's the case.
  public function stream_error_check( $data ) {
    if ( strpos( $data, 'error' ) === false ) {
      return;
    }

    $data = trim( $data );
    $jsonPart = $data;
    if ( strpos( $jsonPart, 'data:' ) === 0 ) {
      $jsonPart = trim( substr( $jsonPart, strlen( 'data:' ) ) );
    }

    $json = json_decode( $jsonPart, true );
    if ( json_last_error() !== JSON_ERROR_NONE ) {
      return; // not valid JSON, nothing to do
    }
    // 1. OpenAI style: { error: {...} }
    $error = null;
    if ( isset( $json['error'] ) ) {
      $error = $json['error'];
    }
    // 2. Google style: [ { error: {...} } ]
    else if ( is_array( $json ) ) {
      foreach ( $json as $item ) {
        if ( isset( $item['error'] ) ) {
          $error = $item['error'];
          break;
        }
      }
    }
    // 3. Some APIs return { type: "error", message: ... }
    else if ( isset( $json['type'] ) && $json['type'] === 'error' ) {
      $error = $json;
    }

    if ( is_null( $error ) ) {
      return;
    }

    $message = $error['message'] ?? ( is_string( $error ) ? $error : null );
    $code = $error['code'] ?? null;
    // Google uses "status" instead of "type" – accept both
    $type = $error['type'] ?? ( $error['status'] ?? null );
    if ( is_null( $message ) ) {
      throw new Exception( 'Unknown error (stream_error_check).' );
    }

    $errorMessage = "Error: $message";
    if ( !is_null( $code ) ) {
      $errorMessage .= " ($code)";
    }
    if ( !is_null( $type ) ) {
      $errorMessage .= " ($type)";
    }

    throw new Exception( $errorMessage );
  }

  protected function init_debug_mode( $query ) {
    // Check if debug mode is enabled in settings
    $this->currentDebugMode = $this->core->get_option( 'module_devtools' ) && $this->core->get_option( 'debug_mode' );
    $this->currentQuery = $query;
  }

  public function stream_handler( $handle, $args, $url ) {
    curl_setopt( $handle, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $handle, CURLOPT_SSL_VERIFYHOST, false );

    // TODO: This is breaking the response. We need to find a way to handle the headers.
    // curl_setopt( $handle, CURLOPT_HEADERFUNCTION, function ( $curl, $header ) {
    //   $length = strlen( $header );
    //   $this->streamHeaders[] = $header;
    //   $this->stream_header_handler( $header );
    //   return $length;
    // });

    curl_setopt( $handle, CURLOPT_WRITEFUNCTION, function ( $curl, $data ) use ( $url ) {
      $length = strlen( $data );

      // Log streaming data if queries debug is enabled
      $queries_debug = $this->core->get_option( 'queries_debug_mode' );
      static $logged_url = false;
      if ( $queries_debug && !$logged_url ) {
        error_log( '[AI Engine Queries Debug] Streaming from: ' . $url );
        $logged_url = true;
      }

      // Bufferize the unfinished stream (if it's the case)
      $this->streamTemporaryBuffer .= $data;
      $this->streamBuffer .= $data;

      // Error Management
      $this->stream_error_check( $this->streamBuffer );

      $lines = explode( "\n", $this->streamTemporaryBuffer );
      if ( substr( $this->streamTemporaryBuffer, -1 ) !== "\n" ) {
        $this->streamTemporaryBuffer = array_pop( $lines );
      }
      else {
        $this->streamTemporaryBuffer = '';
      }

      foreach ( $lines as $line ) {
        if ( $line === '' ) {
          continue;
        }
        if ( strpos( $line, 'data:' ) === 0 ) {
          $line = trim( substr( $line, 5 ) );
          $json = json_decode( trim( $line ), true );

          if ( json_last_error() === JSON_ERROR_NONE ) {
            // Log individual streaming event if queries debug is enabled
            static $event_count = 0;
            if ( $queries_debug && $event_count < 10 ) {
              // Log only the event type and key data, not the entire response
              $event_log = [
                'type' => $json['type'] ?? 'unknown'
              ];

              // Add specific details based on event type
              if ( isset( $json['type'] ) ) {
                if ( $json['type'] === 'response.output_item.added' && isset( $json['item'] ) ) {
                  $event_log['item_type'] = $json['item']['type'] ?? 'unknown';
                  $event_log['name'] = $json['item']['name'] ?? null;
                  $event_log['call_id'] = $json['item']['call_id'] ?? null;
                }
                elseif ( strpos( $json['type'], 'response.function_call' ) === 0 ) {
                  $event_log['call_id'] = $json['call_id'] ?? $json['item_id'] ?? null;
                }
                elseif ( $json['type'] === 'response.output_item.done' && isset( $json['item'] ) ) {
                  $event_log['item_type'] = $json['item']['type'] ?? 'unknown';
                  if ( isset( $json['item']['call_id'] ) ) {
                    $event_log['call_id'] = $json['item']['call_id'];
                  }
                }
              }

              error_log( '[AI Engine Queries Debug] Event: ' . json_encode( $event_log ) );
              $event_count++;
            }

            $content = $this->stream_data_handler( $json );
            if ( !is_null( $content ) ) {

              // Check if content is an Event object
              if ( is_object( $content ) && $content instanceof Meow_MWAI_Event ) {
                // For Event objects, pass the object directly to callback
                // Don't accumulate in streamContent as it's not regular text
                call_user_func( $this->streamCallback, $content );
              }
              else {
                // For regular string content

                // TO CHECK: Not sure why we need to do this to make sure there is a line return in the chatbot
                // If we don't do this, HuggingFace streams "\n" as a token without anything else, and the
                // chatbot doesn't display it.
                if ( $content === "\n" ) {
                  $content = "  \n";
                }

                $this->streamContent .= $content;
                call_user_func( $this->streamCallback, $content );
              }
            }
          }
          else if ( $line !== '[DONE]' && !empty( $line ) ) {
            $this->streamTemporaryBuffer .= $line . "\n";
          }
        }
      }
      return $length;
    } );
  }

  protected function stream_header_handler( $header ) {

  }

  protected function stream_data_handler( $json ) {
    throw new Exception( 'Not implemented.' );
  }

  public function get_models() {
    throw new Exception( 'Not implemented.' );
  }

  public function retrieve_models() {
    throw new Exception( 'Not implemented.' );
  }

  public function run_completion_query( Meow_MWAI_Query_Base $query, $streamCallback = null ): Meow_MWAI_Reply {
    throw new Exception( 'Not implemented.' );
  }

  public function run_assistant_query( Meow_MWAI_Query_Assistant $query, $streamCallback = null ): Meow_MWAI_Reply {
    throw new Exception( 'Not implemented, or not supported in this version of AI Engine.' );
  }

  public function run_embedding_query( Meow_MWAI_Query_Base $query ) {
    throw new Exception( 'Not implemented.' );
  }

  public function run_image_query( Meow_MWAI_Query_Base $query ) {
    throw new Exception( 'Not implemented.' );
  }

  public function run_editimage_query( Meow_MWAI_Query_Base $query ) {
    throw new Exception( 'Not implemented.' );
  }

  public function run_transcribe_query( Meow_MWAI_Query_Base $query ) {
    throw new Exception( 'Not implemented.' );
  }

  public function get_price( Meow_MWAI_Query_Base $query, Meow_MWAI_Reply $reply ) {
    throw new Exception( 'Not implemented.' );
  }

  /**
   * Check the connection to the AI service.
   * This should be a minimal, cost-free API call to verify credentials and connectivity.
   * 
   * @return array {
   *     @type bool   $success      Whether the connection test was successful
   *     @type string $service      The service name (e.g., 'OpenAI', 'Anthropic')
   *     @type string $message      A human-readable message about the test result
   *     @type array  $details      Additional service-specific details
   *     @type string $error        Error message if the test failed
   * }
   */
  public function connection_check() {
    throw new Exception( 'Connection check not implemented for this service.' );
  }
}
