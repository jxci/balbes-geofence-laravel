<?php

namespace App\Support\Geofence\Exceptions;

/**
 * Исключение для таймаутов геосервисов
 */
class GeofenceTimeoutException extends GeofenceException
{
    protected readonly ?float $timeoutSeconds;
    protected readonly ?string $operation;

    public function __construct(
        string $message,
        string $serviceName,
        string $method,
        array $context = [],
        ?float $timeoutSeconds = null,
        ?string $operation = null,
        int $code = 0,
        ?\Exception $previous = null
    ) {
        parent::__construct($message, $serviceName, $method, $context, $code, $previous);
        
        $this->timeoutSeconds = $timeoutSeconds;
        $this->operation = $operation;
    }

    public function getErrorType(): string
    {
        return 'Geofence Timeout Error';
    }

    public function getTimeoutSeconds(): ?float
    {
        return $this->timeoutSeconds;
    }

    public function getOperation(): ?string
    {
        return $this->operation;
    }

    public function getRecommendations(): array
    {
        $recommendations = [
            'Проверьте подключение к интернету',
            'Попробуйте повторить запрос',
            'Рассмотрите возможность увеличения таймаута',
        ];

        if ($this->timeoutSeconds) {
            $recommendations[] = "Таймаут: {$this->timeoutSeconds} секунд";
        }

        if ($this->operation) {
            $recommendations[] = "Операция: {$this->operation}";
        }

        return $recommendations;
    }
}
