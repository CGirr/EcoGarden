<?php

namespace App\Service\Weather;

use App\Service\Weather\Model\WeatherEcoGardenModel;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenWeatherMapService implements WeatherEcoGardenInterface
{

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly TagAwareCacheInterface $cache,
        private readonly string $apiKey
    ) {}

    /**
     * @param string $city
     * @return WeatherEcoGardenModel
     * @throws InvalidArgumentException
     * @throws WeatherException
     */
    public function getWeather(string $city): WeatherEcoGardenModel
    {
        $cacheKey = 'weather_' . $city;

        $data = $this->cache->get($cacheKey, function (ItemInterface $item) use ($city) {
            $item->expiresAfter(3600);
            try {
                $response = $this->httpClient->request(
                    'GET',
                    'https://api.openweathermap.org/data/2.5/weather',
                    [
                        'query' => [
                            'zip' => $city . ',fr',
                            'lang' => 'fr',
                            'appid' => $this->apiKey,
                            'units' => 'metric',
                        ],
                    ]
                );

                return $response->toArray();
            } catch (ClientExceptionInterface|ServerExceptionInterface $e) {
                $statusCode = $e->getResponse()->getStatusCode();
                $message = match ($statusCode) {
                    404 => 'Ville non trouvée',
                    401 => 'Clé API invalide',
                    default => $e->getMessage(),
                };
                throw new WeatherException($statusCode, $message);
            } catch (TransportExceptionInterface $e) {
                throw new WeatherException(503, 'Service météo indisponible');
            }
        });

        return $this->mapToModel($data, $city);
    }

    private function mapToModel(array $data): WeatherEcoGardenModel
    {
        $temperature = $data['main']['temp'];
        $feelsLike = $data['main']['feels_like'];
        $rain = $data['rain']['1h'] ?? 0;
        $windSpeed = $data['wind']['speed'];
        $humidity = $data['main']['humidity'];
        $description = $data['weather'][0]['description'];
        $city = $data['name'];

        return new WeatherEcoGardenModel(
            $temperature,
            $feelsLike,
            $rain,
            $windSpeed,
            $humidity,
            $description,
            $city
        );
    }
}
