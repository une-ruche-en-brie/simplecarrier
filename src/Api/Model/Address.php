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

namespace MondialRelay\MondialRelay\Api\Model;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class Address
 *
 * @author Fabien Sigrand <contact@scaledev.fr>
 */
final class Address
{
    /** @var string */
    private $firstname;

    /** @var string */
    private $lastname;

    /** @var string */
    private $title;

    /** @var string */
    private $streetname;

    /** @var string */
    private $countryCode;

    /** @var string */
    private $postcode;

    /** @var string */
    private $city;

    /** @var string */
    private $addressAdd1;

    /** @var string */
    private $addressAdd2;

    /** @var string */
    private $addressAdd3;

    /** @var string */
    private $phoneNo;

    /** @var string */
    private $mobileNo;

    /** @var string */
    private $email;

    /**
     * Get the value of lastname
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set the value of lastname
     *
     * @return self
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get the value of firstname
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set the value of firstname
     *
     * @return self
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get the value of title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of streetname
     */
    public function getStreetname()
    {
        return $this->streetname;
    }

    /**
     * Set the value of streetname
     *
     * @return self
     */
    public function setStreetname($streetname)
    {
        if (preg_match('/^[0-9A-Za-z_\-\'., \/]{0,30}$/', $streetname)) {
            $this->streetname = $streetname;
        }

        return $this;
    }

    /**
     * Get the value of countryCode
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Set the value of countryCode
     *
     * @return self
     */
    public function setCountryCode($countryCode)
    {
        if (preg_match('/^[A-Z]{2}$/', $countryCode)) {
            $this->countryCode = $countryCode;
        }

        return $this;
    }

    /**
     * Get the value of postcode
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Set the value of postcode
     *
     * @return self
     */
    public function setPostcode($postcode)
    {
        if (preg_match('/^[A-Za-z0-9_\-\' ]{2,10}$/', $postcode)) {
            $this->postcode = $postcode;
        }

        return $this;
    }

    /**
     * Get the value of city
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set the value of city
     *
     * @return self
     */
    public function setCity($city)
    {
        if (preg_match('/^[A-Za-z_\-\'. ]{2,30}$/', $city)) {
            $this->city = $city;
        }

        return $this;
    }

    /**
     * Get the value of addressAdd1
     */
    public function getAddressAdd1()
    {
        return $this->addressAdd1;
    }

    /**
     * Set the value of addressAdd1
     *
     * @return self
     */
    public function setAddressAdd1($addressAdd1)
    {
        if (preg_match('/^[0-9A-Za-z_\-\'., \/]{0,30}$/', $addressAdd1)) {
            $this->addressAdd1 = $addressAdd1;
        }

        return $this;
    }

    /**
     * Get the value of addressAdd2
     */
    public function getAddressAdd2()
    {
        return $this->addressAdd2;
    }

    /**
     * Set the value of addressAdd2
     *
     * @return self
     */
    public function setAddressAdd2($addressAdd2)
    {
        if (preg_match('/^[0-9A-Z_\-\'., \/]{0,30}$/', $addressAdd2)) {
            $this->addressAdd2 = $addressAdd2;
        }

        return $this;
    }

    /**
     * Get the value of addressAdd3
     */
    public function getAddressAdd3()
    {
        return $this->addressAdd3;
    }

    /**
     * Set the value of addressAdd3
     *
     * @return self
     */
    public function setAddressAdd3($addressAdd3)
    {
        if (preg_match('/^[0-9A-Z_\-\'., \/]{0,30}$/', $addressAdd3)) {
            $this->addressAdd3 = $addressAdd3;
        }

        return $this;
    }

    /**
     * Get the value of phoneNo
     */
    public function getPhoneNo()
    {
        return $this->phoneNo;
    }

    /**
     * Set the value of phoneNo
     *
     * @return self
     */
    public function setPhoneNo($phoneNo)
    {
        if (strlen($phoneNo) <= 20) {
            $this->phoneNo = $phoneNo;
        }

        return $this;
    }

    /**
     * Get the value of mobileNo
     */
    public function getMobileNo()
    {
        return $this->mobileNo;
    }

    /**
     * Set the value of mobileNo
     *
     * @return self
     */
    public function setMobileNo($mobileNo)
    {
        if (strlen($mobileNo) <= 20) {
            $this->mobileNo = $mobileNo;
        }

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return self
     */
    public function setEmail($email)
    {
        if (strlen($email) <= 70) {
            $this->email = $email;
        }

        return $this;
    }

    public function getXmlData($dom)
    {
        $address = $dom->createElement('Address');
        $address->appendChild($dom->createElement('Title', $this->getTitle()));
        $address->appendChild($dom->createElement('Firstname', $this->getFirstname()));
        $address->appendChild($dom->createElement('Lastname', $this->getLastname()));
        $address->appendChild($dom->createElement('Streetname', $this->getStreetname()));
        $address->appendChild($dom->createElement('HouseNo', ''));
        $address->appendChild($dom->createElement('CountryCode', $this->getCountryCode()));
        $address->appendChild($dom->createElement('PostCode', $this->getPostcode()));
        $address->appendChild($dom->createElement('City', $this->getCity()));
        $address->appendChild($dom->createElement('AddressAdd1', $this->getAddressAdd1()));
        $address->appendChild($dom->createElement('AddressAdd2', $this->getAddressAdd2()));
        $address->appendChild($dom->createElement('AddressAdd3', $this->getAddressAdd3()));
        $address->appendChild($dom->createElement('PhoneNo', $this->getPhoneNo()));
        $address->appendChild($dom->createElement('MobileNo', $this->getMobileNo()));
        $address->appendChild($dom->createElement('Email', $this->getEmail()));

        return $address;
    }
}
