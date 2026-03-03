<?php
/**
 * NOTICE OF LICENSE
 *
 * @author Mondial Relay <offrestart@mondialrelay.fr>
 * @copyright Copyright (c) Mondial Relay
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class MondialrelayCarrierMethod extends ObjectModel
{
    /** @var int The id of the associated Prestashop carrier */
    public $id_carrier;

    /** @var string See Webservice 'ModeLiv' field */
    public $delivery_mode;

    /** @var string type of delivery that impact country */
    public $delivery_type;

    /** @var string See Webservice 'Assurance' field */
    public $insurance_level;

    /**
     * @var int 0/1 : Was the carrier deleted ? We need to keep it for history purposes
     */
    public $is_deleted;

    /**
     * @var int Id carrier reference
     */
    public $id_reference;

    public $date_add;
    public $date_upd;

    public static $definition = [
        'table' => 'mondialrelay_carrier_method',
        'primary' => 'id_mondialrelay_carrier_method',
        'fields' => [
            'id_carrier' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'delivery_mode' => ['type' => self::TYPE_STRING, 'values' => ['24R', 'APM', 'MED', 'HOM'], 'required' => true, 'size' => 3],
            'delivery_type' => ['type' => self::TYPE_STRING, 'values' => ['MR', 'IP'], 'required' => true],
            'insurance_level' => ['type' => self::TYPE_STRING, 'values' => [0, 1, 2, 3, 4, 5], 'default' => 0, 'size' => 1],
            'is_deleted' => ['type' => self::TYPE_BOOL, 'default' => 0, 'validate' => 'isBool'],
            'id_reference' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    /**
     * @var array The delivery modes using relays
     */
    public static $relayDeliveryModes = ['MED', 'APM', '24R'];

    /**
     * Returns an array of insurance levels labels indexed with the associated
     * value.
     * We can't set this method as "static", as calls to the translation method
     * won't be parsed by PS if we don't use the "$this" syntax
     *
     * @return array
     */
    public function getInsuranceLevelsList()
    {
        return [
            0 => '0 : ' . $this->l('No insurance'),
            1 => '1 : ' . $this->l('Complementary Insurance Lv1'),
            2 => '2 : ' . $this->l('Complementary Insurance Lv2'),
            3 => '3 : ' . $this->l('Complementary Insurance Lv3'),
            4 => '4 : ' . $this->l('Complementary Insurance Lv4'),
            5 => '5 : ' . $this->l('Complementary Insurance Lv5'),
        ];
    }

    /**
     * Returns an array of delivery modes labels indexed with the associated
     * value
     *
     * @return array
     */
    public function getDeliveryModesList()
    {
        $deliveryModesList = [
            '24R' => $this->l('The entire Mondial Relay network'),
            'MED' => $this->l('Point Relais®'),
            'APM' => $this->l('Locker'),
        ];

        if (Configuration::get(MondialRelay::HOME_DELIVERY)) {
            $deliveryModesList['HOM'] = $this->l('Home delivery');
        }

        return $deliveryModesList;
    }

    /**
     * Returns an array of delivery type labels indexed with the associated
     * value
     *
     * @return array
     */
    public function getDeliveryTypesList()
    {
        return [
            'MR' => $this->l('MondialRelay: France, Belgium, Luxembourg, Netherlands, Germany, Austria, Poland'),
            'IP' => $this->l('InPost: Spain, Portugal, Italy'),
        ];
    }

    /**
     * Returns the default weight range values for a carrier, in an array with
     * 'min' and 'max' keys
     *
     * @param float $weightCoeff The weight coefficient to use when calculating the values
     * @param string $deliveryMode The carrier's delivery mode
     */
    public static function getCarrierDefaultRangeWeightValues($weightCoeff, $deliveryMode)
    {
        $ranges = [
            '24R' => [
                'min' => 0,
                'max' => 25000 / $weightCoeff,
            ],
            'APM' => [
                'min' => 0,
                'max' => 25000 / $weightCoeff,
            ],
            'MED' => [
                'min' => 0,
                'max' => 25000 / $weightCoeff,
            ],
            'HOM' => [
                'min' => 0,
                'max' => 30000 / $weightCoeff,
            ],
        ];
        return isset($ranges[$deliveryMode]) ? $ranges[$deliveryMode] : false;
    }

    /**
     * Returns the default weight price values for a carrier, in an array with
     * 'min' and 'max' keys
     * @return array
     */
    public static function getCarrierDefaultRangePriceValues()
    {
        return [
            'min' => 0,
            'max' => 10000,
        ];
    }

    /**
     * Gets a Mondial Relay carrier method from its id_carrier
     *
     * @param int $id_carrier
     * @return MondialrelayCarrierMethod|false
     */
    public static function getFromNativeCarrierId($id_carrier)
    {
        $query = new DbQuery();
        $query->select('*')
            ->from(self::$definition['table'])
            ->where('id_carrier = ' . (int) $id_carrier);

        $res = Db::getInstance()->getRow($query);

        if ($res) {
            $carrierMethod = new MondialrelayCarrierMethod();
            $carrierMethod->hydrate($res);
            return $carrierMethod;
        }

        return false;
    }

    /**
     * Returns an array of native Carrier objects that are linked to Mondial Relay.
     * This will not check wether a carrier is *available* for a shop; the $id_shop
     * is only used for translation purposes.
     *
     * @param bool $active true/false will return active/inactive carriers; null will return both
     * @param int $id_shop null will return values with the default shop
     * @param int $id_lang null will return values with the default language
     * @param bool $deleted true/false will return deleted/existing carriers; null will return both
     * @return bool|Carrier
     */
    public static function getAllPrestashopCarriers($active = true, $id_shop = null, $id_lang = null, $deleted = false)
    {
        if (!$id_lang) {
            $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        }

        if (!$id_shop) {
            $id_shop = (int) Configuration::get('PS_SHOP_DEFAULT');
        }

        $intLang = (int) $id_lang;
        $intShop = (int) $id_shop;

        $query = new DbQuery();
        $query->select('c.*, cl.*')
            ->from(self::$definition['table'], 'mr_cm')
            ->innerJoin(
                Carrier::$definition['table'],
                'c',
                'c.id_carrier = mr_cm.id_carrier'
            )
            ->leftJoin(
                Carrier::$definition['table'] . '_lang',
                'cl',
                "cl.id_carrier = c.id_carrier AND cl.id_lang = $intLang AND cl.id_shop = $intShop"
            );

        if ($deleted !== null) {
            $query->where('mr_cm.is_deleted = ' . (int) $deleted)
            ->where('c.deleted = ' . (int) $deleted);
        }
        if ($active !== null) {
            $query->where('c.active = ' . (int) $active);
        }

        $res = Db::getInstance()->executeS($query);

        if ($res === false) {
            return false;
        }
        if (empty($res)) {
            return [];
        }

        return ObjectModel::hydrateCollection('Carrier', $res, $id_lang);
    }

    /**
     * Checks if an array contains native carrier ids associated to a Mondial
     * Relay active carrier method, and returns only those ids
     * @param array $nativeCarriersIds An array of native carriers ids
     * @param bool $onlyRelays
     * @param bool $deletedIncluded
     * @return false|array
     * @throws PrestaShopDatabaseException
     */
    public static function findMondialrelayCarrierIds($nativeCarriersIds, $onlyRelays = false, $deletedIncluded = false)
    {
        if (empty($nativeCarriersIds)) {
            return false;
        }

        $nativeCarriersReferencesIds = array_map(function ($carrierId) {
            return (new Carrier($carrierId))->id_reference;
        }, $nativeCarriersIds);

        $query = new DbQuery();
        $query->select('id_carrier')->from(self::$definition['table'])
            ->where(
                'id_reference IN (' .
                implode(',', array_map(function ($i) {
                    return (int) $i;
                }, $nativeCarriersReferencesIds)) .
                ')'
            );

        if (!$deletedIncluded) {
            $query->where('is_deleted = 0');
        }

        if ($onlyRelays) {
            $query->where(
                'delivery_mode IN (\'' .
                implode('\', \'', array_map(function ($relayDeliveryMode) {
                    return pSQL($relayDeliveryMode);
                }, self::$relayDeliveryModes)) .
                '\')'
            );
        }

        $res = Db::getInstance()->executeS($query);
        if (!$res) {
            return false;
        }
        return array_column($res, 'id_carrier');
    }

    /**
     * Checks if a carrier method needs to have a selected relay
     * @return bool
     */
    public function needsRelay()
    {
        return in_array($this->delivery_mode, self::$relayDeliveryModes);
    }

    /**
     * Translation function.
     *
     * @param string $string The string to translate
     * @param string $specific The name of the file, if different from the calling class
     *
     * @return string
     */
    protected function l($string, $specific = false)
    {
        if (!$specific) {
            $specific = basename(str_replace('\\', '/', get_class($this)));
        }
        return Translate::getModuleTranslation('mondialrelay', $string, $specific);
    }

    /**
     * Disables native carriers associated to Mondial Relay in the shops from
     * the given list
     *
     * @param array $id_shop_list
     * @return bool
     */
    public static function removeNativeCarriersFromShops($id_shop_list)
    {
        $deleteFromCarrierShop = 'DELETE FROM ' . _DB_PREFIX_ . 'carrier_shop '
            . 'WHERE id_carrier IN ('
            . 'SELECT id_carrier FROM ' . _DB_PREFIX_ . self::$definition['table'] . ' '
            . ') '
            . 'AND id_shop IN (' .
            implode(', ', array_map(
                function ($i) {
                    return (int) $i;
                },
                $id_shop_list
            ))
            . ')';

        return Db::getInstance()->execute($deleteFromCarrierShop);
    }

    /**
     * Gets the IDs of methods which use Lockers.
     *
     * @return array
     * @throws PrestaShopDatabaseException
     *
     * @author Pascal Fischer <contact@scaledev.fr>
     * @since 3.3.2
     */
    public static function getLockersIdsList()
    {
        $lockersIdsList = Db::getInstance()->executeS((new DbQuery())
            ->select(self::$definition['primary'])
            ->from(self::$definition['table'])
            ->where('delivery_mode = \'APM\'')
        );

        if (!is_array($lockersIdsList)) {
            return [];
        }

        return $lockersIdsList;
    }
}
