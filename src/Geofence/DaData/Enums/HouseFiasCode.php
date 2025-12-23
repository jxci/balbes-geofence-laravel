<?php

namespace App\Support\Geofence\DaData\Enums;

/**
 * Признак наличия дома в ФИАС (qc_house)
 */
enum HouseFiasCode: int
{
    case IN_FIAS = 2;
    case NOT_IN_FIAS = 10;

    public function getDescription(): string
    {
        return match ($this) {
            self::IN_FIAS => 'Дом найден в ФИАС',
            self::NOT_IN_FIAS => 'Дом не найден в ФИАС',
        };
    }

    public function isInFias(): bool
    {
        return $this === self::IN_FIAS;
    }

    public function getDeliveryProbability(GeoAccuracyCode $geoAccuracy): string
    {
        if ($this === self::IN_FIAS) {
            return 'high';
        }

        return match ($geoAccuracy) {
            GeoAccuracyCode::EXACT => 'high',
            GeoAccuracyCode::NEAREST_HOUSE => 'medium',
            default => 'low',
        };
    }

    public static function fromInt(?int $value): ?self
    {
        return $value !== null ? self::tryFrom($value) : null;
    }
}

