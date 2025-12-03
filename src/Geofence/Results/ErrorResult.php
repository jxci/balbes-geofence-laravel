<?php

namespace App\Support\Geofence\Results;

use App\Support\Geofence\Enums\GeofenceErrorType;
use App\Support\Geofence\Enums\GeofenceServiceType;

/**
 * Базовый класс для результатов с ошибками
 */
abstract class ErrorResult extends BaseResult
{
    protected readonly string $errorMessage;
    protected readonly ?string $errorCode;
    protected readonly ?array $errorDetails;
    protected readonly ?\Exception $originalException;
    protected readonly GeofenceErrorType $errorType;

    public function __construct(
        array $rawData,
        string $serviceName,
        string $method,
        string $errorMessage,
        GeofenceErrorType $errorType,
        ?string $errorCode = null,
        ?array $errorDetails = null,
        ?\Exception $originalException = null,
        ?string $correlationId = null
    ) {
        parent::__construct($rawData, $serviceName, $method, $correlationId);

        $this->errorMessage = $errorMessage;
        $this->errorType = $errorType;
        $this->errorCode = $errorCode;
        $this->errorDetails = $errorDetails;
        $this->originalException = $originalException;
    }

    public function isSuccess(): bool
    {
        return false;
    }

    public function getCount(): int
    {
        return 0;
    }

    /**
     * Получить сообщение об ошибке
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * Получить код ошибки
     */
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    /**
     * Получить детали ошибки
     */
    public function getErrorDetails(): ?array
    {
        return $this->errorDetails;
    }

    /**
     * Получить оригинальное исключение
     */
    public function getOriginalException(): ?\Exception
    {
        return $this->originalException;
    }

    /**
     * Проверить, есть ли оригинальное исключение
     */
    public function hasOriginalException(): bool
    {
        return $this->originalException !== null;
    }

    /**
     * Получить тип оригинального исключения
     */
    public function getOriginalExceptionType(): ?string
    {
        return $this->originalException ? get_class($this->originalException) : null;
    }

    /**
     * Получить трейс оригинального исключения
     */
    public function getOriginalExceptionTrace(): ?string
    {
        return $this->originalException ? $this->originalException->getTraceAsString() : null;
    }

    /**
     * Получить тип ошибки
     */
    public function getErrorTypeEnum(): GeofenceErrorType
    {
        return $this->errorType;
    }

    /**
     * Получить отображаемое название типа ошибки
     */
    public function getErrorTypeDisplayName(): string
    {
        return $this->errorType->getDisplayName();
    }

    /**
     * Получить класс исключения для данного типа ошибки
     */
    public function getExceptionClass(): string
    {
        return $this->errorType->getExceptionClass();
    }

    /**
     * Получить тип сервиса для ошибки
     */
    public function getServiceType(): ?GeofenceServiceType
    {
        return $this->errorType->getServiceType();
    }

    /**
     * Получить тип ошибки (строка для обратной совместимости)
     */
    public function getErrorType(): string
    {
        return $this->errorType->getDisplayName();
    }

    /**
     * Получить рекомендации по устранению ошибки
     */
    abstract public function getRecommendations(): array;

    public function getMetadata(): array
    {
        return [
            'error' => true,
            'error_message' => $this->errorMessage,
            'error_code' => $this->errorCode,
            'error_type' => $this->getErrorType(),
            'error_details' => $this->errorDetails,
            'original_exception' => $this->hasOriginalException() ? [
                'type' => $this->getOriginalExceptionType(),
                'message' => $this->originalException->getMessage(),
                'code' => $this->originalException->getCode(),
                'file' => $this->originalException->getFile(),
                'line' => $this->originalException->getLine(),
                'trace' => $this->getOriginalExceptionTrace(),
            ] : null,
            'recommendations' => $this->getRecommendations(),
        ];
    }
}
