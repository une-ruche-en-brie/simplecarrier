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

namespace MondialRelay\MondialRelay\Api\Request;

use Configuration;
use MondialRelay;
use MondialRelay\MondialRelay\Api\Client;
use MondialRelay\MondialRelay\Api\Response\TestConnexionResponse;
use Tools;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class GetProductsCategoriesListRequest.
 *
 * @author Fabien Sigrand <contact@scaledev.fr>
 */
final class TestConnexionRequest
{
    public const URL_TEST = 'https://connect-api-sandbox.mondialrelay.com/api/shipment';

    public const URL_PROD = 'https://connect-api.mondialrelay.com/api/shipment';

    /** @var Client */
    private $client;

    /** @var TestConnexionResponse */
    private $response;

    private string $apiKey;

    /**
     * GetProductsCategoriesListRequest constructor.
     *
     * @param string $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->response = new TestConnexionResponse();
    }

    /**
     * Executes the request.
     *
     * @return $this
     */
    public function execute()
    {
        $isTestMode = (bool) Tools::getValue(MondialRelay::TEST_MODE);
        $apiUrl = $isTestMode ? self::URL_TEST : self::URL_PROD;

        $response = $this->client
            ->init($apiUrl, Client::METHOD_POST)
            ->call($this->getXmlDatas())
            ->getResponse();

        Configuration::updateValue(MondialRelay::TEST_MODE, (int) $isTestMode);
        $this->response->setClientResponse($response);

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

        return $dom->saveXML(null, LIBXML_NOEMPTYTAG);
    }

    /**
     * Gets the associated response of the request.
     *
     * @return TestConnexionResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Gets the API key.
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }
}
