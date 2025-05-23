<?php
if (!class_exists("wpdreamsCustomPostTypes")) {
    /**
     * Class wpdreamsCustomPostTypes
     *
     * A custom post types selector UI element with.
     *
     * @package  WPDreams/OptionsFramework/Classes
     * @category Class
     * @author Ernest Marcinko <ernest.marcinko@wp-dreams.com>
     * @link http://wp-dreams.com, http://codecanyon.net/user/anago/portfolio
     * @copyright Copyright (c) 2017, Ernest Marcinko
     */
    class wpdreamsCustomPostTypes extends wpdreamsType {
        private $types, $selected;
        
        private $args = array(
            'exclude' => array("revision", "nav_menu_item", "attachment")
        );

        function getType() {
            parent::getType();
            $this->processData();

            echo "
      <div class='wpdreamsCustomPostTypes' id='wpdreamsCustomPostTypes-" . self::$_instancenumber . "'>
        <fieldset>
          <legend>" . $this->label . "</legend>";
            echo '<div class="sortablecontainer" id="sortablecontainer' . self::$_instancenumber . '">
            <div class="arrow-all-left"></div>
            <div class="arrow-all-right"></div>
            <p>Available post types</p><ul id="sortable' . self::$_instancenumber . '" class="connectedSortable">';
            if ($this->types != null && is_array($this->types)) {
                foreach ($this->types as $k => $v) {
                    if ($this->selected == null || !in_array($k, $this->selected)) {
                        echo '<li class="ui-state-default" data-ptype="'.$k.'">'
                            . $v->labels->name .
                            '<span class="extra_info">['.$k.']</span></li>';
                    }
                }
            }
            echo "</ul></div>";
            echo '<div class="sortablecontainer"><p>Drag here the post types you want to use!</p><ul id="sortable_conn' . self::$_instancenumber . '" class="connectedSortable">';
            if ($this->selected != null && is_array($this->selected)) {
                foreach ($this->selected as $k => $v) {
					if ( !isset($this->types[trim($v)]) ) {
						continue;
					}
                    echo '<li class="ui-state-default" data-ptype="'.$v.'">'
                        .$this->types[trim($v)]->labels->name.
                        '<span class="extra_info">['.$v.']</span></li>';
                }
            }
            echo "</ul></div>";
            echo "
         <input isparam=1 type='hidden' value='" . $this->data . "' name='" . $this->name . "'>";
            echo "
         <input type='hidden' value='wpdreamsCustomPostTypes' name='classname-" . $this->name . "'>";
            echo "
        </fieldset>
      </div>";
        }

        function processData() {
            // Get the args first if exists
            if ( is_array($this->data) && isset($this->data['args']) )
                $this->args = array_merge($this->args, $this->data['args']);

            $this->types = get_post_types('', "objects");
            foreach ($this->types as $k => &$v) {
                if (count($this->args['exclude']) > 0 && in_array($k, $this->args['exclude'])) {
                    unset($this->types[$k]);
                    continue;
                }
                if ( $k == 'attachment' ) {
                    $v->labels->name = 'Attachment - Media';
                }
            }

            if ( is_array($this->data) && isset($this->data['value']) ) {
                // If called from back-end non-post context
                $this->selected = $this->decode_param($this->data['value']);
                $this->data = $this->encode_param($this->data['value']);
            } else {
                // POST method or something else
                $this->selected = $this->decode_param($this->data);
                $this->data = $this->encode_param($this->data);
            }
        }

        final function getData() {
            return $this->data;
        }

        final function getSelected() {
            return $this->selected;
        }
    }
}