<?php


namespace Domain\Services;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Domain\Entity\Restaurant;

class RestaurantService
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

    public function save(Restaurant $restaurant, $flush = true)
    {
        $this->em->persist($restaurant);
        if ($flush) {
            $this->em->flush();
        }
    }
}
