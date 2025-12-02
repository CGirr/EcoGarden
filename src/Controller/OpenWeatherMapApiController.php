<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Weather\WeatherEcoGardenInterface;
use App\Service\Weather\WeatherException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class OpenWeatherMapApiController extends AbstractController
{
    /**
     * @throws WeatherException
     */
    #[Route('/api/weather/{city}', name: 'weather_city', methods: ['GET'])]
    public function getWeatherByCity(string $city, WeatherEcoGardenInterface $weatherEcoGarden): JsonResponse
    {
        $weather = $weatherEcoGarden->getWeather($city);

        return $this->json($weather);
    }

    #[Route('/api/weather', name: 'weather', methods: ['GET'])]
    public function getWeather(
        WeatherEcoGardenInterface $weatherEcoGarden
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();
        $city = $user->getCity();
        $weather = $weatherEcoGarden->getWeather($city);

        return $this->json($weather);
    }
}
