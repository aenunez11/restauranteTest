<?php

namespace Tests\Domain\Entity;

use App\Tests\ParentTestCase;
use Domain\Entity\Restaurant;
use Domain\Entity\Segment;

/**
 * Class BrandTest
 * @package Tests\Domain\Benchmark\Factory
 */
class RestaurantTest extends ParentTestCase
{

    /** @var string */
    const RESTAURANT_INPUT_PATH = 'Domain/Entity/restaurant/inputs/';
    /** @var string */
    const RESTAURANT_OUTPUT_PATH = 'Domain/Entity/restaurant/outputs/';

    /**
     * Implements setUp of PHPUnit
     */
    protected function setUp(): void
    {
        $this->inputResourceMiddlePath = self::RESTAURANT_INPUT_PATH;
        $this->outputResourceMiddlePath = self::RESTAURANT_OUTPUT_PATH;
    }

    /**
     * Test Getters of Restaurant Entity
     */
    public function testRestaurantEntityGetters()
    {
        $inputs = $this->loadJsonFromInputResources("data.json");
        $outputs = $this->loadJsonFromOutputResources("results.json");
        $entity = new Restaurant();
        $entity->setName($inputs["name"]);
        $this->assertEquals($outputs["name"], $entity->getName());
    }

}