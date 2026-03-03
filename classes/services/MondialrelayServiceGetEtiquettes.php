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

class MondialrelayServiceGetEtiquettes extends MondialrelayService
{
    /**
     * {@inheritDoc}
     */
    protected $function = 'WSI3_GetEtiquettes';

    /**
     * {@inheritDoc}
     */
    protected $fields = [
        'Enseigne' => [
            'required' => true,
            'regex' => '#^[0-9A-Z]{2}[0-9A-Z ]{6}$#',
        ],
        // Should be an array of expedition numbers, for convenience
        'Expeditions' => [
            'required' => true,
            'regex' => '#^[0-9]{0,8}(;[0-9]{0,8})*$#',
        ],
        'Langue' => [
            'required' => true,
            // Original regex : ^[A-Z]{2}$
            // But we only have 4 languages available, so...
            'regex' => '#^FR|ES|NL|EN$#',
        ],
        // Required, but set by the service if it's absent
        'Security' => [
            'regex' => '#^[0-9A-Z]{32}$#',
        ],
    ];

    /**
     * @var array
     *
     * Usually retrieved from configuration; the ISO code for the
     * labels language. Can be set for the whole service by using the setter, and
     * will never overwrite already set field
     *
     * @see MondialrelayServiceGetEtiquettes::setLangue()
     * @see MondialrelayServiceGetEtiquettes::preprocessData()
     */
    protected $webservice_Langue = '';

    /**
     * {@inheritDoc}
     */
    protected function __construct()
    {
        parent::__construct();
        $this->webservice_Langue = Configuration::get(Mondialrelay::LABEL_LANG);
    }

    /**
     * {@inheritDoc}
     */
    public function init($data)
    {
        $this->data = $data;
        return $this->setPayloadFromData();
    }

    /**
     * Preprocess a data item
     *
     * @param int $key
     * @param array $item
     * @return array the preprocessed item
     */
    protected function preprocessData($key, $item)
    {
        if (empty($item['Langue'])) {
            $item['Langue'] = $this->webservice_Langue;
        }

        if (!empty($item['Expeditions'])) {
            $item['Expeditions'] = implode(';', $item['Expeditions']);
        }

        return $item;
    }

    public function processLangue($key, $value, $item)
    {
        return Tools::strtoupper($value);
    }

    /**
     * {@inheritDoc}
     */
    protected function parseResult($soapClient, $result, $key)
    {
        $this->result[$key] = $result->{$this->function . 'Result'};
    }

    public function setLangue($langue)
    {
        $this->webservice_Langue = $langue;
    }
}
