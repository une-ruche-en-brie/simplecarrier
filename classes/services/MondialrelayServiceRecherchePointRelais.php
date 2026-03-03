<?php
/**
 * NOTICE OF LICENSE
 *
 * @author Mondial Relay <offrestart@mondialrelay.fr>
 * @copyright Copyright (c) Mondial Relay
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
require_once _PS_MODULE_DIR_ . '/mondialrelay/classes/services/MondialrelayService.php';

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Search for relays using country and postcode
 */
class MondialrelayServiceRecherchePointRelais extends MondialrelayService
{
    /**
     * {@inheritDoc}
     */
    protected $function = 'WSI4_PointRelais_Recherche';

    /**
     * {@inheritDoc}
     */
    protected $fields = [
        'Enseigne' => [
            'required' => true,
            'regex' => '#^[0-9A-Z]{2}[0-9A-Z ]{6}$#',
        ],
        'Pays' => [
            'required' => true,
            'regex' => '#^[A-Z]{2}$#',
        ],
        // We'll never use Latitude / Longitude
        'CP' => [
            'required' => true,
        ],
        'Poids' => [
            // 15 <= weight <= 9 999 999 (grams)
            'regex' => '#^1[5-9]$|^[2-9][0-9]$|^[0-9]{3,7}$#',
        ],
        'Action' => [
            'regex' => '#^(REL|24R|24L|24X|DRI)$#',
        ],
        'DelaiEnvoi' => [
            'regex' => '#^-?[0-9]{2})$#',
        ],
        'RayonRecherche' => [
            'regex' => '#^[0-9]{1,4}$#',
        ],
        'NombreResultats' => [
            'regex' => '#^[0-3][0-9]$#',
        ],
        // Required, but set by the service if it's absent
        'Security' => [
            'regex' => '#^[0-9A-Z]{32}$#',
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function init($data)
    {
        $this->data = $data;
        return $this->setPayloadFromData();
    }

    /**
     * Validates a zipcode
     *
     * @param int $key the position of the validated item in the $data array
     * @param string $value the 'CP' value of the item in the $data array
     * @return bool
     */
    protected function processCP($key, $value, $item)
    {
        if (MondialrelayTools::checkZipcodeByCountry($value['zipcode'], $value['iso_country'])) {
            return $value['zipcode'];
        }

        $this->errors[$key][] = $this->l('Invalid zipcode for country %s : %s', false, [$value['iso_country'], $value['zipcode']]);
        return false;
    }

    /**
     * {@inheritDoc}
     */
    protected function parseResult($soapClient, $result, $key)
    {
        $this->result[$key] = $result->{$this->function . 'Result'};

        // Remove useless and undocumented nesting level...
        if (isset($this->result[$key]->PointsRelais->PointRelais_Details)) {
            $this->result[$key]->PointsRelais = $this->result[$key]->PointsRelais->PointRelais_Details;
        }
    }
}
