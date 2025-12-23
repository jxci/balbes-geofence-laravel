<?php

namespace App\Support\Geofence\DaData\Enums;

/**
 * Признак центра района или региона (capital_marker)
 */
enum CapitalMarker: string
{
    case NONE = '0';
    case AREA_CENTER = '1';
    case REGION_CENTER = '2';
    case AREA_AND_REGION_CENTER = '3';
    case CENTRAL_AREA = '4';

    public function getDescription(): string
    {
        return match ($this) {
            self::NONE => 'Ничего из перечисленного',
            self::AREA_CENTER => 'Центр района',
            self::REGION_CENTER => 'Центр региона',
            self::AREA_AND_REGION_CENTER => 'Центр района и региона',
            self::CENTRAL_AREA => 'Центральный район региона',
        };
    }

    public function isCenter(): bool
    {
        return $this !== self::NONE;
    }

    public function isRegionCenter(): bool
    {
        return in_array($this, [self::REGION_CENTER, self::AREA_AND_REGION_CENTER], true);
    }

    public static function fromString(?string $value): ?self
    {
        return $value !== null ? self::tryFrom($value) : null;
    }
}

