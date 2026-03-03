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
 * Get informations for relays
 */
class MondialrelayServiceInfosPointRelais extends MondialrelayService
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
        'NumPointRelais' => [
            'required' => true,
            'regex' => '#^[0-9]{6}$#',
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
