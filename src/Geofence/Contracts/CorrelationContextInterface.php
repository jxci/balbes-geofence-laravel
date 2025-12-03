<?php

namespace App\Support\Geofence\Contracts;

/**
 * Интерфейс для работы с correlation ID
 */
interface CorrelationContextInterface
{
    /**
     * Получить текущий correlation ID
     */
    public function getCorrelationId(): ?string;

    /**
     * Установить correlation ID
     */
    public function setCorrelationId(string $correlationId): void;

    /**
     * Проверить, установлен ли correlation ID
     */
    public function hasCorrelationId(): bool;

    /**
     * Сгенерировать новый correlation ID
     */
    public function generateCorrelationId(): string;
}
