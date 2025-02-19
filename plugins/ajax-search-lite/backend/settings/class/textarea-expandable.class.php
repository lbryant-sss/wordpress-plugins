<?php
if (!class_exists("wd_TextareaExpandable")) {
    /**
     * Class wpdreamsTextarea
     *
     * A simple textarea field.
     *
     * @package  WPDreams/OptionsFramework/Classes
     * @category Class
     * @author Ernest Marcinko <ernest.marcinko@wp-dreams.com>
     * @link http://codecanyon.net/user/wpdreams/portfolio
     * @copyright Copyright (c) 2019, Ernest Marcinko
     */
    class wd_TextareaExpandable extends wpdreamsType {
        public function getType() {
            parent::getType();
            echo "<label class='wd_textarea_expandable' for='wd_textareae_" . self::$_instancenumber . "'>" . esc_html($this->label) . "</label>";
            echo "<textarea rows='1' data-min-rows='1' class='wd_textarea_expandable' id='wd_textareae_" . self::$_instancenumber . "' name='" . $this->name . "'>" . stripslashes(esc_html($this->data)) . "</textarea>";
        }
    }
}