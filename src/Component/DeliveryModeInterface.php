<?php
/**
 * NOTICE OF LICENSE
 *
 * @author Mondial Relay <offrestart@mondialrelay.fr>
 * @copyright Copyright (c) Mondial Relay
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace MondialRelay\MondialRelay\Component;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Interface DeliveryModeInterface
 *
 * @author Pascal Fischer <contact@scaledev.fr>
 * @since 3.3.2
 */
interface DeliveryModeInterface
{
    const MONDIAL_RELAY_POINT_RELAIS = 'MED';
    const MONDIAL_RELAY_LOCKER = 'APM';
}
