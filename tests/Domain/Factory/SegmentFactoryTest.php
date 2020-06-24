<?php
/**
 * Created by PhpStorm.
 * User: aenun
 * Date: 24/06/2020
 * Time: 11:01
 */

namespace App\Tests\Domain\Factory;

use App\Tests\ParentTestCase;
use Domain\Services\Factory\SegmentFactory;

class SegmentFactoryTest extends ParentTestCase
{
    /** @var string */
    const SEGMENT_INPUT_PATH = 'Domain/Entity/segment/inputs/';
    /** @var string */
    const SEGMENT_OUTPUT_PATH = 'Domain/Entity/segment/outputs/';
    private $segmentFactory;

    /**
     * Implements setUp of PHPUnit
     */
    protected function setUp(): void
    {
        $this->inputResourceMiddlePath = self::SEGMENT_INPUT_PATH;
        $this->outputResourceMiddlePath = self::SEGMENT_OUTPUT_PATH;
        $this->segmentFactory = new SegmentFactory();

    }

    public function testCreateByData()
    {
        $inputs = $this->loadJsonFromInputResources("data.json");
        $outputs = $this->loadJsonFromOutputResources("results.json");

        $segment = $this->segmentFactory->create($inputs['uidentifier'],$inputs['name']);

        $this->assertEquals($outputs['uidentifier'], $segment->getUidentifier());
        $this->assertEquals($outputs['name'], $segment->getName());
    }
}
