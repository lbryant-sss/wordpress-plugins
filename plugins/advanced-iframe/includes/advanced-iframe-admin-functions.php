<?php
defined('_VALID_AI') or die('Direct Access to this location is not allowed.');

function printFullscreenButtonStyle($options, $label, $id, $description, $url = '') {
  if (!isset($options[$id]) || empty($options[$id])) {
    $options[$id] = 'black';
  }

  $offset = '';
  if (ai_startsWith($label, 'i-')) {
    $offset = 'class="' . substr($label, 0, 5) . '" ';
    $label = substr($label, 5);
  }

  echo '
      <tr id="tr-' . $id . '">
      <th scope="row" ' . $offset . '>' . $label . renderExampleIcon($url) . '</th>
      <td><span class="hide-print">
      ';
  echo '<input type="radio" id="' . $id . '1" name="' . $id . '" value="black" ';
  if ($options[$id] === "black") {
    echo 'checked="checked"';
  }
  echo ' /> ' . __('Black', 'advanced-iframe');

  echo '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '2" name="' . $id . '" value="black2" ';
  if ($options[$id] === "black2") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('Black 2', 'advanced-iframe');

  echo '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '3" name="' . $id . '" value="white" ';
  if ($options[$id] === "white") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('White', 'advanced-iframe');


  echo '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '4" name="' . $id . '" value="custom" ';
  if ($options[$id] === "custom") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('Custom', 'advanced-iframe');


  echo '<br>
    </span><p class="description">' . $description . '</p></td>
    </tr>
    ';
}

function printAiExternalLocal($options, $label, $id, $description, $default = 'false', $url = '') {
  if (!isset($options[$id]) || empty($options[$id])) {
    $options[$id] = $default;
  }

  echo '
      <tr id="tr-' . $id . '">
      <th scope="row">' . $label . renderExampleIcon($url) . '</th>
      <td><span class="hide-print">
      ';
  echo '<input type="radio" id="' . $id . '1" name="' . $id . '" value="true" ';
  if ($options[$id] === "true") {
    echo 'checked="checked"';
  }
  echo ' /> ' . __('Site', 'advanced-iframe');

  echo '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '2" name="' . $id . '" value="admin" ';
  if ($options[$id] === "admin") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('Admin', 'advanced-iframe');

  echo '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '3" name="' . $id . '" value="all" ';
  if ($options[$id] === "all") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('All', 'advanced-iframe');


  echo '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '4" name="' . $id . '" value="false" ';
  if ($options[$id] === "false") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('No', 'advanced-iframe');


  echo '<br>
    </span><p class="description">' . $description . '</p></td>
    </tr>
    ';
}

/**
 * lazy: is a good candidate for lazy-loading.
 * eager: is not a good candidate for lazy-loading. Load right away.
 * auto: browser will determine whether or not to lazily load.
 * false: loading is not rendered at all.
 */
function printAiLazy($options, $label, $id, $description, $url = '', $default = 'lazy') {
  if (!isset($options[$id]) || empty($options[$id])) {
    $options[$id] = $default;
  }

  echo '
      <tr id="tr-' . $id . '">
      <th scope="row">' . $label . renderExampleIcon($url) . '</th>
      <td><span class="hide-print">
      ';
  echo '<input type="radio" id="' . $id . '1" name="' . $id . '" value="lazy" ';
  if ($options[$id] === "lazy") {
    echo 'checked="checked"';
  }
  echo ' /> ' . __('lazy', 'advanced-iframe');

  echo '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '2" name="' . $id . '" value="eager" ';
  if ($options[$id] === "eager") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('eager', 'advanced-iframe');

  echo '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '3" name="' . $id . '" value="auto" ';
  if ($options[$id] === "auto") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('auto', 'advanced-iframe');


  echo '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '4" name="' . $id . '" value="false" ';
  if ($options[$id] === "false") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('No', 'advanced-iframe');


  echo '<br>
    </span><p class="description">' . $description . '</p></td>
    </tr>
    ';
}


/**
 * Prints the fullscreen radio selection
 */
function printFullscreenButton($options, $label, $id, $description, $url = '', $default = 'false') {
  if (!isset($options[$id]) || empty($options[$id])) {
    $options[$id] = $default;
  }

  echo '
      <tr id="tr-' . $id . '">
      <th scope="row">' . $label . renderExampleIcon($url) . '</th>
      <td><span class="hide-print">
      ';
  echo '<input type="radio" id="' . $id . '1" name="' . $id . '" value="top" ';
  if ($options[$id] === "top") {
    echo 'checked="checked"';
  }
  echo ' /> ' . __('Right top corner &nbsp; ', 'advanced-iframe');

  echo '<input type="radio" id="' . $id . '2" name="' . $id . '" value="top_scroll" ';
  if ($options[$id] === "top_scroll") {
    echo 'checked="checked"';
  }
  echo ' /> ' . __('Right top corner + scrollbar', 'advanced-iframe');

  echo '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '3" name="' . $id . '" value="bottom" ';
  if ($options[$id] === "bottom") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('Right bottom corner', 'advanced-iframe');

  echo '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '4" name="' . $id . '" value="bottom_scroll" ';
  if ($options[$id] === "bottom_scroll") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('Right bottom corner + scrollbar', 'advanced-iframe');

  echo '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '5" name="' . $id . '" value="top_left" ';
  if ($options[$id] === "top_left") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('Left top corner', 'advanced-iframe');

  echo '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '6" name="' . $id . '" value="bottom_left" ';
  if ($options[$id] === "bottom_left") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('Left bottom corner', 'advanced-iframe');

  echo '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '7" name="' . $id . '" value="false" ';
  if ($options[$id] === "false") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('No', 'advanced-iframe');


  echo '<br>
    </span><p class="description">' . $description . '</p></td>
    </tr>
    ';
}

/**
 *  Prints a simple true/false radio selection
 */
function printTrueFalse($isPro, $options, $label, $id, $description, $default = 'false', $url = '', $showSave = false) {
  if (!isset($options[$id]) || empty($options[$id])) {
    $options[$id] = $default;
  }

  $offset = '';
  if (ai_startsWith($label, 'i-')) {
    $offset = 'class="' . substr($label, 0, 5) . '" ';
    $label = substr($label, 5);
  }

  if (!isset($options['demo']) || $options['demo'] === 'false') {
    $isPro = false;
  }
  $pro_class = $isPro ? ' class="ai-pro"' : '';

  if ($isPro) {
    $label = '<span title="Pro feature">' . $label . '</span>';
  }

  echo '
      <tr' . $pro_class . ' id="tr-' . $id . '">
      <th scope="row" ' . $offset . '>' . $label . renderExampleIcon($url) . renderExternalWorkaroundIcon($showSave) . '</th>
      <td><span class="hide-print">
      ';
  echo '<input type="radio" id="' . $id . '1" name="' . $id . '" value="true" ';
  if ($options[$id] === "true") {
    echo 'checked="checked"';
  }
  echo ' /> ' . __('Yes', 'advanced-iframe') . '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '2" name="' . $id . '" value="false" ';
  if ($options[$id] === "false") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('No', 'advanced-iframe') . '<br>
    </span><p class="description">' . $description . '</p></td>
    </tr>
    ';
}

/**
 *  Prints a radio selection for the external workaround
 */
function printTrueFalseHeight($isPro, $options, $label, $id, $description, $default = 'false', $url = '', $showSave = false) {
  if (!isset($options[$id]) || empty($options[$id])) {
    $options[$id] = $default;
  }

  $offset = '';
  if (ai_startsWith($label, 'i-')) {
    $offset = 'class="' . substr($label, 0, 5) . '" ';
    $label = substr($label, 5);
  }

  if (!isset($options['demo']) || $options['demo'] === 'false') {
    $isPro = false;
  }
  $pro_class = $isPro ? ' class="ai-pro"' : '';

  if ($isPro) {
    $label = '<span title="Pro feature">' . $label . '</span>';
  }

  echo '
      <tr' . $pro_class . ' id="tr-' . $id . '">
      <th scope="row" ' . $offset . '>' . $label . renderExampleIcon($url) . renderExternalWorkaroundIcon($showSave) . '</th>
      <td><span class="hide-print">
      ';
  echo '<input type="radio" id="' . $id . '1" name="' . $id . '" value="true" ';
  if ($options[$id] === "true") {
    echo 'checked="checked"';
  }
  echo ' /> ' . __('Yes', 'advanced-iframe') . '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '2" name="' . $id . '" value="external" ';
  if ($options[$id] === "external") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('External', 'advanced-iframe') . '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '3" name="' . $id . '" value="false" ';
  if ($options[$id] === "false") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('No', 'advanced-iframe') . '<br>
    </span><p class="description">' . $description . '</p></td>
    </tr>
    ';
}


