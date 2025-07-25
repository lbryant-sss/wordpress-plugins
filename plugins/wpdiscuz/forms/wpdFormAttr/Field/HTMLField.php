<?php

namespace wpdFormAttr\Field;

class HTMLField extends Field {

    protected function dashboardForm() {
        ?>
        <div class="wpd-field-body" style="display: <?php echo esc_attr($this->display); ?>">
            <div class="wpd-field-option wpdiscuz-item">
                <input class="wpd-field-type" type="hidden" value="<?php echo esc_attr($this->type); ?>"
                       name="<?php echo esc_attr($this->fieldInputName); ?>[type]"/>
                <label for="<?php echo esc_attr($this->fieldInputName); ?>[name]"><?php esc_html_e("Name", "wpdiscuz"); ?>
                    :</label>
                <input class="wpd-field-name" type="text" value="<?php echo esc_attr($this->fieldData["name"]); ?>"
                       name="<?php echo esc_attr($this->fieldInputName); ?>[name]"
                       id="<?php echo esc_attr($this->fieldInputName); ?>[name]" required/>
            </div>
            <div class="wpd-field-option wpdiscuz-item">
                <?php $value = isset($this->fieldData["value"]) ? $this->fieldData["value"] : ""; ?>
                <label for="<?php echo esc_attr($this->fieldInputName); ?>[value]"><?php esc_html_e("HTML Code", "wpdiscuz"); ?>
                    :</label>
                <textarea required name="<?php echo esc_attr($this->fieldInputName); ?>[value]"
                          id="<?php echo esc_attr($this->fieldInputName); ?>[value]"><?php echo esc_html($value); ?></textarea>
            </div>
            <div class="wpd-field-option">
                <label for="<?php echo esc_attr($this->fieldInputName); ?>[is_show_sform]"><?php esc_html_e("Display on reply form", "wpdiscuz"); ?>
                    :</label>
                <input type="checkbox" value="1" <?php checked($this->fieldData["is_show_sform"], 1, true); ?>
                       name="<?php echo esc_attr($this->fieldInputName); ?>[is_show_sform]"
                       id="<?php echo esc_attr($this->fieldInputName); ?>[is_show_sform]"/>
            </div>
            <div class="wpd-field-option">
                <label for="<?php echo esc_attr($this->fieldInputName); ?>[show_for_guests]"><?php esc_html_e("Display for Guests", "wpdiscuz"); ?>
                    :</label>
                <input type="checkbox" value="1" <?php checked($this->fieldData["show_for_guests"], 1, true); ?>
                       name="<?php echo esc_attr($this->fieldInputName); ?>[show_for_guests]"
                       id="<?php echo esc_attr($this->fieldInputName); ?>[show_for_guests]"/>
            </div>
            <div class="wpd-field-option">
                <label for="<?php echo esc_attr($this->fieldInputName); ?>[show_for_users]"><?php esc_html_e("Display for Registered Users", "wpdiscuz"); ?>
                    :</label>
                <input type="checkbox" value="1" <?php checked($this->fieldData["show_for_users"], 1, true); ?>
                       name="<?php echo esc_attr($this->fieldInputName); ?>[show_for_users]"
                       id="<?php echo esc_attr($this->fieldInputName); ?>[show_for_users]"/>
            </div>
            <div style="clear:both;"></div>
        </div>
        <?php
    }

    public function frontFormHtml($name, $args, $options, $currentUser, $uniqueId, $isMainForm) {
        if (!$this->isShowForUser($args, $currentUser) || !$isMainForm && !$args["is_show_sform"])
            return;
        echo $args["value"];
    }

    public function sanitizeFieldData($data) {
        $cleanData         = [];
        $cleanData["type"] = sanitize_text_field($data["type"]);
        if (isset($data["name"])) {
            $name              = sanitize_text_field(trim(strip_tags($data["name"])));
            $cleanData["name"] = $name ? $name : $this->fieldDefaultData["name"];
        }
        if (isset($data["value"])) {
            $cleanData["value"] = wp_kses_post(trim($data["value"]));
        }
        if (isset($data["is_show_sform"])) {
            $cleanData["is_show_sform"] = intval($data["is_show_sform"]);
        } else {
            $cleanData["is_show_sform"] = 0;
        }
        if (isset($data["show_for_guests"])) {
            $cleanData["show_for_guests"] = intval($data["show_for_guests"]);
        } else {
            $cleanData["show_for_guests"] = 0;
        }
        if (isset($data["show_for_users"])) {
            $cleanData["show_for_users"] = intval($data["show_for_users"]);
        } else {
            $cleanData["show_for_users"] = 0;
        }

        return wp_parse_args($cleanData, $this->fieldDefaultData);
    }

    protected function initDefaultData() {
        $this->fieldDefaultData = [
            "name"               => "",
            "desc"               => "",
            "value"              => "",
            "required"           => "0",
            "loc"                => "top",
            "is_show_on_comment" => "0",
            "is_show_sform"      => "1",
            "no_insert_meta"     => "1",
            "show_for_guests"    => 1,
            "show_for_users"     => 1,
        ];
    }

    public function editCommentHtml($key, $value, $data, $comment) {

    }

    public function frontHtml($value, $args) {

    }

    public function validateFieldData($fieldName, $args, $options, $currentUser) {

    }

}
