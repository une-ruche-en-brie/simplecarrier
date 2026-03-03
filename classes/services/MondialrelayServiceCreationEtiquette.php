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

class MondialrelayServiceCreationEtiquette extends MondialrelayService
{
    /**
     * {@inheritDoc}
     */
    protected $function = 'WSI2_CreationEtiquette';

    /**
     * {@inheritDoc}
     */
    protected $fields = [
        'Enseigne' => [
            'required' => true,
            'regex' => '#^[0-9A-Z]{2}[0-9A-Z ]{6}$#',
        ],
        'ModeCol' => [
            'required' => true,
            'regex' => '#^(CCC|CDR|CDS|REL)$#',
        ],
        'ModeLiv' => [
            'required' => true,
            'regex' => '#^(LCC|LD1|LDS|24R|ESP|HOM)$#',
        ],
        'NDossier' => [
            'regex' => '#^(|[0-9A-Z_ -]{0,15})$#',
        ],
        'NClient' => [
            'regex' => '#^(|[0-9A-Z]{0,9})$#',
        ],
        'Expe_Langage' => [
            'required' => true,
            'regex' => '#^[A-Z]{2}$#',
        ],
        // Expediteur (Civilite lastname firstname)
        'Expe_Ad1' => [
            'required' => true,
            'regex' => '#^[0-9A-Z_\-\'., /]{2,32}$#',
        ],
        // Expediteur (Complement)
        'Expe_Ad2' => [
            'regex' => '#^[0-9A-Z_\-\'., /]{0,32}$#',
        ],
        // Expediteur (Rue)
        'Expe_Ad3' => [
            'required' => true,
            'regex' => '#^[0-9A-Z_\-\'., /]{0,32}$#',
        ],
        // Expediteur (Complement)
        'Expe_Ad4' => [
            'regex' => '#^[0-9A-Z]{2}[0-9A-Z ]{6}$#',
        ],
        'Expe_Ville' => [
            'required' => true,
            'regex' => '#^[A-Z_\-\' 0-9]{2,26}$#',
        ],
        'Expe_CP' => [
            'required' => true,
        ],
        // ISO code
        'Expe_Pays' => [
            'required' => true,
            'regex' => '#^[A-Z]{2}$#',
        ],
        'Expe_Tel1' => [
            'required' => true,
            'regex' => '#^((00|\+)[1-9]{2}|0)[0-9][0-9]{7,11}$#',
        ],
        'Expe_Tel2' => [
            'regex' => '#^((00|\+)[1-9]{2}|0)[0-9][0-9]{7,11}$#',
        ],
        'Expe_Mail' => [
            'regex' => '#^[\w\-\.\@_]{0,70}$#',
        ],
        'Dest_Langage' => [
            'required' => true,
            // Original regex : ^[A-Z]{2}$
            // But we only have 4 languages available, so...
            'regex' => '#^FR|ES|NL|EN$#',
        ],
        'Dest_Ad1' => [
            'required' => true,
            'regex' => '#^[0-9A-Z_\-\'., /]{2,32}$#',
        ],
        'Dest_Ad2' => [
            'regex' => '#^[0-9A-Z_\-\'., /]{2,32}$#',
        ],
        'Dest_Ad3' => [
            'required' => true,
            'regex' => '#^[0-9A-Z_\-\'., /]{2,32}$#',
        ],
        'Dest_Ad4' => [
            'regex' => '#^[0-9A-Z_\-\'., /]{0,32}$#',
        ],
        'Dest_Ville' => [
            'required' => true,
            'regex' => '#^[A-Z_\-\'. 0-9]{2,26}$#',
        ],
        'Dest_CP' => [
            'required' => true,
        ],
        'Dest_Pays' => [
            'required' => true,
            'regex' => '#^[A-Z]{2}$#',
        ],
        'Dest_Tel1' => [
            'regex' => '#^((00|\+)[1-9]{2}|0)[0-9][0-9]{7,11}$#',
        ],
        'Dest_Tel2' => [
            'regex' => '#^((00|\+)[1-9]{2}|0)[0-9][0-9]{7,11}$#',
        ],
        'Dest_Mail' => [
            'regex' => '#^[\w\-\.\@_]{0,70}$#',
        ],
        'Poids' => [
            'required' => true,
            // 15 <= weight <= 9 999 999 (grams)
            'regex' => '#^1[5-9]$|^[2-9][0-9]$|^[0-9]{3,7}$#',
        ],
        'Longueur' => [
            'regex' => '#^[0-9]{0,3}$#',
        ],
        'Taille' => [
            'regex' => '#^(XS|S|M|L|XL|XXL|3XL)$#',
        ],
        // This will be set to 1 if not specified
        'NbColis' => [
            'required' => true,
            'regex' => '#^[0-9]{1,2}$#',
        ],
        // This will be set to 0 if not specified
        'CRT_Valeur' => [
            'required' => true,
            'regex' => '#^[0-9]{1,7}$#',
        ],
        'CRT_Devise' => [
            'regex' => '#^(|EUR)$#',
        ],
        'Exp_Valeur' => [
            'regex' => '#^[0-9]{0,7}$#',
        ],
        'Exp_Devise' => [
            'regex' => '#^(|EUR)$#',
        ],
        'COL_Rel_Pays' => [
            'regex' => '#^[A-Z]{2}$#',
        ],
        'COL_Rel' => [
            'regex' => '#^(|[0-9]{6})$#',
        ],
        'LIV_Rel_Pays' => [
            'regex' => '#^[A-Z]{2}$#',
        ],
        'LIV_Rel' => [
            'regex' => '#^(|[0-9]{6})$#',
        ],
        'TAvisage' => [
            'regex' => '#^(|O|N)$#',
        ],
        'TReprise' => [
            'regex' => '#^(|O|N)$#',
        ],
        'Montage' => [
            'regex' => '#^(|[0-9]{1,3})$#',
        ],
        'TRDV' => [
            'regex' => '#^(|O|N)$#',
        ],
        'Assurance' => [
            'regex' => '#^(|[0-9A-Z]{1})$#',
        ],
        'Instructions' => [
            'regex' => '#^[0-9A-Z_\-\'., /]{0,31}#',
        ],
        // Required, but set by the service if it's absent
        'Security' => [
            'regex' => '#^[0-9A-Z]{32}$#',
        ],
        'Texte' => [
            'regex' => '#^([^<>&\']{3,30})(\(cr\)[^<>&\']{0,30}) {0,9}$#',
        ],
    ];

