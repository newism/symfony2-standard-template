<?php

namespace Nsm\Bundle\ContactCardBundle\Entity;

/**
 * ContactCard
 */
class ContactCard
{
    /**
     * @var integer
     */
    private $id;


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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $addresses;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $emails;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $immps;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $telephones;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $urls;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->addresses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->emails = new \Doctrine\Common\Collections\ArrayCollection();
        $this->immps = new \Doctrine\Common\Collections\ArrayCollection();
        $this->telephones = new \Doctrine\Common\Collections\ArrayCollection();
        $this->urls = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add addresses
     *
     * @param \Nsm\Bundle\ContactCardBundle\Entity\Address $addresses
     *
     * @return ContactCard
     */
    public function addAddress(\Nsm\Bundle\ContactCardBundle\Entity\Address $addresses)
    {
        $this->addresses[] = $addresses;

        return $this;
    }

    /**
     * Remove addresses
     *
     * @param \Nsm\Bundle\ContactCardBundle\Entity\Address $addresses
     */
    public function removeAddress(\Nsm\Bundle\ContactCardBundle\Entity\Address $addresses)
    {
        $this->addresses->removeElement($addresses);
    }

    /**
     * Get addresses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Add emails
     *
     * @param \Nsm\Bundle\ContactCardBundle\Entity\Email $emails
     *
     * @return ContactCard
     */
    public function addEmail(\Nsm\Bundle\ContactCardBundle\Entity\Email $emails)
    {
        $this->emails[] = $emails;

        return $this;
    }

    /**
     * Remove emails
     *
     * @param \Nsm\Bundle\ContactCardBundle\Entity\Email $emails
     */
    public function removeEmail(\Nsm\Bundle\ContactCardBundle\Entity\Email $emails)
    {
        $this->emails->removeElement($emails);
    }

    /**
     * Get emails
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * Add immps
     *
     * @param \Nsm\Bundle\ContactCardBundle\Entity\Immp $immps
     *
     * @return ContactCard
     */
    public function addImmp(\Nsm\Bundle\ContactCardBundle\Entity\Immp $immps)
    {
        $this->immps[] = $immps;

        return $this;
    }

    /**
     * Remove immps
     *
     * @param \Nsm\Bundle\ContactCardBundle\Entity\Immp $immps
     */
    public function removeImmp(\Nsm\Bundle\ContactCardBundle\Entity\Immp $immps)
    {
        $this->immps->removeElement($immps);
    }

    /**
     * Get immps
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImmps()
    {
        return $this->immps;
    }

    /**
     * Add telephones
     *
     * @param \Nsm\Bundle\ContactCardBundle\Entity\Telephone $telephones
     *
     * @return ContactCard
     */
    public function addTelephone(\Nsm\Bundle\ContactCardBundle\Entity\Telephone $telephones)
    {
        $this->telephones[] = $telephones;

        return $this;
    }

    /**
     * Remove telephones
     *
     * @param \Nsm\Bundle\ContactCardBundle\Entity\Telephone $telephones
     */
    public function removeTelephone(\Nsm\Bundle\ContactCardBundle\Entity\Telephone $telephones)
    {
        $this->telephones->removeElement($telephones);
    }

    /**
     * Get telephones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTelephones()
    {
        return $this->telephones;
    }

    /**
     * Add urls
     *
     * @param \Nsm\Bundle\ContactCardBundle\Entity\Url $urls
     *
     * @return ContactCard
     */
    public function addUrl(\Nsm\Bundle\ContactCardBundle\Entity\Url $urls)
    {
        $this->urls[] = $urls;

        return $this;
    }

    /**
     * Remove urls
     *
     * @param \Nsm\Bundle\ContactCardBundle\Entity\Url $urls
     */
    public function removeUrl(\Nsm\Bundle\ContactCardBundle\Entity\Url $urls)
    {
        $this->urls->removeElement($urls);
    }

    /**
     * Get urls
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUrls()
    {
        return $this->urls;
    }
}
