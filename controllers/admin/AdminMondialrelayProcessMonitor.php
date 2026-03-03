<?php
/**
 * NOTICE OF LICENSE
 *
 * @author Mondial Relay <offrestart@mondialrelay.fr>
 * @copyright Copyright (c) Mondial Relay
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
require_once _PS_MODULE_DIR_ . '/mondialrelay/mondialrelay.php';

use MondialrelayClasslib\Extensions\ProcessMonitor\Controllers\Admin\AdminProcessMonitorController;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminMondialrelayProcessMonitorController extends AdminProcessMonitorController
{
}
