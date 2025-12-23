<?php

namespace App\Support\Geofence\DaData\Enums;

/**
 * Код точности координат (qc_geo)
 */
enum GeoAccuracyCode: int
{
    case EXACT = 0;
    case NEAREST_HOUSE = 1;
    case STREET = 2;
    case SETTLEMENT = 3;
    case CITY = 4;
    case NOT_DETERMINED = 5;

    public function getDescription(): string
    {
        return match ($this) {
            self::EXACT => 'Точные координаты',
            self::NEAREST_HOUSE => 'Ближайший дом',
            self::STREET => 'Улица',
            self::SETTLEMENT => 'Населенный пункт',
            self::CITY => 'Город',
            self::NOT_DETERMINED => 'Координаты не определены',
        };
    }

    public function isExact(): bool
    {
        return $this === self::EXACT;
    }

    public function isSuitableForCourierDelivery(): bool
    {
        return in_array($this, [self::EXACT, self::NEAREST_HOUSE], true);
    }

    public static function fromInt(?int $value): ?self
    {
        return $value !== null ? self::tryFrom($value) : null;
    }
}

