<?php

namespace App\Support\Geofence\YandexGeo\Results;

use App\Support\Geofence\YandexGeo\Objects\GeoObject;
use App\Support\Geofence\Results\SuccessResult;

/**
 * Результат геокодирования YandexGeo
 */
class YandexGeoGeocodeResult extends SuccessResult
{
    /**
     * Получить все результаты как GeoObject
     */
    public function getResults(): array
    {
        $featureMembers = $this->rawData['response']['GeoObjectCollection']['featureMember'] ?? [];
        $results = [];
        
        foreach ($featureMembers as $featureMember) {
            $results[] = new GeoObject($featureMember['GeoObject'] ?? []);
        }
        
        return $results;
    }

    /**
     * Получить первый результат как GeoObject
     */
    public function getFirstGeo(): ?GeoObject
    {
        $results = $this->getResults();
        return $results[0] ?? null;
    }

    /**
     * Получить результат по индексу как GeoObject
     */
    public function getGeo(int $index): ?GeoObject
    {
        $results = $this->getResults();
        return $results[$index] ?? null;
    }

    /**
     * Найти геообъект по условию
     */
    public function findGeo(callable $callback): ?GeoObject
    {
        foreach ($this->getResults() as $geo) {
            if ($callback($geo)) {
                return $geo;
            }
        }
        return null;
    }

    /**
     * Фильтровать геообъекты по условию
     */
    public function filterGeos(callable $callback): array
    {
        return array_filter($this->getResults(), $callback);
    }

    /**
     * Получить геообъекты с координатами
     */
    public function getGeosWithCoordinates(): array
    {
        return $this->filterGeos(function (GeoObject $geo) {
            return $geo->hasCoordinates();
        });
    }

    /**
     * Получить геообъекты по точности
     */
    public function getGeosByAccuracy(string $accuracy): array
    {
        return $this->filterGeos(function (GeoObject $geo) use ($accuracy) {
            return $geo->getAccuracy() === $accuracy;
        });
    }

    /**
     * Получить геообъекты по типу
     */
    public function getGeosByKind(string $kind): array
    {
        return $this->filterGeos(function (GeoObject $geo) use ($kind) {
            return $geo->getKind() === $kind;
        });
    }

    /**
     * Получить геообъекты по стране
     */
    public function getGeosByCountry(string $country): array
    {
        return $this->filterGeos(function (GeoObject $geo) use ($country) {
            return $geo->getCountry() === $country;
        });
    }

    /**
     * Получить геообъекты по городу
     */
    public function getGeosByCity(string $city): array
    {
        return $this->filterGeos(function (GeoObject $geo) use ($city) {
            return $geo->getCity() === $city;
        });
    }

    public function getCount(): int
    {
        return count($this->rawData['response']['GeoObjectCollection']['featureMember'] ?? []);
    }

    public function getMetadata(): array
    {
        return [
            'service' => 'YandexGeo',
            'method' => 'geocode',
            'count' => $this->getCount(),
            'has_results' => $this->hasResults(),
        ];
    }
}