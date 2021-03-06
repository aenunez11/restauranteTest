<?php

namespace Domain\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Domain\Entity\Segment;

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

    public function save(Segment $segment, $flush = true)
    {
        $this->em->persist($segment);
        if ($flush) {
            $this->em->flush();
        }
    }

    public function remove(Segment $segment)
    {
        $this->em->remove($segment);
        $this->em->flush();
    }
}
