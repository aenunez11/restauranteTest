<?php


namespace Domain\Services\Importer;


use Domain\Services\Factory\RestaurantFactory;
use Domain\Services\RestaurantService;
use Doctrine\ORM\EntityManagerInterface;
use Domain\Repository\RestaurantRepository;
use Domain\Services\Factory\SegmentFactory;
use Domain\Services\SegmentService;
use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;

class SegmentImporter
{
    private $segmentFactory;
    private $segmentService;
    private $restaurantFactory;
    private $restaurantService;
    private $restaurantRepository;
    private $entityManager;

    public function __construct(
        SegmentFactory $segmentFactory,
        SegmentService $segmentService,
        RestaurantFactory $restaurantFactory,
        RestaurantService $restaurantService,
        RestaurantRepository $restaurantRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->segmentFactory = $segmentFactory;
        $this->segmentService = $segmentService;
        $this->restaurantFactory = $restaurantFactory;
        $this->restaurantService = $restaurantService;
        $this->restaurantRepository = $restaurantRepository;
        $this->entityManager = $entityManager;
    }

    /**
     *
     * [
     * "name": "Medium Segment",
     * "size": 200,
     * "uidentifier": "9189b84d994347bd888cb9cc0a6f1e83",
     * "restaruants": [
     * [
     * "name": "Bar Restaurante en Barbastro La Cerámica",
     * "street_address": "Calle Cerámica Industrial VI",
     * "latitude": "42.0421852",
     * "longitude": "0.1383939",
     * "city_name": "Barbastro",
     * "popularity_rate": "6.04",
     * "satisfaction_rate": "8.5",
     * "last_avg_price": "30",
     * "total_reviews": "3",
     * "uidentifier": "0e38005b09304a2596412341ea922016"
     * ],
     *  ]
     *  ]
     *
     */
    public function import(array $data)
    {
        //recorrer los segmentos
        foreach ($data as $segmentData) {
            //crear el segmento
            $segment = $this->segmentFactory->create($segmentData['uidentifier'],$segmentData['name']);

            //recorrer los restaurantes
            $avg_price = 0;
            $total_restaurants = 0;
            $avg_popularity_rate = 0;
            $avg_satisfaction_rate = 0;
            $total_reviews = 0;
            foreach ($segmentData['restaruants'] as $restaurantData) {
                //chequear si existe
                if (null === $restaurant = $this->restaurantRepository->findOneBy(
                        ['uidentifier' => $restaurantData['uidentifier']]
                    )) {
                    // Si no existe, crearlo
                    $restaurant = $this->restaurantFactory->createByData($restaurantData);

                }
                // Asignarlo al segmento
                $restaurant->addSegment($segment);
                //guardar el restaurante
                $this->restaurantService->save($restaurant);

                $avg_price += $restaurantData['last_avg_price'];
                $avg_popularity_rate += $restaurantData['popularity_rate'];
                $avg_satisfaction_rate += $restaurantData['satisfaction_rate'];
                $total_reviews += $restaurantData['total_reviews'];
                $total_restaurants += 1;
            }

            //guadar el segmento
            $segment->setAveragePrice($avg_price/$total_restaurants);
            $segment->setAveragePopularityRate($avg_popularity_rate/$total_restaurants);
            $segment->setAverageSatisfactionRate($avg_satisfaction_rate/$total_restaurants);
            $segment->setTotalReviews($total_reviews);
            $this->segmentService->save($segment);

            $this->entityManager->clear();
        }
    }
}