    /**
     * @var string
     *
     * Usually constant; can be set for the whole service by using
     * the setter, and will never overwrite an already set "ModeCol" field
     *
     * @see MondialrelayServiceCreationEtiquette::setModeCol()
     * @see MondialrelayServiceCreationEtiquette::preprocessData()
     */
    protected $webservice_ModeCol = '';

    /**
     * @var array
     *
     * Usually retrieved from configuration;
     * array indexed with the webservice "Expe_*" fields. Can be set for the
     * whole service by using the setter, and will never overwrite already set
     * "Expe_*" fields
     *
     * @see MondialrelayServiceCreationEtiquette::setModeCol()
     * @see MondialrelayServiceCreationEtiquette::preprocessData()
     */
    protected $webservice_ExpeAddress = [];

    /**
     * @var array
     *
     * Usually retrieved from configuration; the ISO code for the
     * label language. Can be set for the whole service by using the setter, and
     * will never overwrite already set field
     *
     * @see MondialrelayServiceCreationEtiquette::setDestLangage()
     * @see MondialrelayServiceCreationEtiquette::preprocessData()
     */
    protected $webservice_Dest_Langage = '';

    /**
     * @var array
     *
     * Default value. Can be set for the whole service by using the
     * setter, and will never overwrite already set field
     *
     * @see MondialrelayServiceCreationEtiquette::setCRTDevise()
     * @see MondialrelayServiceCreationEtiquette::preprocessData()
     */
    protected $webservice_CRT_Devise = 'EUR';

    /**
     * {@inheritDoc}
     */
    protected function __construct()
    {
        parent::__construct();

        // While this doesn't really depend on the context, it is (for now) a
        // constant in the module.
        $this->webservice_ModeCol = Mondialrelay::COLLECTION_MODE;

        $this->webservice_ExpeAddress = [
            'Expe_Langage' => Configuration::get(Mondialrelay::LABEL_LANG),
            'Expe_Ad1' => Tools::replaceAccentedChars(Configuration::get('PS_SHOP_NAME')),
            'Expe_Ad3' => Tools::replaceAccentedChars(Configuration::get('PS_SHOP_ADDR1')),
            // Deleted, cause too many failed for the process
            // 'Expe_Ad4' => Configuration::get('PS_SHOP_ADDR2'),
            'Expe_Ville' => Tools::replaceAccentedChars(Configuration::get('PS_SHOP_CITY')),
            'Expe_CP' => Configuration::get('PS_SHOP_CODE'),
            'Expe_Pays' => Country::getIsoById(Configuration::get('PS_SHOP_COUNTRY_ID')),
            'Expe_Tel1' => MondialrelayTools::getFormattedPhonenumber(Configuration::get('PS_SHOP_PHONE')),
            'Expe_Mail' => Configuration::get('PS_SHOP_EMAIL'),
        ];

        $this->webservice_Dest_Langage = Configuration::get(Mondialrelay::LABEL_LANG);
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
        if (empty($item['ModeCol'])) {
            $item['ModeCol'] = $this->webservice_ModeCol;
        }

        if (empty($item['CRT_Devise'])) {
            $item['CRT_Devise'] = $this->webservice_CRT_Devise;
        }

        if (empty($item['Dest_Langage'])) {
            $item['Dest_Langage'] = $this->webservice_Dest_Langage;
        }

        foreach ($this->webservice_ExpeAddress as $expeFieldName => $expeValue) {
            if (empty($item[$expeFieldName])) {
                $item[$expeFieldName] = $expeValue;
            }
        }

        // These values are not dependent on the context; so it doesn't really
        // make sense to use settable default values
        if (empty($item['NbColis'])) {
            $item['NbColis'] = 1;
        }
        if (empty($item['CRT_Valeur'])) {
            $item['CRT_Valeur'] = 0;
        }

        return $item;
    }

