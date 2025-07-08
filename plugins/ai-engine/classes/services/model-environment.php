<?php

class Meow_MWAI_Services_ModelEnvironment {
  private $core;

  public function __construct( $core ) {
    $this->core = $core;
  }

  public function validate_env_model( $query ) {
    if ( !$query || !is_object( $query ) ) {
      throw new Exception( 'Invalid query object provided to validate_env_model.' );
    }

    // The query object uses envId, not env
    $env = $query->envId ?? $query->env ?? null;
    $model = $query->model;

    if ( empty( $env ) && empty( $model ) ) {
      $this->set_default_env_and_model( $query, 'ai_default_env', 'ai_default_model' );
    }
    else if ( empty( $env ) && !empty( $model ) ) {
      // If the model is available in the list of models, we can use it
      $envs = $this->core->get_option( 'ai_envs' );
      $models = $this->core->get_option( 'ai_models' );

      // First check custom models
      if ( !empty( $models ) ) {
        foreach ( $models as $currentModel ) {
          if ( $currentModel['model'] === $model && isset( $currentModel['envId'] ) ) {
            $query->envId = $currentModel['envId'];
            $query->env = $currentModel['envId']; // Set both for compatibility
            $query->model = $currentModel['model'];
            return;
          }
        }
      }

      // Then check models in environments
      foreach ( $envs as $envId => $env ) {
        if ( isset( $env['models'] ) ) {
          foreach ( $env['models'] as $envModel ) {
            if ( $envModel['model'] === $model ) {
              $query->envId = $envId;
              $query->env = $envId; // Set both for compatibility
              $query->model = $model;
              return;
            }
          }
        }
      }

      throw new Exception( 'The environment is required.' );
    }
    else if ( !empty( $env ) && empty( $model ) ) {
      $this->set_default_env_and_model( $query, 'ai_default_env', 'ai_default_model' );
    }
    else {
      // We have both, let's continue
    }
  }

  private function set_default_env_and_model( $query, $envOption, $modelOption ) {
    $env = $this->core->get_option( $envOption );
    $model = $this->core->get_option( $modelOption );
    if ( !empty( $env ) ) {
      // Use envId property which is what the query object uses
      $query->envId = $env;
      $query->env = $env; // Set both for compatibility
    }
    if ( !empty( $model ) ) {
      $query->model = $model;
    }
  }

  public function get_embeddings_env( $envId = null ) {
    // Use provided envId or fall back to default
    if ( empty( $envId ) ) {
      $envId = $this->core->get_option( 'embeddings_default_env' );
    }
    
    // Get embeddings environments (not AI environments)
    $envs = $this->core->get_option( 'embeddings_envs' );
    if ( !empty( $envs ) ) {
      foreach ( $envs as $env ) {
        if ( isset( $env['id'] ) && $env['id'] === $envId ) {
          return $env;
        }
      }
    }
    
    return null;
  }

  public function get_ai_env( $envId ) {
    $envs = $this->core->get_option( 'ai_envs' );
    if ( !empty( $envs ) ) {
      foreach ( $envs as $env ) {
        if ( isset( $env['id'] ) && $env['id'] === $envId ) {
          return $env;
        }
      }
    }
    return null;
  }

  public function get_assistant( $envId, $assistantId ) {
    $env = $this->get_ai_env( $envId );
    if ( isset( $env['assistants'] ) ) {
      foreach ( $env['assistants'] as $assistant ) {
        if ( $assistant['id'] === $assistantId ) {
          return $assistant;
        }
      }
    }
    return null;
  }

  public function get_engine_models( $query ) {
    $envId = $query->env;
    $env = $this->get_ai_env( $envId );
    $models = apply_filters( 'mwai_engine_models', [], $env, $query );
    return $models;
  }
}
