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
 * Upgrade to 3.3.0
 * @param Mondialrelay $module
 * @return bool
 * @throws PrestaShopDatabaseException
 */
function upgrade_module_3_3_0($module)
{
   $tableName = _DB_PREFIX_ . \MondialrelayCarrierMethod::$definition['table'];
   
   $query = 'ALTER TABLE `' . $tableName . '` MODIFY COLUMN `delivery_mode` ENUM(
      \'24R\',
      \'MED\',
      \'APM\',
      \'DRI\',
      \'LD1\',
      \'LDS\',
      \'HOM\'
   ) NOT NULL';
   
   $query = 'UPDATE `' . $tableName . '` SET `delivery_mode` = \'MED\' WHERE `delivery_mode` = \'24R\'';
   
   $query = 'ALTER TABLE `' . $tableName . '` MODIFY COLUMN `delivery_mode` ENUM(
      \'MED\',
      \'APM\',
      \'DRI\',
      \'LD1\',
      \'LDS\',
      \'HOM\'
   ) NOT NULL';
    
   return true;
}
