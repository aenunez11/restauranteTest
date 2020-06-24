<?php


namespace Domain\Services\Factory;


use Domain\Entity\Restaurant;

class RestaurantFactory
{

    public function createByData(array $restaurantData): Restaurant
    {
        $restaurant = (new Restaurant())
            ->setUidentifier($restaurantData['uidentifier'])
            ->setName($restaurantData['name'])
            ->setAddress($restaurantData['street_address'])
            ->setLatitude($restaurantData['latitude'])
            ->setLongitude($restaurantData['longitude'])
            ->setCityName($restaurantData['city_name'])
            ->setPopularityRate($restaurantData['popularity_rate'])
            ->setSatisfactionRate($restaurantData['satisfaction_rate'])
            ->setAveragePrice($restaurantData['last_avg_price'])
            ->setTotalReviews($restaurantData['total_reviews']);

        return $restaurant;
    }
}
