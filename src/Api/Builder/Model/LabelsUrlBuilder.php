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

namespace MondialRelay\MondialRelay\Api\Builder\Model;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class LabelsUrlBuilder
 *
 * @author Fabien Sigrand <contact@scaledev.fr>
 */
final class LabelsUrlBuilder
{
    const BASE_URL = 'https://connect.mondialrelay.com';

    public static function build(array $expeditionNumbers, $format)
    {
        $urlToEncode = '/' . \Configuration::get(\MondialRelay::WEBSERVICE_ENSEIGNE);
        $urlToEncode .= '/etiquette/GetStickersExpeditionsAnonyme2?ens=' . \Configuration::get(\MondialRelay::WEBSERVICE_ENSEIGNE);
        $urlToEncode .= '&expedition=' . implode(";", $expeditionNumbers) . '&lg=' . \Configuration::get(\MondialRelay::API2_CULTURE);

        $enseigne = strpos(\Configuration::get(\MondialRelay::WEBSERVICE_ENSEIGNE), 'BDTEST') !== false ? '' : \Configuration::get(\MondialRelay::WEBSERVICE_KEY);

        $crc = hash('md5', $enseigne . '_' . $urlToEncode);

        return self::BASE_URL . $urlToEncode . '&format=' . $format . '&crc=' . strtoupper($crc);
    }
}