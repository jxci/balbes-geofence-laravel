<?php

namespace App\Support\Geofence\DaData\Objects;

use App\Support\Geofence\DaData\Enums\AddressCompletenessCode;
use App\Support\Geofence\DaData\Enums\AddressQualityCode;
use App\Support\Geofence\DaData\Enums\BeltwayHit;
use App\Support\Geofence\DaData\Enums\CapitalMarker;
use App\Support\Geofence\DaData\Enums\FiasActualityState;
use App\Support\Geofence\DaData\Enums\FiasLevel;
use App\Support\Geofence\DaData\Enums\GeoAccuracyCode;
use App\Support\Geofence\DaData\Enums\HouseFiasCode;
use App\Support\Geofence\DaData\Objects\Concerns\HasDataAccess;

/**
 * Объект адреса DaData
 * 
 * Предоставляет типизированный доступ ко всем полям ответа DaData API
 * для стандартизации адресов (clean/address, suggest/address)
 */
class AddressObject
{
    use HasDataAccess;

    protected readonly array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Получить исходный адрес (source)
     */
    public function getSource(): ?string
    {
        $data = $this->getData();
        return $data['source'] ?? null;
    }

    /**
     * Получить стандартизованный адрес (result)
     */
    public function getResult(): ?string
    {
        $data = $this->getData();
        return $data['result'] ?? null;
    }

    /**
     * Получить полный адрес (value)
     */
    public function getValue(): string
    {
        return $this->data['value'] ?? '';
    }

    /**
     * Получить полный адрес без ограничений (unrestricted_value)
     */
    public function getUnrestrictedValue(): string
    {
        return $this->data['unrestricted_value'] ?? '';
    }

    /**
     * Получить необработанные данные из поля data
     */
    public function getData(): array
    {
        return $this->data['data'] ?? [];
    }

    /**
     * Получить все исходные данные (включая value, unrestricted_value, data)
     */
    public function getRawData(): array
    {
        return $this->data;
    }

    /**
     * Получить значение поля из data по ключу
     * Универсальный метод для доступа к любым полям
     * Алиас для getField() для обратной совместимости
     */
    public function getDataField(string $key, mixed $default = null): mixed
    {
        return $this->getField($key, $default);
    }


    /**
     * Получить координаты
     */
    public function getCoordinates(): ?array
    {
        $data = $this->getData();
        if (isset($data['geo_lat']) && isset($data['geo_lon'])) {
            return [
                'lat' => (float) $data['geo_lat'],
                'lng' => (float) $data['geo_lon'],
            ];
        }
        return null;
    }

    /**
     * Получить широту
     */
    public function getLatitude(): ?float
    {
        $data = $this->getData();
        return isset($data['geo_lat']) ? (float) $data['geo_lat'] : null;
    }

    /**
     * Получить долготу
     */
    public function getLongitude(): ?float
    {
        $data = $this->getData();
        return isset($data['geo_lon']) ? (float) $data['geo_lon'] : null;
    }

    /**
     * Проверить, есть ли координаты
     */
    public function hasCoordinates(): bool
    {
        return $this->getCoordinates() !== null;
    }


    /**
     * Получить почтовый индекс
     */
    public function getPostalCode(): ?string
    {
        $data = $this->getData();
        return $data['postal_code'] ?? null;
    }

    /**
     * Получить страну
     */
    public function getCountry(): ?string
    {
        $data = $this->getData();
        return $data['country'] ?? null;
    }

    /**
     * Получить ISO-код страны
     */
    public function getCountryIsoCode(): ?string
    {
        $data = $this->getData();
        return $data['country_iso_code'] ?? null;
    }

    /**
     * Получить федеральный округ
     */
    public function getFederalDistrict(): ?string
    {
        $data = $this->getData();
        return $data['federal_district'] ?? null;
    }


    /**
     * Получить регион
     */
    public function getRegion(): ?string
    {
        $data = $this->getData();
        return $data['region'] ?? null;
    }

    /**
     * Получить регион с типом (например, "г Москва")
     */
    public function getRegionWithType(): ?string
    {
        $data = $this->getData();
        return $data['region_with_type'] ?? null;
    }

