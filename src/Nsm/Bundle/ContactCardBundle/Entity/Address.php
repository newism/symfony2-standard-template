<?php

namespace Nsm\Bundle\ContactCardBundle\Entity;

/**
 * Address
 */
class Address
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $streetAddress;

    /**
     * @var string
     */
    private $locality;

    /**
     * @var string
     */
    private $region;

    /**
     * @var string
     */
    private $postalCode;

    /**
     * @var string
     */
    private $countryName;

    /**
     * @var string
     */
    private $geolat;

    /**
     * @var string
     */
    private $geolong;

    /**
     * @var \Nsm\Bundle\ContactCardBundle\Entity\ContactCard
     */
    private $contactCard;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Address
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set streetAddress
     *
     * @param string $streetAddress
     *
     * @return Address
     */
    public function setStreetAddress($streetAddress)
    {
        $this->streetAddress = $streetAddress;

        return $this;
    }

    /**
     * Get streetAddress
     *
     * @return string 
     */
    public function getStreetAddress()
    {
        return $this->streetAddress;
    }

    /**
     * Set locality
     *
     * @param string $locality
     *
     * @return Address
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * Get locality
     *
     * @return string 
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * Set region
     *
     * @param string $region
     *
     * @return Address
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return string 
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode
     *
     * @return Address
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode
     *
     * @return string 
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set countryName
     *
     * @param string $countryName
     *
     * @return Address
     */
    public function setCountryName($countryName)
    {
        $this->countryName = $countryName;

        return $this;
    }

    /**
     * Get countryName
     *
     * @return string 
     */
    public function getCountryName()
    {
        return $this->countryName;
    }

    /**
     * Set geolat
     *
     * @param string $geolat
     *
     * @return Address
     */
    public function setGeolat($geolat)
    {
        $this->geolat = $geolat;

        return $this;
    }

    /**
     * Get geolat
     *
     * @return string 
     */
    public function getGeolat()
    {
        return $this->geolat;
    }

    /**
     * Set geolong
     *
     * @param string $geolong
     *
     * @return Address
     */
    public function setGeolong($geolong)
    {
        $this->geolong = $geolong;

        return $this;
    }

    /**
     * Get geolong
     *
     * @return string 
     */
    public function getGeolong()
    {
        return $this->geolong;
    }

    /**
     * Set contactCard
     *
     * @param \Nsm\Bundle\ContactCardBundle\Entity\ContactCard $contactCard
     *
     * @return Address
     */
    public function setContactCard(\Nsm\Bundle\ContactCardBundle\Entity\ContactCard $contactCard = null)
    {
        $this->contactCard = $contactCard;

        return $this;
    }

    /**
     * Get contactCard
     *
     * @return \Nsm\Bundle\ContactCardBundle\Entity\ContactCard 
     */
    public function getContactCard()
    {
        return $this->contactCard;
    }
}

