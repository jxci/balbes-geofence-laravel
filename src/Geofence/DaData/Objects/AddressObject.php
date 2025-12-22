<?php

namespace App\Support\Geofence\DaData\Objects;

/**
 * Объект адреса DaData
 */
class AddressObject
{
    protected readonly array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Получить полный адрес
     */
    public function getValue(): string
    {
        return $this->data['value'] ?? '';
    }

    /**
     * Получить необработанные данные
     */
    public function getData(): array
    {
        return $this->data['data'] ?? [];
    }

    /**
     * Получить координаты
     */
    public function getCoordinates(): ?array
    {
        $data = $this->getData();
        if (isset($data['geo_lat']) && isset($data['geo_lon'])) {
            return [
                'lat' => (float) $data['geo_lat'],
                'lng' => (float) $data['geo_lon'],
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
     * Получить точность геокодирования
     */
    public function getAccuracy(): ?string
    {
        $data = $this->getData();
        return $data['accuracy'] ?? null;
    }

    /**
     * Получить уровень детализации
     */
    public function getLevel(): ?string
    {
        $data = $this->getData();
        return $data['level'] ?? null;
    }

    /**
     * Получить почтовый индекс
     */
    public function getPostalCode(): ?string
    {
        $data = $this->getData();
        return $data['postal_code'] ?? null;
    }

    /**
     * Получить страну
     */
    public function getCountry(): ?string
    {
        $data = $this->getData();
        return $data['country'] ?? null;
    }

    /**
     * Получить регион
     */
    public function getRegion(): ?string
    {
        $data = $this->getData();
        return $data['region'] ?? null;
    }

    /**
     * Получить город
     */
    public function getCity(): ?string
    {
        $data = $this->getData();
        return $data['city'] ?? null;
    }

    /**
     * Получить улицу
     */
    public function getStreet(): ?string
    {
        $data = $this->getData();
        return $data['street'] ?? null;
    }

    /**
     * Получить дом
     */
    public function getHouse(): ?string
    {
        $data = $this->getData();
        return $data['house'] ?? null;
    }

    /**
     * Получить квартиру
     */
    public function getFlat(): ?string
    {
        $data = $this->getData();
        return $data['flat'] ?? null;
    }

    /**
     * Проверить, есть ли координаты
     */
    public function hasCoordinates(): bool
    {
        return $this->getCoordinates() !== null;
    }

    /**
     * Получить детальную информацию об адресе
     */
    public function getDetailedInfo(): array
    {
        return [
            'value' => $this->getValue(),
            'coordinates' => $this->getCoordinates(),
            'accuracy' => $this->getAccuracy(),
            'level' => $this->getLevel(),
            'postal_code' => $this->getPostalCode(),
            'country' => $this->getCountry(),
            'region' => $this->getRegion(),
            'city' => $this->getCity(),
            'street' => $this->getStreet(),
            'house' => $this->getHouse(),
            'flat' => $this->getFlat(),
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
        $data = $this->data;
        $result = [];
        $addressData = $data['data'] ?? [];

        # Нормализуем координаты (поддержка geo_lat/geo_lon и latitude/longitude)
        if (isset($addressData['geo_lat']) && isset($addressData['geo_lon'])) {
            $result['latitude'] = (float)$addressData['geo_lat'];
            $result['longitude'] = (float)$addressData['geo_lon'];
            $result['geo_lat'] = $result['latitude'];
            $result['geo_lon'] = $result['longitude'];
        }

        # Добавляем value (полный адрес из data['value'])
        $result['value'] = $data['value'] ?? '';

        # Добавляем стандартные поля адреса из data['data']
        $fields = ['city', 'street', 'house', 'country', 'region', 'district', 'building', 'apartment', 'postal_code', 'flat'];
        foreach ($fields as $field) {
            if (isset($addressData[$field])) {
                $result[$field] = $addressData[$field];
            }
        }

        # Добавляем метаданные (accuracy, level и т.д.)
        if (isset($addressData['accuracy'])) {
            $result['accuracy'] = $addressData['accuracy'];
        }
        if (isset($addressData['level'])) {
            $result['level'] = $addressData['level'];
        }

        # Добавляем все остальные поля из исходных данных
        # Нормализованные поля перезапишут исходные при совпадении ключей
        $result = array_merge($data, $result);

        return $result;
    }
}