function printTopBottom($options, $label, $id, $description, $default = 'top', $url = '', $showSave = false) {
  if (!isset($options[$id]) || empty($options[$id])) {
    $options[$id] = $default;
  }

  $offset = '';
  if (ai_startsWith($label, 'i-')) {
    $offset = 'class="' . substr($label, 0, 5) . '" ';
    $label = substr($label, 5);
  }

  $isPro = true;
  if (!isset($options['demo']) || $options['demo'] === 'false') {
    $isPro = false;
  }
  $pro_class = $isPro ? ' class="ai-pro"' : '';

  if ($isPro) {
    $label = '<span title="Pro feature">' . $label . '</span>';
  }

  echo '
      <tr' . $pro_class . ' id="tr-' . $id . '">
      <th scope="row" ' . $offset . '>' . $label . renderExampleIcon($url) . renderExternalWorkaroundIcon($showSave) . '</th>
      <td><span class="hide-print">
      ';
  echo '<input type="radio" id="' . $id . '1" name="' . $id . '" value="top" ';
  if ($options[$id] === "top") {
    echo 'checked="checked"';
  }
  echo ' /> ' . __('Top', 'advanced-iframe') . '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '2" name="' . $id . '" value="bottom" ';
  if ($options[$id] === "bottom") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('Bottom', 'advanced-iframe') . '<br>
    </span><p class="description">' . $description . '</p></td>
    </tr>
    ';
}


/**
 *  Prints the input field for the scrolling settings
 */
function printAutoNo($options, $label, $id, $description) {
  $offset = '';
  if (ai_startsWith($label, 'i-')) {
    $offset = 'class="' . substr($label, 0, 5) . '" ';
    $label = substr($label, 5);
  }

  echo '
      <tr id="tr-' . $id . '">
      <th scope="row" ' . $offset . '>' . $label . '</th>
      <td><span class="hide-print">
      ';
  echo '<input type="radio" id="' . $id . '1" name="' . $id . '" value="auto" ';
  if ($options[$id] === "auto") {
    echo 'checked="checked"';
  }
  echo ' /> ' . __('Yes', 'advanced-iframe') . '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '2" name="' . $id . '" value="no" ';
  if ($options[$id] === "no") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('No', 'advanced-iframe') . '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '3" name="' . $id . '" value="none" ';
  if ($options[$id] === "none") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('Not rendered', 'advanced-iframe') . '<br></span>
    <p class="description">' . $description . '</p></td>
    </tr>
    ';
}

/**
 *  Prints the input field for the auto zoom settings
 */
function printSameRemote($options, $label, $id, $description, $url = '', $showSave = false) {
  $offset = '';
  if (ai_startsWith($label, 'i-')) {
    $offset = 'class="' . substr($label, 0, 5) . '" ';
    $label = substr($label, 5);
  }

  $isPro = true;
  if (!isset($options['demo']) || $options['demo'] === 'false') {
    $isPro = false;
  }
  $pro_class = $isPro ? ' class="ai-pro"' : '';

  if ($isPro) {
    $label = '<span title="Pro feature">' . $label . '</span>';
  }

  echo '
      <tr' . $pro_class . ' id="tr-' . $id . '">
      <th scope="row" ' . $offset . '>' . $label . renderExampleIcon($url) . renderExternalWorkaroundIcon($showSave) . '</th>
      <td><span class="hide-print">
      ';
  echo '<input type="radio" id="' . $id . '1" name="' . $id . '" value="same" ';
  if ($options[$id] === "same") {
    echo 'checked="checked"';
  }
  echo ' /> ' . __('Same domain', 'advanced-iframe') . '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '2" name="' . $id . '" value="remote" ';
  if ($options[$id] === "remote") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('Remote domain', 'advanced-iframe') . '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '3" name="' . $id . '" value="false" ';
  if ($options[$id] === "false") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('No', 'advanced-iframe') . '<br></span>
    <p class="description">' . $description . '</p></td>
    </tr>
    ';
}


function printTrueExternalFalse($options, $label, $id, $description, $url = '', $showSave = false) {
  $offset = '';
  if (ai_startsWith($label, 'i-')) {
    $offset = 'class="' . substr($label, 0, 5) . '" ';
    $label = substr($label, 5);
  }

  $isPro = true;
  if (!isset($options['demo']) || $options['demo'] === 'false') {
    $isPro = false;
  }
  $pro_class = $isPro ? ' class="ai-pro"' : '';

  if ($isPro) {
    $label = '<span title="Pro feature">' . $label . '</span>';
  }

  echo '
      <tr' . $pro_class . ' id="tr-' . $id . '">
      <th scope="row" ' . $offset . '>' . $label . renderExampleIcon($url) . renderExternalWorkaroundIcon($showSave) . '</th>
      <td><span class="hide-print">
      ';
  echo '<input type="radio" id="' . $id . '1" name="' . $id . '" value="true" ';
  if ($options[$id] === "true") {
    echo 'checked="checked"';
  }
  echo ' /> ' . __('Yes', 'advanced-iframe') . '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '2" name="' . $id . '" value="external" ';
  if ($options[$id] === "external") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('External', 'advanced-iframe') . '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '3" name="' . $id . '" value="false" ';
  if ($options[$id] === "false") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('No', 'advanced-iframe') . '<br></span>
    <p class="description">' . $description . '</p></td>
    </tr>
    ';
}

function printTrueDebugFalse($options, $label, $id, $description, $url = '', $showSave = false) {
  $offset = '';
  if (ai_startsWith($label, 'i-')) {
    $offset = 'class="' . substr($label, 0, 5) . '" ';
    $label = substr($label, 5);
  }

  $isPro = true;
  if (!isset($options['demo']) || $options['demo'] === 'false') {
    $isPro = false;
  }
  $pro_class = $isPro ? ' class="ai-pro"' : '';

  if ($isPro) {
    $label = '<span title="Pro feature">' . $label . '</span>';
  }

  echo '
      <tr' . $pro_class . ' id="tr-' . $id . '">
      <th scope="row" ' . $offset . '>' . $label . renderExampleIcon($url) . renderExternalWorkaroundIcon($showSave) . '</th>
      <td><span class="hide-print">
      ';
  echo '<input type="radio" id="' . $id . '1" name="' . $id . '" value="true" ';
  if ($options[$id] === "true") {
    echo 'checked="checked"';
  }
  echo ' /> ' . __('Yes', 'advanced-iframe') . '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '2" name="' . $id . '" value="debug" ';
  if ($options[$id] === "debug") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('Debug', 'advanced-iframe') . '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '3" name="' . $id . '" value="false" ';
  if ($options[$id] === "false") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('No (iframe)', 'advanced-iframe') . '<br></span>
    <p class="description">' . $description . '</p></td>
    </tr>
    ';
}

function printTrueFalseFull($options, $label, $id, $description, $url = '') {
  if (!isset($options[$id]) || empty($options[$id])) {
    $options[$id] = 'false';
  }

  $offset = '';
  if (ai_startsWith($label, 'i-')) {
    $offset = 'class="' . substr($label, 0, 5) . '" ';
    $label = substr($label, 5);
  }

  $isPro = true;
  if (!isset($options['demo']) || $options['demo'] === 'false') {
    $isPro = false;
  }
  $pro_class = $isPro ? ' class="ai-pro"' : '';

  if ($isPro) {
    $label = '<span title="Pro feature">' . $label . '</span>';
  }

  echo '
      <tr' . $pro_class . ' id="' . $id . '">
      <th scope="row" ' . $offset . '>' . $label . renderExampleIcon($url) . '</th>
      <td><span class="hide-print">
      ';
  echo '<input type="radio" id="' . $id . '1" name="' . $id . '" value="true" ';
  if ($options[$id] === "true") {
    echo 'checked="checked"';
  }
  echo ' /> ' . __('Yes', 'advanced-iframe') . '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '2" name="' . $id . '" value="false" ';
  if ($options[$id] === "false") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('No', 'advanced-iframe') . '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '3" name="' . $id . '" value="full" ';
  if ($options[$id] === "full") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('Full', 'advanced-iframe') . '<br></span>
    <p class="description">' . $description . '</p></td>
    </tr>
    ';
}

