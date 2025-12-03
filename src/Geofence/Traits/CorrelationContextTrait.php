<?php

namespace App\Support\Geofence\Traits;

use App\Support\Geofence\Contracts\CorrelationContextInterface;
use Illuminate\Support\Str;

/**
 * Трейт для работы с correlation ID
 */
trait CorrelationContextTrait
{
    protected ?string $correlationId = null;

    /**
     * Получить текущий correlation ID
     */
    public function getCorrelationId(): ?string
    {
        return $this->correlationId;
    }

    /**
     * Установить correlation ID
     */
    public function setCorrelationId(string $correlationId): void
    {
        $this->correlationId = $correlationId;
    }

    /**
     * Проверить, установлен ли correlation ID
     */
    public function hasCorrelationId(): bool
    {
        return $this->correlationId !== null;
    }

    /**
     * Сгенерировать новый correlation ID
     */
    public function generateCorrelationId(): string
    {
        $this->correlationId = (string) Str::uuid();
        return $this->correlationId;
    }

    /**
     * Обеспечить наличие correlation ID (сгенерировать если нет)
     */
    public function ensureCorrelationId(): string
    {
        if (!$this->hasCorrelationId()) {
            $this->generateCorrelationId();
        }
        return $this->correlationId;
    }
}
