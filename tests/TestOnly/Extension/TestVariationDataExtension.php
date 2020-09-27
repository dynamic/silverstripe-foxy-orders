<?php

namespace Dynamic\Foxy\Orders\Tests\TestOnly\Extension;

use Dynamic\Foxy\Model\Variation;
use Dynamic\Foxy\Orders\Tests\TestOnly\Page\TestProduct;
use SilverStripe\ORM\DataExtension;

/**
 * Class TestVariationDataExtension
 * @package Dynamic\Foxy\Orders\Tests\TestOnly\Extension
 */
class TestVariationDataExtension extends DataExtension
{
    /**
     * @var string[]
     */
    private static $has_one = [
        'Product' => TestProduct::class,
    ];

    /**
     * @param null $class
     * @param null $extensionClass
     * @param null $args
     * @return array
     */
    public static function get_extra_config($class = null, $extensionClass = null, $args = null)
    {
        $config = [];

        // Only add these extensions if the $class is set to DataExtensionTest_Player, to
        // test that the argument works.
        if (strcasecmp($class, Variation::class) === 0) {
            $config['has_one'] = [
                'Product' => TestProduct::class,
            ];
        }

        return $config;
    }
}
