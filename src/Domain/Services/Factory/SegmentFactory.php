<?php

namespace Domain\Services\Factory;

use Domain\Entity\Segment;

class SegmentFactory
{
    public function create($uidentifier = null,$name = null): Segment
    {
        $segment = new Segment();
        $segment->setCreatedAt(new \DateTime('now'));

        if(!empty($uidentifier)) {
            $segment->setUidentifier($uidentifier);
        }
        if(!empty($name)){
            $segment->setName($name);
        }

        return $segment;
    }
}
