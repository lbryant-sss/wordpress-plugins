<?php

namespace wpdFormAttr\Field;

use wpdFormAttr\FormConst\wpdFormConst;
use wpdFormAttr\Tools\Sanitizer;

abstract class Field {

    private static $instance = [];
    protected $isDefault;
    protected $display;
    protected $displayForGuest;
    protected $displayForUser;
    protected $name;
    protected $type;
    protected $fieldInputName;
    protected $fieldData;
    protected $fieldDefaultData;
    protected $commenter;

    private function __construct() {
        $this->initType();
        $this->initDefaultData();
        $this->commenter = wp_get_current_commenter();
    }

    public static function getInstance() {
        $currenClass = get_called_class();
        if (!isset(self::$instance[$currenClass])) {
            self::$instance[$currenClass] = new $currenClass;
        }
        return self::$instance[$currenClass];
    }

    abstract protected function dashboardForm();

    abstract public function editCommentHtml($key, $value, $data, $comment);

    abstract public function frontFormHtml($name, $args, $options, $currentUser, $uniqueId, $isMainForm);

    abstract public function validateFieldData($fieldName, $args, $options, $currentUser);

    abstract public function frontHtml($value, $args);

    public function drawContent($value, $args) {
        if ($args["is_show_on_comment"] || (is_admin() && !wp_doing_ajax())) {
            return $this->frontHtml($value, $args);
        }
        return "";
    }

    public function dashboardFormHtml($row, $col, $name, $args) {
        $this->display = "none";
        $this->setName($name);
        $this->setFieldData($args);
        $this->initIputsNames($row, $col);
        ?>
        <div class="wpd-field <?php echo $this->isDefault ? "wpd-default-field" : ""; ?>">
            <div class="wpd-field-head">
                <?php echo htmlentities($this->fieldData["name"]); ?>
                <?php
                if ($this->fieldData["type"] === "wpdFormAttr\Field\DefaultField\Submit") {
                    esc_html_e(" (Submit Button)", "wpdiscuz");
                } elseif ($this->fieldData["type"] === "wpdFormAttr\Field\DefaultField\Captcha") {
                    esc_html_e("Google reCAPTCHA", "wpdiscuz");
                } elseif (strpos($this->fieldData["type"], "wpdFormAttr\Field\DefaultField") === false) {
                    $fieldLable = str_replace("wpdFormAttr\Field\\", "", $this->fieldData["type"]);
                    echo " ( " . htmlentities(str_replace("Field", "", $fieldLable)) . " )";
                }
                ?>
                <div class="wpd-field-actions">
                    <i class="fas fa-pencil-alt" title="<?php esc_attr_e("Edit", "wpdiscuz"); ?>"></i>
                    <?php if (!$this->isDefault) {
                        ?>
                        |<i class="fas fa-trash-alt" title="<?php esc_attr_e("Delete", "wpdiscuz"); ?>"></i>
                    <?php }
                    ?>
                </div>
            </div>
            <?php
            $this->dashboardForm($row, $col, $name, $this->fieldData);
            ?>
        </div>
        <?php
    }

    public function dashboardFormDialogHtml($row, $col) {
        $this->fieldData = wp_parse_args($this->fieldData, $this->fieldDefaultData);
        ?>
        <form id="TB_ajaxContent_form">
            <?php
            $this->display = "block";
            $this->generateCustomName();
            $this->initIputsNames($row, $col);
            $this->dashboardForm();
            ?>
            <div class="add-to-form-button-cont">
                <input type="submit" id="wpd-add-field-button" class="button button-primary button-large"
                       value="<?php esc_attr_e("Add To Form", "wpdiscuz"); ?>">
            </div>
        </form>
        <?php
    }

    private function generateCustomName() {
        $this->name = "custom_field_" . uniqid();
    }

    private function initType() {
        $this->type = get_called_class();
    }

    public function setName($name) {
        if (trim($name)) {
            $this->name = $name;
        } else {
            $this->generateCustomName();
        }
    }

