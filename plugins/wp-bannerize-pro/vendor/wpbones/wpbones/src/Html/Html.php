<?php

namespace WPBannerize\WPBones\Html;

class Html
{
    protected static $htmlTags = [
    'a'        => '\WPBannerize\WPBones\Html\HtmlTagA',
    'button'   => '\WPBannerize\WPBones\Html\HtmlTagButton',
    'checkbox' => '\WPBannerize\WPBones\Html\HtmlTagCheckbox',
    'datetime' => '\WPBannerize\WPBones\Html\HtmlTagDatetime',
    'fieldset' => '\WPBannerize\WPBones\Html\HtmlTagFieldSet',
    'form'     => '\WPBannerize\WPBones\Html\HtmlTagForm',
    'input'    => '\WPBannerize\WPBones\Html\HtmlTagInput',
    'label'    => '\WPBannerize\WPBones\Html\HtmlTagLabel',
    'optgroup' => '\WPBannerize\WPBones\Html\HtmlTagOptGroup',
    'option'   => '\WPBannerize\WPBones\Html\HtmlTagOption',
    'select'   => '\WPBannerize\WPBones\Html\HtmlTagSelect',
    'textarea' => '\WPBannerize\WPBones\Html\HtmlTagTextArea',
  ];

    public static function __callStatic($name, $arguments)
    {
        if (in_array($name, array_keys(self::$htmlTags))) {
            $args = (isset($arguments[ 0 ]) && ! is_null($arguments[ 0 ])) ? $arguments[ 0 ] : [];

            return new self::$htmlTags[ $name ]($args);
        }
    }
}
