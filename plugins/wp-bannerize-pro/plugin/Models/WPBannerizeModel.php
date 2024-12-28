<?php

namespace WPBannerize\Models;

use Exception;
use WPBannerize\WPBones\Database\DB;

class WPBannerizeModel
{
  /**
   * @var mixed
   */
  public $table;

  /**
   * @var mixed
   */
  protected $record;

  /**
   * @var array
   */
  protected $where = ['WHERE 1'];

  /**
   * @var string
   */
  protected $count = '';

  /**
   * @var string
   */
  protected $accuracy = '%Y-%m-%d %H:%i:%s';

  /**
   * @var string
   */
  protected $orderBy = '';

  /**
   * @var string
   */
  protected $order = 'ASC';

  /**
   * @var string
   */
  protected $groupBy = '';

  /**
   * @var string
   */
  protected $limit = '';

  /**
   * @var string
   */
  protected $dateFrom = '';

  /**
   * @var string
   */
  protected $dateTo = '';

  /**
   * @var string
   */
  protected $dateIntervalFrom = '';

  /**
   * @var string
   */
  protected $dateIntervalTo = '';

  /**
   * @var array
   */
  protected $categories = [];

  /**
   * @var array
   */
  protected $banners = [];

  public function __construct()
  {
    $this->table = DB::getTableName(get_called_class());
  }

  /**
   * @return mixed
   */
  public static function getTableName()
  {
    $instance = new static();

    return $instance->table;
  }

  /**
   * @param $name
   */
  public function __get($name) {}

  /**
   * @param $name
   * @param $arguments
   * @return mixed|void
   */
  public function __call($name, $arguments)
  {
    $method = 'set' . ucfirst($name) . 'Attribute';

    if (method_exists($this, $method)) {
      return call_user_func_array([$this, $method], $arguments);
    }
  }

  /**
   * @param $name
   * @param $arguments
   * @return mixed|void
   */
  public static function __callStatic($name, $arguments)
  {
    $instance = new static();

    $method = 'set' . ucfirst($name) . 'Attribute';

    if (method_exists($instance, $method)) {
      return call_user_func_array([$instance, $method], $arguments);
    }
  }

  /**
   * @param $id
   */
  public static function find($id) {}

  /**
   * @return mixed
   */
  public static function all()
  {
    $instance = new static();

    return $instance->get();
  }

  /**
   * @param       $values
   * @param array $formats
   * @return \WPBannerize\Models\WPBannerizeModel
   */
  public static function create($values, $formats = [])
  {
    /**
     * @var wpdb $wpdb
     */
    global $wpdb;

    $instance = new static();

    try {
      $result = $wpdb->insert($instance->table, $values, $formats);
    } catch (Exception $e) {
      trigger_error("Error while create a record in {$instance->table} table");
    } finally {
      //
      return $instance;
    }
  }

  /**
   * @param $values
   */
  public static function update($values) {}

  /**
   * @param $value
   */
  public function setWhereAttribute($value)
  {
    $this->where[] = $value;
  }

  /**
   * @param $count
   * @return mixed
   */
  public function setCountAttribute($count)
  {
    $this->count = $count . ',';

    return $this;
  }

  /**
   * @param $accuracy
   * @return mixed
   */
  public function setAccuracyAttribute($accuracy = null)
  {
    $conversion = [
      'seconds' => '%Y-%m-%d %H:%i:%s',
      'minutes' => '%Y-%m-%d %H:%i',
      'hours' => '%Y-%m-%d %H',
      'days' => '%Y-%m-%d',
      'months' => '%Y-%m',
      'years' => '%Y',
    ];

    if (is_null($accuracy)) {
      $this->accuracy = $conversion['seconds'];

      return $this;
    }

    // Check for keys
    if (in_array(strtolower($accuracy), array_keys($conversion))) {
      $this->accuracy = $conversion[strtolower($accuracy)];

      return $this;
    }

    // Check for values
    if (in_array($accuracy, array_values($conversion))) {
      $this->accuracy = $accuracy;

      return $this;
    }

    $this->accuracy = current($conversion);

    return $this;
  }

  /**
   * @param $value
   * @return \WPBannerize\Models\WPBannerizeModel
   */
  public function setOrderByAttribute($value)
  {
    $this->orderBy = $value;

    return $this;
  }

  /**
   * @param $value
   * @return \WPBannerize\Models\WPBannerizeModel
   */
  public function setOrderAttribute($value)
  {
    $this->order = $value;

    return $this;
  }

  /**
   * @param $value
   * @return \WPBannerize\Models\WPBannerizeModel
   */
  public function setGroupByAttribute($value)
  {
    $this->groupBy = $value;

    return $this;
  }

  /**
   * @param $value
   * @return \WPBannerize\Models\WPBannerizeModel
   */
  public function setLimitAttribute($value)
  {
    $this->limit = $value;

    return $this;
  }

  /**
   * @param $value
   * @return \WPBannerize\Models\WPBannerizeModel
   */
  public function setDateFromAttribute($value)
  {
    if (is_numeric($value)) {
      $value = gmdate('Y-m-d H:i:s', $value);
    }
    $this->dateFrom = $value;

    return $this;
  }

  /**
   * @param $value
   * @return \WPBannerize\Models\WPBannerizeModel
   */
  public function setDateToAttribute($value)
  {
    if (is_numeric($value)) {
      $value = gmdate('Y-m-d H:i:s', $value);
    }

    $this->dateTo = $value;

    return $this;
  }

  /**
   * @param $value
   * @return \WPBannerize\Models\WPBannerizeModel
   */
  public function setDateIntervalFromAttribute($value)
  {
    $this->dateIntervalFrom = $value;

    return $this;
  }

  /**
   * @param $value
   * @return \WPBannerize\Models\WPBannerizeModel
   */
  public function setDateIntervalToAttribute($value)
  {
    $this->dateIntervalTo = $value;

    return $this;
  }

  /**
   * @param $value
   * @return \WPBannerize\Models\WPBannerizeModel
   */
  public function setCategoriesAttribute($value)
  {
    if (is_string($value)) {
      $value = explode(',', $value);
    }

    $sanitized_value = empty($value) ? [] : (array) $value;

    $this->categories = array_filter($sanitized_value);

    return $this;
  }

  /**
   * @param $value
   * @return \WPBannerize\Models\WPBannerizeModel
   */
  public function setBannersAttribute($value)
  {
    if (is_string($value)) {
      $value = explode(',', $value);
    }
    $this->banners = $value;

    return $this;
  }
}