    /**
     * Получить тип региона (сокращенный)
     */
    public function getRegionType(): ?string
    {
        $data = $this->getData();
        return $data['region_type'] ?? null;
    }

    /**
     * Получить тип региона (полный)
     */
    public function getRegionTypeFull(): ?string
    {
        $data = $this->getData();
        return $data['region_type_full'] ?? null;
    }

    /**
     * Получить ФИАС-код региона
     */
    public function getRegionFiasId(): ?string
    {
        $data = $this->getData();
        return $data['region_fias_id'] ?? null;
    }

    /**
     * Получить КЛАДР-код региона
     */
    public function getRegionKladrId(): ?string
    {
        $data = $this->getData();
        return $data['region_kladr_id'] ?? null;
    }

    /**
     * Получить ISO-код региона
     */
    public function getRegionIsoCode(): ?string
    {
        $data = $this->getData();
        return $data['region_iso_code'] ?? null;
    }


    /**
     * Получить район в регионе
     */
    public function getArea(): ?string
    {
        $data = $this->getData();
        return $data['area'] ?? null;
    }

    /**
     * Получить район в регионе с типом
     */
    public function getAreaWithType(): ?string
    {
        $data = $this->getData();
        return $data['area_with_type'] ?? null;
    }

    /**
     * Получить тип района в регионе (сокращенный)
     */
    public function getAreaType(): ?string
    {
        $data = $this->getData();
        return $data['area_type'] ?? null;
    }

    /**
     * Получить тип района в регионе (полный)
     */
    public function getAreaTypeFull(): ?string
    {
        $data = $this->getData();
        return $data['area_type_full'] ?? null;
    }

    /**
     * Получить ФИАС-код района
     */
    public function getAreaFiasId(): ?string
    {
        $data = $this->getData();
        return $data['area_fias_id'] ?? null;
    }

    /**
     * Получить КЛАДР-код района
     */
    public function getAreaKladrId(): ?string
    {
        $data = $this->getData();
        return $data['area_kladr_id'] ?? null;
    }


    /**
     * Получить город
     */
    public function getCity(): ?string
    {
        $data = $this->getData();
        return $data['city'] ?? null;
    }

    /**
     * Получить город с типом
     */
    public function getCityWithType(): ?string
    {
        $data = $this->getData();
        return $data['city_with_type'] ?? null;
    }

    /**
     * Получить тип города (сокращенный)
     */
    public function getCityType(): ?string
    {
        $data = $this->getData();
        return $data['city_type'] ?? null;
    }

    /**
     * Получить тип города (полный)
     */
    public function getCityTypeFull(): ?string
    {
        $data = $this->getData();
        return $data['city_type_full'] ?? null;
    }

    /**
     * Получить ФИАС-код города
     */
    public function getCityFiasId(): ?string
    {
        $data = $this->getData();
        return $data['city_fias_id'] ?? null;
    }

    /**
     * Получить КЛАДР-код города
     */
    public function getCityKladrId(): ?string
    {
        $data = $this->getData();
        return $data['city_kladr_id'] ?? null;
    }

    /**
     * Получить административный округ (только для Москвы)
     */
    public function getCityArea(): ?string
    {
        $data = $this->getData();
        return $data['city_area'] ?? null;
    }


    /**
     * Получить район города
     */
    public function getCityDistrict(): ?string
    {
        $data = $this->getData();
        return $data['city_district'] ?? null;
    }

    /**
     * Получить район города с типом
     */
    public function getCityDistrictWithType(): ?string
    {
        $data = $this->getData();
        return $data['city_district_with_type'] ?? null;
    }

    /**
     * Получить тип района города (сокращенный)
     */
    public function getCityDistrictType(): ?string
    {
        $data = $this->getData();
        return $data['city_district_type'] ?? null;
    }

    /**
     * Получить тип района города (полный)
     */
    public function getCityDistrictTypeFull(): ?string
    {
        $data = $this->getData();
        return $data['city_district_type_full'] ?? null;
    }

    /**
     * Получить ФИАС-код района города
     */
    public function getCityDistrictFiasId(): ?string
    {
        $data = $this->getData();
        return $data['city_district_fias_id'] ?? null;
    }

    /**
     * Получить КЛАДР-код района города
     */
    public function getCityDistrictKladrId(): ?string
    {
        $data = $this->getData();
        return $data['city_district_kladr_id'] ?? null;
    }