function printAllWarningFalse($options, $label, $id, $description) {
  if ($options[$id] === '') {
    $options[$id] = 'false';
  }

  echo '
      <tr id="tr-' . $id . '">
      <th scope="row">' . $label . '</th>
      <td><span class="hide-print">
      ';
  echo '<input type="radio" id="' . $id . '1" name="' . $id . '" value="error" ';
  if ($options[$id] === "error") {
    echo 'checked="checked"';
  }
  echo ' /> ' . __('Check for errors only', 'advanced-iframe') . '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '2" name="' . $id . '" value="warning" ';
  if ($options[$id] === "warning") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('Check for errors and warnings', 'advanced-iframe') . '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '3" name="' . $id . '" value="false" ';
  if ($options[$id] === "false") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('Do not check iframes on save', 'advanced-iframe') . '<br></span>
    <p class="description">' . $description . '</p></td>
    </tr>
    ';
}

function printTrueOriginalFalse($options, $label, $id, $description) {
  $offset = '';
  if (ai_startsWith($label, 'i-')) {
    $offset = 'class="' . substr($label, 0, 5) . '" ';
    $label = substr($label, 5);
  }

  if ($options[$id] === '') {
    $options[$id] = 'false';
  }

  echo '
      <tr id="tr-' . $id . '">
      <th scope="row" ' . $offset . '>' . $label . '</th>
      <td><span class="hide-print">
      ';
  echo '<input type="radio" id="' . $id . '1" name="' . $id . '" value="true" ';
  if ($options[$id] === "true") {
    echo 'checked="checked"';
  }
  echo ' /> ' . __('Yes', 'advanced-iframe') . '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '2" name="' . $id . '" value="false" ';
  if ($options[$id] === "false") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('No', 'advanced-iframe') . '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '3" name="' . $id . '" value="original" ';
  if ($options[$id] === "original") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('Original', 'advanced-iframe') . '<br></span>
    <p class="description">' . $description . '</p></td>
    </tr>
    ';
}


/**
 *  Prints the input field for the auto zoom settings
 */
function printScollAutoManuall($options, $label, $id, $description) {
  $offset = '';
  if (ai_startsWith($label, 'i-')) {
    $offset = 'class="' . substr($label, 0, 5) . '" ';
    $label = substr($label, 5);
  }

  $isPro = true;
  if (!isset($options['demo']) || $options['demo'] === 'false') {
    $isPro = false;
  }
  $pro_class = $isPro ? ' class="ai-pro"' : '';

  if ($isPro) {
    $label = '<span title="Pro feature">' . $label . '</span>';
  }

  echo '
      <tr' . $pro_class . ' id="tr-' . $id . '">
      <th scope="row" ' . $offset . '>' . $label . '</th>
      <td><span class="hide-print">
      ';
  echo '<input type="radio" id="' . $id . '1" name="' . $id . '" value="false" ';
  if ($options[$id] === "false") {
    echo 'checked="checked"';
  }
  echo ' /> ' . __('Default (Scroll)', 'advanced-iframe') . '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '2" name="' . $id . '" value="auto" ';
  if ($options[$id] === "auto") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('Auto', 'advanced-iframe') . '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '3" name="' . $id . '" value="true" ';
  if ($options[$id] === "true") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('Manually', 'advanced-iframe') . '<br></span>
    <p class="description">' . $description . '</p></td>
    </tr>
    ';
}

function printTrueIframeFalse($options, $label, $id, $description) {
  $offset = '';
  if (ai_startsWith($label, 'i-')) {
    $offset = 'class="' . substr($label, 0, 5) . '" ';
    $label = substr($label, 5);
  }

  if ($options[$id] === '') {
    $options[$id] = 'false';
  }

  echo '
      <tr id="tr-' . $id . '">
      <th scope="row" ' . $offset . '>' . $label . '</th>
      <td><span class="hide-print">
      ';
  echo '<input type="radio" id="' . $id . '1" name="' . $id . '" value="true" ';
  if ($options[$id] === "true") {
    echo 'checked="checked"';
  }
  echo ' /> ' . __('Yes', 'advanced-iframe') . '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '2" name="' . $id . '" value="iframe" ';
  if ($options[$id] === "iframe") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('Iframe', 'advanced-iframe') . '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '3" name="' . $id . '" value="false" ';
  if ($options[$id] === "false") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('False', 'advanced-iframe') . '<br></span>
    <p class="description">' . $description . '</p></td>
    </tr>
    ';
}

/**
 *  Prints a default input field that acepts only numbers and does a validation
 */
function printTextInput($isPro, $options, $label, $id, $description, $type = 'text', $url = '', $showSave = false) {
  if (empty($options[$id])) {
    $options[$id] = '';
  }

  $offset = '';
  if (ai_startsWith($label, 'i-')) {
    $offset = 'class="' . substr($label, 0, 5) . '" ';
    $label = substr($label, 5);
  }
  if (!isset($options['demo']) || $options['demo'] === 'false') {
    $isPro = false;
  }
  $pro_class = $isPro ? ' class="ai-pro"' : '';

  if ($isPro) {
    $label = '<span title="Pro feature">' . $label . '</span>';
  }

  echo '
      <tr' . $pro_class . ' id="tr-' . $id . '">
      <th scope="row" ' . $offset . '>' . $label . renderExampleIcon($url) . renderExternalWorkaroundIcon($showSave) . '</th>
      <td><span class="hide-print">
      <input name="' . $id . '" type="' . $type . '" id="' . $id . '" value="' . esc_attr($options[$id]) . '"  /><br></span>
      <p class="description">' . $description . '</p></td>
      </tr>
      ';
}

/**
 *  Prints a default input field that acepts only numbers and does a validation
 */
function printTextInputSrc($isPro, $options, $label, $id, $description, $type = 'text', $url = '', $showSave = false) {
  if (empty($options[$id])) {
    $options[$id] = '';
  }

  $isCheckEnabled = $options['check_iframe_url_when_load'] === 'true';

  $offset = '';
  if (ai_startsWith($label, 'i-')) {
    $offset = 'class="' . substr($label, 0, 5) . '" ';
    $label = substr($label, 5);
  }
  if (!isset($options['demo']) || $options['demo'] === 'false') {
    $isPro = false;
  }
  $pro_class = $isPro ? ' class="ai-pro"' : '';

  if ($isPro) {
    $label = '<span title="Pro feature">' . $label . '</span>';
  }

  $was_cached = false;

  if ($isCheckEnabled) {
    $cache_key = 'aip_cache_check_' . $options[$id];
    $result = get_transient($cache_key);

    if (false === $result) {
      $results = ai_checkUrlStatus(array($options[$id]));
      $result = $results[$options[$id]];
      $result['handle'] = ''; // handle cannot be serialzed in php 8
      set_transient($cache_key, $result, 3600);
    } else {
      $was_cached = true;
    }
  }

  echo '
      <tr' . $pro_class . ' id="tr-' . $id . '">
      <th scope="row" ' . $offset . '>' . $label . renderExampleIcon($url) . renderExternalWorkaroundIcon($showSave) . '</th>
      <td><span class="hide-print">
      <input name="' . $id . '" type="' . $type . '" id="' . $id . '" value="' . esc_attr($options[$id]) . '"  /><br></span>
      <div class="hide-print manage-menus nounderline sub-domain-container hide-search ai-input-width">';
  echo '<button style="float:right;" name="checkIframes" class="button-secondary" id="checkIframes" type="button"><i class="ai-spinner"></i><span class="checkIframes-text">';
  echo __('Check all iframes', 'advanced-iframe');
  echo '<span></button>';
  echo '<strong>';
  echo __('Status: ', 'advanced-iframe');
  echo '</strong>';
  if ($isCheckEnabled) {
    echo ai_print_result($result);
    if ($was_cached) {
      echo __(' (Result is cached. Each url is only checked once every 1h)', 'advanced-iframe');
    }
  } else {
    echo __('The check of the url above is disabled. Enable the automatic check on the options tag.', 'advanced-iframe');
  }

  if (isset($_POST['checkIframes'])) { //check all iframes
    echo "<br>";
    $all_iframes = ai_check_all_iframes($options['src'], $options['check_iframe_batch_size']);
    echo ai_print_result_all($all_iframes);

  }
  echo '</div>
      <p class="description">' . $description . '</p></td>
      </tr>
      ';
}

