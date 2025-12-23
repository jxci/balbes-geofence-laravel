<?php

namespace App\Support\Geofence\DaData\Objects\Concerns;

/**
 * Трейт для доступа к данным объекта
 */
trait HasDataAccess
{
    /**
     * Получить необработанные данные
     */
    abstract public function getData(): array;

    /**
     * Получить значение поля по ключу
     * Универсальный метод для доступа к любым полям
     */
    public function getField(string $key, mixed $default = null): mixed
    {
        $data = $this->getData();
        return $data[$key] ?? $default;
    }

    /**
     * Получить все исходные данные объекта
     */
    abstract public function getRawData(): array;

    /**
     * Преобразовать объект в массив
     * Возвращает исходные данные без нормализации
     */
    public function toArray(): array
    {
        return $this->getRawData();
    }
}

