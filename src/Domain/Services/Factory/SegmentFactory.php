<?php

namespace Domain\Services\Factory;

use Domain\Entity\Segment;

class SegmentFactory
{
    public function create(): Segment
    {
        $segment = new Segment();
        $segment->setCreatedAt(new \DateTime('now'));

        return $segment;
    }
}
