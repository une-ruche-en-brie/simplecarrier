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

namespace MondialRelay\MondialRelay\Api;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class Client
 *
 * @author Fabien Sigrand <contact@scaledev.fr>
 */
class Client
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    /** @var resource */
    private $curl;

    /** @var bool|string */
    private $response;

    /** @var string */
    private $userAgent;

    /** @var string */
    private $login;

    /** @var string */
    private $password;

    /** @var string */
    private $customerId;

    /** @var string */
    private $culture;

    /** @var array */
    private $headersList = [];

    /**
     * Set client base connection property
     */
    public function __construct()
    {
        $this->login = \Configuration::get(\Mondialrelay::API2_LOGIN);
        $this->password = \Configuration::get(\MondialRelay::API2_PASSWORD);
        $this->customerId = \Configuration::get(\MondialRelay::API2_CUSTOMER_ID);
        $this->culture = \Configuration::get(\MondialRelay::API2_CULTURE);
    }

    /**
     * Initializes the client.
     *
     * @param string $url
     * @param string $method 'GET' or 'POST'
     * @return $this
     */
    public function init($url, $method = self::METHOD_GET)
    {
        $this->curl = curl_init();

        $this->setUrl($url);

        if ($method == self::METHOD_POST) {
            $this->setOption(CURLOPT_POST, true);
        }

        return $this;
    }

    /**
     * Calls the given URL with the options.
     *
     * @return $this
     */
    public function call($datas)
    {
        $this->setOption(CURLOPT_RETURNTRANSFER, true);

        $this->setUserAgent(
            'mondialrelay_' . \Context::getContext()->controller->module->version
            . '-prestashop_' . _PS_VERSION_
            . '-' . \Configuration::get('PS_SHOP_NAME')
        );
        $this->headersList['Content-Type'] = 'text/xml';
        $this->headersList['Accept'] = 'application/xml';
        $this->setPostFieldsList($datas);

        if (!empty($this->headersList)) {
            $headersList = [];

            foreach ($this->headersList as $headerKey => $headerValue) {
                $headersList[] = $headerKey . ': ' . $headerValue;
            }

            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headersList);
        }

        $this->response = curl_exec($this->curl);
        curl_close($this->curl);

        return $this;
    }

    public function getXmlContextDatas($dom)
    {
        $context = $dom->createElement('Context');
        $context->appendChild($dom->createElement('Login', $this->getLogin()));
        $context->appendChild($dom->createElement('Password', $this->getPassword()));
        $context->appendChild($dom->createElement('CustomerId', $this->getCustomerId()));
        $context->appendChild($dom->createElement('Culture', $this->getCulture()));
        $context->appendChild($dom->createElement('VersionAPI', '1.0'));

        return $context;
    }

    public function getXmlOutputOptionsDatas($dom)
    {
        $options = $dom->createElement('OutputOptions');
        $options->appendChild($dom->createElement('OutputFormat', '10x15'));
        $options->appendChild($dom->createElement('OutputType', 'PdfUrl'));

        return $options;
    }

    /**
     * Sets the URL to use by the client.
     *
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        return $this->setOption(CURLOPT_URL, $url);
    }

    /**
     * Sets the post fields list to send.
     *
     * @param mixed $postFieldsList
     * @return $this
     */
    public function setPostFieldsList($postFieldsList)
    {
        return $this->setOption(CURLOPT_POSTFIELDS, $postFieldsList);
    }

    /**
     * Sets an option to the client.
     *
     * @param string $optionKey
     * @param mixed $optionValue
     * @return $this
     */
    public function setOption($optionKey, $optionValue)
    {
        curl_setopt($this->curl, $optionKey, $optionValue);

        return $this;
    }

    /**
     * Sets the options list to the client.
     *
     * @param array $optionsList
     * @return $this
     */
    public function setOptionsList(array $optionsList)
    {
        foreach ($optionsList as $optionKey => $optionValue) {
            $this->setOption($optionKey, $optionValue);
        }

        return $this;
    }

    /**
     * Gets the client's response.
     *
     * @return bool|string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Gets the "User-Agent" header.
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Sets the "User-Agent" header.
     *
     * @param string $userAgent
     * @return $this
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = htmlspecialchars($userAgent);
        $this->headersList['User-Agent'] = $this->userAgent;

        return $this;
    }

    /**
     * Gets the headers list.
     *
     * @return array
     */
    public function getHeadersList()
    {
        return $this->headersList;
    }

    /**
     * Sets the headers list.
     *
     * @param array $headersList
     * @return $this
     */
    public function setHeadersList(array $headersList)
    {
        foreach ($headersList as $headerKey => $headerValue) {
            $this->headersList[$headerKey] = $headerValue;
        }

        return $this;
    }

    /**
     * Get the value of culture
     */
    public function getCulture()
    {
        return $this->culture;
    }

    /**
     * Set the value of culture
     *
     * @return self
     */
    public function setCulture($culture)
    {
        $this->culture = $culture;
        return $this;
    }

    /**
     * Get the value of customerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * Set the value of customerId
     *
     * @return self
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;

        return $this;
    }

    /**
     * Get the value of password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return self
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of login
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set the value of login
     *
     * @return self
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }
}
