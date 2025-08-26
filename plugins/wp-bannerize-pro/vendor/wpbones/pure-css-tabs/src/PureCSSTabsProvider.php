<?php

namespace WPBannerize\PureCSSTabs;

class PureCSSTabsProvider
{

  /**
   * Return an array of functions to use tabs.
   *
   * @return array
   */
  public static function useTabs()
  {
    return [
      /**
       * @see static::openTab()
       */
      function ($label, $id = null, $selected = false, $group = 'tabs') {
        static::openTab($label, $id, $selected,  $group);
      },
      function () {
        static::closeTab();
      }
    ];
  }

  /**
   * Get the path to the CSS file.
   *
   * @param bool $minified Optional. Default true. TRUE for minified version.
   *
   * @return string
   */
  public static function css($minified = true)
  {
    $file = __FILE__;

    $path = rtrim(plugin_dir_url($file), '\/');

    $minified = $minified ? ".min" : "";

    $css = "{$path}/public/css/wpbones-tabs{$minified}.css";

    return $css;
  }

  /**
   * Enqueue the CSS file.
   *
   * @param bool $minified Optional. Default true. TRUE for minified version.
   */
  public static function enqueueStyles($minified = true)
  {
    wp_enqueue_style(
      'wpbones-tabs',
      self::css($minified),
      [],
      WPBannerize()->Version
    );
  }

  /**
   * Display the open tab.
   *
   * @param string|array  $label    The label of tab or un array with [label, group]
   * @param null          $id       Optional. ID of tab. If null, will sanitize_title() the label.
   * @param bool          $selected Optional. Default false. TRUE for checked.
   * @param string        $group    Optional. Group of tabs when you want to handle multiple tab in the same view. Default 'tabs'
   */
  public static function openTab($label, $id = null, $selected = false, $group = 'tabs')
  {
    if (is_array($label)) {
      [$label, $group] = $label;
    }

    if (is_null($id)) {
      $id = sanitize_title($label);
    }

    if (is_string($selected)) {
      $selected = (sanitize_title($selected) === $id);
    }

?>

    <input id="<?php echo $id ?>"
      type="radio"
      name="<?php echo $group ?>"
      <?php checked($selected, true) ?>
      aria-hidden="true">
    <label for="<?php echo $id ?>"
      tabindex="0">
      <?php echo $label ?>
    </label>
    <div class="wpbones-tab">
  <?php
  }

  public static function closeTab()
  {
    echo '</div>';
  }

  /**
   * Display tabs by array
   *
   *     self::tabs(
   *       'tab-1' => [ 'label' => 'Tab 1', 'content' => 'Hello', 'selected' => true ],
   *       'tab-2' => [ 'label' => 'Tab 1', 'content' => 'Hello' ],
   *       ...
   *     );
   *
   * @param array $array
   */
  public static function tabs($array = [])
  {
    if (! empty($array)) {
      echo '<div class="wpbones-tabs">';

      foreach ($array as $key => $tab) {
        $selected = isset($tab['selected']) ? $tab['selected'] : null;
        self::openTab($tab['label'], $key, $selected);
        echo $tab['content'];
        self::closeTab();
      }
      echo '</div>';
    }
  }
}
