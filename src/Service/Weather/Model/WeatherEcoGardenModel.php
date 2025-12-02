<?php

namespace App\Service\Weather\Model;

/**
 * Data Transfer Object representing normalized weather values for EcoGarden
 */
readonly class WeatherEcoGardenModel
{
    public function __construct(
        private float $temperature,
        private float $feelsLike,
        private float $rain,
        private float $windSpeed,
        private int $humidity,
        private string $description,
        private string $city
    ) {}

    public function getTemperature(): float
    {
        return $this->temperature;
    }

    public function getFeelsLike(): float
    {
        return $this->feelsLike;
    }

    public function getRain(): float
    {
        return $this->rain;
    }

    public function getWindSpeed(): float
    {
        return $this->windSpeed;
    }

    public function getHumidity(): int
    {
        return $this->humidity;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCity(): string
    {
        return $this->city;
    }


}