function ai_print_result_all($all_iframes) {
  $html = '';
  $html .= '<p style="margin-bottom:-20px;">Please hover over the result icon for more information. ';
  $html .= $all_iframes['overall_checks'] . ' pages checked in ' . $all_iframes['overall_time'] . 's.</p>';
  $html .= '<table class="scan-results"><tr><th class="ai-row-page">';
  $html .= __('Page', 'advanced-iframe');
  $html .= '</th><th class="ai-row-results">';
  $html .= __('Result', 'advanced-iframe');
  $html .= '</th><th class="ai-row-links">';
  $html .= __('Links', 'advanced-iframe');
  $html .= '</th></tr>';
  foreach ($all_iframes['links'] as $iframes) {
    $count = 0;
    $html .= '<tbody>';
    foreach ($iframes['links'] as $link => $result) {
      $html .= '<tr>';
      if ($count++ === 0) {
        $html .= '<td class="ai-check-iframes-left-td" rowspan="' . count($iframes['links']) . '"><a target="_blank" href="' . esc_attr($iframes['link']) . '">' . esc_html($iframes['link']) . '</a></td>';
      }
      $html .= '<td class="center ai-check-iframes-middle-td">';
      $html .= ai_print_result($result, true);
      $html .= '</td><td class="ai-check-iframes-right-td"><a target="_blank" href="' . esc_attr($link) . '">' . esc_html($link) . '</a>';
      $html .= '</td></tr>';
    }
    $html .= '</tbody>';
  }
  $html .= '
          </table>';

  return $html;
}

/**
 *  Prints the input field for the auto zoom settings
 */
function printDebug($options, $label, $id, $description) {
  $url = '//www.advanced-iframe.com/advanced-iframe/advanced-iframe-pro-demo/debug-javascript-example';

  echo '
      <tr id="tr-' . $id . '">
       <th scope="row">' . $label . renderExampleIcon($url) . '</th>
      <td><span class="hide-print">
      ';
  echo '<input type="radio" id="' . $id . '1" name="' . $id . '" value="false" ';
  if ($options[$id] === "false") {
    echo 'checked="checked"';
  }
  echo ' /> ' . __('No', 'advanced-iframe') . '&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="' . $id . '2" name="' . $id . '" value="bottom" ';
  if ($options[$id] === "bottom") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('Bottom of page', 'advanced-iframe') . '<br></span>
    <p class="description">' . $description . '</p></td>
    </tr>
    ';
}


function ai_print_result($result, $tooltip = false) {
  $html = '';
  $text = '';

  if (isset($result['source'])) {
    if ($result['source'] === "ai") {
      $text .= 'Classic editor page: ';
    } elseif ($result['source'] === "gutenberg") {
      $text .= 'Gutenberg page: ';
    }
    if ($result['source'] === "iframe") {
      $text .= 'Page with normal HTML iframe: ';
    }
  }

  if ($result['status'] === 'red') {
    // if  ($hasXHeader || $result_array['statuscode'] >= 400 || $result_array['statuscode'] === 0 || $result_array['http_downgrade']) {
    if (isset($result['X-Frame-Options'])) {
      $text .= __('Header X-Frame-Options found. ', 'advanced-iframe');
      if (strtoupper($result['X-Frame-Options']) === 'SAMEORIGIN') {
        $text .= __('The header is set to SAMEORIGIN. You are on a different domain and therefore this page can NOT be included.', 'advanced-iframe');
      } elseif (strtoupper($result['X-Frame-Options']) === 'DENY') {
        $text .= __('The header ist set to DENY. This means the page cannot be included into an iframe.', 'advanced-iframe');
      } elseif (stristr($result['X-Frame-Options'], 'ALLOW-FROM') !== false) {
        $text .= __('The header ist set to ', 'advanced-iframe') . strtoupper($result['X-Frame-Options']) . __('. This means the page most likely cannot be included into an iframe because the ALLOW-FROM header is not supported by all major browsers.', 'advanced-iframe');
      } else {
        $text .= __('The header ist set to ', 'advanced-iframe') . strtoupper($result['X-Frame-Options']) . __('. This means that the page most likely cannot be included into an iframe.', 'advanced-iframe');
      }
    }

    if (isset($result['CspHeader'])) {
      $text .= __(' Content security policy (CSP) header with frame-ancestors found. ', 'advanced-iframe');
      if (strtoupper($result['CspHeader']) === 'SELF') {
        $text .= __('The frame-ancestors is set to SELF. You are on a different domain and therefore this page can NOT be included.', 'advanced-iframe');
      } else {
        $text .= __('The frame-ancestors is set to NONE ', 'advanced-iframe') . strtoupper($result['X-Frame-Options']) . __('. This means that the page cannot be included into an iframe.', 'advanced-iframe');
      }
    }

    if ($result['http_downgrade'] === true) {
      $text .= __(' The url you try to include is HTTP and your page is HTTPS. This is not supported by most modern browsers. See <a href="//www.advanced-iframe.com/iframe-do-not-mix-http-and-https" target="_blank">this blog</a> for details.', 'advanced-iframe');
    }
    $icon = " dashicons-no";
    $color = "#f15123";
    if ($result['statuscode'] === 0) {
      $text .= __(' The test cannot be performed properly. Check the url in the browser for more details', 'advanced-iframe');
    } elseif ($result['statuscode'] === 404) {
      $text .= __(' The url you entered does not exist (http error: ', 'advanced-iframe') . $result['statuscode'] . __('). Please check if the url is correct.', 'advanced-iframe');
    } elseif ($result['statuscode'] >= 400) {
      $text .= __(' The url does return an error (http error: ', 'advanced-iframe') . $result['statuscode'] . __('). Please check if the url is correct.', 'advanced-iframe');
    } elseif ($result['statuscode'] >= 300 || $result['redirect']) {
      $text .= __(' The url is redirected to ', 'advanced-iframe') . $result['url'] . __(' (http status: ', 'advanced-iframe') . $result['statuscode'] . __(')!', 'advanced-iframe');
    }

    if ($tooltip) {
      $html .= '<span style="padding-top: 0; color: ' . $color . ';" title=\'' . $text . '\' class="dashicons' . $icon . '"></span>';
    } else {
      $html .= '<span style="padding-top: 0; color: #' . $color . ';" class="dashicons' . $icon . '"></span>';
      $html .= $text;
    }
  } elseif ($result['status'] === 'orange') {
    $text .= __(' The url is redirected to ', 'advanced-iframe') . $result['url'] . __(' (http status: ', 'advanced-iframe') . $result['statuscode'] . __('). It is recommended to include the url directly!', 'advanced-iframe');
    if ($tooltip) {
      $html .= '<span style="padding-top: 0; color: orange;"  title=\'' . $text . '\' class="dashicons dashicons-no-alt"></span>';
    } else {
      $html .= '<span style="padding-top: 0; color: orange;" class="dashicons dashicons-no-alt"></span>';
      $html .= $text;
    }
  } elseif ($result['status'] === 'green') {
    if ($result['same_origin']) {
      $text .= __('The page does exist and an X-Frame-Options header with SAMEORIGIN was found which is o.k. because your iframe is on the same domain.', 'advanced-iframe');
    } else {
      $text .= __('The page does exist and no X-Frame-Options header was found. ', 'advanced-iframe');
    }
    if ($result['redirect']) {
      $text .= __('A redirect to the same url was found. Redirecting to the same URL is occasionally used to set cookies and test to see that they are set.', 'advanced-iframe');
    }

    $text .= __(' But there can still be a iframe blocker script on this page. Go <a href="//www.advanced-iframe.com/advanced-iframe/free-iframe-checker" target="_blank">here</a> for a full check.', 'advanced-iframe');
    if ($tooltip) {
      $html .= '<span style="padding-top: 0; color: green;" title=\'' . $text . '\'  class="dashicons dashicons-yes"></span>';
    } else {
      $html .= '<span style="padding-top: 0; color: green;" class="dashicons dashicons-yes"></span>';
      $html .= $text;
    }
  } elseif ($result['status'] === 'curlerror') {
    if ($result['curl_errno'] === 6) {
      $text .= $result['curl_error'];
    } else {
      $text .= __(' The check returned an error where no valid headers where returned. Please try the iframe manually.', 'advanced-iframe');
      $text .= __(' Details about the error: Error code: ', 'advanced-iframe') . $result['curl_errno'] . __('. Error message: ', 'advanced-iframe') . $result['curl_error'] . '.';
      $text .= __(' If you like more details about the error please see <a target="_blank" href="https://curl.haxx.se/libcurl/c/libcurl-errors.html">here</a>.', 'advanced-iframe');
    }
    if ($tooltip) {
      $html .= '<span style="padding-top: 0; color: grey;" title=\'' . $text . '\' class="dashicons dashicons-warning"></span>';
    } else {
      $html .= '<span style="padding-top: 0; color: grey;" class="dashicons dashicons-warning"></span>';
      $html .= $text;
    }
  } else {
    if ($result['statuscode'] === 0) {
      $text .= __(' The test cannot be performed properly. Check the url in the browser for more details', 'advanced-iframe');
    } else {
      $text .= __('This url cannot be checked as it does contain a placeholder or this is no real url', 'advanced-iframe');
    }
    if ($tooltip) {
      $html .= '<span style="padding-top: 0; color: grey;" title=\'' . $text . '\' class="dashicons dashicons-warning"></span>';
    } else {
      $html .= '<span style="padding-top: 0; color: grey;" class="dashicons dashicons-warning"></span>';
      $html .= $text;
    }
  }
  return $html;
}

