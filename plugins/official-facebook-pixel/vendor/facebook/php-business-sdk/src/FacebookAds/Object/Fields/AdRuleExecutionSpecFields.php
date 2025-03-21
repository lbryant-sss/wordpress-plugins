<?php
 /*
 * Copyright (c) Meta Platforms, Inc. and affiliates.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */

namespace FacebookAds\Object\Fields;

use FacebookAds\Enum\AbstractEnum;

/**
 * This class is auto-generated.
 *
 * For any issues or feature requests related to this class, please let us know
 * on github and we'll fix in our codegen framework. We'll not be able to accept
 * pull request for this class.
 *
 */

class AdRuleExecutionSpecFields extends AbstractEnum {

  const EXECUTION_OPTIONS = 'execution_options';
  const EXECUTION_TYPE = 'execution_type';
  const IS_ONCE_OFF = 'is_once_off';
  const ID = 'id';

  public function getFieldTypes() {
    return array(
      'execution_options' => 'list<AdRuleExecutionOptions>',
      'execution_type' => 'ExecutionType',
      'is_once_off' => 'bool',
      'id' => 'string',
    );
  }
}
