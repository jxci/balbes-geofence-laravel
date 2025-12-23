<?php

namespace App\Support\Geofence\DaData\Results;

use App\Support\Geofence\DaData\Enums\AddressCompletenessCode;
use App\Support\Geofence\DaData\Enums\AddressQualityCode;
use App\Support\Geofence\DaData\Enums\FiasLevel;
use App\Support\Geofence\DaData\Enums\GeoAccuracyCode;
use App\Support\Geofence\DaData\Objects\AddressObject;
use App\Support\Geofence\Results\SuccessResult;

/**
 * Результат поиска адресов DaData
 */
class DaDataSuggestResult extends SuccessResult
{
    /**
     * Получить все результаты как AddressObject
     */
    public function getResults(): array
    {
        $suggestions = $this->rawData['suggestions'] ?? [];
        $results = [];
        
        foreach ($suggestions as $suggestion) {
            $results[] = new AddressObject($suggestion);
        }
        
        return $results;
    }

    /**
     * Получить первый результат как AddressObject
     */
    public function getFirstAddress(): ?AddressObject
    {
        $results = $this->getResults();
        return $results[0] ?? null;
    }

    /**
     * Получить результат по индексу как AddressObject
     */
    public function getAddress(int $index): ?AddressObject
    {
        $results = $this->getResults();
        return $results[$index] ?? null;
    }

    /**
     * Найти адрес по условию
     */
    public function findAddress(callable $callback): ?AddressObject
    {
        foreach ($this->getResults() as $address) {
            if ($callback($address)) {
                return $address;
            }
        }
        return null;
    }

    /**
     * Фильтровать адреса по условию
     */
    public function filterAddresses(callable $callback): array
    {
        return array_filter($this->getResults(), $callback);
    }

    /**
     * Получить адреса с координатами
     */
    public function getAddressesWithCoordinates(): array
    {
        return $this->filterAddresses(function (AddressObject $address) {
            return $address->hasCoordinates();
        });
    }

    /**
     * Получить адреса по точности (устаревший метод, используйте getAddressesByGeoAccuracy)
     * @deprecated Используйте getAddressesByGeoAccuracy(GeoAccuracyCode)
     */
    public function getAddressesByAccuracy(string $accuracy): array
    {
        return $this->filterAddresses(function (AddressObject $address) use ($accuracy) {
            return $address->getAccuracy() === $accuracy;
        });
    }

    /**
     * Получить адреса по точности координат (ENUM)
     */
    public function getAddressesByGeoAccuracy(GeoAccuracyCode $accuracy): array
    {
        return $this->filterAddresses(function (AddressObject $address) use ($accuracy) {
            return $address->getQcGeoEnum() === $accuracy;
        });
    }

    /**
     * Получить адреса с точными координатами
     */
    public function getAddressesWithExactCoordinates(): array
    {
        return $this->getAddressesByGeoAccuracy(GeoAccuracyCode::EXACT);
    }

    /**
     * Получить адреса пригодные для курьерской доставки
     */
    public function getAddressesSuitableForCourierDelivery(): array
    {
        return $this->filterAddresses(function (AddressObject $address) {
            $geoAccuracy = $address->getQcGeoEnum();
            return $geoAccuracy && $geoAccuracy->isSuitableForCourierDelivery();
        });
    }

    /**
     * Получить адреса по уровню детализации (устаревший метод, используйте getAddressesByFiasLevel)
     * @deprecated Используйте getAddressesByFiasLevel(FiasLevel)
     */
    public function getAddressesByLevel(string $level): array
    {
        return $this->filterAddresses(function (AddressObject $address) use ($level) {
            return $address->getLevel() === $level;
        });
    }

    /**
     * Получить адреса по уровню детализации ФИАС (ENUM)
     */
    public function getAddressesByFiasLevel(FiasLevel $level): array
    {
        return $this->filterAddresses(function (AddressObject $address) use ($level) {
            return $address->getFiasLevelEnum() === $level;
        });
    }

    /**
     * Получить адреса по коду качества (ENUM)
     */
    public function getAddressesByQualityCode(AddressQualityCode $qualityCode): array
    {
        return $this->filterAddresses(function (AddressObject $address) use ($qualityCode) {
            return $address->getQcEnum() === $qualityCode;
        });
    }

    /**
     * Получить адреса по коду пригодности к рассылке (ENUM)
     */
    public function getAddressesByCompletenessCode(AddressCompletenessCode $completenessCode): array
    {
        return $this->filterAddresses(function (AddressObject $address) use ($completenessCode) {
            return $address->getQcCompleteEnum() === $completenessCode;
        });
    }

