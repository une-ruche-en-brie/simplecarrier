<?php
/**
 * NOTICE OF LICENSE
 *
 * @author Mondial Relay <offrestart@mondialrelay.fr>
 * @copyright Copyright (c) Mondial Relay
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace MondialRelay\MondialRelay\Core\DbMigration;

use MondialRelay\MondialRelay\Core\Component\Logger;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class AbstractDbMigration
 *
 * @author Pascal Fischer <contact@scaledev.fr>
 * @since 3.3.2
 */
abstract class AbstractDbMigration implements DbMigrationInterface
{
    /** @var Logger */
    private $logger;

    /**
     * AbstractDbMigration constructor.
     */
    public function __construct()
    {
        $this->logger = new Logger();
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
