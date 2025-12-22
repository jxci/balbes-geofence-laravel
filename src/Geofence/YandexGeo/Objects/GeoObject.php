<?php

namespace App\Support\Geofence\YandexGeo\Objects;

/**
 * Объект геолокации YandexGeo
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
        if (isset($this->data['Point']['pos'])) {
            $pos = explode(' ', $this->data['Point']['pos']);
            if (count($pos) === 2) {
                return [
                    'lng' => (float) $pos[0],
                    'lat' => (float) $pos[1],
                ];
            }
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
     * Получить адрес
     */
    public function getAddress(): ?string
    {
        return $this->data['metaDataProperty']['GeocoderMetaData']['text'] ?? null;
    }

    /**
     * Получить точность
     */
    public function getAccuracy(): ?string
    {
        return $this->data['metaDataProperty']['GeocoderMetaData']['precision'] ?? null;
    }

    /**
     * Получить тип объекта
     */
    public function getKind(): ?string
    {
        return $this->data['metaDataProperty']['GeocoderMetaData']['kind'] ?? null;
    }

    /**
     * Получить страну
     */
    public function getCountry(): ?string
    {
        $components = $this->data['metaDataProperty']['GeocoderMetaData']['Address']['Components'] ?? [];
        foreach ($components as $component) {
            if ($component['kind'] === 'country') {
                return $component['name'];
            }
        }
        return null;
    }

    /**
     * Получить регион
     */
    public function getRegion(): ?string
    {
        $components = $this->data['metaDataProperty']['GeocoderMetaData']['Address']['Components'] ?? [];
        foreach ($components as $component) {
            if ($component['kind'] === 'province') {
                return $component['name'];
            }
        }
        return null;
    }

    /**
     * Получить город
     */
    public function getCity(): ?string
    {
        $components = $this->data['metaDataProperty']['GeocoderMetaData']['Address']['Components'] ?? [];
        foreach ($components as $component) {
            if ($component['kind'] === 'locality') {
                return $component['name'];
            }
        }
        return null;
    }

    /**
     * Получить улицу
     */
    public function getStreet(): ?string
    {
        $components = $this->data['metaDataProperty']['GeocoderMetaData']['Address']['Components'] ?? [];
        foreach ($components as $component) {
            if ($component['kind'] === 'street') {
                return $component['name'];
            }
        }
        return null;
    }

    /**
     * Получить дом
     */
    public function getHouse(): ?string
    {
        $components = $this->data['metaDataProperty']['GeocoderMetaData']['Address']['Components'] ?? [];
        foreach ($components as $component) {
            if ($component['kind'] === 'house') {
                return $component['name'];
            }
        }
        return null;
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
            'address' => $this->getAddress(),
            'accuracy' => $this->getAccuracy(),
            'kind' => $this->getKind(),
            'country' => $this->getCountry(),
            'region' => $this->getRegion(),
            'city' => $this->getCity(),
            'street' => $this->getStreet(),
            'house' => $this->getHouse(),
            'has_coordinates' => $this->hasCoordinates(),
        ];
    }

    /**
     * Преобразовать объект в массив в едином формате
     * 
     * @return array Массив с данными адреса в унифицированном формате
     */
    public function toArray(): array
    {
        $result = [];

        # Нормализуем координаты
        $latitude = $this->getLatitude();
        $longitude = $this->getLongitude();
        if ($latitude !== null && $longitude !== null) {
            $result['latitude'] = $latitude;
            $result['longitude'] = $longitude;
            $result['geo_lat'] = $latitude;
            $result['geo_lon'] = $longitude;
        }

        # Добавляем value (полный адрес)
        $result['value'] = $this->getAddress() ?? '';

        # Добавляем стандартные поля адреса
        $result['city'] = $this->getCity();
        $result['street'] = $this->getStreet();
        $result['house'] = $this->getHouse();
        $result['country'] = $this->getCountry();
        $result['region'] = $this->getRegion();
        $result['accuracy'] = $this->getAccuracy();
        $result['kind'] = $this->getKind();

        # Добавляем все остальные поля из исходных данных
        # Нормализованные поля перезапишут исходные при совпадении ключей
        $result = array_merge($this->data, $result);

        return $result;
    }
}
