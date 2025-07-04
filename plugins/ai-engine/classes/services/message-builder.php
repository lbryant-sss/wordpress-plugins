<?php

/**
* Service for building and transforming messages for different AI APIs.
*
* Simplifies the complex message building logic by breaking it down
* into smaller, focused methods.
*/
class Meow_MWAI_Services_MessageBuilder {
  private Meow_MWAI_Core $core;

  public function __construct( Meow_MWAI_Core $core ) {
    $this->core = $core;
  }

  /**
  * Build messages array for Responses API format
  */
  public function build_responses_api_messages( Meow_MWAI_Query_Base $query ): array {
    $messages = [];

    // Handle different query types
    if ( $query instanceof Meow_MWAI_Query_Feedback ) {
      $messages = $this->build_feedback_messages( $query );
    }
    else {
      $messages = $this->convert_messages_to_responses_format( $query->messages );
    }

    // Add user message with attachments if needed
    if ( !( $query instanceof Meow_MWAI_Query_Feedback ) ) {
      $messages = $this->add_user_message_with_attachments( $messages, $query );
    }

    return $messages;
  }

  /**
  * Build messages for feedback queries
  */
  private function build_feedback_messages( Meow_MWAI_Query_Feedback $query ): array {
    $messages = [];

    // Convert existing messages
    $messages = $this->convert_messages_to_responses_format( $query->messages );

    // Process feedback blocks
    if ( !empty( $query->blocks ) ) {
      $messages = $this->add_feedback_results( $messages, $query->blocks );
    }

    return $messages;
  }

  /**
  * Convert role-based messages to Responses API format
  */
  private function convert_messages_to_responses_format( array $messages ): array {
    $converted = [];

    foreach ( $messages as $message ) {
      if ( !isset( $message['role'] ) ) {
        // Already in Responses API format
        $converted[] = $message;
        continue;
      }

      // Handle assistant messages with tool calls
      if ( $message['role'] === 'assistant' && isset( $message['tool_calls'] ) ) {
        $converted = array_merge(
          $converted,
          $this->convert_assistant_with_tools( $message )
        );
      }
      else {
        // Regular messages stay as-is
        $converted[] = $message;
      }
    }

    return $converted;
  }

  /**
  * Convert assistant message with tool calls to separate messages
  */
  private function convert_assistant_with_tools( array $message ): array {
    $messages = [];

    // Add assistant text if present
    if ( !empty( $message['content'] ) ) {
      $messages[] = [
        'role' => 'assistant',
        'content' => $message['content']
      ];
    }

    // Convert each tool call to function_call message
    if ( isset( $message['tool_calls'] ) ) {
      foreach ( $message['tool_calls'] as $toolCall ) {
        $functionCall = Meow_MWAI_Data_FunctionCall::from_tool_call( $toolCall, $message );
        $messages[] = [
          'type' => 'function_call',
          'call_id' => $functionCall->id,
          'name' => $functionCall->name,
          'arguments' => $functionCall->get_arguments_json()
        ];
      }
    }

    return $messages;
  }

  /**
  * Add feedback results to messages
  */
  private function add_feedback_results( array $messages, array $blocks ): array {
    $functionResults = [];
    $processedCallIds = [];

    foreach ( $blocks as $block ) {
      if ( !isset( $block['feedbacks'] ) ) {
        continue;
      }

      foreach ( $block['feedbacks'] as $feedback ) {
        $toolId = $feedback['request']['toolId'] ?? null;

        // Skip duplicates
        if ( !$toolId || in_array( $toolId, $processedCallIds ) ) {
          continue;
        }

        // Create function result object
        $result = Meow_MWAI_Data_FunctionResult::success(
          $toolId,
          $feedback['reply']['value'] ?? null
        );

        $functionResults[] = $result->to_responses_api_format();
        $processedCallIds[] = $toolId;
      }
    }

    // Add function results at the end
    return array_merge( $messages, $functionResults );
  }

