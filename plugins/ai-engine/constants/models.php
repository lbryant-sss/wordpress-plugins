<?php

// Price as of June 2024: https://openai.com/api/pricing/

define( 'MWAI_OPENAI_MODELS', [
  /*
    GPT-5
    The best model for coding and agentic tasks across domains
    https://platform.openai.com/docs/models/gpt-5
    */
  [
    'model' => 'gpt-5',
    'name' => 'GPT-5',
    'family' => 'gpt-5',
    'features' => ['completion'],
    'price' => [
      'in' => 1.25,
      'out' => 10.00,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 128000,
    'maxContextualTokens' => 400000,
    'finetune' => false,
    'tags' => ['core', 'chat', 'vision', 'functions', 'json', 'responses', 'mcp', 'reasoning', 'verbosity'],
    'tools' => ['web_search', 'image_generation', 'file_search', 'code_interpreter'],
    'params' => [
      'reasoning' => ['minimal', 'low', 'medium', 'high'],
      'verbosity' => ['low', 'medium', 'high']
    ]
  ],
  /*
    GPT-5 Mini
    Efficient and cost-effective GPT-5 variant
    https://platform.openai.com/docs/models/gpt-5-mini
    */
  [
    'model' => 'gpt-5-mini',
    'name' => 'GPT-5 Mini',
    'family' => 'gpt-5',
    'features' => ['completion'],
    'price' => [
      'in' => 0.25,
      'out' => 2.00,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 128000,
    'maxContextualTokens' => 400000,
    'finetune' => false,
    'tags' => ['core', 'chat', 'vision', 'functions', 'json', 'responses', 'mcp', 'reasoning', 'verbosity'],
    'tools' => ['web_search', 'image_generation', 'file_search', 'code_interpreter'],
    'params' => [
      'reasoning' => ['minimal', 'low', 'medium', 'high'],
      'verbosity' => ['low', 'medium', 'high']
    ]
  ],
  /*
    GPT-5 Nano
    Ultra-fast and lightweight GPT-5 model
    https://platform.openai.com/docs/models/gpt-5-nano
    */
  [
    'model' => 'gpt-5-nano',
    'name' => 'GPT-5 Nano',
    'family' => 'gpt-5',
    'features' => ['completion'],
    'price' => [
      'in' => 0.05,
      'out' => 0.40,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 128000,
    'maxContextualTokens' => 400000,
    'finetune' => false,
    'tags' => ['core', 'chat', 'vision', 'functions', 'json', 'responses', 'mcp', 'reasoning', 'verbosity'],
    'tools' => ['web_search', 'image_generation', 'file_search', 'code_interpreter'],
    'params' => [
      'reasoning' => ['minimal', 'low', 'medium', 'high'],
      'verbosity' => ['low', 'medium', 'high']
    ]
  ],
  /*
    GPT-5 Chat
    GPT-5 model used in ChatGPT
    https://platform.openai.com/docs/models/gpt-5
    */
  [
    'model' => 'gpt-5-chat-latest',
    'name' => 'GPT-5 Chat',
    'family' => 'gpt-5',
    'features' => ['completion'],
    'price' => [
      'in' => 1.25,
      'out' => 10.00,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 16384,
    'maxContextualTokens' => 128000,
    'finetune' => false,
    'tags' => ['core', 'chat', 'vision', 'responses', 'mcp'],
    'tools' => ['web_search', 'image_generation', 'file_search', 'code_interpreter'],
    'params' => [
      'verbosity' => ['low', 'medium', 'high']
    ]
  ],
  /*
    GPT 4.1
    Flagship GPT model for complex tasks
    https://platform.openai.com/docs/models/gpt-4.1
    */
  [
    'model' => 'gpt-4.1',
    'name' => 'GPT-4.1',
    'family' => 'gpt-4',
    'features' => ['completion'],
    'price' => [
      'in' => 2.00,
      'out' => 8.00,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 32768,
    'maxContextualTokens' => 1047576,
    'finetune' => false,
    'tags' => ['core', 'chat', 'vision', 'functions', 'json', 'finetune', 'responses', 'mcp'],
    'tools' => ['web_search', 'image_generation', 'code_interpreter']
  ],
  /*
      GPT-4.1 mini
      Balanced for intelligence, speed, and cost
      https://platform.openai.com/docs/models/gpt-4.1-mini
      */
  [
    'model' => 'gpt-4.1-mini',
    'name' => 'GPT-4.1 Mini',
    'family' => 'gpt-4',
    'features' => ['completion'],
    'price' => [
      'in' => 0.40,
      'out' => 1.60,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 32768,
    'maxContextualTokens' => 1047576,
    'finetune' => false,
    'tags' => ['core', 'chat', 'vision', 'functions', 'json', 'finetune', 'responses', 'mcp'],
    'tools' => ['web_search', 'image_generation', 'code_interpreter']
  ],
  /*
        GPT-4.1 nano
        Fastest, most cost-effective GPT-4.1 model
        https://platform.openai.com/docs/models/gpt-4.1-nano
        */
  [
    'model' => 'gpt-4.1-nano',
    'name' => 'GPT-4.1 Nano',
    'family' => 'gpt-4',
    'features' => ['completion'],
    'price' => [
      'in' => 0.10,
      'out' => 0.40,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 32768,
    'maxContextualTokens' => 1047576,
    'finetune' => false,
    'tags' => ['core', 'chat', 'vision', 'functions', 'json', 'finetune', 'responses', 'mcp'],
    'tools' => ['image_generation']
  ],
  /*
          GPT-4o
          Fast, intelligent, flexible GPT model
          https://platform.openai.com/docs/models/gpt-4o
          */
  [
    'model' => 'gpt-4o',
    'name' => 'GPT-4o',
    'family' => 'gpt-4',
    'features' => ['completion'],
    'price' => [
      'in' => 2.50,
      'out' => 10.00,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 16384,
    'maxContextualTokens' => 128000,
    'finetune' => false,
    'tags' => ['core', 'chat', 'vision', 'functions', 'json', 'finetune', 'mcp', 'responses'],
    'tools' => ['web_search', 'image_generation', 'code_interpreter']
  ],
  /*
            GPT-4o mini
            Fast, affordable small model for focused tasks
            https://platform.openai.com/docs/models/gpt-4o-mini
            */
  [
    'model' => 'gpt-4o-mini',
    'name' => 'GPT-4o Mini',
    'family' => 'gpt-4',
    'features' => ['completion'],
    'price' => [
      'in' => 0.15,
      'out' => 0.60,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 16384,
    'maxContextualTokens' => 128000,
    'finetune' => [
      'in' => 0.15,
      'out' => 0.60,
      'train' => 3.00
    ],
    'tags' => ['core', 'chat', 'vision', 'functions', 'json', 'finetune', 'mcp', 'responses'],
    'tools' => ['web_search', 'image_generation', 'code_interpreter']
  ],
  /*
            o1
            High-intelligence reasoning mode
            https://platform.openai.com/docs/models/o1
            */
  [
    'model' => 'o1',
    'name' => 'o1',
    'family' => 'o1',
    'features' => ['completion'],
    'price' => [
      'in' => 15.00,
      'out' => 60.00,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 100000,
    'maxContextualTokens' => 200000,
    'tags' => ['core', 'chat', 'o1-model', 'reasoning', 'mcp']
  ],
  [
    'model' => 'o1-mini',
    'name' => 'o1 Mini',
    'family' => 'o1',
    'features' => ['completion'],
    'price' => [
      'in' => 1.10,
      'out' => 4.40,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 65536,
    'maxContextualTokens' => 128000,
    'tags' => ['core', 'chat', 'o1-model', 'reasoning', 'mcp']
  ],
  /*
            o3
            Advanced reasoning model
            https://platform.openai.com/docs/models/o3
            */
  [
    'model' => 'o3',
    'name' => 'o3',
    'family' => 'o3',
    'features' => ['completion'],
    'price' => [
      'in' => 15.00,
      'out' => 60.00,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 100000,
    'maxContextualTokens' => 200000,
    'tags' => ['core', 'chat', 'o1-model', 'reasoning', 'responses', 'mcp'],
    'tools' => ['web_search', 'image_generation', 'code_interpreter']
  ],
  /*
              o3-mini
              Fast, flexible, intelligent reasoning model
              https://platform.openai.com/docs/models/o3-mini
              */
  [
    'model' => 'o3-mini',
    'name' => 'o3 Mini',
    'family' => 'o3',
    'features' => ['completion'],
    'price' => [
      'in' => 1.10,
      'out' => 4.40,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 100000,
    'maxContextualTokens' => 200000,
    'tags' => ['core', 'chat', 'o1-model', 'reasoning', 'responses', 'mcp'],
    'tools' => ['web_search', 'image_generation', 'code_interpreter']
  ],
  /*
                GPT Realtime
                Production-ready speech-to-speech model with MCP, image input, and SIP support
                https://platform.openai.com/docs/models/gpt-realtime
                */
  [
    'model' => 'gpt-realtime',
    'name' => 'GPT Realtime',
    'family' => 'realtime',
    'features' => ['core', 'realtime', 'functions'],
    'price' => [
      'text' => [
        'in' => 4.00,
        'cache' => 0.40,
        'out' => 16.00,
      ],
      'audio' => [
        'in' => 32.00,
        'cache' => 0.40,
        'out' => 64.00,
      ],
      'image' => [
        'in' => 5.00,
        'cache' => 0.50,
      ]
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 4096,
    'maxContextualTokens' => 128000,
    'finetune' => false,
    'tags' => ['core', 'realtime', 'functions', 'vision', 'mcp']
  ],
  /*
                GPT-4o Realtime
                Model capable of realtime text and audio inputs and outputs
                https://platform.openai.com/docs/models/gpt-4o-realtime-preview
                */
  [
    'model' => 'gpt-4o-realtime-preview',
    'name' => 'GPT-4o Realtime (Preview)',
    'family' => 'realtime',
    'features' => ['core', 'realtime', 'functions'],
    'price' => [
      'text' => [
        'in' => 5.00,
        'cache' => 2.50,
        'out' => 20.00,
      ],
      'audio' => [
        'in' => 40.00,
        'cache' => 2.50,
        'out' => 80.00,
      ]
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 4096,
    'maxContextualTokens' => 128000,
    'finetune' => false,
    'tags' => ['core', 'realtime', 'functions']
  ],
  /*
            GPT-4o mini Realtime
            Smaller realtime model for text and audio inputs and outputs
            https://platform.openai.com/docs/models/gpt-4o-mini-realtime-preview
            */
  [
    'model' => 'gpt-4o-mini-realtime-preview',
    'name' => 'GPT-4o Mini Realtime (Preview)',
    'family' => 'realtime',
    'features' => ['core', 'realtime', 'functions'],
    'price' => [
      'text' => [
        'in' => 0.60,
        'cache' => 0.30,
        'out' => 2.40,
      ],
      'audio' => [
        'in' => 10.00,
        'cache' => 0.30,
        'out' => 20.00,
      ]
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 4096,
    'maxContextualTokens' => 128000,
    'finetune' => false,
    'tags' => ['core', 'realtime', 'functions']
  ],
  /*
        GPT-4
        An older high-intelligence GPT model
        https://platform.openai.com/docs/models/gpt-4
        */
  [
    'model' => 'gpt-4',
    'name' => 'GPT-4',
    'family' => 'gpt-4',
    'features' => ['completion'],
    'price' => [
      'in' => 30.00,
      'out' => 60.00,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 8192,
    'maxContextualTokens' => 8192,
    'finetune' => false,
    'tags' => ['core', 'chat', 'functions']
  ],
  /*
        GPT-4 Turbo
        An older high-intelligence GPT model
        https://platform.openai.com/docs/models/gpt-4-turbo
        */
  [
    'model' => 'gpt-4-turbo',
    'name' => 'GPT-4 Turbo',
    'family' => 'gpt-4',
    'features' => ['completion'],
    'price' => [
      'in' => 10.00,
      'out' => 30.00,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 4096,
    'maxContextualTokens' => 128000,
    'finetune' => false,
    'tags' => ['core', 'chat', 'vision', 'functions', 'json']
  ],
  /*
        GPT-3.5 Turbo
        Legacy GPT model for cheaper chat and non-chat tasks
        https://platform.openai.com/docs/models/gpt-3.5-turbo
        */
  [
    'model' => 'gpt-3.5-turbo',
    'name' => 'GPT-3.5 Turbo',
    'family' => 'gpt-3',
    'features' => ['completion'],
    'price' => [
      'in' => 0.50,
      'out' => 1.50,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 4096,
    'maxContextualTokens' => 16385,
    'finetune' => [
      'in' => 3.00,
      'out' => 6.00,
      'train' => 8.00
    ],
    'tags' => ['core', 'chat', '4k', 'finetune', 'functions']
  ],
  /*
      DALL·E 3
      Our latest image generation model
      https://platform.openai.com/docs/models/dall-e-3
      */
  [
    'model' => 'gpt-image-1',
    'name' => 'GPT Image 1 (High)',
    'family' => 'gpt-image',
    'features' => ['text-to-image'],
    'resolutions' => [
      [
        'name' => '1024x1024',
        'label' => '1024x1024',
        'price' => 0.167
      ],
      [
        'name' => '1024x1536',
        'label' => '1024x1536',
        'price' => 0.25
      ],
      [
        'name' => '1536x1024',
        'label' => '1536x1024',
        'price' => 0.25
      ]
    ],
    'type' => 'image',
    'unit' => 1,
    'finetune' => false,
    'tags' => ['core', 'image', 'image-edit', 'responses']
  ],
  [
    'model' => 'dall-e-3',
    'name' => 'DALL-E 3',
    'family' => 'dall-e',
    'features' => ['text-to-image'],
    'resolutions' => [
      [
        'name' => '1024x1024',
        'label' => '1024x1024',
        'price' => 0.040
      ],
      [
        'name' => '1024x1792',
        'label' => '1024x1792',
        'price' => 0.080
      ],
      [
        'name' => '1792x1024',
        'label' => '1792x1024',
        'price' => 0.080
      ]
    ],
    'type' => 'image',
    'unit' => 1,
    'finetune' => false,
    'tags' => ['core', 'image']
  ],
  [
    'model' => 'dall-e-3-hd',
    'name' => 'DALL-E 3 (HD)',
    'family' => 'dall-e',
    'features' => ['text-to-image'],
    'resolutions' => [
      [
        'name' => '1024x1024',
        'label' => '1024x1024',
        'price' => 0.080
      ],
      [
        'name' => '1024x1792',
        'label' => '1024x1792',
        'price' => 0.120
      ],
      [
        'name' => '1792x1024',
        'label' => '1792x1024',
        'price' => 0.120
      ]
    ],
    'type' => 'image',
    'unit' => 1,
    'finetune' => false,
    'tags' => ['core', 'image']
  ],
  // Embedding models:
  [
    'model' => 'text-embedding-3-small',
    'name' => 'Embedding 3-Small',
    'family' => 'text-embedding',
    'features' => ['embedding'],
    'price' => 0.02,
    'type' => 'token',
    'unit' => 1 / 1000000,
    'finetune' => false,
    'dimensions' => [ 512, 1536 ],
    'tags' => ['core', 'embedding'],
  ],
  [
    'model' => 'text-embedding-3-large',
    'name' => 'Embedding 3-Large',
    'family' => 'text-embedding',
    'features' => ['embedding'],
    'price' => 0.13,
    'type' => 'token',
    'unit' => 1 / 1000000,
    'finetune' => false,
    'dimensions' => [ 256, 1024, 3072 ],
    'tags' => ['core', 'embedding'],
  ],
  [
    'model' => 'text-embedding-ada-002',
    'name' => 'Embedding Ada-002',
    'family' => 'text-embedding',
    'features' => ['embedding'],
    'price' => 0.10,
    'type' => 'token',
    'unit' => 1 / 1000000,
    'finetune' => false,
    'dimensions' => [ 1536 ],
    'tags' => ['core', 'embedding'],
  ],
  // Audio Models:
  [
    'model' => 'gpt-4o-transcribe',
    'name' => 'GPT-4o Transcribe',
    'family' => 'whisper',
    'features' => ['speech-to-text'],
    'price' => 0.006,
    'type' => 'second',
    'unit' => 1,
    'finetune' => false,
    'tags' => ['core', 'audio'],
  ],
  [
    'model' => 'gpt-4o-mini-transcribe',
    'name' => 'GPT-4o Mini Transcribe',
    'family' => 'whisper',
    'features' => ['speech-to-text'],
    'price' => 0.003,
    'type' => 'second',
    'unit' => 1,
    'finetune' => false,
    'tags' => ['core', 'audio'],
  ],
  [
    'model' => 'whisper-1',
    'name' => 'Whisper',
    'family' => 'whisper',
    'features' => ['speech-to-text'],
    'price' => 0.006,
    'type' => 'second',
    'unit' => 1,
    'finetune' => false,
    'tags' => ['core', 'audio'],
  ],
  /*
                  Depecated Models
                  */
  [
    'model' => 'gpt-4.5-preview',
    'name' => 'GPT-4.5 (Preview)',
    'family' => 'gpt4.5',
    'features' => ['completion'],
    'price' => [
      'in' => 75.00,
      'out' => 150.00,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 16384,
    'maxContextualTokens' => 128000,
    'finetune' => false,
    'tags' => ['core', 'chat', 'vision', 'functions', 'json', 'deprecated']
  ],
  [
    'model' => 'dall-e',
    'name' => 'DALL-E 2',
    'family' => 'dall-e',
    'features' => ['text-to-image'],
    'resolutions' => [
      [
        'name' => '256x256',
        'label' => '256x256',
        'price' => 0.016
      ],
      [
        'name' => '512x512',
        'label' => '512x512',
        'price' => 0.018
      ],
      [
        'name' => '1024x1024',
        'label' => '1024x1024',
        'price' => 0.020
      ]
    ],
    'type' => 'image',
    'unit' => 1,
    'finetune' => false,
    'tags' => ['core', 'image', 'deprecated']
  ],
  // [
  //   "model" => "gpt-3.5-turbo-16k",
  //   "description" => "Offers 4 times the context length of gpt-3.5-turbo at twice the price.",
  //   "name" => "GPT-3.5 Turbo 16k",
  //   "family" => "turbo",
  //   "features" => ['completion'],
  //   "price" => [
  //     "in" => 30.00,
  //     "out" => 40.0,
  //   ],
  //   "type" => "token",
  //   "unit" => 1 / 1000000,
  //   "maxTokens" => 16385,
  //   "finetune" => false,
  //   "tags" => ['core', 'chat', '16k']
  // ],
  // [
  //   "model" => "gpt-3.5-turbo-instruct",
  //   "name" => "GPT-3.5 Turbo Instruct",
  //   "family" => "turbo-instruct",
  //   "features" => ['completion'],
  //   "price" => [
  //     "in" => 0.50,
  //     "out" => 2.00,
  //   ],
  //   "type" => "token",
  //   "unit" => 1 / 1000000,
  //   "finetune" => [
  //     "in" => 0.03,
  //     "out" => 0.06,
  //   ],
  //   "maxTokens" => 4096,
  //   "tags" => ['core', 'chat', '4k']
  // ],
] );

define( 'MWAI_ANTHROPIC_MODELS', [
  [
    'model' => 'claude-opus-4-20250514',
    'name' => 'Claude-4 Opus (2025/05/14)',
    'family' => 'claude-4',
    'features' => ['completion'],
    'price' => [
      'in' => 15.00,
      'out' => 75.00,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 32000,
    'maxContextualTokens' => 200000,
    'finetune' => false,
    'tags' => ['core', 'chat', 'vision', 'functions', 'reasoning', 'mcp']
  ],
  [
    'model' => 'claude-sonnet-4-20250514',
    'name' => 'Claude-4 Sonnet (2025/05/14)',
    'family' => 'claude-4',
    'features' => ['completion'],
    'price' => [
      'in' => 3.00,
      'out' => 15.00,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 64000,
    'maxContextualTokens' => 200000,
    'finetune' => false,
    'tags' => ['core', 'chat', 'vision', 'functions', 'reasoning', 'mcp']
  ],
  [
    'model' => 'claude-3-7-sonnet-latest',
    'name' => 'Claude-3.7 Sonnet',
    'family' => 'claude-3',
    'features' => ['completion'],
    'price' => [
      'in' => 3.00,
      'out' => 15.00,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 64000,
    'maxContextualTokens' => 200000,
    'finetune' => false,
    'tags' => ['core', 'chat', 'vision', 'functions', 'reasoning', 'mcp']
  ],
  [
    'model' => 'claude-3-5-sonnet-latest',
    'name' => 'Claude-3.5 Sonnet',
    'family' => 'claude-3',
    'features' => ['completion'],
    'price' => [
      'in' => 3.00,
      'out' => 15.00,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 4096,
    'maxContextualTokens' => 200000,
    'finetune' => false,
    'tags' => ['core', 'chat', 'vision', 'functions', 'mcp']
  ],
  [
    'model' => 'claude-3-5-sonnet-20241022',
    'name' => 'Claude-3.5 Sonnet (2024/10/22)',
    'family' => 'claude-3',
    'features' => ['completion'],
    'price' => [
      'in' => 3.00,
      'out' => 15.00,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 4096,
    'maxContextualTokens' => 200000,
    'finetune' => false,
    'tags' => ['core', 'chat', 'vision', 'files', 'functions', 'mcp']
  ],
  [
    'model' => 'claude-3-5-sonnet-20240620',
    'name' => 'Claude-3.5 Sonnet (2024/06/20)',
    'family' => 'claude-3',
    'features' => ['completion'],
    'price' => [
      'in' => 3.00,
      'out' => 15.00,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 4096,
    'maxContextualTokens' => 200000,
    'finetune' => false,
    'tags' => ['core', 'chat', 'vision', 'functions', 'mcp']
  ],
  [
    'model' => 'claude-3-sonnet-20240229',
    'name' => 'Claude-3 Sonnet (2024/02/29)',
    'family' => 'claude-3',
    'features' => ['completion'],
    'price' => [
      'in' => 3.00,
      'out' => 15.00,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 4096,
    'maxContextualTokens' => 200000,
    'finetune' => false,
    'tags' => ['core', 'chat', 'vision', 'functions', 'deprecated']
  ],
  [
    'model' => 'claude-3-opus-latest',
    'name' => 'Claude-3 Opus (Latest)',
    'family' => 'claude-3',
    'features' => ['completion'],
    'price' => [
      'in' => 15.00,
      'out' => 75.00,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 4096,
    'maxContextualTokens' => 200000,
    'finetune' => false,
    'tags' => ['core', 'chat', 'vision', 'functions']
  ],
  [
    // TODO: Starting January 5, 2026 at 9AM PT, Anthropic is retiring and will no longer support Claude Opus 3 (claude-3-opus-20240229) on the API.
    'model' => 'claude-3-opus-20240229',
    'name' => 'Claude-3 Opus (2024/02/29)',
    'family' => 'claude-3',
    'features' => ['completion'],
    'price' => [
      'in' => 15.00,
      'out' => 75.00,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 4096,
    'maxContextualTokens' => 200000,
    'finetune' => false,
    'tags' => ['core', 'chat', 'vision', 'functions']
  ],
  [
    'model' => 'claude-3-5-haiku-20241022',
    'name' => 'Claude-3.5 Haiku (2024/10/22)',
    'family' => 'claude-3',
    'features' => ['completion'],
    'price' => [
      'in' => 1.00,
      'out' => 5.00,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 8192,
    'maxContextualTokens' => 200000,
    'finetune' => false,
    'tags' => ['core', 'chat']
  ],
  [
    'model' => 'claude-3-haiku-20240307',
    'name' => 'Claude-3 Haiku (2024/03/07)',
    'family' => 'claude-3',
    'features' => ['completion'],
    'price' => [
      'in' => 0.25,
      'out' => 1.25,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'maxCompletionTokens' => 4096,
    'maxContextualTokens' => 200000,
    'finetune' => false,
    'tags' => ['core', 'chat', 'vision', 'functions']
  ]
] );

define( 'MWAI_PERPLEXITY_MODELS', [
  [
    'model' => 'sonar-pro',
    'name' => 'Sonar Pro',
    'family' => 'sonar',
    'features' => ['completion'],
    'price' => [
      'in' => 3.00,
      'out' => 15.00,
      'search' => 5.00,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'searchUnit' => 1 / 1000,
    'maxCompletionTokens' => 8192,
    'maxContextualTokens' => 200000,
    'finetune' => false,
    'tags' => ['core', 'chat'],
  ],
  [
    'model' => 'sonar',
    'name' => 'Sonar',
    'family' => 'sonar',
    'features' => ['completion'],
    'price' => [
      'in' => 1.00,
      'out' => 1.00,
      'search' => 5.00,
    ],
    'type' => 'token',
    'unit' => 1 / 1000000,
    'searchUnit' => 1 / 1000,
    'maxCompletionTokens' => 4096,
    'maxContextualTokens' => 127000,
    'finetune' => false,
    'tags' => ['core', 'chat'],
  ],
] );

// Mistral AI Models
// Models are fetched dynamically from the Mistral API
define( 'MWAI_MISTRAL_MODELS', [] );
