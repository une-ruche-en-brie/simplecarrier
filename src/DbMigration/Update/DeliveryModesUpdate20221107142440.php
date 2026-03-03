<?php
/**
 * NOTICE OF LICENSE
 *
 * @author Mondial Relay <offrestart@mondialrelay.fr>
 * @copyright Copyright (c) Mondial Relay
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace MondialRelay\MondialRelay\DbMigration\Update;

use MondialRelay\MondialRelay\Core\DbMigration\AbstractDbMigration;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class DeliveryModesUpdate20221107142440
 *
 * @author Pascal Fischer <contact@scaledev.fr>
 * @since 3.3.2
 */
final class DeliveryModesUpdate20221107142440 extends AbstractDbMigration
{
    /**
     * {@inheritDoc}
     */
    public function execute()
    {
        $this->getLogger()->addLog('Start migration: ' . self::class);

        if ($this->alterCarrierMethodTable()) {
            $this->getLogger()->addLog('Migration finished: ' . self::class);

            return true;
        }

        $this->getLogger()->addLog('Stop migration: ' . self::class);

        return false;
    }

    /**
     * Updates the "delivery_mode" column of the "carrier_method" table from
     * database:
     * - Add the "MED" and "APM" value to the list of values
     * - Replace all the "24R" values by "MED"
     * - Remove the "24R" value from the list of values
     *
     * @return bool
     */
    private function alterCarrierMethodTable()
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

        if (\Db::getInstance()->execute($query)) {
            $this->getLogger()->addSuccess('"MED" and "APM" values has been added to the values list of the "delivery_mode" column of the "' . $tableName . '" table.');
        } else {
            $this->getLogger()->addError('Impossible to add "MED" and "APM" values to the values list of the "delivery_mode" column of the "' . $tableName . '" table.');

            return false;
        }

        $query = 'UPDATE `' . $tableName . '` SET `delivery_mode` = \'MED\' WHERE `delivery_mode` = \'24R\'';

        if (\Db::getInstance()->execute($query)) {
            $this->getLogger()->addSuccess('"24R" values has been replaced by "MED" value to the "delivery_mode" column from the "' . $tableName . '" table.');
        } else {
            $this->getLogger()->addError('Impossible to update the "delivery_mode" values from the "' . $tableName . '" table');

            return false;
        }

        $query = 'ALTER TABLE `' . $tableName . '` MODIFY COLUMN `delivery_mode` ENUM(
            \'MED\',
            \'APM\',
            \'DRI\',
            \'LD1\',
            \'LDS\',
            \'HOM\'
        ) NOT NULL';

        if (\Db::getInstance()->execute($query)) {
            $this->getLogger()->addSuccess('"24R" value has been removed from the values list of the "delivery_mode" column of the "' . $tableName . '" table.');
        } else {
            $this->getLogger()->addError('Impossible to remove the "24R" value from the values list of the "delivery_mode" of the "' . $tableName . '" table');

            return false;
        }

        return true;
    }
}
