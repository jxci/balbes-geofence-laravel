<?php

namespace App\Support\Geofence\DaData\Results;

use App\Support\Geofence\DaData\Objects\GeoObject;
use App\Support\Geofence\Results\SuccessResult;

/**
 * Результат геокодирования DaData
 */
class DaDataGeocodeResult extends SuccessResult
{
    /**
     * Получить все результаты как GeoObject
     */
    public function getResults(): array
    {
        $suggestions = $this->rawData['suggestions'] ?? [];
        $results = [];
        
        foreach ($suggestions as $suggestion) {
            $results[] = new GeoObject($suggestion['data'] ?? []);
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
     * Получить геообъекты по уровню детализации
     */
    public function getGeosByLevel(string $level): array
    {
        return $this->filterGeos(function (GeoObject $geo) use ($level) {
            return $geo->getLevel() === $level;
        });
    }

    public function getCount(): int
    {
        return count($this->rawData['suggestions'] ?? []);
    }

    public function getMetadata(): array
    {
        return [
            'service' => 'DaData',
            'method' => 'geocode',
            'count' => $this->getCount(),
            'has_results' => $this->hasResults(),
        ];
    }
}
