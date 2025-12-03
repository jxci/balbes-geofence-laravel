<?php

namespace App\Support\Geofence\Exceptions\DaData;

/**
 * Исключение для ошибок квоты DaData
 */
class DaDataQuotaException extends DaDataException
{
    protected readonly ?int $remainingQuota;
    protected readonly ?int $usedQuota;
    protected readonly ?string $quotaResetTime;

    public function __construct(
        string $message,
        string $method,
        array $context = [],
        ?int $remainingQuota = null,
        ?int $usedQuota = null,
        ?string $quotaResetTime = null,
        int $code = 0,
        ?\Exception $previous = null
    ) {
        parent::__construct($message, $method, $context, $code, $previous);
        
        $this->remainingQuota = $remainingQuota;
        $this->usedQuota = $usedQuota;
        $this->quotaResetTime = $quotaResetTime;
    }

    public function getErrorType(): string
    {
        return 'DaData Quota Exceeded';
    }

    public function getRemainingQuota(): ?int
    {
        return $this->remainingQuota;
    }

    public function getUsedQuota(): ?int
    {
        return $this->usedQuota;
    }

    public function getQuotaResetTime(): ?string
    {
        return $this->quotaResetTime;
    }

    public function getRecommendations(): array
    {
        $recommendations = [
            'Превышена квота запросов к DaData API',
            'Попробуйте повторить запрос позже',
            'Рассмотрите возможность увеличения тарифа',
        ];

        if ($this->remainingQuota !== null) {
            $recommendations[] = "Осталось запросов: {$this->remainingQuota}";
        }

        if ($this->usedQuota !== null) {
            $recommendations[] = "Использовано запросов: {$this->usedQuota}";
        }

        if ($this->quotaResetTime) {
            $recommendations[] = "Квота сбросится: {$this->quotaResetTime}";
        }

        return $recommendations;
    }
}
