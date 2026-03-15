<?php
/*
 * This file is part of Simple Carrier module
 *
 * Copyright(c) Nicolas Roudaire  https://www.une-ruche-en-brie.fr/
 * Licensed under the OSL version 3.0 license.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use MondialrelayClasslib\Actions\ActionsHandler;

class AdminMondialrelayCarriersSettingsController extends AdminMondialrelayController
{
    protected $fields_form_newMondialrelayCarrier = [];

    /** @var array see MondialrelayCarrierMethod::getInsuranceLevelsList() */
    protected $insuranceLevelsList = [];

    /** @var array see MondialrelayCarrierMethod::getDeliveryModesList() */
    protected $deliveryModesList = [];

    /** @var array */
    protected $deliveryTypesList = [];

    public function __construct()
    {
        $this->table = MondialrelayCarrierMethod::$definition['table'];

        $carrierMethod = new MondialrelayCarrierMethod();
        $this->insuranceLevelsList = $carrierMethod->getInsuranceLevelsList();
        $this->deliveryModesList = $carrierMethod->getDeliveryModesList();
        $this->deliveryTypesList = $carrierMethod->getDeliveryTypesList();

        parent::__construct();

        $this->initList();
    }

    public function init()
    {
        $this->initNewMondialrelayCarrierFormFields();

        parent::init();
    }

    public function initList()
    {
        $this->explicitSelect = true;

        $this->fields_list = [
            $this->identifier => [
                'title' => $this->module->l('ID Mondial Relay carrier', 'AdminMondialrelayCarriersSettingsController'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'id_carrier' => [
                'title' => $this->module->l('ID Prestashop Carrier', 'AdminMondialrelayCarriersSettingsController'),
                'filter_key' => 'p_c!id_carrier',
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'name' => [
                'title' => $this->module->l('Carrier', 'AdminMondialrelayCarriersSettingsController'),
                'filter_key' => 'p_c!name',
            ],
            'delivery_mode' => [
                'title' => $this->module->l('Delivery mode', 'AdminMondialrelayCarriersSettingsController'),
                'callback' => 'getDeliveryModeLabel',
            ],
            'delivery_type' => [
                'title' => $this->module->l('Delivery type', 'AdminMondialrelayCarriersSettingsController'),
                'callback' => 'getDeliveryTypeLabel',
            ],
            'insurance_level' => [
                'title' => $this->module->l('Insurance', 'AdminMondialrelayCarriersSettingsController'),
                'callback' => 'getInsuranceLevelLabel',
            ],
        ];

        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . Carrier::$definition['table'] . '` p_c ON p_c.id_carrier = a.id_carrier ';
        $this->_join .= Shop::addSqlAssociation(Carrier::$definition['table'], 'p_c');
        $this->_where = 'AND p_c.deleted = 0';
        $this->_group = 'GROUP BY a.id_carrier';

        $this->actions = ['edit', 'delete'];
    }

    public function renderList()
    {
        // Render form before list
        $helper = new HelperForm();
        $this->setHelperDisplay($helper);
        $helper->fields_value = ['name' => '', 'delivery_mode' => '', 'delivery_type' => '', 'insurance_level' => '', 'delay' => $this->module->l('Period of 3 to 5 days from the package being made available.', 'AdminMondialrelayCarriersSettingsController')];

        $this->content .= $helper->generateForm($this->fields_form_newMondialrelayCarrier);

        // Render list
        $list = parent::renderList();
        if (!empty($this->_list)) {
            $this->content .= $list;

            return;
        }

        // If list is empty, we have a custom message
        $tpl = $this->createTemplate('list_empty.tpl');
        $tpl->assign([
            'title' => $this->helper->title,
            'message' => $this->module->l('No shipping methods available. Please create a new carrier using the form above.', 'AdminMondialrelayCarriersSettingsController'),
        ]);
        $this->content .= $tpl->fetch();

        return '';
    }

    public function setHelperDisplay(Helper $helper)
    {
        parent::setHelperDisplay($helper);

        // If we're setting up the list
        if (get_class($helper) == 'HelperList') {
            // We need to set the helper's identifier as the one from "Carrier"
            // to have the links referencing the right object
            $helper->identifier = Carrier::$definition['primary'];

            unset($helper->toolbar_btn['new']);
            $helper->title = $this->module->l('Carriers List', 'AdminMondialrelayCarriersSettingsController');
        }
    }

    protected function initNewMondialrelayCarrierFormFields()
    {
        $description = $this->module->l('Create a new carrier(s) associated with the Mondial Relay module. [br] You will be able to add additional settings to this carrier once it is created via [a] Shipping > Carriers[/a]. [br] Please note that it is required to modify your carrier shipping fees, delivery time, package weight, height, etc... [br] Please pay attention that, by default, a new carrier will be available for every zone enabled in your shop.', 'AdminMondialrelayCarriersSettingsController', ['href' => $this->context->link->getAdminLink('AdminCarriers')]);

        $this->fields_form_newMondialrelayCarrier = [[
            'form' => [
                'legend' => [
                    'title' => $this->module->l('Create a New Carrier', 'AdminMondialrelayCarriersSettingsController'),
                    'icon' => 'icon-cog',
                ],
                'description' => $description,
                'input' => [
                    [
                        'label' => $this->module->l('Carrier name', 'AdminMondialrelayCarriersSettingsController'),
                        'name' => 'name',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'label' => $this->module->l('Delivery time', 'AdminMondialrelayCarriersSettingsController'),
                        'name' => 'delay',
                        'type' => 'text',
                        'required' => true,
                    ],
                    [
                        'label' => $this->module->l('Delivery mode', 'AdminMondialrelayCarriersSettingsController'),
                        'name' => 'delivery_mode',
                        'type' => 'select',
                        'class' => 'fixed-width-xxl',
                        'options' => [
                            'id' => 'value',
                            'name' => 'label',
                            'query' => MondialRelayTools::formatArrayForSelect($this->deliveryModesList),
                        ],
                        'hint' => $this->module->l('Please consult the details of your offer to find informations about your delivery mode options.', 'AdminMondialrelayCarriersSettingsController'),
                        'required' => true,
                    ],
                    [
                        'label' => $this->module->l('Delivery type (branding show in front office)', 'AdminMondialrelayCarriersSettingsController'),
                        'name' => 'delivery_type',
                        'type' => 'select',
                        'class' => 'fixed-width-xxl',
                        'options' => [
                            'id' => 'value',
                            'name' => 'label',
                            'query' => MondialRelayTools::formatArrayForSelect($this->deliveryTypesList),
                        ],
                        'hint' => $this->module->l('Please select the area where the carrier will be suggested', 'AdminMondialrelayCarriersSettingsController'),
                        'required' => true,
                    ],
                    [
                        'label' => $this->module->l('Insurance', 'AdminMondialrelayCarriersSettingsController'),
                        'name' => 'insurance_level',
                        'type' => 'select',
                        'class' => 'fixed-width-xxl',
                        'options' => [
                            'id' => 'value',
                            'name' => 'label',
                            'query' => MondialRelayTools::formatArrayForSelect($this->insuranceLevelsList),
                        ],
                        'hint' => $this->module->l('Please consult the details of your offer to find informations about your insurance options.', 'AdminMondialrelayCarriersSettingsController'),
                        'required' => true,
                    ],
                ],
                'submit' => [
                    'title' => $this->module->l('Save', 'AdminMondialrelayCarriersSettingsController'),
                    'name' => 'submitAddNewMondialrelayCarrier',
                    // We have to change our button id, otherwise some PS native
                    // JS script will hide it.
                    'id' => 'mondialrelay_submit-carrier-btn',
                ],
            ],
        ]];
    }

    public function initProcess()
    {
        parent::initProcess();
        if (Tools::isSubmit('submitAddNewMondialrelayCarrier')) {
            $this->action = 'addNewMondialrelayCarrier';
        }
    }

    /**
     * Add new shipping method.
     *
     * @return bool
     */
    protected function processAddNewMondialrelayCarrier()
    {
        // Validate fields
        foreach ($this->fields_form_newMondialrelayCarrier[0]['form']['input'] as $field) {
            $value = trim(Tools::getValue($field['name']));

            if (!empty($field['required']) && empty($value) && (string) $value != '0') {
                $this->errors[] = $this->module->l('Field %field% is required.', 'AdminMondialrelayCarriersSettingsController', ['%field%' => $field['label']]);
                continue;
            }
        }

        if (!empty($this->errors)) {
            return false;
        }

        // Create the handler
        $handler = new ActionsHandler();

        // Set input data
        $handler->setConveyor([
            'name' => Tools::getValue('name'),
            'delay' => Tools::getValue('delay'),
            'delivery_mode' => Tools::getValue('delivery_mode'),
            'delivery_type' => Tools::getValue('delivery_type'),
            'insurance_level' => Tools::getValue('insurance_level'),
            'weight_coeff' => Configuration::get(MondialRelay::WEIGHT_COEFF),
        ])->addActions(
            'addNativeCarrier',
            'addMondialRelayCarrierMethod',
            'setDefaultZones',
            'setDefaultRangeWeight',
            'setDefaultRangePrice',
            'setDefaultGroups'
        );

        // Process actions chain
        try {
            $processStatus = $handler->process('NewCarrier');
        } catch (Exception $e) {
            $actionsResult = $handler->getConveyor();
            if (!empty($actionsResult['errors'])) {
                $this->errors = array_merge($this->errors, $actionsResult['errors']);
            }
            $this->errors[] = $this->module->l('Could not add new carrier : %error%', 'AdminMondialrelayCarriersSettingsController', ['%error%' => $e->getMessage()]);

            // If process failed, delete native carrier if it exists
            if (!empty($actionsResult['carrier']) && Validate::isLoadedObject($actionsResult['carrier'])) {
                $actionsResult['carrier']->delete();
            }

            return false;
        }

        // Get process result, set errors if any
        $actionsResult = $handler->getConveyor();

        // If process failed, delete native carrier if it exists
        if (!$processStatus) {
            if (!empty($actionsResult['carrier']) && Validate::isLoadedObject($actionsResult['carrier'])) {
                $actionsResult['carrier']->delete();
            }
            if (!empty($actionsResult['errors'])) {
                $this->errors = array_merge($this->errors, $actionsResult['errors']);
            }

            return false;
        }

        if (!empty($actionsResult['errors'])) {
            $this->warnings = array_merge($this->warnings, $actionsResult['errors']);
        }
        $this->confirmations[] = $this->module->l('Carrier successfully created.', 'AdminMondialrelayCarriersSettingsController');

        return true;
    }

    public function getDeliveryModeLabel($delivery_mode, $data)
    {
        return $this->deliveryModesList[$delivery_mode];
    }

    public function getDeliveryTypeLabel($delivery_type, $data)
    {
        return $this->deliveryTypesList[$delivery_type];
    }

    public function getInsuranceLevelLabel($delivery_mode, $data)
    {
        return $this->insuranceLevelsList[$delivery_mode];
    }

    /**
     * Displays an "edit" link; we need it pointing to the AdminCarrierWizard
     * controller.
     *
     * Most of this code is from AdminCarriers
     *
     * @param string $token
     * @param int    $id
     * @param string $name
     *
     * @return string
     */
    public function displayEditLink($token, $id, $name)
    {
        $tpl = $this->createTemplate('helpers/list/list_action_edit.tpl');
        if (!array_key_exists('Edit', self::$cache_lang)) {
            self::$cache_lang['Edit'] = $this->module->l('Edit', 'Helper');
        }

        $tpl->assign([
            'href' => $this->context->link->getAdminLink('AdminCarrierWizard')
                . '&id_carrier=' . (int) $id
                . '&action_origin=AdminMondialrelayCarriersSettings',
            'target' => '_blank',
            'action' => $this->module->l('View / Edit', 'AdminMondialrelayCarriersSettingsController'),
            'id' => $id,
        ]);

        return $tpl->fetch();
    }

    /**
     * Displays a "delete" link; we need it pointing to the AdminCarriers
     * controller.
     *
     * Most of this code is from AdminCarriers
     *
     * @param string $token
     * @param int    $id
     * @param string $name
     *
     * @return string
     */
    public function displayDeleteLink($token, $id, $name)
    {
        $tpl = $this->createTemplate('helpers/list/list_action_delete.tpl');

        if (!array_key_exists('Delete', self::$cache_lang)) {
            self::$cache_lang['Delete'] = $this->module->l('Delete', 'Helper');
        }

        if (!array_key_exists('DeleteItem', self::$cache_lang)) {
            self::$cache_lang['DeleteItem'] = $this->module->l('Delete selected item?', 'Helper');
        }

        if (!array_key_exists('Name', self::$cache_lang)) {
            self::$cache_lang['Name'] = $this->module->l('Name:', 'Helper');
        }

        if (!is_null($name)) {
            $name = '\n\n' . self::$cache_lang['Name'] . ' ' . $name;
        }

        $data = [
            $this->identifier => $id,
            'href' => $this->context->link->getAdminLink('AdminCarriers')
                . '&id_carrier=' . (int) $id
                . '&deletecarrier=1'
                . '&action_origin=AdminMondialrelayCarriersSettings',
            'action' => self::$cache_lang['Delete'],
        ];

        if ($this->specificConfirmDelete !== false) {
            $data['confirm'] = !is_null($this->specificConfirmDelete) ? '\r' . $this->specificConfirmDelete : addcslashes(Tools::htmlentitiesDecodeUTF8(self::$cache_lang['DeleteItem'] . $name), '\'');
        }

        $tpl->assign(array_merge($this->tpl_delete_link_vars, $data));

        return $tpl->fetch();
    }
}
