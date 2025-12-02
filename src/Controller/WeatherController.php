<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Weather\WeatherEcoGardenInterface;
use App\Service\Weather\WeatherException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

final class WeatherController extends AbstractController
{
    public function __construct(private readonly WeatherEcoGardenInterface $weatherEcoGarden)
    {}

    /**
     * @throws WeatherException
     */
    #[Route('/api/weather/{city}', name: 'weather_city', methods: ['GET'])]
    #[OA\Get(
        description: 'Get weather data for a specified city',
        summary: 'Weather for a specified city',
        tags: ['weather'],
    )]
    #[OA\Response(response: 200, description: 'Formatted weather data from an external API for the specified city')]
    #[OA\Parameter(
        name: 'city',
        description: 'Zip code of a city',
        in: 'path',
        required: true,
    )]
    public function getWeatherByCity(string $city): JsonResponse
    {
        $weather = $this->weatherEcoGarden->getWeather($city);

        return $this->json($weather);
    }

    #[Route('/api/weather', name: 'weather', methods: ['GET'])]
    #[OA\Get(
        description: 'Get weather data depending on the user\'s location',
        summary: 'Weather data for user',
        tags: ['weather'],
    )]
    #[OA\Response(response: 200, description: 'Formatted weather data from an external API for the user\'s city')]
    public function getWeather(): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();
        $city = $user->getCity();
        $weather = $this->weatherEcoGarden->getWeather($city);

        return $this->json($weather);
    }
}
