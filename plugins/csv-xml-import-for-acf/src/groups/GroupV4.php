<?php

namespace pmai_acf_add_on\groups;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class GroupV4
 * @package pmai_acf_add_on\groups
 */
class GroupV4 extends Group {

    /**
     *  Init group field for ACF v4.x
     */
    public function initFields() {

        foreach (get_post_meta($this->group['ID'], '') as $cur_meta_key => $cur_meta_val) {
            if (strpos($cur_meta_key, 'field_') !== 0) {
                continue;
            }
            $this->fieldsData[] = empty($cur_meta_val[0]) ? array() : unserialize($cur_meta_val[0]);
        }

        if (count($this->fieldsData)) {
            $sortArray = array();
            foreach ($this->fieldsData as $field) {
                foreach ($field as $key => $value) {
                    if (!isset($sortArray[$key])) {
                        $sortArray[$key] = array();
                    }
                    $sortArray[$key][] = $value;
                }
            }
            array_multisort($sortArray["order_no"], SORT_ASC, $this->fieldsData);
        }
        // create field instances
        parent::initFields();
    }
}