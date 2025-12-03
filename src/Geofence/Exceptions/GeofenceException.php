<?php

namespace App\Support\Geofence\Exceptions;

use Exception;

/**
 * Базовое исключение для геосервисов
 */
abstract class GeofenceException extends Exception
{
    protected readonly string $serviceName;
    protected readonly string $method;
    protected readonly array $context;

    public function __construct(
        string $message,
        string $serviceName,
        string $method,
        array $context = [],
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        
        $this->serviceName = $serviceName;
        $this->method = $method;
        $this->context = $context;
    }

    /**
     * Получить имя сервиса
     */
    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    /**
     * Получить метод, в котором произошла ошибка
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Получить контекст ошибки
     */
    public function getContext(): array
    {
        return $this->context;
    }


    /**
     * Получить детальную информацию об ошибке
     */
    public function getDetailedMessage(): string
    {
        return sprintf(
            "[%s@%s] %s | Context: %s",
            $this->serviceName,
            $this->method,
            $this->getMessage(),
            json_encode($this->context, JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * Получить тип ошибки
     */
    abstract public function getErrorType(): string;

    /**
     * Получить рекомендации по исправлению
     */
    abstract public function getRecommendations(): array;
}
