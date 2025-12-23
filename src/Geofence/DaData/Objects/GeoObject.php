<?php

namespace App\Support\Geofence\DaData\Objects;

use App\Support\Geofence\DaData\Enums\FiasLevel;
use App\Support\Geofence\DaData\Enums\GeoAccuracyCode;
use App\Support\Geofence\DaData\Objects\Concerns\HasDataAccess;

/**
 * Объект геолокации DaData
 * 
 * Используется для результатов геокодирования (поиск координат по адресу)
 * Содержит те же данные, что и AddressObject, но в контексте геокодирования
 */
class GeoObject
{
    use HasDataAccess;

    protected readonly array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Получить необработанные данные
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Получить все исходные данные объекта
     */
    public function getRawData(): array
    {
        return $this->data;
    }

    /**
     * Получить координаты
     */
    public function getCoordinates(): ?array
    {
        if (isset($this->data['geo_lat']) && isset($this->data['geo_lon'])) {
            return [
                'lat' => (float) $this->data['geo_lat'],
                'lng' => (float) $this->data['geo_lon'],
            ];
        }
        return null;
    }

    /**
     * Получить широту
     */
    public function getLatitude(): ?float
    {
        return isset($this->data['geo_lat']) ? (float) $this->data['geo_lat'] : null;
    }

    /**
     * Получить долготу
     */
    public function getLongitude(): ?float
    {
        return isset($this->data['geo_lon']) ? (float) $this->data['geo_lon'] : null;
    }

    /**
     * Проверить, есть ли координаты
     */
    public function hasCoordinates(): bool
    {
        return $this->getCoordinates() !== null;
    }

    /**
     * Получить полный адрес (value)
     */
    public function getValue(): ?string
    {
        return $this->data['value'] ?? null;
    }

    /**
     * Получить адрес (address)
     */
    public function getAddress(): ?string
    {
        return $this->data['address'] ?? $this->getValue();
    }

    /**
     * Получить город
     */
    public function getCity(): ?string
    {
        return $this->data['city'] ?? null;
    }

    /**
     * Получить улицу
     */
    public function getStreet(): ?string
    {
        return $this->data['street'] ?? null;
    }

    /**
     * Получить дом
     */
    public function getHouse(): ?string
    {
        return $this->data['house'] ?? null;
    }

    /**
     * Получить страну
     */
    public function getCountry(): ?string
    {
        return $this->data['country'] ?? null;
    }

    /**
     * Получить регион
     */
    public function getRegion(): ?string
    {
        return $this->data['region'] ?? null;
    }

    /**
     * Получить почтовый индекс
     */
    public function getPostalCode(): ?string
    {
        return $this->data['postal_code'] ?? null;
    }

    /**
     * Получить код точности координат (qc_geo)
     */
    public function getQcGeo(): ?int
    {
        return isset($this->data['qc_geo']) ? (int) $this->data['qc_geo'] : null;
    }

    /**
     * Получить код точности координат как ENUM
     */
    public function getQcGeoEnum(): ?GeoAccuracyCode
    {
        return GeoAccuracyCode::fromInt($this->getQcGeo());
    }

    /**
     * Получить уровень детализации (fias_level)
     */
    public function getFiasLevel(): ?string
    {
        return $this->data['fias_level'] ?? null;
    }

    /**
     * Получить уровень детализации ФИАС как ENUM
     */
    public function getFiasLevelEnum(): ?FiasLevel
    {
        return FiasLevel::fromString($this->getFiasLevel());
    }

    /**
     * Получить точность (устаревший метод, используйте getQcGeo)
     * @deprecated Используйте getQcGeo()
     */
    public function getAccuracy(): ?string
    {
        $qcGeo = $this->getQcGeo();
        if ($qcGeo === null) {
            return null;
        }
        return match($qcGeo) {
            0 => 'exact',
            1 => 'house',
            2 => 'street',
            3 => 'settlement',
            4 => 'city',
            5 => 'none',
            default => null,
        };
    }

    /**
     * Получить уровень детализации (устаревший метод, используйте getFiasLevel)
     * @deprecated Используйте getFiasLevel()
     */
    public function getLevel(): ?string
    {
        return $this->getFiasLevel();
    }


    /**
     * Получить детальную информацию
     * Возвращает структурированный массив с основными полями
     */
    public function getDetailedInfo(): array
    {
        return [
            'address' => $this->getAddress(),
            'value' => $this->getValue(),
            'coordinates' => $this->getCoordinates(),
            'city' => $this->getCity(),
            'street' => $this->getStreet(),
            'house' => $this->getHouse(),
            'country' => $this->getCountry(),
            'region' => $this->getRegion(),
            'postal_code' => $this->getPostalCode(),
            'has_coordinates' => $this->hasCoordinates(),
            'qc_geo' => $this->getQcGeo(),
            'fias_level' => $this->getFiasLevel(),
        ];
    }
}