  /**
  * Add user message with attachments
  */
  private function add_user_message_with_attachments( array $messages, Meow_MWAI_Query_Base $query ): array {
    if ( !$query->attachedFile ) {
      // Simple text message
      $messages[] = [
        'role' => 'user',
        'content' => [
          [
            'type' => 'input_text',
            'text' => $query->get_message()
          ]
        ]
      ];
    }
    else {
      // Message with image attachment
      $content = [
        [
          'type' => 'input_text',
          'text' => $query->get_message()
        ]
      ];

      // Add image
      $imageUrl = $query->image_remote_upload === 'url'
      ? $query->attachedFile->get_url()
        : $query->attachedFile->get_inline_base64_url();

      $content[] = [
        'type' => 'input_image',
        'image_url' => $imageUrl
      ];

      $messages[] = [
        'role' => 'user',
        'content' => $content
      ];
    }

    return $messages;
  }

  /**
  * Build feedback-only messages for Responses API with previous_response_id
  */
  public function build_feedback_only_messages( Meow_MWAI_Query_Feedback $query ): array {
    $messages = [];

    if ( empty( $query->blocks ) ) {
      return $messages;
    }

    $processedCallIds = [];

    foreach ( $query->blocks as $block ) {
      if ( !isset( $block['feedbacks'] ) ) {
        continue;
      }

      foreach ( $block['feedbacks'] as $feedback ) {
        $toolId = $feedback['request']['toolId'] ?? null;

        if ( !$toolId || in_array( $toolId, $processedCallIds ) ) {
          continue;
        }

        // First: Echo the function_call from the model
        if ( isset( $feedback['request']['rawMessage']['tool_calls'] ) ) {
          foreach ( $feedback['request']['rawMessage']['tool_calls'] as $toolCall ) {
            if ( $toolCall['id'] === $toolId ) {
              $functionCall = Meow_MWAI_Data_FunctionCall::from_tool_call( $toolCall );
              $messages[] = [
                'type' => 'function_call',
                'call_id' => $functionCall->id,
                'name' => $functionCall->name,
                'arguments' => $functionCall->get_arguments_json()
              ];
              break;
            }
          }
        }

        // Second: Add the function result
        $result = Meow_MWAI_Data_FunctionResult::success( $toolId, $feedback['reply']['value'] ?? '' );
        $messages[] = $result->to_responses_api_format();

        $processedCallIds[] = $toolId;
      }
    }

    return $messages;
  }

  /**
  * Build messages for Chat Completions API
  */
  public function build_chat_completions_messages( Meow_MWAI_Query_Base $query ): array {
    $messages = [];

    // Add system message if present
    if ( !empty( $query->instructions ) ) {
      $messages[] = [
        'role' => 'system',
        'content' => $query->instructions
      ];
    }

    // Add conversation messages
    if ( !empty( $query->messages ) ) {
      $messages = array_merge( $messages, $query->messages );
    }

    // Add current user message
    if ( !( $query instanceof Meow_MWAI_Query_Feedback ) ) {
      $messages[] = [
        'role' => 'user',
        'content' => $query->get_message()
      ];
    }

    return $messages;
  }

  /**
  * Validate message order for Responses API
  */
  public function validate_message_order( array $messages ): bool {
    // Responses API is flexible with message order
    // but certain patterns should be maintained

    // Check for function_call followed by function_call_output
    for ( $i = 0; $i < count( $messages ) - 1; $i++ ) {
      $current = $messages[$i];
      $next = $messages[$i + 1];

      // If we have a function_call, the next related message should be function_call_output
      if ( isset( $current['type'] ) && $current['type'] === 'function_call' ) {
        if ( isset( $next['type'] ) && $next['type'] === 'function_call_output' ) {
          // Validate matching call_ids
          if ( $current['call_id'] !== $next['call_id'] ) {
            return false;
          }
        }
      }
    }

    return true;
  }
}