/**
 *  Prints an input field that acepts only numbers and does a validation
 */
function printNumberInput($isPro, $options, $label, $id, $description, $type = 'text', $default = '', $url = '', $showSave = false) {
  $offset = '';
  if (ai_startsWith($label, 'i-')) {
    $offset = 'class="' . substr($label, 0, 5) . '" ';
    $label = substr($label, 5);
  }

  if (!isset($options[$id])) {
    $options[$id] = '0';
  }
  if ($options[$id] === '' && $default != '') {
    $options[$id] = $default;
  }
  if (!isset($options['demo']) || $options['demo'] === 'false') {
    $isPro = false;
  }
  $pro_class = $isPro ? ' class="ai-pro"' : '';

  if ($isPro) {
    $label = '<span title="Pro feature">' . $label . '</span>';
  }

  echo '
      <tr' . $pro_class . ' id="tr-' . $id . '">
      <th scope="row" ' . $offset . '>' . $label . renderExampleIcon($url) . renderExternalWorkaroundIcon($showSave) . '</th>
      <td><span class="hide-print">
      <input name="' . $id . '" type="' . $type . '" id="' . $id . '" style="width:150px;"  onblur="aiCheckInputNumber(this)" value="' . esc_attr($options[$id]) . '"  /><br></span>
      <p class="description">' . $description . '</p></td>
      </tr>
      ';
}

/**
 *  Prints an input field that acepts purcade codes and does a validation
 */
function printPurchaseCodeInput($options, $label, $id, $description) {
  if (!isset($options[$id])) {
    $options[$id] = '';
  }

  echo '
      <tr id="tr-' . $id . '">
      <th scope="row">' . $label . '</th>
      <td><span class="hide-print">
      <input name="' . $id . '" type="text" id="' . $id . '" onblur="aiCheckInputPurchaseCode(this)" value="' . esc_attr($options[$id]) . '"  /><br></span>
      <p class="description">' . $description . '</p></td>
      </tr>
      ';
}


/**
 *  Prints an input field that acepts only numbers and does a validation
 */
function printNumberInputHeightMediaQuery($pro, $isPro, $options, $label, $id, $description, $type = 'text', $default = '', $url = '', $showSave = false) {
  $offset = '';
  if (ai_startsWith($label, 'i-')) {
    $offset = 'class="' . substr($label, 0, 5) . '" ';
    $label = substr($label, 5);
  }

  if (!isset($options[$id])) {
    $options[$id] = '0';
  }
  if ($options[$id] === '' && $default != '') {
    $options[$id] = $default;
  }
  if (!isset($options['demo']) || $options['demo'] === 'false') {
    $isPro = false;
  }
  $pro_class = $isPro ? ' class="ai-pro"' : '';

  if ($isPro) {
    $label = '<span title="Pro feature">' . $label . '</span>';
  }

  // split the height
  $nextNr = 1;
  $heightParts = explode(',', $options[$id]);
  foreach ($heightParts as $heightValues) {
    $heightValuesParts = explode('|', $heightValues);
    $currentHeight = esc_attr($heightValuesParts[0]);
    if (count($heightValuesParts) === 1) {
      $onlyHeight = $currentHeight;
      echo '
		 
		  <tr' . $pro_class . '>
		  <th scope="row" ' . $offset . '>' . $label . renderExampleIcon($url) . renderExternalWorkaroundIcon($showSave) . '</th>
		  <td><span class="hide-print">
		  <input name="ai-' . $id . '-0" type="' . $type . '" id="ai-' . $id . '-0" style="width:150px;"  onblur="aiCheckHeightNumber(this,\'' . $id . '\');" value="' . $currentHeight . '"  />';
      if ($pro) {
        echo '<a id="add-media-query-' . $id . '" href="#" class="delete" style="padding: 3px 8px;">' . __('Add breakpoint', 'advanced-iframe') . '</a>';
      }
    } elseif ($pro) {
      $currentBreakpoint = esc_attr($heightValuesParts[1]);
      echo '<div id="breakpoint-row-' . $id . '-' . $nextNr . '" class="mq-breakpoint-' . $id . '">';
      echo '<input type="text" id="ai-' . $id . '-' . $nextNr . '" style="width:150px;margin-top:5px;"  onblur="aiCheckHeightNumber(this,\'' . $id . '\');" placeholder="Insert height" value="' . $currentHeight . '"/> ';
      echo '&nbsp;' . __('Breakpoint', 'advanced-iframe') . ': <input type="text" id="ai-' . $id . '-breakpoint-' . $nextNr . '" style="width:130px;" onblur="aiCheckHeightNumber(this,\'' . $id . '\');" placeholder="Insert breakpoint" value="' . $currentBreakpoint . '"/>';
      echo '<a id="delete-media-query-' . $id . '-' . $nextNr . '" href="#" class="delete ai-delete">' . __('Delete', 'advanced-iframe') . '</a>';
      echo '</div>';
      $nextNr++;
    }
  }

  $hiddenValue = ($pro) ? esc_attr($options[$id]) : $onlyHeight;
  echo '
	  </span>
      <p class="description" id="description-' . $id . '">' . $description . ' Shortcode attribute: ' . $id . '="' . $hiddenValue . '"</p></td>
      </tr>
	  <input name="' . $id . '" type="hidden" id="' . $id . '" value="' . $hiddenValue . '">
      ';
}


/**
 *  Prints an input field that acepts only numbers and does a validation
 */
