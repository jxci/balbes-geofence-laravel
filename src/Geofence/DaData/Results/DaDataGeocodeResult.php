<?php

namespace App\Support\Geofence\DaData\Results;

use App\Support\Geofence\DaData\Enums\FiasLevel;
use App\Support\Geofence\DaData\Enums\GeoAccuracyCode;
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
     * Получить геообъекты по точности (устаревший метод, используйте getGeosByGeoAccuracy)
     * @deprecated Используйте getGeosByGeoAccuracy(GeoAccuracyCode)
     */
    public function getGeosByAccuracy(string $accuracy): array
    {
        return $this->filterGeos(function (GeoObject $geo) use ($accuracy) {
            return $geo->getAccuracy() === $accuracy;
        });
    }

    /**
     * Получить геообъекты по точности координат (ENUM)
     */
    public function getGeosByGeoAccuracy(GeoAccuracyCode $accuracy): array
    {
        return $this->filterGeos(function (GeoObject $geo) use ($accuracy) {
            return $geo->getQcGeoEnum() === $accuracy;
        });
    }

    /**
     * Получить геообъекты с точными координатами
     */
    public function getGeosWithExactCoordinates(): array
    {
        return $this->getGeosByGeoAccuracy(GeoAccuracyCode::EXACT);
    }

    /**
     * Получить геообъекты пригодные для курьерской доставки
     */
    public function getGeosSuitableForCourierDelivery(): array
    {
        return $this->filterGeos(function (GeoObject $geo) {
            $geoAccuracy = $geo->getQcGeoEnum();
            return $geoAccuracy && $geoAccuracy->isSuitableForCourierDelivery();
        });
    }

    /**
     * Получить геообъекты по уровню детализации (устаревший метод, используйте getGeosByFiasLevel)
     * @deprecated Используйте getGeosByFiasLevel(FiasLevel)
     */
    public function getGeosByLevel(string $level): array
    {
        return $this->filterGeos(function (GeoObject $geo) use ($level) {
            return $geo->getLevel() === $level;
        });
    }

    /**
     * Получить геообъекты по уровню детализации ФИАС (ENUM)
     */
    public function getGeosByFiasLevel(FiasLevel $level): array
    {
        return $this->filterGeos(function (GeoObject $geo) use ($level) {
            return $geo->getFiasLevelEnum() === $level;
        });
    }

    /**
     * Получить лучший геообъект (первый с точными координатами или первый в списке)
     */
    public function getBestGeo(): ?GeoObject
    {
        $exactGeos = $this->getGeosWithExactCoordinates();
        if (!empty($exactGeos)) {
            return $exactGeos[0];
        }

        $suitableGeos = $this->getGeosSuitableForCourierDelivery();
        if (!empty($suitableGeos)) {
            return $suitableGeos[0];
        }

        return $this->getFirstGeo();
    }

    /**
     * Получить геообъекты с максимальной точностью координат
     */
    public function getGeosWithHighestAccuracy(): array
    {
        $exact = $this->getGeosWithExactCoordinates();
        if (!empty($exact)) {
            return $exact;
        }

        $nearestHouse = $this->getGeosByGeoAccuracy(GeoAccuracyCode::NEAREST_HOUSE);
        if (!empty($nearestHouse)) {
            return $nearestHouse;
        }

        return $this->getGeosByGeoAccuracy(GeoAccuracyCode::STREET);
    }

    /**
     * Получить геообъекты отсортированные по точности координат (от более точных к менее точным)
     */
    public function getGeosSortedByAccuracy(): array
    {
        $geos = $this->getResults();
        
        usort($geos, function (GeoObject $a, GeoObject $b) {
            $aAccuracy = $a->getQcGeoEnum();
            $bAccuracy = $b->getQcGeoEnum();
            
            if ($aAccuracy === null && $bAccuracy === null) return 0;
            if ($aAccuracy === null) return 1;
            if ($bAccuracy === null) return -1;
            
            return $aAccuracy->value <=> $bAccuracy->value;
        });
        
        return $geos;
    }

    /**
     * Получить геообъекты отсортированные по расстоянию (для обратного геокодирования)
     * Геообъекты в ответе DaData уже отсортированы по удалению от координат
     */
    public function getGeosSortedByDistance(): array
    {
        return $this->getResults();
    }

    /**
     * Получить геообъекты в указанном городе
     */
    public function getGeosInCity(string $city): array
    {
        return $this->filterGeos(function (GeoObject $geo) use ($city) {
            $geoCity = $geo->getCity();
            return $geoCity && mb_strtolower($geoCity) === mb_strtolower($city);
        });
    }

    /**
     * Получить геообъекты в указанном регионе
     */
    public function getGeosInRegion(string $region): array
    {
        return $this->filterGeos(function (GeoObject $geo) use ($region) {
            $geoRegion = $geo->getRegion();
            return $geoRegion && mb_strtolower($geoRegion) === mb_strtolower($region);
        });
    }

    /**
     * Получить геообъекты с указанным почтовым индексом
     */
    public function getGeosByPostalCode(string $postalCode): array
    {
        return $this->filterGeos(function (GeoObject $geo) use ($postalCode) {
            return $geo->getPostalCode() === $postalCode;
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
            'method' => $this->getMethod() ?? 'geocode',
            'count' => $this->getCount(),
            'has_results' => $this->hasResults(),
            'has_coordinates' => !empty($this->getGeosWithCoordinates()),
            'exact_coordinates_count' => count($this->getGeosWithExactCoordinates()),
        ];
    }

    /**
     * Получить координаты из первого геообъекта
     * 
     * @return array{latitude: float, longitude: float}|null Массив с координатами или null
     */
    public function getFirstGeoCoordinates(): ?array
    {
        $firstGeo = $this->getFirstGeo();
        if (!$firstGeo || !$firstGeo->hasCoordinates()) {
            return null;
        }

        $coordinates = $firstGeo->getCoordinates();
        if (!$coordinates) {
            return null;
        }

        return [
            'latitude' => (float) $coordinates['lat'],
            'longitude' => (float) $coordinates['lng'],
        ];
    }

    /**
     * Получить координаты из всех геообъектов
     * 
     * @return array<int, array{latitude: float, longitude: float}> Массив координат
     */
    public function getAllGeosCoordinates(): array
    {
        $coordinates = [];
        foreach ($this->getResults() as $geo) {
            if ($geo->hasCoordinates()) {
                $coords = $geo->getCoordinates();
                if ($coords) {
                    $coordinates[] = [
                        'latitude' => (float) $coords['lat'],
                        'longitude' => (float) $coords['lng'],
                    ];
                }
            }
        }
        return $coordinates;
    }

    /**
     * Получить все результаты в виде массива массивов
     * 
     * @return array<int, array> Массив результатов, где каждый элемент - массив с данными адреса
     */
    public function toArray(): array
    {
        $results = [];
        foreach ($this->getResults() as $geoObject) {
            $results[] = $geoObject->toArray();
        }
        return $results;
    }
}
