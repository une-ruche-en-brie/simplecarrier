<?php
/**
 * NOTICE OF LICENSE
 *
 * @author Mondial Relay <offrestart@mondialrelay.fr>
 * @copyright Copyright (c) Mondial Relay
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace MondialRelay\MondialRelay\Core\DbMigration;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Interface DbMigrationInterface
 *
 * @author Pascal Fischer <contact@scaledev.fr>
 * @since 3.3.2
 */
interface DbMigrationInterface
{
    /**
     * Executes a database migration.
     *
     * @return bool TRUE in case of success, else FALSE
     */
    public function execute();
}