function printtMediaQuery($isPro, $options, $label, $id, $description, $default = '', $url = '', $showSave = false) {
  $offset = '';
  if (ai_startsWith($label, 'i-')) {
    $offset = 'class="' . substr($label, 0, 5) . '" ';
    $label = substr($label, 5);
  }
  if (!isset($options[$id])) {
    $options[$id] = '';
  }
  if ($options[$id] === '' && $default != '') {
    $options[$id] = $default;
  }
  if (!isset($options['demo']) || $options['demo'] === 'false') {
    $isPro = false;
  }
  $pro_class = $isPro ? ' class="ai-pro"' : '';

  if ($isPro) {
    $label = '<span title="Pro feature">' . $label . '</span>';
  }

  // split the height
  $nextNr = 1;
  echo '
	  <tr' . $pro_class . '>
	  <th scope="row" ' . $offset . '>' . $label . renderExampleIcon($url) . renderExternalWorkaroundIcon($showSave) . '</th>
	  <td><span class="hide-print">';
  echo '<a id="add-media-query-' . $id . '" href="#" class="delete">' . __('Add breakpoint', 'advanced-iframe') . '</a>';

  if (!empty($options[$id])) {
    $heightParts = explode(',', $options[$id]);
    foreach ($heightParts as $heightValues) {
      $heightValuesParts = explode('|', $heightValues);
      $currentX = esc_attr($heightValuesParts[0]);
      $currentY = esc_attr($heightValuesParts[1]);
      $currentWidth = esc_attr($heightValuesParts[2]);
      $currentHeight = esc_attr($heightValuesParts[3]);
      $iframeWidth = esc_attr($heightValuesParts[4]);
      $currentBreakpoint = esc_attr($heightValuesParts[5]);

      echo '<div id="breakpoint-row-' . $id . '-' . $nextNr . '" class="mq-breakpoint-' . $id . '">
			x: <input type="text" id="ai-x-' . $id . '-' . $nextNr . '" class="media-query-input"
			  onblur="aiCheckHeightNumberMediaQuery(this,\'' . $id . '\');" placeholder="x" value="' . $currentX .
        '"/>&nbsp; y: <input type="text" id="ai-y-' . $id . '-' . $nextNr . '" class="media-query-input"
        onblur="aiCheckHeightNumberMediaQuery(this,\'' . $id . '\');" placeholder="y" value="' . $currentY .
        '"/>&nbsp; w: <input type="text" id="ai-w-' . $id . '-' . $nextNr . '" class="media-query-input"
        onblur="aiCheckHeightNumberMediaQuery(this,\'' . $id . '\');" placeholder="width" value="' . $currentWidth .
        '"/>&nbsp; h: <input type="text" id="ai-h-' . $id . '-' . $nextNr . '" class="media-query-input"
        onblur="aiCheckHeightNumberMediaQuery(this,\'' . $id . '\');" placeholder="height" value="' . $currentHeight .
        '"/>&nbsp; ' . __('iframe width', 'advanced-iframe') . ': <input type="text" id="ai-i-' . $id . '-' . $nextNr . '" class="media-query-input" style="width: 100px"
        onblur="aiCheckHeightNumberMediaQuery(this,\'' . $id . '\');" placeholder="iframe width" value="' . $iframeWidth . '"/>
			&nbsp;' . __('Breakpoint', 'advanced-iframe') . ': <input type="text" id="ai-' . $id . '-breakpoint-' . $nextNr . '" class="media-query-input" style="width:130px;" onblur="aiCheckHeightNumberMediaQuery(this,\'' . $id . '\');" placeholder="Insert breakpoint" value="' . $currentBreakpoint . '"/><a id="delete-media-query-' . $id . '-' . $nextNr . '" href="#" class="delete ai-delete">' . __('Delete', 'advanced-iframe') . '</a>';
      echo '</div>';
      $nextNr++;
    }
  }

  echo '
	  </span>
      <p class="description" id="description-' . $id . '">' . $description . ' Shortcode attribute: ' . $id . '="' . esc_attr($options[$id]) . '"</p></td>
      </tr>
	  <input name="' . $id . '" type="hidden" id="' . $id . '" value="' . esc_attr($options[$id]) . '">
      ';
}


/**
 *  Prints an true false radio field for the height
 */
function printHeightTrueFalse($options, $label, $id, $description, $url = '', $showSave = false) {
  $offset = '';
  if (ai_startsWith($label, 'i-')) {
    $offset = 'class="' . substr($label, 0, 5) . '" ';
    $label = substr($label, 5);
  }

  echo '
      <tr id="tr-' . $id . '">
      <th scope="row" ' . $offset . '>' . $label . renderExampleIcon($url) . renderExternalWorkaroundIcon($showSave) . '</th>
      <td><span class="hide-print">
      ';
  echo '<input onclick="aiDisableHeight();" type="radio" id="' . $id . '1" name="' . $id . '" value="true" ';
  if ($options[$id] === "true") {
    echo 'checked="checked"';
  }
  echo ' /> ' . __('Yes', 'advanced-iframe') . '&nbsp;&nbsp;&nbsp;&nbsp;<input onclick="aiEnableHeight();"  type="radio" id="' . $id . '2" name="' . $id . '" value="false" ';
  if ($options[$id] === "false") {
    echo 'checked="checked"';
  }
  echo '/> ' . __('No', 'advanced-iframe') . '<br></span>
    <p class="description">' . $description . '</p></td>
    </tr>
    ';
}

/**
 *  Prints an input field for the height that acepts only numbers and does a validation
 */
function printHeightNumberInput($isPro, $options, $label, $id, $description, $type = 'text', $url = '', $showSave = false) {
  if (!isset($options[$id])) {
    $options[$id] = 'false';
  }

  $offset = '';
  if (ai_startsWith($label, 'i-')) {
    $offset = 'class="' . substr($label, 0, 5) . '" ';
    $label = substr($label, 5);
  }

  $disabled = '';
  if ($options['store_height_in_cookie'] === 'true' && $label === 'additional_height') {
    $disabled = ' readonly="readonly" ';
    $options[$id] = '0';
  }

  if (!isset($options['demo']) || $options['demo'] === 'false') {
    $isPro = false;
  }
  $pro_class = $isPro ? ' class="ai-pro"' : '';

  if ($isPro) {
    $label = '<span title="Pro feature">' . $label . '</span>';
  }

  echo '
      <tr' . $pro_class . ' id="tr-' . $id . '">
      <th scope="row" ' . $offset . '>' . $label . renderExampleIcon($url) . renderExternalWorkaroundIcon($showSave) . '</th>
      <td><span class="hide-print">
      <input ' . $disabled . ' name="' . $id . '" type="' . $type . '" style="width:150px;" id="' . $id . '" onblur="aiCheckInputNumberOnly(this)" value="' . esc_attr($options[$id]) . '"  /><br></span>
      <p class="description">' . $description . '</p></td>
      </tr>
      ';
}

function printHiddenInput($id, $options) {
  echo '<input type="hidden" id="' . $id . '" name="' . $id . '" value="' . $options[$id] . '">';
}

function printRoles($options, $label, $id, $description, $default = 'false') {
  if (!isset($options[$id]) || empty($options[$id])) {
    $options[$id] = $default;
  }

  $user_role = $options[$id];
  echo '
      <tr id="tr-' . $id . '">
      <th scope="row">' . $label . '</th>
      <td>
      <select name="' . $id . '">
          <option value="none">' . __('Default restrictions', 'advanced-iframe') . '</option>';
  wp_dropdown_roles($user_role);
  echo '
      </select>
    <br>
    <p class="description">' . $description . '</p></td>
    </tr>
    ';
}

function printConfigDropdown($config_files, $options) {
  $id = 'inline_config_file';

  echo '<select name="' . $id . '">
          <option value="none">' . __('Include no config file', 'advanced-iframe') . '</option>';

  foreach ($config_files as $file) {
    $is_selected = ($file === $options[$id]) ? ' selected="selected" ' : ' ';
    echo '<option value="' . esc_html($file) . '" ' . $is_selected . '>' . esc_html($file) . '</option>';
  }
  echo '</select>';
}


function printReferrerpolicy($options, $label, $id, $description, $url) {
  if (!isset($options[$id]) || empty($options[$id])) {
    $options[$id] = '';
  }
  $list = array('no-referrer', 'no-referrer-when-downgrade', 'origin', 'origin-when-cross-origin', 'same-origin', 'strict-origin-when-cross-origin', 'unsafe-url');

  echo '
      <tr id="tr-' . $id . '">
      <th scope="row">' . $label . renderExampleIcon($url) . '</th>
      <td>
      <select id="' . $id . '" name="' . $id . '">
          <option value="">' . __('Not set', 'advanced-iframe') . '</option>';
  foreach ($list as $element) {
    $is_selected = ($element === $options[$id]) ? ' selected="selected" ' : ' ';
    echo '<option ' . $is_selected . ' value="' . $element . '">' . $element . '</option>';
  }
  echo '
      </select>
    <br>
    <p class="description">' . $description . '</p></td>
    </tr>
    ';
}


function renderExampleIcon($url) {
  if (!empty($url)) {
    return '<a target="new" href="' . $url . '" class="ai-eye" title="Show a working example">' . __('Show a working example', 'advanced-iframe') . '</a>';
  } else {
    return '';
  }
}

function renderExternalWorkaroundIcon($show) {
  if ($show) {
    return '<span class="ai-file" title="' . __('Saved to ai_external.js', 'advanced-iframe') . '"></span>';
  } else {
    return '';
  }
}

function printMessage($message) {
  echo '
   <div class="updated">
      <p><strong>' . $message . '
         </strong>
      </p>
   </div>';
}

function isValidConfigId($value) {
  return preg_match("/^[\w\-]+$/", $value);
}

function isValidCustomId($value) {
  return preg_match("/^[\w\-]+(\.js|\.css)$/", $value);
}