    /**
     * Получить населенный пункт
     */
    public function getSettlement(): ?string
    {
        $data = $this->getData();
        return $data['settlement'] ?? null;
    }

    /**
     * Получить населенный пункт с типом (settlement_with_type)
     */
    public function getSettlementWithType(): ?string
    {
        $data = $this->getData();
        return $data['settlement_with_type'] ?? null;
    }

    /**
     * Получить тип населенного пункта (сокращенный)
     */
    public function getSettlementType(): ?string
    {
        $data = $this->getData();
        return $data['settlement_type'] ?? null;
    }

    /**
     * Получить тип населенного пункта (полный)
     */
    public function getSettlementTypeFull(): ?string
    {
        $data = $this->getData();
        return $data['settlement_type_full'] ?? null;
    }

    /**
     * Получить ФИАС-код населенного пункта
     */
    public function getSettlementFiasId(): ?string
    {
        $data = $this->getData();
        return $data['settlement_fias_id'] ?? null;
    }

    /**
     * Получить КЛАДР-код населенного пункта
     */
    public function getSettlementKladrId(): ?string
    {
        $data = $this->getData();
        return $data['settlement_kladr_id'] ?? null;
    }


    /**
     * Получить улицу
     */
    public function getStreet(): ?string
    {
        $data = $this->getData();
        return $data['street'] ?? null;
    }

    /**
     * Получить улицу с типом (например, "ул Сухонская")
     */
    public function getStreetWithType(): ?string
    {
        $data = $this->getData();
        return $data['street_with_type'] ?? null;
    }

    /**
     * Получить тип улицы (сокращенный)
     */
    public function getStreetType(): ?string
    {
        $data = $this->getData();
        return $data['street_type'] ?? null;
    }

    /**
     * Получить тип улицы (полный)
     */
    public function getStreetTypeFull(): ?string
    {
        $data = $this->getData();
        return $data['street_type_full'] ?? null;
    }

    /**
     * Получить ФИАС-код улицы
     */
    public function getStreetFiasId(): ?string
    {
        $data = $this->getData();
        return $data['street_fias_id'] ?? null;
    }

    /**
     * Получить КЛАДР-код улицы
     */
    public function getStreetKladrId(): ?string
    {
        $data = $this->getData();
        return $data['street_kladr_id'] ?? null;
    }


    /**
     * Получить номер земельного участка
     */
    public function getStead(): ?string
    {
        $data = $this->getData();
        return $data['stead'] ?? null;
    }

    /**
     * Получить тип земельного участка
     */
    public function getSteadType(): ?string
    {
        $data = $this->getData();
        return $data['stead_type'] ?? null;
    }

    /**
     * Получить тип земельного участка (полный)
     */
    public function getSteadTypeFull(): ?string
    {
        $data = $this->getData();
        return $data['stead_type_full'] ?? null;
    }

    /**
     * Получить ФИАС-код земельного участка
     */
    public function getSteadFiasId(): ?string
    {
        $data = $this->getData();
        return $data['stead_fias_id'] ?? null;
    }

    /**
     * Получить КЛАДР-код земельного участка
     */
    public function getSteadKladrId(): ?string
    {
        $data = $this->getData();
        return $data['stead_kladr_id'] ?? null;
    }

    /**
     * Получить кадастровый номер земельного участка
     */
    public function getSteadCadnum(): ?string
    {
        $data = $this->getData();
        return $data['stead_cadnum'] ?? null;
    }


    /**
     * Получить дом
     */
    public function getHouse(): ?string
    {
        $data = $this->getData();
        return $data['house'] ?? null;
    }

    /**
     * Получить тип дома (например, "д", "стр", "корп")
     */
    public function getHouseType(): ?string
    {
        $data = $this->getData();
        return $data['house_type'] ?? null;
    }

    /**
     * Получить тип дома (полный)
     */
    public function getHouseTypeFull(): ?string
    {
        $data = $this->getData();
        return $data['house_type_full'] ?? null;
    }

    /**
     * Получить ФИАС-код дома
     */
    public function getHouseFiasId(): ?string
    {
        $data = $this->getData();
        return $data['house_fias_id'] ?? null;
    }

