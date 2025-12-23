<?php

namespace App\Support\Geofence\DaData\Enums;

/**
 * Код проверки адреса (qc)
 */
enum AddressQualityCode: int
{
    case CONFIDENT = 0;
    case EXTRA_PARTS = 1;
    case GARBAGE = 2;
    case ALTERNATIVES = 3;

    public function getDescription(): string
    {
        return match ($this) {
            self::CONFIDENT => 'Адрес распознан уверенно',
            self::EXTRA_PARTS => 'Остались лишние части или недостаточно данных',
            self::GARBAGE => 'Адрес пустой или заведомо мусорный',
            self::ALTERNATIVES => 'Есть альтернативные варианты',
        };
    }

    public function requiresManualCheck(): bool
    {
        return match ($this) {
            self::CONFIDENT, self::GARBAGE => false,
            self::EXTRA_PARTS, self::ALTERNATIVES => true,
        };
    }

    public static function fromInt(?int $value): ?self
    {
        return $value !== null ? self::tryFrom($value) : null;
    }
}

