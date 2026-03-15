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

namespace MondialRelay\MondialRelay\Api\Builder\Model;

use MondialRelay\MondialRelay\Api\Builder\Collection\ParcelCollectionBuilder;
use MondialRelay\MondialRelay\Api\Model\Address;
use MondialRelay\MondialRelay\Api\Model\Mode;
use MondialRelay\MondialRelay\Api\Model\Option;
use MondialRelay\MondialRelay\Api\Model\Parcel;
use MondialRelay\MondialRelay\Api\Model\Value;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class ShipmentsListBuilder.
 *
 * @author Fabien Sigrand <contact@scaledev.fr>
 */
final class ShipmentsListBuilder
{
    /** @var string */
    private $orderNo;

    /** @var string */
    private $customerNo;

    /** @var int */
    private $parcelCount;

    /** @var Mode */
    private $deliveryMode;

    /** @var Mode */
    private $collectionMode;

    /** @var ParcelCollectionBuilder */
    private $parcels;

    /** @var string */
    private $deliveryInstruction;

    /** @var Address */
    private $sender;

    /** @var Address */
    private $recipient;

    /** @var Option */
    private $option;

    /** @var int */
    private $length;

    /** @var int */
    private $width;

    /** @var int */
    private $height;

    public function build($orderId)
    {
        $module = \Module::getInstanceByName('mondialrelay');
        $order = new \Order($orderId);
        if (!\Validate::isLoadedObject($order)) {
            return sprintf(
                $module->l('Could not retrieve order from id : %s', 'GenerateLabelsActions'),
                $orderId
            );
        }

        $carrierMethod = \MondialrelayCarrierMethod::getFromNativeCarrierId($order->id_carrier);
        if (!\Validate::isLoadedObject($carrierMethod)) {
            return sprintf(
                $module->l('Order %s : Could not find Mondial Relay carrier method.', 'GenerateLabelsActions'),
                $orderId
            );
        }

        $selectedRelay = \MondialrelaySelectedRelay::getFromIdCart($order->id_cart);
        if (!\Validate::isLoadedObject($selectedRelay)) {
            return sprintf(
                $module->l('Order %s : Could not find Mondial Relay order.', 'GenerateLabelsActions'),
                $orderId
            );
        }

        if ($selectedRelay->selected_relay_country_iso === 'PL') {
            $weightKg = $selectedRelay->package_weight;

            if ($weightKg <= 5) {
                $selectedRelay->height = 8;
                $selectedRelay->width = 38;
                $selectedRelay->length = 64;
            } elseif ($weightKg <= 15) {
                $selectedRelay->height = 19;
                $selectedRelay->width = 38;
                $selectedRelay->length = 64;
            } else {
                $selectedRelay->height = 38;
                $selectedRelay->width = 39;
                $selectedRelay->length = 64;
            }
        }

        $address = new \Address($order->id_address_delivery);
        if (!\Validate::isLoadedObject($address)) {
            return sprintf(
                $module->l('Order %s : Could not find delivery address.', 'GenerateLabelsActions'),
                $orderId
            );
        }

        $customer = new \Customer($order->id_customer);
        if (!\Validate::isLoadedObject($customer)) {
            return sprintf(
                $module->l('Order %s : Could not find customer.', 'GenerateLabelsActions'),
                $orderId
            );
        }

        $service = \MondialrelayService::getService('Label_Generation');
        if (!$service->checkExpeAddress()) {
            return sprintf(
                $module->l('Order %s : Shop address is not valid.', 'GenerateLabelsActions'),
                $orderId
            );
        }

        $this->setOrderNo($order->id);
        $this->setCustomerNo($order->getCustomer()->id);

        $deliveryMode = (new Mode())->setMode($this->getWebserviceModeLiv($carrierMethod->delivery_mode));
        $collectionMode = (new Mode())->setMode('REL');

        if ($carrierMethod->insurance_level) {
            $this->setOption(
                (new Option())
                    ->setKey('ASS')
                    ->setValue($carrierMethod->insurance_level)
            );
        }

        if ($deliveryMode->getMode() == '24R' || $deliveryMode->getMode() == '24L') {
            $deliveryMode->setLocation($selectedRelay->selected_relay_country_iso . '-' . $selectedRelay->selected_relay_num);
        }

        $parcels = new ParcelCollectionBuilder();
        $weightValue = (new Value())->setValue($selectedRelay->package_weight);
        $parcel = (new Parcel())->setWeight($weightValue);
        if ($selectedRelay->selected_relay_country_iso === 'PL') {
            $parcel->setLength($selectedRelay->length);
            $parcel->setWidth($selectedRelay->width);
            $parcel->setDepth($selectedRelay->height);
        }
        $parcels->addElement($parcel);

        $this->setParcelCount($parcels->getCount());

        $this->setParcels($parcels);

        if ($carrierMethod->needsRelay()) {
            $streetname = $selectedRelay->selected_relay_adr3;
            $addressAdd1 = $selectedRelay->selected_relay_adr1;

            if ($selectedRelay->selected_relay_country_iso === 'PL') {
                $streetname = $addressAdd1;
                $addressAdd1 = '';
            }

            $recipient = (new Address())
                ->setLastname($address->lastname)
                ->setFirstname($address->firstname)
                ->setStreetname($streetname)
                ->setCountryCode($selectedRelay->selected_relay_country_iso)
                ->setPostcode($selectedRelay->selected_relay_postcode)
                ->setCity($selectedRelay->selected_relay_city)
                ->setAddressAdd1($addressAdd1)
                ->setAddressAdd2($selectedRelay->selected_relay_adr2)
                ->setAddressAdd3($selectedRelay->selected_relay_adr3)
                ->setPhoneNo($address->phone)
                ->setMobileNo($address->phone_mobile)
                ->setEmail($customer->email)
            ;
        } else {
            $countryCode = \Country::getIsoById($address->id_country);
            $streetname = \Tools::replaceAccentedChars(\Tools::substr($address->address1, 0, 30));
            $addressAdd1 = \Tools::replaceAccentedChars(\Tools::substr($address->address1, 0, 30));

            if ($countryCode === 'PL') {
                $streetname = $addressAdd1;
                $addressAdd1 = '';
            }

            $recipient = (new Address())
                ->setLastname($address->lastname)
                ->setFirstname($address->firstname)
                ->setStreetname($streetname)
                ->setCountryCode($countryCode)
                ->setPostcode($address->postcode)
                ->setCity(\Tools::replaceAccentedChars($address->city))
                ->setAddressAdd1($addressAdd1)
                ->setAddressAdd2(\Tools::replaceAccentedChars(\Tools::substr($address->address2, 0, 30)))
                ->setAddressAdd3(\Tools::replaceAccentedChars(\Tools::substr($address->company, 0, 30)))
                ->setPhoneNo($address->phone)
                ->setMobileNo($address->phone_mobile)
                ->setEmail($customer->email)
            ;
        }

        $this->setCollectionMode($collectionMode);
        $this->setDeliveryMode($deliveryMode);
        $this->setDeliveryInstruction($order->getFirstMessage());

        $sender = (new Address())
            ->setStreetname(\Tools::replaceAccentedChars(\Configuration::get('PS_SHOP_ADDR1')))
            ->setCountryCode(\Country::getIsoById(\Configuration::get('PS_SHOP_COUNTRY_ID')))
            ->setPostcode(\Configuration::get('PS_SHOP_CODE'))
            ->setCity(\Tools::replaceAccentedChars(\Configuration::get('PS_SHOP_CITY')))
            ->setAddressAdd1(\Tools::replaceAccentedChars(\Configuration::get('PS_SHOP_NAME')))
            ->setAddressAdd3(\Tools::replaceAccentedChars(\Configuration::get('PS_SHOP_ADDR1')))
            ->setPhoneNo(\MondialRelayTools::getFormattedPhonenumber(\Configuration::get('PS_SHOP_PHONE')))
            ->setEmail(\Configuration::get('PS_SHOP_EMAIL'))
        ;

        $this->setSender($sender);
        $this->setRecipient($recipient);

        return true;
    }

