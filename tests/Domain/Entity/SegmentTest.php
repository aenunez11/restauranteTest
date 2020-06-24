<?php

namespace Tests\Domain\Entity;

use App\Tests\ParentTestCase;
use Domain\Entity\Restaurant;
use Domain\Entity\Segment;

/**
 * Class BrandTest
 * @package Tests\Domain\Benchmark\Factory
 */
class SegmentTest extends ParentTestCase
{

    /** @var string */
    const SEGMENT_INPUT_PATH = 'Domain/Entity/segment/inputs/';
    /** @var string */
    const SEGMENT_OUTPUT_PATH = 'Domain/Entity/segment/outputs/';

    /**
     * Implements setUp of PHPUnit
     */
    protected function setUp(): void
    {
        $this->inputResourceMiddlePath = self::SEGMENT_INPUT_PATH;
        $this->outputResourceMiddlePath = self::SEGMENT_OUTPUT_PATH;
    }

    /**
     * Test Getters of Segment Entity
     */
    public function testSegmentEntityGetters()
    {
        $inputs = $this->loadJsonFromInputResources("data.json");
        $outputs = $this->loadJsonFromOutputResources("results.json");

        $segment  = new Segment();
        $segment->setName($inputs["name"]);
        $segment->setUidentifier($inputs["uidentifier"]);

        $this->assertEquals($outputs["name"], $segment->getName());
        $this->assertEquals($outputs["uidentifier"], $segment->getUidentifier());
    }

    public function testSegmentRestaurants()
    {
        $inputs = $this->loadJsonFromInputResources("data.json");

        $segment  = new Segment();
        $segment->setName($inputs["name"]);
        $segment->setUidentifier($inputs["uidentifier"]);

        $restaurant1  = new Restaurant();
        $restaurant1->setName('Rest 1');
        $restaurant1->setUidentifier('12345abc');
        $restaurant1->setTotalReviews(10);
        $restaurant1->addSegment($segment);

        $restaurant2  = new Restaurant();
        $restaurant2->setName('Rest 2');
        $restaurant2->setUidentifier('abcde123');
        $restaurant2->setTotalReviews(15);
        $restaurant2->addSegment($segment);

        $segment->updateValueSegment();

        $this->assertCount(2,$segment->getRestaurants());
        $this->assertEquals(25,$segment->getTotalReviews());

    }
}