    public function setFieldData($args) {
        $this->fieldData = wp_parse_args($args, $this->fieldDefaultData);
    }

    private function initIputsNames($row, $col) {
        $this->fieldInputName = wpdFormConst::WPDISCUZ_META_FORMS_STRUCTURE . "[$row][$col][{$this->name}]";
    }

    public function sanitizeFieldData($data) {
        $cleanData         = [];
        $cleanData["type"] = sanitize_text_field($data["type"]);
        if (isset($data["name"])) {
            $name              = sanitize_text_field(trim(strip_tags($data["name"])));
            $cleanData["name"] = $name ? $name : $this->fieldDefaultData["name"];
        }
        if (isset($data["nameForTotal"])) {
            $nameForTotal              = sanitize_text_field(trim(strip_tags($data["nameForTotal"])));
            $cleanData["nameForTotal"] = $nameForTotal ? $nameForTotal : $this->fieldDefaultData["nameForTotal"];
        }
        if (isset($data["desc"])) {
            $cleanData["desc"] = sanitize_text_field(trim($data["desc"]));
        }

        if (isset($data["values"])) {
            $values = array_filter(explode("\n", trim(strip_tags($data["values"]))));
            foreach ($values as $k => $value) {
                $cleanData["values"][] = sanitize_text_field(trim($value));
            }
        }
        if (isset($data["icon"])) {
            $cleanData["icon"] = sanitize_text_field(trim(strip_tags($data["icon"])));
        }
        if (isset($data["required"])) {
            $cleanData["required"] = intval($data["required"]);
        }
        if (isset($data["is_show_on_comment"])) {
            $cleanData["is_show_on_comment"] = intval($data["is_show_on_comment"]);
        } else {
            $cleanData["is_show_on_comment"] = 0;
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

    protected function isShowForUser($args, $currentUser = null) {
        $isShowForUser = true;
        if (is_admin() && !wp_doing_ajax() && current_user_can("manage_options")) {
            return $isShowForUser;
        }
        if (is_null($currentUser)) {
            $currentUser = wp_get_current_user();
        }
        if (!isset($args["show_for_users"])) {
            $args["show_for_users"] = 1;
        }
        if (!isset($args["show_for_guests"])) {
            $args["show_for_guests"] = 1;
        }
        if ($currentUser->exists() && !$args["show_for_users"]) {
            $isShowForUser = false;
        }

        if (!$currentUser->exists() && !$args["show_for_guests"]) {
            $isShowForUser = false;
        }
        return apply_filters("wpdiscuz_show_field_for_user", $isShowForUser, $currentUser, $args);
    }

    protected function isValidateRequired($args, $currentUser = null) {
        if (!$args["required"] || !$this->isShowForUser($args, $currentUser) || (!$this->isCommentParentZero() && !$args["is_show_sform"]) || (is_admin() && !wp_doing_ajax() && current_user_can("manage_options"))) {
            return false;
        }
        return true;
    }

    protected function isCommentParentZero() {
        $isParent = false;
        $uniqueID = Sanitizer::sanitize(INPUT_POST, "wpdiscuz_unique_id", "FILTER_SANITIZE_STRING");
        $action   = Sanitizer::sanitize(INPUT_POST, "action", "FILTER_SANITIZE_STRING");
        if ($uniqueID) {
            $commentParent = strstr($uniqueID, "_");
            $isParent      = ($action === "editedcomment" && $commentParent === "_0") || ($action === "wpdSaveEditedComment" && $commentParent === "_0") || ($action === "wpdAddComment" && $uniqueID === "0_0") ? true : false;
        }
        return $isParent;
    }

    protected function initDefaultData() {
        $this->fieldDefaultData = [
            "name"               => "",
            "desc"               => "",
            "values"             => [],
            "icon"               => "",
            "required"           => "0",
            "loc"                => "bottom",
            "is_show_on_comment" => 1,
            "is_show_sform"      => 0,
            "show_for_guests"    => 1,
            "show_for_users"     => 1,
        ];
    }

}
