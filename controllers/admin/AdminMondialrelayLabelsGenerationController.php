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

use MondialRelay\MondialRelay\Api\Client;
use MondialRelay\MondialRelay\Api\Request\GenerateLabelsRequest;
use MondialrelayClasslib\Actions\ActionsHandler;
use MondialrelayClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;

class AdminMondialrelayLabelsGenerationController extends AdminMondialrelayController
{
    public const DEFAULT_LABEL_BATCH_SIZE = 10;

    protected $with_mondialrelay_header = false;

    /** @var array see MondialrelayCarrierMethod::getInsuranceLevelsList() */
    protected $insuranceLevelsList = [];

    /**
     * {@inheritDoc}
     * Our list will always have at least one filter (order state).
     */
    protected $filter = true;

    /**
     * {@inheritDoc}
     * We don't want a link on whole lines.
     */
    protected $list_no_link = true;

    public function __construct()
    {
        $this->table = MondialrelaySelectedRelay::$definition['table'];

        $carrierMethod = new MondialrelayCarrierMethod();

        $this->className = get_class($carrierMethod);

        parent::__construct();

        $this->insuranceLevelsList = $carrierMethod->getInsuranceLevelsList();

        $this->initList();
    }

    public function init()
    {
        return parent::init();
    }

    public function initList()
    {
        $this->explicitSelect = true;

        $this->fields_list = [
            'id_order' => [
                'title' => $this->module->l('Order ID', 'AdminMondialrelayLabelsGenerationController'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'filter_key' => 'a!id_order',
            ],
            'customer' => [
                'title' => $this->module->l('Customer', 'AdminMondialrelayLabelsGenerationController'),
                'havingFilter' => true,
            ],
            'current_state' => [
                'title' => $this->module->l('Order Status', 'AdminMondialrelayLabelsGenerationController'),
                'callback' => 'getOrderStateName',
                'type' => 'select',
                'list' => array_column(OrderState::getOrderStates($this->context->language->id), 'name', 'id_order_state'),
                'filter_key' => 'o!current_state',
            ],
            'total_paid' => [
                'title' => $this->module->l('Total price', 'AdminMondialrelayLabelsGenerationController'),
                'type' => 'price',
            ],
            'total_shipping' => [
                'title' => $this->module->l('Total shipping costs', 'AdminMondialrelayLabelsGenerationController'),
                'type' => 'price',
            ],
            'date_add' => [
                'title' => $this->module->l('Date', 'AdminMondialrelayLabelsGenerationController'),
                'filter_key' => 'o!date_add',
                'type' => 'date',
            ],
            'package_weight' => [
                'title' => $this->module->l('Weight (grams)', 'AdminMondialrelayLabelsGenerationController'),
            ],
            'insurance_level' => [
                'title' => $this->module->l('Insurance', 'AdminMondialrelayLabelsGenerationController'),
                'callback' => 'getInsuranceLevelLabel',
                'filter_key' => 'a!insurance_level',
            ],
            'selected_relay_num' => [
                'title' => $this->module->l('MR Number', 'AdminMondialrelayLabelsGenerationController'),
            ],
            'selected_relay_country_iso' => [
                'title' => $this->module->l('MR Country', 'AdminMondialrelayLabelsGenerationController'),
            ],
        ];

        // Build the query
        $this->_select .= 'a.' . MondialrelaySelectedRelay::$definition['primary'] . ', '
            . 'o.id_currency, '
            . 'os_l.name AS os_name, '
            . 'CONCAT(c.firstname, " ", c.lastname) AS `customer`';
        $this->_join .=
            'INNER JOIN `' . _DB_PREFIX_ . MondialrelayCarrierMethod::$definition['table'] . '` mr_cm '
                . 'ON mr_cm.id_mondialrelay_carrier_method = a.id_mondialrelay_carrier_method '
            . 'INNER JOIN `' . _DB_PREFIX_ . Order::$definition['table'] . '` o ON o.id_order = a.id_order '
            . 'INNER JOIN `' . _DB_PREFIX_ . OrderState::$definition['table'] . '_lang` os_l '
                . 'ON os_l.id_order_state = o.current_state AND os_l.id_lang = ' . (int) $this->context->language->id
            . ' LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = o.`id_customer`)';
        $this->_where .= 'AND (a.expedition_num = "" OR a.expedition_num IS NULL) ';
        $this->_where .= 'AND o.id_shop IN (' . implode(', ', Shop::getContextListShopID()) . ') ';

        // Per-row actions
        $this->actions_available[] = 'generate';
        $this->actions = ['generate'];

        // Bulk actions
        $this->bulk_actions = [
            'generateSelectionLabels' => [
                'text' => $this->module->l('Generate labels for selected orders', 'AdminMondialrelayLabelsGenerationController'),
                'icon' => '',
            ],
        ];
    }

    public function getOrderStateName($id_order_state, $data)
    {
        return $data['os_name'];
    }

    public function getInsuranceLevelLabel($insurance_level, $data)
    {
        return $this->insuranceLevelsList[$insurance_level];
    }

    /**
     * Display "generate" and "edit" action link side by side.
     *
     * @see HelperList::displayListContent
     */
    public function displayGenerateLink($token, $id, $name = null)
    {
        $tpl = $this->helper->createTemplate('list_actions.tpl');
        if (!array_key_exists('Generate', HelperList::$cache_lang)) {
            HelperList::$cache_lang['Generate'] = $this->module->l('Generate', 'AdminMondialrelayLabelsGenerationController');
        }

        $tpl->assign([
            'href_generate' => $this->context->link->getAdminLink('AdminMondialrelayLabelsGeneration')
                    . '&generateLabel&' . MondialrelaySelectedRelay::$definition['primary'] . '=' . $id,
            'action' => HelperList::$cache_lang['Generate'],
            'id' => $id,
            'href_edit' => $this->context->link->getAdminLink('AdminMondialrelaySelectedRelay')
                . '&' . $this->helper->identifier . '=' . $id . '&update' . $this->helper->table
                . '&back=' . urlencode($this->context->link->getAdminLink('AdminMondialrelayLabelsGeneration')),
        ]);

        return $tpl->fetch();
    }

    public function setHelperDisplay(Helper $helper)
    {
        parent::setHelperDisplay($helper);
        switch (get_class($this->helper)) {
            case 'HelperList':
                $this->tpl_list_vars['original_content'] = $this->helper->base_folder . 'list_content.tpl';
                $this->tpl_list_vars['view_order_url'] = $this->context->link->getAdminLink('AdminOrders')
                    . '&vieworder&id_order=';
                break;
        }
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        $this->page_header_toolbar_title = $this->module->l('Labels Generation', 'AdminMondialrelayLabelsGenerationController');
    }

    public function initContent()
    {
        $this->informations[] = $this->module->l('You can create labels for all Mondial relay orders in selected status. You can select multiple lines or all orders if you want. Please use bulk actions for selection and labels generations. To see the labels History please go to the Labels History tab.', 'AdminMondialrelayLabelsGenerationController');

        return parent::initContent();
    }

    public function processFilter()
    {
        $filterOrderState = (int) Configuration::get(MondialRelay::OS_DISPLAY_LABEL);
        if ($filterOrderState) {
            $this->setDefaultFilter('o!current_state', $filterOrderState);
        }

        return parent::processFilter();
    }

    public function initProcess()
    {
        parent::initProcess();
        if (Tools::isSubmit('generateLabel')) {
            $this->action = 'generateLabel';
        }
    }

    public function ajaxProcessGenerateLabel()
    {
        $this->processGenerateLabel();
    }

    /**
     * Prepare (get from DB) and send (return successes / errors), for a single
     * order.
     */
    public function processGenerateLabel()
    {
        if (Configuration::get(MondialRelay::HOME_DELIVERY)) {
            return $this->generateLabelApi2();
        }

        return $this->generateLabelApi1();
    }

    /**
     * Prepare (get from DB) and send (return successes / errors), for a single
     * order.
     */
    public function generateLabelApi1()
    {
        $selectedRelay = new MondialrelaySelectedRelay(
            Tools::getValue(MondialrelaySelectedRelay::$definition['primary'])
        );

        if (!Validate::isLoadedObject($selectedRelay)) {
            $this->errors[] = Tools::displayError('The object cannot be loaded (or found)');

            return false;
        }

        if (!$selectedRelay->selected_relay_num) {
            $this->errors[] = $this->module->l('To collect the labels for home delivery, please provide the API2 identifiers Api 2', 'AdminMondialrelayLabelsGenerationController', ['href' => $this->context->link->getAdminLink('AdminMondialrelayAccountSettings') . '#mondialrelay_requirements-results', 'target' => 'blank']);

            return false;
        }

        if ($selectedRelay->selected_relay_country_iso == 'PL') {
            $this->errors[] = $this->module->l('To collect the labels for Poland delivery, please provide the API2 identifiers Api 2', 'AdminMondialrelayLabelsGenerationController', ['href' => $this->context->link->getAdminLink('AdminMondialrelayAccountSettings') . '#mondialrelay_requirements-results', 'target' => 'blank']);

            return false;
        }

        if (true !== ($error = $this->validateGeneration($selectedRelay))) {
            $this->errors[] = $error;

            return false;
        }

        // Create the handler
        $handler = new ActionsHandler();

        // Set input data
        $handler->setConveyor([
            // The Actions chain was designed with bulk actions in mind, so we
            // have to pass an array
            'order_ids' => [$selectedRelay->id_order],
        ]);

        // Set actions to execute
        $handler->addActions('prepareData', 'generateLabels', 'sendTrackingEmails');

        // Process actions chain
        try {
            $handler->process('GenerateLabels');
        } catch (Exception $e) {
            $this->errors[] = sprintf(
                $this->module->l('Could not generate label : %s', 'AdminMondialrelayLabelsGenerationController'),
                $e->getMessage()
            );
            $this->warnings[] = $this->module->l('Please Check requirements in Help tab to verify if all settings are OK.', 'AdminMondialrelayLabelsGenerationController');

            $actionsResult = $handler->getConveyor();
            if (!empty($actionsResult['errors'])) {
                $this->errors = array_merge($this->errors, $actionsResult['errors']);
            }

            return false;
        }

        // Get process result, set errors if any
        $actionsResult = $handler->getConveyor();
        if (!empty($actionsResult['errors'])) {
            $this->errors[] = $this->module->l('Could not generate label.', 'AdminMondialrelayLabelsGenerationController');
            $this->errors = array_merge($this->errors, $actionsResult['errors']);
            $this->warnings[] = $this->module->l('Please Check requirements in Help tab to verify if all settings are OK.', 'AdminMondialrelayLabelsGenerationController');

            return false;
        }
        $this->confirmations[] = $this->module->l('Label generated; see the "Labels History" tab to download it.', 'AdminMondialrelayLabelsGenerationController');
    }

    /**
     * Undocumented function.
     *
     * @return bool
     */
    public function generateLabelApi2()
    {
        $selectedRelays = ($this->boxes) ? $this->boxes : [Tools::getValue(MondialrelaySelectedRelay::$definition['primary'])];
        $batches = array_chunk($selectedRelays, self::DEFAULT_LABEL_BATCH_SIZE);

        $response = [
            'success' => false,
            'nb_orders' => sizeof($selectedRelays),         // NB de commande a traiter
            'nb_labels' => 0,                               // NB d'étiquettes générées
            'batches' => [
                'nb_total' => sizeof($batches),
                'nb_success' => 0,
                'nb_errors' => 0,
            ],
        ];

        try {
            ProcessLoggerHandler::logInfo($this->module->l('Start of label generation.', 'GenerateLabelsActions'));

            // 1. Vérification de la configuration de la boutique.
            //
            if (!$this->checkShopAddress()) {
                throw new Exception($this->module->l('Please Check requirements in Help tab to verify if all settings are OK.', 'AdminMondialrelayLabelsGenerationController'));
            }

            // 2. Traitement des lots d'etiquettes a générés
            //
            $msgBatchStart = $this->module->l('Start of processing batch number %s on %s.', 'GenerateLabelsActions');
            $msgBatchEnd = $this->module->l('End of processing for batch number %s (%s labels generated / %s orders).', 'GenerateLabelsActions');

            foreach ($batches as $b => $batch) {
                $num = $b + 1;

                ProcessLoggerHandler::logInfo(sprintf($msgBatchStart, $num, $response['batches']['nb_total']));

                $labelGenerationBatch = $this->processLabelGenerationBatch($batch);
                $response['nb_labels'] += $labelGenerationBatch['nb_label_generated'];

                // 2.1 Traitement du lot réalisé avec succes.
                //
                if ($labelGenerationBatch['success'] === true) {
                    $msg = sprintf($msgBatchEnd, $num, $labelGenerationBatch['nb_label_generated'], sizeof($batch));

                    if ($labelGenerationBatch['nb_label_generated'] === sizeof($batch)) {
                        ProcessLoggerHandler::logSuccess($msg);
                    } else {
                        // Succes partiel.
                        ProcessLoggerHandler::logError($msg);
                    }

                    $response['batches']['nb_success']++;

                    continue;
                }

                // 2.2 Traitement commande par commande du lot avec les erreurs.
                //
                ProcessLoggerHandler::logError($this->module->l('One or more errors occurred while processing this batch.', 'GenerateLabelsActions'));

                $response['batches']['nb_errors']++;

                $nbLabels = 0;

                foreach ($batch as $idRelay) {
                    $labelGeneration = $this->processLabelGenerationBatch([$idRelay]);

                    $mondialrelaySelectedRelay = new MondialrelaySelectedRelay($idRelay);
                    $orderId = $mondialrelaySelectedRelay->id_order;

                    $nbLabels += $labelGeneration['nb_label_generated'];

                    if (!$labelGeneration['success']) {
                        foreach ($labelGeneration['errors'] as $error) {
                            $e = "{$this->module->l('Order ID', 'AdminMondialrelayLabelsGenerationController')} {$orderId} - {$error}";

                            $this->errors[] = $e;
                            ProcessLoggerHandler::logError($e);
                        }
                    } else {
                        $m = "{$this->module->l('Order ID', 'AdminMondialrelayLabelsGenerationController')} {$orderId} - success";
                        ProcessLoggerHandler::logSuccess($m);
                    }
                }

                $response['nb_labels'] += $nbLabels;

                ProcessLoggerHandler::logError(sprintf($msgBatchEnd, $num, $nbLabels, sizeof($batch)));
            }
        } catch (Throwable $e) {
            $message = $this->module->l('Error', 'GenerateLabelsActions');
            $message = "{$message} : {$e->getMessage()}";

            ProcessLoggerHandler::logError($message);

            $this->errors[] = $message;
        } finally {
            // 3. Gestion du résultat du traitements
            //
            $msgResult = $this->module->l('%d/%d labels generated.', 'GenerateLabelsActions');
            $msgResult = sprintf($msgResult, $response['nb_labels'], $response['nb_orders']);

            if ($response['nb_labels'] === $response['nb_orders']) {
                $response['success'] = true;

                $this->confirmations[] = $this->module->l('Label generated; see the "Labels History" tab to download it.', 'AdminMondialrelayLabelsGenerationController');

                ProcessLoggerHandler::logSuccess("{$this->module->l('All labels generated.', 'GenerateLabelsActions')} ({$msgResult})");
            } else {
                ProcessLoggerHandler::logError($msgResult);
            }

            // Fin du traitement
            //
            ProcessLoggerHandler::logInfo($this->module->l('End of label generation', 'GenerateLabelsActions'));
            ProcessLoggerHandler::saveLogsInDb();
        }

        return $response['success'];
    }

    /**
     * Undocumented function.
     *
     * @return array
     */
    protected function checkMondialrelaySelectedRelay(array $selectedRelays)
    {
        $errorMsg = $this->module->l('The MondialrelaySelectedRelay object %s cannot be loaded (or found)');
        $orders = [];

        foreach ($selectedRelays as $idRelay) {
            $selectedRelay = new MondialrelaySelectedRelay($idRelay);

            if (!$this->checkRelay($selectedRelay)) {
                $this->errors[] = Tools::displayError(sprintf($errorMsg, $idRelay));
            } else {
                $orders[] = $selectedRelay->id_order;
            }
        }

        return $orders;
    }

    protected function processLabelGenerationBatch(array $selectedRelays)
    {
        $response = [
            'success' => false,
            'nb_label_generated' => 0,
            'errors' => [],
        ];

        // 1. Génération des étiquettes Mondial Relay.
        $orders = $this->checkMondialrelaySelectedRelay($selectedRelays);
        $labels = $this->generateLabels($orders, $response['errors']);

        $response['nb_label_generated'] = (!is_countable($labels)) ? 0 : count($labels);

        // 2. Traitement post-process.
        $handler = new ActionsHandler();
        $handler->setConveyor(['updatedOrders' => $labels]);
        $handler->addActions('sendTrackingEmails');
        $handler->process('GenerateLabels');

        $actionsResult = $handler->getConveyor();

        if (!empty($actionsResult['errors'])) {
            $response['errors'] = array_merge($response['errors'], $actionsResult['errors']);
        } else {
            $response['success'] = true;
        }

        return $response;
    }

    protected function generateLabels(array $selectedOrders, &$errors = [])
    {
        $shipmentData = [];

        if (empty($selectedOrders)) {
            // $this->errors[] = $this->module->l('No data to prepare.', 'GenerateLabelsActions');
        } else {
            $mondialRelayClient = new Client();

            $request = new GenerateLabelsRequest($mondialRelayClient, $selectedOrders);
            $result = $request->execute();

            $this->errors = array_merge($this->errors, $result->getErrors());
            $errors = $result->getErrors();

            $response = $result->getResponse()->getClientResponse();

            $dom = new DOMDocument();
            $dom->loadXML($response);

            $shipmentsList = $dom->getElementsByTagName('ShipmentsList');
            $statusList = $dom->getElementsByTagName('StatusList');

            if ($shipmentsList->length) {
                $shipments = $shipmentsList->item(0)->getElementsByTagName('Shipment');
                $shipmentData = $this->setShipmentInformations($shipments);
            }

            if ($statusList->length) {
                $status = $statusList->item(0)->getElementsByTagName('Status');
                $this->getShipmentError($status);
            }
        }

        return $shipmentData;
    }

    public function getShipmentError($statusList)
    {
        foreach ($statusList as $status) {
            $error = $status->getAttribute('Code') . ' - ' . $status->getAttribute('Message');
            if ($status->getAttribute('Code') == 0 || $status->getAttribute('Level') == 'Warning') {
                $this->warnings[] = $error;
            } else {
                $this->errors[] = $error;
            }
        }

        return true;
    }

    public function setShipmentInformations($shipments)
    {
        $shipmentData = [];
        $referenceExterne = null;

        foreach ($shipments as $shipment) {
            $shipmentNumber = $shipment->getAttribute('ShipmentNumber');

            $labelElements = $shipment->getElementsByTagName('LabelValues');
            foreach ($labelElements as $labelElement) {
                if ($labelElement->getAttribute('Key') === 'MR.Expedition.ReferenceExterne') {
                    $referenceExterne = $labelElement->getAttribute('Value');
                    break;
                }
            }

            $outputElement = $shipment->getElementsByTagName('Output')->item(0);
            if ($outputElement) {
                $labelUrl = $outputElement->textContent;
            } else {
                $labelUrl = '';
            }

            $this->setSelectedRelay($referenceExterne, $labelUrl, $shipmentNumber);

            if (!$this->checkOrderValidity($referenceExterne, $shipmentNumber)) {
                continue;
            }

            $this->updateOrderStatus($referenceExterne);

            $shipmentData[] = new Order($referenceExterne);
        }

        return $shipmentData;
    }

    public function updateOrderStatus($orderId)
    {
        $order = new Order($orderId);

        // Change order state
        $newOrderStateId = (int) Configuration::get(MondialRelay::OS_LABEL_GENERATED, null, null, $order->id_shop);
        if ($newOrderStateId) {
            $employee = Context::getContext()->employee;
            $orderHistory = new OrderHistory();
            $orderHistory->id_order = $order->id;
            $orderHistory->id_employee = ($employee && $employee->id) ? $employee->id : MondialrelaySelectedRelay::getOrderEmployee((int) $order->id);
            $orderHistory->changeIdOrderState($newOrderStateId, $order->id);
            $orderHistory->addWithemail();
        }
    }

    public function checkOrderValidity($OrderId, $trackingNumber)
    {
        $order = new Order($OrderId);
        if (!Validate::isLoadedObject($order)) {
            $error = sprintf(
                $this->module->l('A label was generated, but the PrestaShop order to update was not found : %s', 'GenerateLabelsActions'),
                $OrderId
            );

            ProcessLoggerHandler::logError($error);
            $this->errors[] = $error;

            return false;
        }

        $orderCarrier = new OrderCarrier($order->getIdOrderCarrier());
        if (!Validate::isLoadedObject($orderCarrier)) {
            $error = sprintf(
                $this->module->l('A label was generated, but the PrestaShop order_carrier to update was not found : %s', 'GenerateLabelsActions'),
                $OrderId
            );

            ProcessLoggerHandler::logError($error);
            $this->errors[] = $error;
        } else {
            $orderCarrier->tracking_number = $trackingNumber;
            $orderCarrier->save();
        }

        return true;
    }

    public function setSelectedRelay($orderId, $labelUrl, $expeditionNum)
    {
        $selectedRelay = MondialrelaySelectedRelay::getFromIdCart((new Order($orderId))->id_cart);
        $selectedRelay->label_url = $labelUrl;
        $selectedRelay->expedition_num = $expeditionNum;

        $selectedRelay->setTrackingUrl(
            Configuration::get(MondialRelay::WEBSERVICE_ENSEIGNE),
            Configuration::get(MondialRelay::WEBSERVICE_BRAND_CODE),
            Configuration::get('PS_LANG_DEFAULT'),
            Configuration::get(MondialRelay::WEBSERVICE_KEY)
        );

        $selectedRelay->date_label_generation = date('Y-m-d H:i:s');

        $selectedRelay->save();
    }

    public function checkRelay($selectedRelay)
    {
        if (!Validate::isLoadedObject($selectedRelay)) {
            $this->errors[] = Tools::displayError('The object cannot be loaded (or found)');

            return false;
        }

        if (true !== ($error = $this->validateGeneration($selectedRelay))) {
            $this->errors[] = $error;

            return false;
        }

        return true;
    }

    public function checkShopAddress()
    {
        $service = MondialrelayService::getService('Label_Generation');
        if (!$service->checkExpeAddress()) {
            $errors = $service->getErrors();
            foreach ($errors['generic'] as $error) {
                ProcessLoggerHandler::logError($error);
                $this->errors[] = $error;
            }
            ProcessLoggerHandler::saveLogsInDb();

            return false;
        }

        return true;
    }

    public function processBulkGenerateSelectionLabels()
    {
        if (Configuration::get(MondialRelay::HOME_DELIVERY)) {
            return $this->generateLabelApi2();
        }

        return $this->BulkGenerateSelectionLabelsApi1();
    }

    /**
     * Prepare (get from DB) and send (return successes / errors), for multiple
     * orders.
     */
    public function BulkGenerateSelectionLabelsApi1()
    {
        $selectionIds = $this->boxes;

        $orderIds = [];
        $errorFormat = $this->module->l('Order %s : %s', 'AdminMondialrelayLabelsGenerationController');
        foreach ($selectionIds as $id_mondialrelay_selected_relay) {
            $selectedRelay = new MondialrelaySelectedRelay($id_mondialrelay_selected_relay);
            if (!Validate::isLoadedObject($selectedRelay)) {
                $this->errors[] = Tools::displayError(sprintf(
                    'The MondialrelaySelectedRelay object %s cannot be loaded (or found)',
                    $id_mondialrelay_selected_relay
                ));
                continue;
            }

            if (!$selectedRelay->selected_relay_num) {
                $this->errors[] = $this->module->l('To collect the labels for home delivery, please provide the API2 identifiers Api 2', 'AdminMondialrelayLabelsGenerationController', ['href' => $this->context->link->getAdminLink('AdminMondialrelayAccountSettings') . '#mondialrelay_requirements-results', 'target' => 'blank']);

                continue;
            }

            if (true !== ($error = $this->validateGeneration($selectedRelay))) {
                $this->errors[] = sprintf(
                    $errorFormat,
                    $selectedRelay->id_order,
                    $error
                );
                continue;
            }

            $orderIds[] = $selectedRelay->id_order;
        }

        // Create the handler
        $handler = new ActionsHandler();

        // Set input data
        $handler->setConveyor([
            // The Actions chain was designed with bulk actions in mind, so we
            // have to pass an array
            'order_ids' => $orderIds,
        ]);

        // Set actions to execute
        $handler->addActions('prepareData', 'generateLabels', 'sendTrackingEmails');

        // Process actions chain
        try {
            $handler->process('GenerateLabels');
        } catch (Exception $e) {
            $this->errors[] = sprintf(
                $this->module->l('Could not generate label : %s', 'AdminMondialrelayLabelsGenerationController'),
                $e->getMessage()
            );

            $actionsResult = $handler->getConveyor();

            if (!empty($actionsResult['errors'])) {
                $this->errors = array_merge($this->errors, $actionsResult['errors']);
            }

            $this->warnings[] = $this->module->l('Please Check requirements in Help tab to verify if all settings are OK.', 'AdminMondialrelayLabelsGenerationController');

            return false;
        }

        // Get process result, set errors if any
        $actionsResult = $handler->getConveyor();
        if (!empty($actionsResult['errors'])) {
            $this->errors = array_merge($this->errors, $actionsResult['errors']);
            $this->warnings[] = $this->module->l('Please Check requirements in Help tab to verify if all settings are OK.', 'AdminMondialrelayLabelsGenerationController');

            return false;
        }
        $this->confirmations[] = $this->module->l('Labels generated; see the "Labels History" tab to download them.', 'AdminMondialrelayLabelsGenerationController');
    }

    /**
     * Basic validation before generating a label.
     *
     * @param MondialrelaySelectedRelay $selectedRelay
     *
     * @return string|true
     */
    public function validateGeneration($selectedRelay)
    {
        if ($selectedRelay->expedition_num) {
            return $this->module->l('A label was already generated for this order.', 'AdminMondialrelayLabelsGenerationController');
        }

        if (!$selectedRelay->id_order || !Validate::isLoadedObject(new Order($selectedRelay->id_order))) {
            return $this->module->l('This MR order has no associated PrestaShop order.', 'AdminMondialrelayLabelsGenerationController');
        }

        if (!$selectedRelay->package_weight || $selectedRelay->package_weight < MondialRelay::MINIMUM_PACKAGE_WEIGHT) {
            return sprintf(
                $this->module->l('You must set a weight for the order (15 grams minimum).', 'AdminMondialrelayLabelsGenerationController'),
                MondialRelay::MINIMUM_PACKAGE_WEIGHT
            );
        }

        return true;
    }
}
