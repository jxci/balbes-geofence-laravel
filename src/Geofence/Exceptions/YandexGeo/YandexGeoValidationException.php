<?php

namespace App\Support\Geofence\Exceptions\YandexGeo;

/**
 * Исключение для ошибок валидации Yandex Geo
 */
class YandexGeoValidationException extends YandexGeoException
{
    protected readonly ?string $invalidField;
    protected readonly ?string $validationRule;
    protected readonly ?string $inputValue;

    public function __construct(
        string $message,
        string $method,
        array $context = [],
        ?string $invalidField = null,
        ?string $validationRule = null,
        ?string $inputValue = null,
        int $code = 0,
        ?\Exception $previous = null
    ) {
        parent::__construct($message, $method, $context, $code, $previous);
        
        $this->invalidField = $invalidField;
        $this->validationRule = $validationRule;
        $this->inputValue = $inputValue;
    }

    public function getErrorType(): string
    {
        return 'Yandex Geo Validation Error';
    }

    public function getInvalidField(): ?string
    {
        return $this->invalidField;
    }

    public function getValidationRule(): ?string
    {
        return $this->validationRule;
    }

    public function getInputValue(): ?string
    {
        return $this->inputValue;
    }

    public function getRecommendations(): array
    {
        $recommendations = [
            'Проверьте корректность входных данных',
            'Убедитесь, что адрес написан правильно',
            'Попробуйте упростить запрос',
            'Проверьте формат координат (широта, долгота)',
        ];

        if ($this->invalidField) {
            $recommendations[] = "Проблемное поле: {$this->invalidField}";
        }

        if ($this->validationRule) {
            $recommendations[] = "Нарушенное правило: {$this->validationRule}";
        }

        if ($this->inputValue) {
            $recommendations[] = "Проблемное значение: {$this->inputValue}";
        }

        return $recommendations;
    }
}