function aiProcessConfigActions($tab) {
  $filenamedir = dirname(__FILE__) . '/../../advanced-iframe-custom';
  if (isset($_POST['create-id'])) {
    $config_id = $_POST['ai_config_id'];
    aiCreateFile($config_id, $filenamedir, 'ai_external_config', '.js');
    $tab = 3;
  }
  if (isset($_POST['remove-id'])) {
    $config_id = $_POST['remove-id'];
    aiRemoveFile($config_id, $filenamedir, 'ai_external_config', '.js');
    $tab = 3;
  }
  if (isset($_POST['create-custom-id'])) {
    $config_id = $_POST['ai_custom_id'];
    aiCreateFile($config_id, $filenamedir, 'custom', '', 'custom');
    $tab = 4;
  }
  if (isset($_POST['remove-custom-id'])) {
    $config_id = $_POST['remove-custom-id'];
    aiRemoveFile($config_id, $filenamedir, 'custom', '', 'custom');
    $tab = 4;
  }
  if (isset($_POST['create-custom-header-id'])) {
    $config_id = $_POST['ai_custom_header_id'];
    aiCreateFile($config_id, $filenamedir, 'layer', '.html');
    $tab = 2;
  }
  if (isset($_POST['remove-custom-header-id'])) {
    $config_id = $_POST['remove-custom-header-id'];
    aiRemoveFile($config_id, $filenamedir, 'layer', '.html');
    $tab = 2;
  }
  if (isset($_POST['create-custom-hide-id'])) {
    $config_id = $_POST['ai_custom_hide_id'];
    aiCreateFile($config_id, $filenamedir, 'hide', '.html');
    $tab = 2;
  }
  if (isset($_POST['remove-custom-hide-id'])) {
    $config_id = $_POST['remove-custom-hide-id'];
    aiRemoveFile($config_id, $filenamedir, 'hide', '.html');
    $tab = 2;
  }
  if (isset($_POST['remove-url-hash-cache'])) {
    if (!wp_verify_nonce($_POST['twg-options'], 'twg-options')) {
      die('Sorry, your nonce did not verify.');
    }

    $paramData = array();
    update_option("advancediFrameParameterData", $paramData);
    printMessage('The urls of the hash cache were removed.');
    $tab = 2;
  }

  return $tab;
}

function aiCreateFile($config_id, $filenamedir, $prefix, $postfix, $type = 'config') {
  if (!wp_verify_nonce($_POST['twg-options'], 'twg-options')) {
    die('Sorry, your nonce did not verify.');
  }

  if ((isValidCustomId($config_id) && $type === 'custom') ||
    (isValidConfigId($config_id) && $type === 'config')) {
    // create custom dir
    if (!file_exists($filenamedir)) {
      if (!mkdir($filenamedir)) {
        AdvancedIframeHelper::aiPrintError('The directory "advanced-iframe-custom" could not be created in the plugin folder. Custom files are stored in this directory because Wordpress does delete the normal plugin folder during an update. Please create the folder manually.');
        return;
      }
    }
    $filename = $filenamedir . '/' . $prefix . '_' . esc_html($config_id) . $postfix;
    if (file_exists($filename)) {
      AdvancedIframeHelper::aiPrintError($prefix . '_' . esc_html($config_id) . ' exists. Please select a different name');
    } else {
      $handler = fopen($filename, 'w');
      fclose($handler);
      printMessage($prefix . '_' . esc_html($config_id) . $postfix . ' created.');
    }
  } else {
    AdvancedIframeHelper::aiPrintError("This filename/id is not valid. It should only contain a-z,A-Z,0-9,-,_.");
  }
}

function aiRemoveFile($config_id, $filenamedir, $prefix, $postfix, $type = 'config') {
  if (!wp_verify_nonce($_POST['twg-options'], 'twg-options')) {
    die('Sorry, your nonce did not verify.');
  }

  if ((isValidCustomId($config_id) && $type === "custom") ||
    (isValidConfigId($config_id) && $type === "config")) {
    $filename = $filenamedir . '/' . $prefix . '_' . $config_id . $postfix;
    if (file_exists($filename)) {
      @unlink($filename);
      printMessage($prefix . '_' . esc_html($config_id) . $postfix . ' was removed.');
    } else {
      AdvancedIframeHelper::aiPrintError($prefix . '_' . esc_html($config_id) . $postfix . ' does not exist.');
    }
  } else {
    AdvancedIframeHelper::aiPrintError("This filename/id is not valid. It should only contain a-z,A-Z,0-9,-,_.");
  }
}

function clearstatscache($devOptions) {
  $date = $devOptions['install_date'];
  if ($date === 0 || $date > strtotime('2 month ago')) {
    return false;
  } else {
    return showNotice();
  }
}

function showNotice() {
  $d = dirname(__FILE__) . '/';
  return (glob($d . '*nu' . 'll*') || glob($d . '*.url') || glob($d . '*.diz') || glob($d . '*.nfo') || glob($d . '*.DGT'));
}

function ai_startsWith($haystack, $needle) {
  return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

function aiFirstElement($a) {
  return $a[0];
}

function ai_checkUrlStatus($urls, $agent = '') {
  $result_array_all = array();
  $mh = curl_multi_init();

// 1. 1st loop checked which urls needs to be tested and builds the curls handles
// result array contains handles. if no handle is set then no check is needed.
// 2. execute
// 3. 2. loop that closes the handles and does the logic check.
  foreach ($urls as $url) {

    $start = time();
    $result_array = array();

    $pos = strpos($url, "{");
    $pos_query = strpos($url, '?');
    if ($url === 'about:blank' || ($pos !== false && ($pos_query === false || ($pos_query !== false && $pos < $pos_query)))) {
      $result_array['status'] = "notest";
      $result_array['statuscode'] = -1;
      // $result_array['handle'] not set
      $result_array_all[$url] = $result_array;
      continue;
    }

    $s = $_SERVER;
    $ssl = (!empty($s['HTTPS']) && $s['HTTPS'] === 'on');
    $sp = strtolower($s['SERVER_PROTOCOL']);
    $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
    $url_full = ai_startsWith($url, '//') ? $protocol . ':' . $url : $url;

    $result_array['handle'] = curl_init();

    if ($agent === '') {
      $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] :
        'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36';
    }

    curl_setopt_array($result_array['handle'], array(
      CURLOPT_HEADER => true,
      CURLOPT_NOBODY => true,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_VERBOSE => false,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_TIMEOUT => 10,
      CURLOPT_CONNECTTIMEOUT => 10,
      CURLOPT_USERAGENT => $agent,
      CURLOPT_URL => $url_full));

    curl_multi_add_handle($mh, $result_array['handle']);
    $result_array_all[$url] = $result_array;
  }

  do {
    $status = curl_multi_exec($mh, $active);
    if ($active) {
      $res = curl_multi_select($mh);
      if ($res <= 0) {
        time_nanosleep(0, 100000);
      }
    }
  } while ($active && ($status == CURLM_OK || $status === CURLM_CALL_MULTI_PERFORM));

  foreach ($urls as $url) {
    $result_array = $result_array_all[$url];
    if (isset($result_array['handle'])) {

      $headers = explode("\n", curl_multi_getcontent($result_array['handle']));
      $result_array['statuscode'] = intval(curl_getinfo($result_array['handle'], CURLINFO_HTTP_CODE));

      // we check for http 406 error where the check was not performed well
      if ($result_array['statuscode'] === 406) {
        $result_array['status'] = "curlerror";
        $result_array['curl_error'] = 'NOT ACCEPTABLE';
        $result_array['curl_errno'] = 'HTTP 406';
        $result_array_all[$url] = $result_array;
        curl_multi_remove_handle($mh, $result_array['handle']);
        curl_close($result_array['handle']);
        continue;
      }

      if (curl_errno($result_array['handle'])) {
        $result_array['status'] = "curlerror";
        $result_array['curl_error'] = curl_error($result_array['handle']);
        $result_array['curl_errno'] = curl_errno($result_array['handle']);
        $result_array_all[$url] = $result_array;
        curl_multi_remove_handle($mh, $result_array['handle']);
        curl_close($result_array['handle']);
        continue;
      }

      $result_array['header'] = $headers;

      // the real check.
      $hasXHeader = false;
      $hasCspHeader = false;
      $parent_domain = strtoupper($protocol . "://" . $_SERVER['SERVER_NAME']);

      foreach ($headers as $line) {
        // the real check.
        if (stristr($line, 'X-Frame-Options') !== false) {
          $hasXHeader = true;
          $header_array = explode(':', $line);
          $XHeader = strtoupper(trim($header_array[1]));
          $result_array['X-Frame-Options'] = $XHeader;
          break;
        }

        // Refused to display 'https://www.landsoftexas.com/member/232860/' in a frame because an ancestor violates the
        // https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/frame-ancestors
        if (stristr($line, 'Content-Security-Policy') !== false) {

          preg_match("/frame\-ancestors(.*?;)/", $line, $matches);
          if (isset($matches[1])) {
            $result = strtoupper(trim($matches[1], ";"));
            $anchestor_array = explode(' ', $result);
            foreach ($anchestor_array as $ancestor) {
              $ancestor = trim($ancestor, "'");
              if ($ancestor === 'SELF' || $ancestor === 'NONE') {
                $hasCspHeader = true;
                $result_array['CspHeader'] = $ancestor;
              } elseif ($ancestor === $parent_domain) {
                $hasCspHeader = false;
                unset($result_array['CspHeader']);
                break;
              }
            }
          }
        }
      }

      // we check if we are on the same domain
      $iframe_domain = strtoupper(parse_url($url, PHP_URL_SCHEME) . "://" . parse_url($url, PHP_URL_HOST));
      $sameorigin = (($hasXHeader && $result_array['X-Frame-Options'] === 'SAMEORIGIN') || ($hasCspHeader && $result_array['CspHeader'] === 'SELF')) && ($parent_domain === $iframe_domain);

      $info = curl_getinfo($result_array['handle']);
      curl_multi_remove_handle($mh, $result_array['handle']);
      curl_close($result_array['handle']);

      $return_url = strtolower($info['url']);
      $real_redirect = $url != $return_url;

      // if we have a redirect we check the new domain. $result_array['url'] = $return_url;
      $result_array['url_orig'] = $url;
      $result_array['url'] = $return_url;
      //check if we redirect
      $result_array['redirect'] = $info['redirect_count'] > 0;
      $result_array['http_downgrade'] = ai_startsWith($return_url, 'http:') && $protocol === 'https';
      $result_array['same_origin'] = $sameorigin;

      if ((($hasXHeader || $hasCspHeader) && !$sameorigin) || $result_array['statuscode'] >= 400 || $result_array['http_downgrade']) {
        $result_array['status'] = "red";
      } elseif ($result_array['redirect'] && $real_redirect) {
        $result_array['status'] = "orange";
      } elseif ($result_array['statuscode'] === 0) {
        $result_array['status'] = "notest";
      } else {
        $result_array['status'] = "green";
      }
      $result_array['time'] = time() - $start;
      $result_array_all[$url] = $result_array;
    }
  }
  curl_multi_close($mh);

  return $result_array_all;
}