    public function getXmlData($dom)
    {
        $shipment = $dom->createElement('Shipment');
        $options = $dom->createElement('Options');
        $optionSource = $dom->createElement('Option');
        $keyAttribute = $dom->createAttribute('Key');
        $keyAttribute->value = 'Source';
        $valueAttribute = $dom->createAttribute('Value');
        $valueAttribute->value = 'PRESTASHOP';
        $optionSource->appendChild($keyAttribute);
        $optionSource->appendChild($valueAttribute);
        $options->appendChild($optionSource);
        $shipment->appendChild($options);
        $shipment->appendChild($dom->createElement('OrderNo', $this->getOrderNo()));
        $shipment->appendChild($dom->createElement('CustomerNo', $this->getCustomerNo()));
        $shipment->appendChild($dom->createElement('ParcelCount', $this->getParcelCount()));
        if ($this->getParcels()) {
            $shipment->appendChild($this->getParcels()->getXmlDatas($dom));
        }

        $deliveryMode = $dom->createElement('DeliveryMode');
        $mode = $dom->createAttribute('Mode');
        $mode->value = $this->getDeliveryMode()->getMode() ?: '';
        $location = $dom->createAttribute('Location');
        $location->value = $this->getDeliveryMode()->getLocation() ?: '';
        $deliveryMode->appendChild($mode);
        $deliveryMode->appendChild($location);
        $shipment->appendChild($deliveryMode);

        $collectionMode = $dom->createElement('CollectionMode');
        $mode = $dom->createAttribute('Mode');
        $mode->value = $this->getCollectionMode()->getMode() ?: '';
        $location = $dom->createAttribute('Location');
        $location->value = $this->getCollectionMode()->getLocation() ?: '';
        $collectionMode->appendChild($mode);
        $collectionMode->appendChild($location);
        $shipment->appendChild($collectionMode);

        $option = $dom->createElement('Option');
        $key = $dom->createAttribute('Key');
        $key->value = $this->getOption() ? $this->getOption()->getKey() : '';
        $value = $dom->createAttribute('Value');
        $value->value = $this->getOption() ? $this->getOption()->getValue() : '';

        $option->appendChild($key);
        $option->appendChild($value);
        $shipment->appendChild($option);

        $dom->createElement('DeliveryInstruction', $this->getDeliveryInstruction());

        $sender = $dom->createElement('Sender');
        $sender->appendChild($this->getSender()->getXmlData($dom));

        $recipient = $dom->createElement('Recipient');
        $recipient->appendChild($this->getRecipient()->getXmlData($dom));

        $shipment->appendChild($sender);
        $shipment->appendChild($recipient);

        return $shipment;
    }