    /**
     * Получить адреса пригодные для почтовой рассылки
     */
    public function getAddressesSuitableForMailDelivery(): array
    {
        return $this->filterAddresses(function (AddressObject $address) {
            $completeness = $address->getQcCompleteEnum();
            return $completeness && $completeness->isSuitableForDelivery();
        });
    }

    /**
     * Получить адреса требующие ручной проверки
     */
    public function getAddressesRequiringManualCheck(): array
    {
        return $this->filterAddresses(function (AddressObject $address) {
            $quality = $address->getQcEnum();
            return $quality && $quality->requiresManualCheck();
        });
    }

    /**
     * Получить лучший адрес (первый с точными координатами или первый в списке)
     */
    public function getBestAddress(): ?AddressObject
    {
        $exactAddresses = $this->getAddressesWithExactCoordinates();
        if (!empty($exactAddresses)) {
            return $exactAddresses[0];
        }

        $suitableAddresses = $this->getAddressesSuitableForCourierDelivery();
        if (!empty($suitableAddresses)) {
            return $suitableAddresses[0];
        }

        return $this->getFirstAddress();
    }

    /**
     * Получить адреса с максимальной точностью координат
     */
    public function getAddressesWithHighestAccuracy(): array
    {
        $exact = $this->getAddressesWithExactCoordinates();
        if (!empty($exact)) {
            return $exact;
        }

        $nearestHouse = $this->getAddressesByGeoAccuracy(GeoAccuracyCode::NEAREST_HOUSE);
        if (!empty($nearestHouse)) {
            return $nearestHouse;
        }

        return $this->getAddressesByGeoAccuracy(GeoAccuracyCode::STREET);
    }

    /**
     * Получить адреса отсортированные по точности координат (от более точных к менее точным)
     */
    public function getAddressesSortedByAccuracy(): array
    {
        $addresses = $this->getResults();
        
        usort($addresses, function (AddressObject $a, AddressObject $b) {
            $aAccuracy = $a->getQcGeoEnum();
            $bAccuracy = $b->getQcGeoEnum();
            
            if ($aAccuracy === null && $bAccuracy === null) return 0;
            if ($aAccuracy === null) return 1;
            if ($bAccuracy === null) return -1;
            
            return $aAccuracy->value <=> $bAccuracy->value;
        });
        
        return $addresses;
    }

    /**
     * Получить адреса отсортированные по расстоянию (для обратного геокодирования)
     * Адреса в ответе DaData уже отсортированы по удалению от координат
     */
    public function getAddressesSortedByDistance(): array
    {
        return $this->getResults();
    }

    /**
     * Получить адреса в указанном городе
     */
    public function getAddressesInCity(string $city): array
    {
        return $this->filterAddresses(function (AddressObject $address) use ($city) {
            $addressCity = $address->getCity();
            return $addressCity && mb_strtolower($addressCity) === mb_strtolower($city);
        });
    }

    /**
     * Получить адреса в указанном регионе
     */
    public function getAddressesInRegion(string $region): array
    {
        return $this->filterAddresses(function (AddressObject $address) use ($region) {
            $addressRegion = $address->getRegion();
            return $addressRegion && mb_strtolower($addressRegion) === mb_strtolower($region);
        });
    }

    /**
     * Получить адреса с указанным почтовым индексом
     */
    public function getAddressesByPostalCode(string $postalCode): array
    {
        return $this->filterAddresses(function (AddressObject $address) use ($postalCode) {
            return $address->getPostalCode() === $postalCode;
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
            'method' => $this->getMethod(),
            'count' => $this->getCount(),
            'has_results' => $this->hasResults(),
            'has_coordinates' => !empty($this->getAddressesWithCoordinates()),
            'exact_coordinates_count' => count($this->getAddressesWithExactCoordinates()),
        ];
    }

    /**
     * Получить координаты из первого адреса
     * 
     * @return array{latitude: float, longitude: float}|null Массив с координатами или null
     */
    public function getFirstAddressCoordinates(): ?array
    {
        $firstAddress = $this->getFirstAddress();
        if (!$firstAddress || !$firstAddress->hasCoordinates()) {
            return null;
        }

        $coordinates = $firstAddress->getCoordinates();
        if (!$coordinates) {
            return null;
        }

        return [
            'latitude' => (float) $coordinates['lat'],
            'longitude' => (float) $coordinates['lng'],
        ];
    }

    /**
     * Получить координаты из всех адресов
     * 
     * @return array<int, array{latitude: float, longitude: float}> Массив координат
     */
    public function getAllAddressesCoordinates(): array
    {
        $coordinates = [];
        foreach ($this->getResults() as $address) {
            if ($address->hasCoordinates()) {
                $coords = $address->getCoordinates();
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
        foreach ($this->getResults() as $addressObject) {
            $results[] = $addressObject->toArray();
        }
        return $results;
    }
}
