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

use MondialrelayClasslib\Extensions\ProcessLogger\ProcessLoggerHandler;
use MondialrelayClasslib\Extensions\ProcessMonitor\Controllers\Front\CronController;

if (!defined('_PS_VERSION_')) {
    exit;
}

class MondialrelayOrdersStatusUpdateModuleFrontController extends CronController
{
    public function checkAccess()
    {
        if (Tools::getValue('deprecated_task') && Tools::getValue('secure_key') && Tools::getValue('secure_key') == Configuration::get(MondialRelay::DEPRECATED_SECURE_KEY)) {
            return true;
        }

        return parent::checkAccess();
    }

    public function processCron($data)
    {
        ProcessLoggerHandler::openLogger($this->processMonitor);

        if (Tools::getValue('deprecated_task')) {
            ProcessLoggerHandler::logError(
                $this->module->l('You are using a deprecated CRON url. Please note that starting from the v3.1.0 of the module, you should change the URL of your Cron task. You can still use the old Cron task in the v3.0.x of the module. For getting the new Cron task URL please check the "Advanced Settings" tab.', 'ordersStatusUpdate')
            );
        }

        try {
            if (!$this->updateOrdersStatus()) {
                ProcessLoggerHandler::logError(
                    $this->module->l('Failed to update orders.', 'ordersStatusUpdate')
                );
            }
        } catch (Exception $ex) {
            ProcessLoggerHandler::logError(sprintf(
                $this->module->l('Failed to update orders : %s', 'ordersStatusUpdate'),
                $ex->getMessage()
            ));
        }

        try {
            if (!$this->cleanUnusedRelaySelections()) {
                ProcessLoggerHandler::logError(
                    $this->module->l('Failed to clean unused relay selections.', 'ordersStatusUpdate')
                );
            }
        } catch (Exception $ex) {
            ProcessLoggerHandler::logError(sprintf(
                $this->module->l('Failed to clean unused relay selections %s', 'ordersStatusUpdate'),
                $ex->getMessage()
            ));
        }

        ProcessLoggerHandler::closeLogger();

        return $data;
    }

    /**
     * Checks if order statuses were updated from the Mondial Relay API, and
     * updates our own if needed.
     *
     * @return bool
     */
    protected function updateOrdersStatus()
    {
        ProcessLoggerHandler::openLogger($this->processMonitor);
        ProcessLoggerHandler::logInfo($this->module->l('Start updating orders...', 'ordersStatusUpdate'));

        $newOrderStateId = (int) Configuration::get(MondialRelay::OS_ORDER_DELIVERED);
        if (!$newOrderStateId) {
            ProcessLoggerHandler::logInfo(
                $this->module->l('No order status configured for delivered orders; aborting.', 'ordersStatusUpdate')
            );
            ProcessLoggerHandler::saveLogsInDb();

            return true;
        }

        $selectedRelays = MondialrelaySelectedRelay::getAllUndeliveredWithLabel();
        if (empty($selectedRelays)) {
            ProcessLoggerHandler::logInfo(
                $this->module->l('No orders to update.', 'ordersStatusUpdate')
            );
            ProcessLoggerHandler::closeLogger();

            return true;
        }

        ProcessLoggerHandler::logInfo(sprintf(
            $this->module->l('%d to check...', 'ordersStatusUpdate'),
            count($selectedRelays)
        ));

        $params = [];
        foreach ($selectedRelays as $selectedRelay) {
            $params[] = [
                'selectedRelay' => $selectedRelay,
                'Expedition' => $selectedRelay->expedition_num,
            ];
        }

        $service = MondialrelayService::getService('Order_Trace');

        // Set data
        if (!$service->init($params)) {
            foreach ($this->formatServiceErrors($params, $service->getErrors()) as $error) {
                ProcessLoggerHandler::logError($error);
            }
            ProcessLoggerHandler::closeLogger();

            return false;
        }

        // Send data
        if (!$service->send()) {
            foreach ($this->formatServiceErrors($params, $service->getErrors()) as $error) {
                ProcessLoggerHandler::logError($error);
            }
            ProcessLoggerHandler::closeLogger();

            return false;
        }

        $resultSet = $service->getResult();

        foreach ($resultSet as $key => $result) {
            $selectedRelay = $params[$key]['selectedRelay'];

            // If we failed to retrieve the order
            if (!MondialrelayServiceTracingColis::isSuccessStatCode($result->STAT)) {
                ProcessLoggerHandler::logError(sprintf(
                    $this->module->l('Order %s : API error %d : %s', 'ordersStatusUpdate'),
                    $selectedRelay->id_order,
                    $result->STAT,
                    $service->getErrorFromStatCode($result->STAT)
                ));
                continue;
            }

            if ($result->STAT == MondialrelayServiceTracingColis::STAT_CODE_DELIVERED) {
                $id_employee = MondialrelaySelectedRelay::getOrderEmployee((int) $selectedRelay->id_order);
                $history = new OrderHistory();
                $history->id_order = (int) $selectedRelay->id_order;
                $history->id_employee = (int) $id_employee;
                $history->changeIdOrderState($newOrderStateId, (int) $selectedRelay->id_order);
                $history->addWithemail();
                ProcessLoggerHandler::logInfo(sprintf(
                    $this->module->l('Order %d updated.'),
                    $selectedRelay->id_order
                ));
            }
        }

        ProcessLoggerHandler::logInfo(
            $this->module->l('Finished updating orders.', 'ordersStatusUpdate')
        );

        ProcessLoggerHandler::closeLogger();

        return true;
    }

