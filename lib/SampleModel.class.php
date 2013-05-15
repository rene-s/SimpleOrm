<?php
/**
 * Sample Model instance
 *
 * @method Sample static::findOneBy()
 * @method Sample static::findBy()
 */
class Sample extends SimpleOrm
{
  /**
   * @var string
   */
  protected $id, $someName;

  /**
   * @var string
   */
  protected static $table = 'sample';
}
