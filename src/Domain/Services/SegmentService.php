<?php

namespace Domain\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class SegmentService
{

    /** @var EntityManager */
    private $em;

    /**
     * SegmentService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


}
