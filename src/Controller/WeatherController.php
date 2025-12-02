<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Weather\WeatherEcoGardenInterface;
use App\Service\Weather\WeatherException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class WeatherController extends AbstractController
{
    public function __construct(private readonly WeatherEcoGardenInterface $weatherEcoGarden)
    {}

    /**
     * @throws WeatherException
     */
    #[Route('/api/weather/{city}', name: 'weather_city', methods: ['GET'])]
    public function getWeatherByCity(string $city): JsonResponse
    {
        $weather = $this->weatherEcoGarden->getWeather($city);

        return $this->json($weather);
    }

    #[Route('/api/weather', name: 'weather', methods: ['GET'])]
    public function getWeather(): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();
        $city = $user->getCity();
        $weather = $this->weatherEcoGarden->getWeather($city);

        return $this->json($weather);
    }
}
