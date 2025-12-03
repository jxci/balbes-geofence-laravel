<?php

namespace App\Support\Geofence\Exceptions\DaData;

/**
 * Исключение для ошибок DaData API
 */
class DaDataApiException extends DaDataException
{
    protected readonly ?string $apiErrorCode;
    protected readonly ?string $apiErrorMessage;

    public function __construct(
        string $message,
        string $method,
        array $context = [],
        ?string $apiErrorCode = null,
        ?string $apiErrorMessage = null,
        int $code = 0,
        ?\Exception $previous = null
    ) {
        parent::__construct($message, $method, $context, $code, $previous);
        
        $this->apiErrorCode = $apiErrorCode;
        $this->apiErrorMessage = $apiErrorMessage;
    }

    public function getErrorType(): string
    {
        return 'DaData API Error';
    }

    public function getApiErrorCode(): ?string
    {
        return $this->apiErrorCode;
    }

    public function getApiErrorMessage(): ?string
    {
        return $this->apiErrorMessage;
    }

    public function getRecommendations(): array
    {
        $recommendations = parent::getRecommendations();

        if ($this->apiErrorCode) {
            $recommendations[] = "API Error Code: {$this->apiErrorCode}";
        }

        if ($this->apiErrorMessage) {
            $recommendations[] = "API Error Message: {$this->apiErrorMessage}";
        }

        return $recommendations;
    }
}