    /**
     * Get the value of recipient.
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * Set the value of recipient.
     *
     * @return self
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * Get the value of sender.
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set the value of sender.
     *
     * @return self
     */
    public function setSender($sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get the value of deliveryInstruction.
     */
    public function getDeliveryInstruction()
    {
        return $this->deliveryInstruction;
    }

    /**
     * Set the value of deliveryInstruction.
     *
     * @return self
     */
    public function setDeliveryInstruction($deliveryInstruction)
    {
        if (preg_match('/^[0-9a-zA-Z_\-\'.,\/ ]{0,30}$/', $deliveryInstruction)) {
            $this->deliveryInstruction = $deliveryInstruction;
        }

        return $this;
    }

    /**
     * Get the value of parcels.
     */
    public function getParcels()
    {
        return $this->parcels;
    }

    /**
     * Set the value of parcels.
     *
     * @return self
     */
    public function setParcels($parcels)
    {
        $this->parcels = $parcels;

        return $this;
    }

    /**
     * Get the value of collectionMode.
     */
    public function getCollectionMode()
    {
        return $this->collectionMode;
    }

    /**
     * Set the value of collectionMode.
     *
     * @return self
     */
    public function setCollectionMode($collectionMode)
    {
        $this->collectionMode = $collectionMode;

        return $this;
    }

    /**
     * Get the value of deliveryMode.
     */
    public function getDeliveryMode()
    {
        return $this->deliveryMode;
    }

    /**
     * Set the value of deliveryMode.
     *
     * @return self
     */
    public function setDeliveryMode($deliveryMode)
    {
        $this->deliveryMode = $deliveryMode;

        return $this;
    }

    /**
     * Get the value of parcelCount.
     */
    public function getParcelCount()
    {
        return $this->parcelCount;
    }

    /**
     * Set the value of parcelCount.
     *
     * @return self
     */
    public function setParcelCount($parcelCount)
    {
        if (preg_match('/^[0-9]{1,2}$/', $parcelCount)) {
            $this->parcelCount = $parcelCount;
        }

        return $this;
    }

    /**
     * Get the value of customerNo.
     */
    public function getCustomerNo()
    {
        return $this->customerNo;
    }

    /**
     * Set the value of customerNo.
     *
     * @return self
     */
    public function setCustomerNo($customerNo)
    {
        if (preg_match('/^(|[0-9AZ]{0,9})$/', $customerNo)) {
            $this->customerNo = $customerNo;
        }

        return $this;
    }

    /**
     * Get the value of orderNo.
     */
    public function getOrderNo()
    {
        return $this->orderNo;
    }

    /**
     * Set the value of orderNo.
     *
     * @return self
     */
    public function setOrderNo($orderNo)
    {
        if (preg_match('/^(|[0-9A-Z_-]{0,15})$/', $orderNo)) {
            $this->orderNo = $orderNo;
        }

        return $this;
    }

    /**
     * Gets the webservice "ModeLiv" parameter's value.
     *
     * @param string $deliveryMode
     *
     * @return string
     */
    private function getWebserviceModeLiv($deliveryMode)
    {
        return (in_array($deliveryMode, ['MED', 'APM']))
            ? '24R'
            : $deliveryMode
        ;
    }

    /**
     * Get the value of option.
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * Set the value of option.
     *
     * @return self
     */
    public function setOption($option)
    {
        $this->option = $option;

        return $this;
    }
}