    /**
     * Получить КЛАДР-код дома
     */
    public function getHouseKladrId(): ?string
    {
        $data = $this->getData();
        return $data['house_kladr_id'] ?? null;
    }

    /**
     * Получить кадастровый номер дома
     */
    public function getHouseCadnum(): ?string
    {
        $data = $this->getData();
        return $data['house_cadnum'] ?? null;
    }

    /**
     * Получить количество квартир в доме
     */
    public function getHouseFlatCount(): ?string
    {
        $data = $this->getData();
        return $data['house_flat_count'] ?? null;
    }


    /**
     * Получить корпус/строение (block)
     */
    public function getBlock(): ?string
    {
        $data = $this->getData();
        return $data['block'] ?? null;
    }

    /**
     * Получить тип корпуса/строения (сокращенный)
     */
    public function getBlockType(): ?string
    {
        $data = $this->getData();
        return $data['block_type'] ?? null;
    }

    /**
     * Получить тип корпуса/строения (полный)
     */
    public function getBlockTypeFull(): ?string
    {
        $data = $this->getData();
        return $data['block_type_full'] ?? null;
    }

    /**
     * Проверить, есть ли блок/корпус
     */
    public function hasBlock(): bool
    {
        return $this->getBlock() !== null;
    }

    /**
     * Получить полный адрес дома (дом + блок, если есть)
     */
    public function getFullHouse(): string
    {
        $house = $this->getHouse() ?? '';
        $block = $this->getBlock();
        $blockType = $this->getBlockType();
        
        if ($block) {
            $blockParts = array_filter([$blockType, $block]);
            $house .= ' ' . implode(' ', $blockParts);
        }
        
        return trim($house);
    }


    /**
     * Получить квартиру
     */
    public function getFlat(): ?string
    {
        $data = $this->getData();
        return $data['flat'] ?? null;
    }

    /**
     * Получить тип квартиры (сокращенный)
     */
    public function getFlatType(): ?string
    {
        $data = $this->getData();
        return $data['flat_type'] ?? null;
    }

    /**
     * Получить тип квартиры (полный)
     */
    public function getFlatTypeFull(): ?string
    {
        $data = $this->getData();
        return $data['flat_type_full'] ?? null;
    }

    /**
     * Получить ФИАС-код квартиры
     */
    public function getFlatFiasId(): ?string
    {
        $data = $this->getData();
        return $data['flat_fias_id'] ?? null;
    }

    /**
     * Получить кадастровый номер квартиры
     */
    public function getFlatCadnum(): ?string
    {
        $data = $this->getData();
        return $data['flat_cadnum'] ?? null;
    }

    /**
     * Получить площадь квартиры
     */
    public function getFlatArea(): ?string
    {
        $data = $this->getData();
        return $data['flat_area'] ?? null;
    }

    /**
     * Получить рыночную стоимость м²
     */
    public function getSquareMeterPrice(): ?string
    {
        $data = $this->getData();
        return $data['square_meter_price'] ?? null;
    }

    /**
     * Получить рыночную стоимость квартиры
     */
    public function getFlatPrice(): ?string
    {
        $data = $this->getData();
        return $data['flat_price'] ?? null;
    }

    /**
     * Проверить, есть ли квартира
     */
    public function hasFlat(): bool
    {
        return $this->getFlat() !== null;
    }


    /**
     * Получить подъезд
     */
    public function getEntrance(): ?string
    {
        $data = $this->getData();
        return $data['entrance'] ?? null;
    }

    /**
     * Получить этаж
     */
    public function getFloor(): ?string
    {
        $data = $this->getData();
        return $data['floor'] ?? null;
    }

    /**
     * Получить абонентский ящик
     */
    public function getPostalBox(): ?string
    {
        $data = $this->getData();
        return $data['postal_box'] ?? null;
    }

    /**
     * Получить комнату
     */
    public function getRoom(): ?string
    {
        $data = $this->getData();
        return $data['room'] ?? null;
    }

    /**
     * Получить тип комнаты (сокращенный)
     */
    public function getRoomType(): ?string
    {
        $data = $this->getData();
        return $data['room_type'] ?? null;
    }

