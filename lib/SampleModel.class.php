<?php
/**
 * Sample Model instance
 *
 * @method Sample static::findOneBy()
 */
class Sample extends SimpleOrm
{
  /**
   * Array with table fields
   *
   * @var array
   */
  protected $_payload = array(
    "id" => null,
    "someName" => null,
    "bitmask" => null
  );

  /**
   * @var string
   */
  protected static $table = 'sample';
}
