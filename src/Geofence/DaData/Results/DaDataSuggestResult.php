<?php

namespace App\Support\Geofence\DaData\Results;

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
     * Получить адреса по точности
     */
    public function getAddressesByAccuracy(string $accuracy): array
    {
        return $this->filterAddresses(function (AddressObject $address) use ($accuracy) {
            return $address->getAccuracy() === $accuracy;
        });
    }

    /**
     * Получить адреса по уровню детализации
     */
    public function getAddressesByLevel(string $level): array
    {
        return $this->filterAddresses(function (AddressObject $address) use ($level) {
            return $address->getLevel() === $level;
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
            'method' => 'suggest',
            'count' => $this->getCount(),
            'has_results' => $this->hasResults(),
        ];
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