    /**
     * Получить тип комнаты (полный)
     */
    public function getRoomTypeFull(): ?string
    {
        $data = $this->getData();
        return $data['room_type_full'] ?? null;
    }


    /**
     * Получить ФИАС-код адреса
     */
    public function getFiasId(): ?string
    {
        $data = $this->getData();
        return $data['fias_id'] ?? null;
    }

    /**
     * Получить ФИАС-код (алиас для обратной совместимости)
     */
    public function getFiasCode(): ?string
    {
        $data = $this->getData();
        return $data['fias_code'] ?? null;
    }

    /**
     * Получить уровень детализации ФИАС
     */
    public function getFiasLevel(): ?string
    {
        $data = $this->getData();
        return $data['fias_level'] ?? null;
    }

    /**
     * Получить уровень детализации ФИАС как ENUM
     */
    public function getFiasLevelEnum(): ?FiasLevel
    {
        return FiasLevel::fromString($this->getFiasLevel());
    }

    /**
     * Получить признак актуальности адреса в ФИАС
     */
    public function getFiasActualityState(): ?string
    {
        $data = $this->getData();
        return $data['fias_actuality_state'] ?? null;
    }

    /**
     * Получить признак актуальности адреса в ФИАС как ENUM
     */
    public function getFiasActualityStateEnum(): ?FiasActualityState
    {
        return FiasActualityState::fromString($this->getFiasActualityState());
    }

    /**
     * Получить КЛАДР-код адреса
     */
    public function getKladrId(): ?string
    {
        $data = $this->getData();
        return $data['kladr_id'] ?? null;
    }

    /**
     * Получить идентификатор GeoNames
     */
    public function getGeonameId(): ?string
    {
        $data = $this->getData();
        return $data['geoname_id'] ?? null;
    }

    /**
     * Получить признак центра района или региона
     */
    public function getCapitalMarker(): ?string
    {
        $data = $this->getData();
        return $data['capital_marker'] ?? null;
    }

    /**
     * Получить признак центра района или региона как ENUM
     */
    public function getCapitalMarkerEnum(): ?CapitalMarker
    {
        return CapitalMarker::fromString($this->getCapitalMarker());
    }


    /**
     * Получить код ОКАТО
     */
    public function getOkato(): ?string
    {
        $data = $this->getData();
        return $data['okato'] ?? null;
    }

    /**
     * Получить код ОКТМО
     */
    public function getOktmo(): ?string
    {
        $data = $this->getData();
        return $data['oktmo'] ?? null;
    }

    /**
     * Получить код ИФНС для физических лиц
     */
    public function getTaxOffice(): ?string
    {
        $data = $this->getData();
        return $data['tax_office'] ?? null;
    }

    /**
     * Получить код ИФНС для организаций
     */
    public function getTaxOfficeLegal(): ?string
    {
        $data = $this->getData();
        return $data['tax_office_legal'] ?? null;
    }

    /**
     * Получить часовой пояс
     */
    public function getTimezone(): ?string
    {
        $data = $this->getData();
        return $data['timezone'] ?? null;
    }


    /**
     * Получить признак нахождения внутри кольцевой дороги
     */
    public function getBeltwayHit(): ?string
    {
        $data = $this->getData();
        return $data['beltway_hit'] ?? null;
    }

    /**
     * Получить признак нахождения внутри кольцевой дороги как ENUM
     */
    public function getBeltwayHitEnum(): ?BeltwayHit
    {
        return BeltwayHit::fromString($this->getBeltwayHit());
    }

    /**
     * Получить расстояние от кольцевой дороги в км
     */
    public function getBeltwayDistance(): ?string
    {
        $data = $this->getData();
        return $data['beltway_distance'] ?? null;
    }


    /**
     * Получить код проверки адреса (qc)
     */
    public function getQc(): ?int
    {
        $data = $this->getData();
        return isset($data['qc']) ? (int) $data['qc'] : null;
    }

    /**
     * Получить код проверки адреса как ENUM
     */
    public function getQcEnum(): ?AddressQualityCode
    {
        return AddressQualityCode::fromInt($this->getQc());
    }

    /**
     * Получить код пригодности к рассылке (qc_complete)
     */
    public function getQcComplete(): ?int
    {
        $data = $this->getData();
        return isset($data['qc_complete']) ? (int) $data['qc_complete'] : null;
    }

