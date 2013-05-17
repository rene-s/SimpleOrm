<?php
/**
 * Sample Model instance.
 *
 * Define correct type hinting like this:
 *
 * @method Sample findOneBy()
 * @method Sample[] findBy()
 * @method Sample[] findByQuery()
 * @method Sample[] collectRecords()
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

  /**
   * Find or create page.
   *
   * Example user-implemented model method returning a model instance.
   *
   * @param string $someName Page name
   *
   * @return Sample
   */
  public function findOrCreate($someName)
  {
    $sample = $this->findOneBy("someName", $someName);

    if (!($sample instanceOf Sample)) {
      $sample = new Sample(array("someName" => $someName, "bitmask" => 0));
      $sample->save();
    }

    return $sample;
  }
}
