<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from ScaleDEV.
 * Use, copy, modification or distribution of this source file without written
 * license agreement from ScaleDEV is strictly forbidden.
 * In order to obtain a license, please contact us: contact@scaledev.fr
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise à une licence commerciale
 * concédée par la société ScaleDEV.
 * Toute utilisation, reproduction, modification ou distribution du présent
 * fichier source sans contrat de licence écrit de la part de ScaleDEV est
 * expressément interdite.
 * Pour obtenir une licence, veuillez nous contacter : contact@scaledev.fr
 * ...........................................................................
 * @author ScaleDEV <contact@scaledev.fr>
 * @copyright Copyright (c) ScaleDEV - 12 RUE CHARLES MORET - 10120 SAINT-ANDRE-LES-VERGERS - FRANCE
 * @license Commercial license
 * Support: support@scaledev.fr
 */

namespace MondialRelay\MondialRelay\Api\Model;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class DeliveryModeBuilder
 *
 * @author Fabien Sigrand <contact@scaledev.fr>
 */
final class Mode
{
    /** @var string */
    private $mode;

    /** @var string */
    private $location;

    /**
     * Get the value of location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set the value of location
     *
     * @return self
     */
    public function setLocation($location)
    {
        if (preg_match('/^[0-9A-Z-]{0,10}$/', $location)) {
            $this->location = $location;
        }

        return $this;
    }

    /**
     * Get the value of mode
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Set the value of mode
     *
     * @return self
     */
    public function setMode($mode)
    {
        if (preg_match('/^[0-9A-Z]{3}$/', $mode)) {
            $this->mode = $mode;
        }

        return $this;
    }
}
