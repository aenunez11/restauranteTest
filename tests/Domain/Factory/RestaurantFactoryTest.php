<?php
/**
 * Created by PhpStorm.
 * User: aenun
 * Date: 24/06/2020
 * Time: 11:01
 */

namespace App\Tests\Domain\Factory;

use App\Tests\ParentTestCase;
use Domain\Services\Factory\RestaurantFactory;

class RestaurantFactoryTest extends ParentTestCase
{
    /** @var string */
    const RESTAURANT_INPUT_PATH = 'Domain/Entity/restaurant/inputs/';
    /** @var string */
    const RESTAURANT_OUTPUT_PATH = 'Domain/Entity/restaurant/outputs/';

    private $restaurantFactory;

    /**
     * Implements setUp of PHPUnit
     */
    protected function setUp(): void
    {
        $this->inputResourceMiddlePath = self::RESTAURANT_INPUT_PATH;
        $this->outputResourceMiddlePath = self::RESTAURANT_OUTPUT_PATH;
        $this->restaurantFactory = new RestaurantFactory();
    }

    public function testCreateByData()
    {
        $inputs = $this->loadJsonFromInputResources("data.json");
        $outputs = $this->loadJsonFromOutputResources("results.json");

        $restaurant = $this->restaurantFactory->createByData($inputs);

        $this->assertEquals($outputs['uidentifier'], $restaurant->getUidentifier());
        $this->assertEquals($outputs['name'], $restaurant->getName());
    }
}
