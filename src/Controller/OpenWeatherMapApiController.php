<?php

namespace App\Controller;

use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class OpenWeatherMapApiController extends AbstractController
{
    /**
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws InvalidArgumentException
     */
    #[Route('/api/weather/{city}', name: 'weather_city', methods: ['GET'])]
    public function getWeatherByZipCode(
        string $city,
        HttpClientInterface $httpClient,
        TagAwareCacheInterface $cache,
        ParameterBagInterface $params,
    ): JsonResponse
    {
        $cacheKey = "weather" . $city;
        $weatherData = $cache->get($cacheKey, function (ItemInterface $item) use ($city, $httpClient, $params) {
            $apiKey = $params->get('openweathermap_api_key');
            $item->expiresAfter(3600);

            $url = sprintf(
                'https://api.openweathermap.org/data/2.5/weather?zip=%s,fr&lang=fr&appid=%s&units=metric',
                $city,
                $apiKey,
            );

            $response = $httpClient->request('GET', $url);

            return $response->getContent();
        });


        return new JsonResponse($weatherData, Response::HTTP_OK, [], true);
    }

    #[Route('/api/weather', name: 'weather', methods: ['GET'])]
    public function getWeather(
        HttpClientInterface $httpClient,
        TagAwareCacheInterface $cache,
        ParameterBagInterface $params,
    ): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $city = $user->getCity();

        $cacheKey = "weather" . $city;
        $weatherData = $cache->get($cacheKey, function (ItemInterface $item) use ($city, $httpClient, $params) {
            $apiKey = $params->get('openweathermap_api_key');
            $item->expiresAfter(3600);

            $url = sprintf(
                'https://api.openweathermap.org/data/2.5/weather?zip=%s,fr&lang=fr&appid=%s&units=metric',
                $city,
                $apiKey,
            );

            $response = $httpClient->request('GET', $url);

            return $response->getContent();
        });


        return new JsonResponse($weatherData, Response::HTTP_OK, [], true);
    }
}