    /**
     * Получить код пригодности к рассылке как ENUM
     */
    public function getQcCompleteEnum(): ?AddressCompletenessCode
    {
        return AddressCompletenessCode::fromInt($this->getQcComplete());
    }

    /**
     * Получить признак наличия дома в ФИАС (qc_house)
     */
    public function getQcHouse(): ?int
    {
        $data = $this->getData();
        return isset($data['qc_house']) ? (int) $data['qc_house'] : null;
    }

    /**
     * Получить признак наличия дома в ФИАС как ENUM
     */
    public function getQcHouseEnum(): ?HouseFiasCode
    {
        return HouseFiasCode::fromInt($this->getQcHouse());
    }

    /**
     * Получить код точности координат (qc_geo)
     */
    public function getQcGeo(): ?int
    {
        $data = $this->getData();
        return isset($data['qc_geo']) ? (int) $data['qc_geo'] : null;
    }

    /**
     * Получить код точности координат как ENUM
     */
    public function getQcGeoEnum(): ?GeoAccuracyCode
    {
        return GeoAccuracyCode::fromInt($this->getQcGeo());
    }

    /**
     * Получить нераспознанную часть адреса
     */
    public function getUnparsedParts(): ?string
    {
        $data = $this->getData();
        return $data['unparsed_parts'] ?? null;
    }


    /**
     * Получить список ближайших станций метро
     */
    public function getMetro(): array
    {
        $data = $this->getData();
        return $data['metro'] ?? [];
    }

    /**
     * Проверить, есть ли информация о метро
     */
    public function hasMetro(): bool
    {
        return !empty($this->getMetro());
    }


    /**
     * Получить административное деление
     */
    public function getDivisions(): ?array
    {
        $data = $this->getData();
        return $data['divisions'] ?? null;
    }

    /**
     * Получить административное деление (алиас)
     */
    public function getAdministrative(): ?array
    {
        $divisions = $this->getDivisions();
        return $divisions['administrative'] ?? null;
    }


    /**
     * Получить точность геокодирования (устаревший метод, используйте getQcGeoEnum)
     * @deprecated Используйте getQcGeoEnum()
     */
    public function getAccuracy(): ?string
    {
        $qcGeo = $this->getQcGeoEnum();
        if ($qcGeo === null) {
            return null;
        }
        return match($qcGeo) {
            GeoAccuracyCode::EXACT => 'exact',
            GeoAccuracyCode::NEAREST_HOUSE => 'house',
            GeoAccuracyCode::STREET => 'street',
            GeoAccuracyCode::SETTLEMENT => 'settlement',
            GeoAccuracyCode::CITY => 'city',
            GeoAccuracyCode::NOT_DETERMINED => 'none',
        };
    }

    /**
     * Получить уровень детализации (устаревший метод, используйте getFiasLevelEnum)
     * @deprecated Используйте getFiasLevelEnum()
     */
    public function getLevel(): ?string
    {
        return $this->getFiasLevel();
    }


    /**
     * Получить детальную информацию об адресе
     * Возвращает структурированный массив с основными полями
     */
    public function getDetailedInfo(): array
    {
        return [
            'value' => $this->getValue(),
            'unrestricted_value' => $this->getUnrestrictedValue(),
            'coordinates' => $this->getCoordinates(),
            'postal_code' => $this->getPostalCode(),
            'country' => $this->getCountry(),
            'region' => $this->getRegion(),
            'region_with_type' => $this->getRegionWithType(),
            'city' => $this->getCity(),
            'city_with_type' => $this->getCityWithType(),
            'settlement_with_type' => $this->getSettlementWithType(),
            'street' => $this->getStreet(),
            'street_with_type' => $this->getStreetWithType(),
            'house' => $this->getHouse(),
            'block' => $this->getBlock(),
            'flat' => $this->getFlat(),
            'has_coordinates' => $this->hasCoordinates(),
            'has_block' => $this->hasBlock(),
            'has_flat' => $this->hasFlat(),
            'fias_id' => $this->getFiasId(),
            'kladr_id' => $this->getKladrId(),
            'qc' => $this->getQc(),
            'qc_geo' => $this->getQcGeo(),
        ];
    }
}

