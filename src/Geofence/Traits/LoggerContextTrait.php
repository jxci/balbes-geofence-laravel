<?php

namespace App\Support\Geofence\Traits;

use App\Support\Geofence\Contracts\LoggerContextInterface;
use Psr\Log\LoggerInterface;

/**
 * Трейт для работы с логгером
 */
trait LoggerContextTrait
{
    protected ?LoggerInterface $logger = null;

    /**
     * Получить логгер
     */
    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Установить логгер
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * Проверить, установлен ли логгер
     */
    public function hasLogger(): bool
    {
        return $this->logger !== null;
    }

    /**
     * Логирование с проверкой наличия логгера
     */
    protected function log(string $level, string $message, array $context = []): void
    {
        if ($this->hasLogger()) {
            $this->logger->log($level, $message, $context);
        }
    }

    /**
     * Логирование информации
     */
    protected function logInfo(string $message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }

    /**
     * Логирование ошибки
     */
    protected function logError(string $message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }

    /**
     * Логирование предупреждения
     */
    protected function logWarning(string $message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }

    /**
     * Логирование отладки
     */
    protected function logDebug(string $message, array $context = []): void
    {
        $this->log('debug', $message, $context);
    }
}
