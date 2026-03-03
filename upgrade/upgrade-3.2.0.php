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

/**
 * Upgrade to 3.2.0
 * @param Mondialrelay $module
 * @return bool
 * @throws PrestaShopDatabaseException
 */
function upgrade_module_3_2_0($module)
{
    $sqlDescribeColumns = 'SHOW COLUMNS FROM `' . _DB_PREFIX_ . '%tableName%`';

    $describeMethodColumns = Db::getInstance()->executeS(
        str_replace('%tableName%', 'mondialrelay_carrier_method', $sqlDescribeColumns)
    );

    if (!array_search('delivery_type', array_column($describeMethodColumns, 'Field'))) {
        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'mondialrelay_carrier_method` ADD delivery_type varchar(255) NOT NULL';
        Db::getInstance()->execute($sql);
    }

    $query = new DbQuery();
    $query->select('id_mondialrelay_carrier_method');
    $query->from('mondialrelay_carrier_method');

    $mrCarriers = Db::getInstance()->executeS($query);

    foreach ($mrCarriers as $mrCarrier) {
        Db::getInstance()->update(
            'mondialrelay_carrier_method',
            ['delivery_type' => 'MR'],
            'id_mondialrelay_carrier_method = ' . (int) $mrCarrier['id_mondialrelay_carrier_method']
        );
    }

    return true;
}
