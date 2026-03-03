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

namespace MondialRelay\MondialRelay\Api\Request;

use Configuration;
use MondialRelay;
use MondialRelay\MondialRelay\Api\Builder\Model\ShipmentsListBuilder;
use MondialRelay\MondialRelay\Api\Client;
use MondialRelay\MondialRelay\Api\Response\GenerateLabelsResponse;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class GetProductsCategoriesListRequest
 *
 * @author Fabien Sigrand <contact@scaledev.fr>
 */
final class GenerateLabelsRequest
{
    const URL_TEST = 'https://connect-api-sandbox.mondialrelay.com/api/shipment';

    const URL_PROD = 'https://connect-api.mondialrelay.com/api/shipment';

    /** @var Client */
    private $client;

    /** @var GenerateLabelsResponse */
    private $response;

    /** @var array */
    private $orderIds;

    /** @var array */
    private $errors;

    /**
     * GetProductsCategoriesListRequest constructor.
     *
     * @param string $apiKey
     */
    public function __construct(Client $client, array $orderIds)
    {
        $this->client = $client;
        $this->errors = [];
        $this->orderIds = $orderIds;
        $this->response = new GenerateLabelsResponse();
    }

    /**
     * Executes the request.
     *
     * @return $this
     */
    public function execute()
    {
        $this->response->setClientResponse($this->client
            ->init(
                ((bool) Configuration::get(Mondialrelay::TEST_MODE)) ? self::URL_TEST : self::URL_PROD,
                Client::METHOD_POST
            )
            ->call($this->getXmlDatas())
            ->getResponse()
        );

        return $this;
    }

    public function getXmlDatas()
    {
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;

        $parent = $dom->createElement('ShipmentCreationRequest');
        $domAttribute1 = $dom->createAttribute('xmlns:xsi');
        $domAttribute1->value = 'http://www.w3.org/2001/XMLSchema-instance';
        $domAttribute2 = $dom->createAttribute('xmlns:xsd');
        $domAttribute2->value = 'http://www.w3.org/2001/XMLSchema';
        $domAttribute3 = $dom->createAttribute('xmlns');
        $domAttribute3->value = 'http://www.example.org/Request';

        $parent->appendChild($domAttribute1);
        $parent->appendChild($domAttribute2);
        $parent->appendChild($domAttribute3);

        $dom->appendChild($parent);

        $parent->appendChild($this->client->getXmlContextDatas($dom));
        $parent->appendChild($this->client->getXmlOutputOptionsDatas($dom));

        $shipmentsList = $dom->createElement('ShipmentsList');
        foreach ($this->orderIds as $orderId) {
            $shipment = (new ShipmentsListBuilder());
            $buildedShipment = $shipment->build($orderId);
            if ($buildedShipment === true) {
                $shipmentsList->appendChild($shipment->getXmlData($dom));
            } else {
                $this->errors[] = $buildedShipment;
            }
        }

        $parent->appendChild($shipmentsList);
        return $dom->saveXML(null, LIBXML_NOEMPTYTAG);
    }

    /**
     * Gets the associated response of the request.
     *
     * @return GenerateLabelsResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get the value of orderIds
     */
    public function getOrderIds()
    {
        return $this->orderIds;
    }

    /**
     * Get the value of errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Set the value of errors
     *
     * @return self
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;

        return $this;
    }
}