    public function processExpeLangage($key, $value, $item)
    {
        return Tools::strtoupper($value);
    }

    public function processDestLangage($key, $value, $item)
    {
        return Tools::strtoupper($value);
    }

    public function processExpeAd1($key, $value, $item)
    {
        return Tools::strtoupper($value);
    }

    public function processDestAd1($key, $value, $item)
    {
        return Tools::strtoupper($value);
    }

    public function processExpeAd2($key, $value, $item)
    {
        return Tools::strtoupper($value);
    }

    public function processDestAd2($key, $value, $item)
    {
        return Tools::strtoupper($value);
    }

    public function processExpeAd3($key, $value, $item)
    {
        return Tools::strtoupper($value);
    }

    public function processDestAd3($key, $value, $item)
    {
        return Tools::strtoupper($value);
    }

    public function processExpeAd4($key, $value, $item)
    {
        return Tools::strtoupper($value);
    }

    public function processDestAd4($key, $value, $item)
    {
        return Tools::strtoupper($value);
    }

    public function processExpeVille($key, $value, $item)
    {
        return Tools::strtoupper($value);
    }

    public function processDestVille($key, $value, $item)
    {
        return Tools::strtoupper($value);
    }

    /**
     * Validates an expedition zipcode
     *
     * @param int $key the position of the validated item in the $data array
     * @param string $value the 'Expe_CP' value of the item in the $data array
     * @return bool
     */
    protected function processExpeCP($key, $value, $item)
    {
        $iso_country = $item['Expe_Pays'];
        if (MondialrelayTools::checkZipcodeByCountry($value, $iso_country)) {
            return $value;
        }

        $this->errors[$key][] = $this->l('The zipcode %s is invalid for the country selected in the shop contact information. Please add a valide zip code or change the country in your PrestaShop contact details.', false, [$value]);
        return false;
    }

    /**
     * Validates a destination zipcode
     *
     * @param int $key the position of the validated item in the $data array
     * @param string $value the 'Dest_CP' value of the item in the $data array
     *
     * @return bool
     */
    protected function processDestCP($key, $value, $item)
    {
        $iso_country = $item['Dest_Pays'];
        if (MondialrelayTools::checkZipcodeByCountry($value, $iso_country)) {
            return $value;
        }

        $this->errors[$key][] = $this->l('Invalid destination zipcode for country %s : %s', false, [$iso_country, $value]);
        return false;
    }

    /**
     * {@inheritDoc}
     */
    protected function parseResult($soapClient, $result, $key)
    {
        $this->result[$key] = $result->{$this->function . 'Result'};
    }

    /**
     * Utility function; will check the $webservice_ExpeAddress fields for errors
     * and add them in $errors['generic']. Returns true if no errors.
     *
     * @return bool
     */
    public function checkExpeAddress()
    {
        $this->data = [
            $this->webservice_ExpeAddress,
        ];

        $addressFieldsErrorMessages = [
            'Expe_Ad1' => 'Expe_Ad1 : ' . $this->l('Please check your store name configuration.'),
            'Expe_Ad3' => 'Expe_Ad3 : ' . $this->l('Please check your shop address(line 1) configuration.'),
            'Expe_Ville' => 'Expe_Ville : ' . $this->l('Please check your city configuration.'),
            'Expe_CP' => 'Expe_CP : ' . $this->l('Your zipcode is invalid for the country selected in the shop contact information. Please add a valid zipcode or change the country in your PrestaShop contact details.'),
            'Expe_Pays' => 'Expe_Pays : ' . $this->l('Please check your country configuration.'),
            'Expe_Tel1' => 'Expe_Tel1 : ' . $this->l('Please check your phone number configuration.'),
        ];

        $addressFieldsErrors = [];
        foreach (array_keys($this->webservice_ExpeAddress) as $fieldName) {
            if ($fieldName == 'Expe_Langage' || $this->validateField(0, $this->data[0], $fieldName)) {
                continue;
            }
            $addressFieldsErrors[] = $addressFieldsErrorMessages[$fieldName];
        }

        if (!empty($addressFieldsErrors)) {
            $this->errors['generic'][] = $this->l('Please kindly correct the following errors on the contact page :');
            $this->errors['generic'] = array_merge($this->errors['generic'], $addressFieldsErrors);
        }

        return empty($this->errors['generic']);
    }

    public function setModeCol($modeCol)
    {
        $this->webservice_ModeCol = $modeCol;
    }

    public function setExpeAddress($expeAddress)
    {
        foreach ($expeAddress as $expeFieldName => $expeValue) {
            if (!preg_match('#^Expe_.+#', $expeFieldName) || !isset($this->fields[$expeFieldName])) {
                continue;
            }
            $this->webservice_ExpeAddress[$expeFieldName] = $expeValue;
        }
    }

    public function setDestLangage($dest_langage)
    {
        $this->webservice_Dest_Langage = $dest_langage;
    }

    public function setCRTDevise($crt_devise)
    {
        $this->webservice_CRT_Devise = $crt_devise;
    }
}