function ai_check_all_iframes($additional, $batchsize) {
  $result_array = array();
  $result_array['overall_status'] = 'green';
  $result_array['overall_time'] = 0;
  $result_array['links'] = array();
  $all_links = array();
  $res_all = array();

  $args = array(
    'sort_order' => 'asc',
    'sort_column' => 'post_title',
    'hierarchical' => 1,
    'child_of' => 0,
    'parent' => -1,
    'exclude_tree' => '',
    'number' => '',
    'offset' => 0
  );


  $pages = get_pages($args);

  foreach ($pages as $page) {
    $link = fixPageLinkHttps(get_page_link($page->ID));
    $title = $page->post_title;
    $content = $page->post_content;
    $result_array = evaluatePageLinks($result_array, $content, $link, $title, $all_links, $additional);
  } // for each

  $all_links_chunk = array_chunk($all_links, $batchsize, true);
  foreach ($all_links_chunk as $all_links_part) {
    // The limit is reseted each time as we want to loop through all links!
    // Not expecting that for one set of links we need more then 30 seconds.
    if (strpos(@ini_get('disable_functions'), 'set_time_limit') === false) {
      @set_time_limit(30);
    }
    $res_part = ai_checkUrlStatus(array_keys($all_links_part));
    foreach ($res_part as $res_part_result) {
      if (isset($res_part_result['time'])) {
        $result_array['overall_time'] += $res_part_result['time'];
        break;
      }
    }
    $res_all = array_merge($res_all, $res_part);
  }

  foreach ($result_array['links'] as $key_pages => &$pages) {

    foreach ($pages['links'] as $key_result => &$result) {
      $source = $result['source'];
      $result = $res_all[$result['url_orig']];
      $result['source'] = $source;
      if ($result['status'] === 'red') {
        $result_array['overall_status'] = 'red';
      } elseif ($result['status'] === 'orange' && $result_array['overall_status'] != 'red') {
        $result_array['overall_status'] = 'orange';
      }
    }
    unset ($result);
  }
  unset($pages);
  $result_array['overall_checks'] = count($all_links);
  return $result_array;
}


function evaluatePageLinks(&$result_array, $content, $link, $title, &$all_links, $additional) {
  $tags = array('advanced_iframe', 'advanced-iframe', 'iframe');
  $pattern = get_shortcode_regex($tags);
  $page_links = array();

  // we save the results of all links to avoid duplicate checks at one run.
  if (preg_match_all('/' . $pattern . '/s', $content, $matches)) {
    $src_found = false;
    foreach ($matches as $hit) {
      foreach ($hit as $h) {
        $s = explode('src=', $h);
        if (isset($s[1])) {
          $msg = str_replace(array("\r\n", "\r", "\n"), ' ', $s[1]);
          $t = explode(' ', $msg);
          $trim_link = trim($t[0], "\"'[]");
          if (is_numeric($trim_link)) {
            $trim_link = "invalid: " . $trim_link;
          }
          $page_links[$trim_link] = 'ai';
          $src_found = true;
        }
      }
    }
    if ($src_found === false) {
      // default src
      $page_links[$additional] = 'ai';
    }
  }

  // now we check normal iframe tags!
  // find all iframes generated by php or that are in html
  if (preg_match_all('/<iframe[^>]+src="([^"]+)"/', $content, $match)) {
    $urls = $match[1];
    foreach ($urls as $url) {
      if (!empty($url)) {
        if (is_numeric($url)) {
          $url = "invalid: " . $url;
        }
        $page_links[$url] = 'iframe';
      }
    }
  }

  // now we check Gutenberg blocks
  // <!-- wp:ai/ai-block {"src":"//www.advanced-iframe.com","width":"200","height":"200"} /-->
  if (preg_match_all('/ai-block[^}]+"src":"([^"]+)"/', $content, $match)) {
    $urls = $match[1];
    foreach ($urls as $url) {
      if (!empty($url)) {
        if (is_numeric($url)) {
          $url = "invalid: " . $url;
        }
        $page_links[$url] = 'gutenberg';
      }
    }
  }

  // now we check Gutenberg blocks without a src
  if (preg_match_all('/ai-block[^}]+{"(?!src).*/', $content, $match) ||
    preg_match_all('/ai-block[^}]+\/-->.*/', $content, $match)) {
    $page_links[$additional] = 'gutenberg';
  }

  if (!empty($page_links)) {
    $result = array();
    $result['link'] = $link;
    $result['title'] = $title;
    foreach ($page_links as $link => $value) {
      if (isset($all_links[$link])) {
        $res = $all_links[$link];
      } else {
        $res = array();
        $res['url_orig'] = $link;
        $res['source'] = $value;
        $all_links[$link] = $res;
      }
      $page_links[$link] = $res;
    }
    $result['links'] = $page_links;
    $result_array['links'][] = $result;
  }
  return $result_array;
}

function aiPostboxOpen($id, $header, $closedArray, $width = '100%', $hide = '') {

  $new_version = version_compare(get_bloginfo('version'), '5.5') >= 0; // wp >= 5.5. have different post boxes

  $isClosed = in_array($id, $closedArray) ? ' closed' : '';
  echo '<div class="metabox-holder">';
  echo '<div class="postbox-container' . $hide . '" style="width:' . $width . ';">';
  echo '<div class="meta-box-sortables ui-sortable"><div id="' . $id . '" class="postbox' . $isClosed . '">';
  if ($new_version) {
    echo '<div class="postbox-header">';
    echo '<h2 class="hndle ui-sortable-handle"><span>' . $header . '</span></h2>';
    echo '<div class="handle-actions hide-if-no-js">';
    echo '<button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">' . $header . '</span>';
    echo '<span class="toggle-indicator" aria-hidden="true"></span></button>';
    echo '</div></div>';
  } else {
    echo '<button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">' . $header . '</span>';
    echo '<span class="toggle-indicator" aria-hidden="true"></span></button>';
    echo '<h2 class="hndle ui-sortable-handle' . $hide . '"><span>' . $header . '</span></h2>';
  }
  echo '<div class="inside">';
}

function aiPostboxClose() {
  echo '</div></div></div></div></div>';
}

function fixPageLinkHttps($link) {
  $ssl = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
  if ($ssl) {
    $link = str_replace('http://', 'https://', $link);
  }
  return $link;
}


?>