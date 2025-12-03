<?php

namespace App\Support\Geofence\DaData\Objects;

/**
 * Объект геолокации DaData
 */
class GeoObject
{
    protected readonly array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
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
        $coordinates = $this->getCoordinates();
        return $coordinates['lat'] ?? null;
    }

    /**
     * Получить долготу
     */
    public function getLongitude(): ?float
    {
        $coordinates = $this->getCoordinates();
        return $coordinates['lng'] ?? null;
    }

    /**
     * Получить точность
     */
    public function getAccuracy(): ?string
    {
        return $this->data['accuracy'] ?? null;
    }

    /**
     * Получить уровень детализации
     */
    public function getLevel(): ?string
    {
        return $this->data['level'] ?? null;
    }

    /**
     * Получить адрес
     */
    public function getAddress(): ?string
    {
        return $this->data['address'] ?? null;
    }

    /**
     * Получить необработанные данные
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Проверить, есть ли координаты
     */
    public function hasCoordinates(): bool
    {
        return $this->getCoordinates() !== null;
    }

    /**
     * Получить детальную информацию
     */
    public function getDetailedInfo(): array
    {
        return [
            'coordinates' => $this->getCoordinates(),
            'accuracy' => $this->getAccuracy(),
            'level' => $this->getLevel(),
            'address' => $this->getAddress(),
            'has_coordinates' => $this->hasCoordinates(),
        ];
    }
}
