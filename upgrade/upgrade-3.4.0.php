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
 * Upgrade to 3.3.3
 * @param Mondialrelay $module
 * @return bool
 * @throws PrestaShopDatabaseException
 */
function upgrade_module_3_4_0($module)
{
    $sqlDescribeColumns = 'SHOW COLUMNS FROM `' . _DB_PREFIX_ . '%tableName%`';

    $describeMethodColumns = Db::getInstance()->executeS(
        str_replace('%tableName%', 'mondialrelay_carrier_method', $sqlDescribeColumns)
    );

    Db::getInstance()->update(
        'mondialrelay_carrier_method',
        ['delivery_mode' => 'HOM'],
        'delivery_mode = \'LDS\' OR delivery_mode = \'LD1\''
    );

    $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'mondialrelay_carrier_method` 
            CHANGE `delivery_mode` `delivery_mode` 
            ENUM(\'24R\',\'APM\',\'MED\',\'HOM\') NOT NULL';
    Db::getInstance()->execute($sql);

    return true;
}
