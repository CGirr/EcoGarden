<?php

namespace App\Service\Weather;

use App\Service\Weather\Model\WeatherEcoGardenModel;

interface WeatherEcoGardenInterface
{
    public function getWeather(string $city): WeatherEcoGardenModel;
}
