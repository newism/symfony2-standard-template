<?php

namespace ClubEvo\Bundle\ApiBundle\Entity;

/**
 * Person
 */
class Person
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $familyName;

    /**
     * @var string
     */
    private $givenName;

    /**
     * @var string
     */
    private $additionalNames;

    /**
     * @var string
     */
    private $honorificPrefix;

    /**
     * @var string
     */
    private $honorificSuffix;

    /**
     * @var string
     */
    private $nickName;

    /**
     * @var string
     */
    private $birthday;

    /**
     * @var string
     */
    private $gender;

    /**
     * @var \Nsm\Bundle\ContactCardBundle\Entity\ContactCard
     */
    private $contactCard;


    /**
     * Set id
     *
     * @param integer $id
     *
     * @return Person
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

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
     * Set familyName
     *
     * @param string $familyName
     *
     * @return Person
     */
    public function setFamilyName($familyName)
    {
        $this->familyName = $familyName;

        return $this;
    }

    /**
     * Get familyName
     *
     * @return string 
     */
    public function getFamilyName()
    {
        return $this->familyName;
    }

    /**
     * Set givenName
     *
     * @param string $givenName
     *
     * @return Person
     */
    public function setGivenName($givenName)
    {
        $this->givenName = $givenName;

        return $this;
    }

    /**
     * Get givenName
     *
     * @return string 
     */
    public function getGivenName()
    {
        return $this->givenName;
    }

    /**
     * Set additionalNames
     *
     * @param string $additionalNames
     *
     * @return Person
     */
    public function setAdditionalNames($additionalNames)
    {
        $this->additionalNames = $additionalNames;

        return $this;
    }

    /**
     * Get additionalNames
     *
     * @return string 
     */
    public function getAdditionalNames()
    {
        return $this->additionalNames;
    }

    /**
     * Set honorificPrefix
     *
     * @param string $honorificPrefix
     *
     * @return Person
     */
    public function setHonorificPrefix($honorificPrefix)
    {
        $this->honorificPrefix = $honorificPrefix;

        return $this;
    }

    /**
     * Get honorificPrefix
     *
     * @return string 
     */
    public function getHonorificPrefix()
    {
        return $this->honorificPrefix;
    }

    /**
     * Set honorificSuffix
     *
     * @param string $honorificSuffix
     *
     * @return Person
     */
    public function setHonorificSuffix($honorificSuffix)
    {
        $this->honorificSuffix = $honorificSuffix;

        return $this;
    }

    /**
     * Get honorificSuffix
     *
     * @return string 
     */
    public function getHonorificSuffix()
    {
        return $this->honorificSuffix;
    }

    /**
     * Set nickName
     *
     * @param string $nickName
     *
     * @return Person
     */
    public function setNickName($nickName)
    {
        $this->nickName = $nickName;

        return $this;
    }

    /**
     * Get nickName
     *
     * @return string 
     */
    public function getNickName()
    {
        return $this->nickName;
    }

    /**
     * Set birthday
     *
     * @param string $birthday
     *
     * @return Person
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return string 
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return Person
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set contactCard
     *
     * @param \Nsm\Bundle\ContactCardBundle\Entity\ContactCard $contactCard
     *
     * @return Person
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

