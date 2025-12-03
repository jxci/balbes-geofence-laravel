<?php

namespace App\Support\Geofence\Contracts;

use Psr\Log\LoggerInterface;

/**
 * Интерфейс для работы с логгером
 */
interface LoggerContextInterface
{
    /**
     * Получить логгер
     */
    public function getLogger(): ?LoggerInterface;

    /**
     * Установить логгер
     */
    public function setLogger(LoggerInterface $logger): void;

    /**
     * Проверить, установлен ли логгер
     */
    public function hasLogger(): bool;
}
