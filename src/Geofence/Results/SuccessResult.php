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
     * Получить первый результат
     */
    public function getFirstResult(): ?array
    {
        $results = $this->getResults();
        return $results[0] ?? null;
    }

    /**
     * Получить последний результат
     */
    public function getLastResult(): ?array
    {
        $results = $this->getResults();
        return end($results) ?: null;
    }

    /**
     * Получить результат по индексу
     */
    public function getResult(int $index): ?array
    {
        $results = $this->getResults();
        return $results[$index] ?? null;
    }

    /**
     * Фильтровать результаты по условию
     */
    public function filterResults(callable $callback): array
    {
        return array_filter($this->getResults(), $callback);
    }

    /**
     * Найти результат по условию
     */
    public function findResult(callable $callback): ?array
    {
        foreach ($this->getResults() as $result) {
            if ($callback($result)) {
                return $result;
            }
        }
        return null;
    }
}