    /**
     * Each order has its own data an errors set; so we need to assemble the two
     * to create a common errors array.
     *
     * @param type $data
     * @param type $serviceErrors
     */
    protected function formatServiceErrors($data, $serviceErrors)
    {
        $errors = [];
        $errorFormat = $this->module->l('Order %s : API response : %s', 'ordersStatusUpdate');
        foreach ($serviceErrors as $key => $errors) {
            if ($key == 'generic') {
                foreach ($errors as $error) {
                    $errors[] = $error;
                }
                continue;
            }
            $selectedRelay = $data[$key]['selectedRelay'];
            $id_order = Validate::isLoadedObject($selectedRelay) ? $selectedRelay->id_order : '?';

            foreach ($errors as $error) {
                $errors[] = sprintf(
                    $errorFormat,
                    $id_order,
                    $error
                );
            }
        }

        return $errors;
    }

    protected function cleanUnusedRelaySelections()
    {
        ProcessLoggerHandler::logInfo(
            $this->module->l('Start cleaning unused relay selections...', 'ordersStatusUpdate')
        );

        // Delete unused addresses
        // Get "old" relay selections
        $tablenameMrSelectedRelay = _DB_PREFIX_ . 'mondialrelay_selected_relay';
        $tablenamePsOrders = _DB_PREFIX_ . 'orders';
        $paramState = (int) Configuration::get(MondialRelay::OS_ORDER_DELIVERED);

        $query = "SELECT mr_sr.*
                  FROM `{$tablenameMrSelectedRelay}` mr_sr
                  LEFT JOIN `{$tablenamePsOrders}` o ON mr_sr.id_order = o.id_order
                  WHERE (
                      mr_sr.id_order IS NULL
                      AND DATE_ADD(mr_sr.date_upd, INTERVAL 1 DAY) < NOW()
                      AND (mr_sr.selected_relay_num IS NOT NULL AND mr_sr.selected_relay_num <> '')
                  ) OR o.current_state = {$paramState}"
        ;

        $selectedRelaysData = Db::getInstance()->executeS($query);
        if (empty($selectedRelaysData)) {
            ProcessLoggerHandler::logInfo(
                $this->module->l('No relay selections to remove.', 'ordersStatusUpdate')
            );

            return true;
        }

        ProcessLoggerHandler::logInfo(sprintf(
            $this->module->l('%d selections to remove...', 'ordersStatusUpdate'),
            count($selectedRelaysData)
        ));

        foreach ($selectedRelaysData as $line) {
            $selectedRelay = new MondialrelaySelectedRelay();
            $selectedRelay->hydrate($line);

            if ($selectedRelay->id_address_delivery
            && !MondialrelaySelectedRelay::isUsedRelayAddress($selectedRelay->id_address_delivery)) {
                // Delete the address
                if (Address::addressExists($selectedRelay->id_address_delivery)) {
                    $address = new Address($selectedRelay->id_address_delivery);
                    $address->delete();
                }

                // Update cart if needed
                $cart = new Cart($selectedRelay->id_cart);
                if (Validate::isLoadedObject($cart) && $cart->id_address_delivery == $selectedRelay->id_address_delivery) {
                    ProcessLoggerHandler::logInfo(sprintf(
                        $this->module->l('Reset cart %d delivery option...', 'ordersStatusUpdate'),
                        $cart->id
                    ));

                    // Set any address from customer
                    $cart->updateAddressId($cart->id_address_delivery, (int) Address::getFirstCustomerAddressId((int) $cart->id_customer));
                    // Reset delivery option
                    $cart->setDeliveryOption(null);
                    $cart->save();
                }
            }

            // Delete selection
            $selectedRelay->delete();
        }

        ProcessLoggerHandler::logInfo(
            $this->module->l('Finished cleaning unused relay selections.', 'ordersStatusUpdate')
        );

        ProcessLoggerHandler::closeLogger();

        return true;
    }
}
