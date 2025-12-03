<?php

namespace App\Support\Geofence\Results;

/**
 * Базовый класс для всех результатов геосервисов
 */
abstract class BaseResult
{
    protected readonly array $rawData;
    protected readonly string $serviceName;
    protected readonly string $method;
    protected readonly ?string $correlationId;

    public function __construct(
        array $rawData,
        string $serviceName,
        string $method,
        ?string $correlationId = null
    ) {
        $this->rawData = $rawData;
        $this->serviceName = $serviceName;
        $this->method = $method;
        $this->correlationId = $correlationId;
    }

    /**
     * Получить сырые данные ответа
     */
    public function getRawData(): array
    {
        return $this->rawData;
    }
    
    /**
     * Получить данные результата (алиас для getRawData)
     */
    public function getData(): array
    {
        return $this->rawData;
    }

    /**
     * Получить имя сервиса
     */
    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    /**
     * Получить метод, который был вызван
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Получить correlation ID
     */
    public function getCorrelationId(): ?string
    {
        return $this->correlationId;
    }

    /**
     * Проверить, является ли результат успешным
     */
    abstract public function isSuccess(): bool;

    /**
     * Получить количество найденных результатов
     */
    abstract public function getCount(): int;

    /**
     * Проверить, есть ли результаты
     */
    public function hasResults(): bool
    {
        return $this->getCount() > 0;
    }

    /**
     * Получить метаданные результата
     */
    abstract public function getMetadata(): array;

    /**
     * Получить детальную информацию о результате
     */
    public function getDetailedInfo(): array
    {
        return [
            'service' => $this->serviceName,
            'method' => $this->method,
            'correlation_id' => $this->correlationId,
            'success' => $this->isSuccess(),
            'count' => $this->getCount(),
            'has_results' => $this->hasResults(),
            'metadata' => $this->getMetadata(),
            'raw_data' => $this->rawData,
        ];
    }
}
