<?php

namespace App\Support\Geofence\Results;

/**
 * Базовый класс для успешных результатов
 */
abstract class SuccessResult extends BaseResult
{
    public function isSuccess(): bool
    {
        return true;
    }

    /**
     * Получить все результаты
     */
    abstract public function getResults(): array;

    /**
     * Получить все результаты в виде массива массивов
     * 
     * @return array<int, array> Массив результатов, где каждый элемент - массив с данными адреса
     */
    abstract public function toArray(): array;

    /**
     * Получить первый результат в виде массива
     * 
     * @return array|null Массив с данными первого результата или null
     */
    public function getFirstResult(): ?array
    {
        $results = $this->toArray();
        return $results[0] ?? null;
    }

    /**
     * Получить последний результат
     */
    public function getLastResult(): ?array
    {
        $results = $this->toArray();
        return end($results) ?: null;
    }

    /**
     * Получить результат по индексу
     */
    public function getResult(int $index): ?array
    {
        $results = $this->toArray();
        return $results[$index] ?? null;
    }

    /**
     * Фильтровать результаты по условию
     */
    public function filterResults(callable $callback): array
    {
        return array_filter($this->toArray(), $callback);
    }

    /**
     * Найти результат по условию
     */
    public function findResult(callable $callback): ?array
    {
        foreach ($this->toArray() as $result) {
            if ($callback($result)) {
                return $result;
            }
        }
        return null;
    }

    /**
     * Получить нормализованные данные результата
     * 
     * Унифицирует формат данных между разными провайдерами
     * 
     * @return array<int, array> Массив результатов в едином формате
     */
    public function getNormalizedData(): array
    {
        return $this->toArray();
    }
}
