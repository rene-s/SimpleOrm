<?php
/**
 * SimpleOrm
 *
 * PHP Version 5.5
 *
 * @category Database
 * @package  SimpleOrm
 * @author   Rene Schmidt <rene@reneschmidt.de>
 * @license  https://www.gnu.org/licenses/lgpl.html LGPLv3
 * @link     https://reneschmidt.de/
 */
namespace SimpleOrm\Tests;

use SimpleOrm\SimpleOrm;

/**
 * Sample Model instance.
 *
 * Define correct type hinting like this:
 *
 * @method Sample findOneBy()
 * @method Sample[] findBy()
 * @method Sample[] findByQuery()
 * @method Sample[] collectRecords()
 *
 * @category Database
 * @package  SimpleOrm
 * @author   Rene Schmidt <rene@reneschmidt.de>
 * @license  https://www.gnu.org/licenses/lgpl.html LGPLv3
 * @link     https://reneschmidt.de/
 */
class Sample extends SimpleOrm
{
    /**
     * Array with table fields
     *
     * @var array
     */
    protected $payload = array(
        "id" => null, // first field is primary key
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

        if (!($sample instanceof Sample)) {
            $sample = new Sample(array("someName" => $someName, "bitmask" => 0));
            $sample->save();
        }

        return $sample;
    }
}
