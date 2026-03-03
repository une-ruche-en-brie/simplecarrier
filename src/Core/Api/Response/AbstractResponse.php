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

namespace MondialRelay\MondialRelay\Core\Api\Response;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class AbstractResponse
 *
 * @author Fabien Sigrand <contact@scaledev.fr>
 * @since tag
 */
abstract class AbstractResponse implements ResponseInterface
{
    /** @var bool|string */
    private $clientResponse;

    /**
     * Gets the client's response.
     *
     * @return bool|string
     */
    public function getClientResponse()
    {
        return $this->clientResponse;
    }

    /**
     * Sets the client's response.
     *
     * @param bool|string
     * @return $this
     */
    public function setClientResponse($clientResponse)
    {
        $this->clientResponse = $clientResponse;

        return $this;
    }
}
