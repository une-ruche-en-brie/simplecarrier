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

$_GET['deprecated_task'] = true;
$_GET['fc'] = 'module';
$_GET['module'] = 'mondialrelay';
$_GET['controller'] = 'ordersStatusUpdate';

include dirname(__FILE__) . '/../../index.php';
