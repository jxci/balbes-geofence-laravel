<?php

namespace App\Support\Geofence\DaData\Enums;

/**
 * Признак актуальности адреса в ФИАС (fias_actuality_state)
 */
enum FiasActualityState: string
{
    case ACTUAL = '0';
    case RENAMED = '1-50';
    case REASSIGNED = '51';
    case DELETED = '99';

    public function getDescription(): string
    {
        return match ($this) {
            self::ACTUAL => 'Актуальный',
            self::RENAMED => 'Переименован',
            self::REASSIGNED => 'Переподчинен',
            self::DELETED => 'Удален',
        };
    }

    public function isActual(): bool
    {
        return $this === self::ACTUAL;
    }

    public function isDeleted(): bool
    {
        return $this === self::DELETED;
    }

    public static function fromString(?string $value): ?self
    {
        if ($value === null || $value === '') {
            return null;
        }

        $intValue = (int) $value;

        if ($intValue === 0) {
            return self::ACTUAL;
        }

        if ($intValue === 51) {
            return self::REASSIGNED;
        }

        if ($intValue === 99) {
            return self::DELETED;
        }

        if ($intValue >= 1 && $intValue <= 50) {
            return self::RENAMED;
        }

        return null;
    }
}

