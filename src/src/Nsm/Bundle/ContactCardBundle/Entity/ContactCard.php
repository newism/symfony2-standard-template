<?php

namespace Nsm\Bundle\ContactCardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nsm\Bundle\CoreBundle\Entity\EntityInterface;

/**
 * ContactCard
 */
class ContactCard implements EntityInterface
{
    /**
     * @var integer
     */
    private $id;

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
    private $impps;

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
        $this->impps = new \Doctrine\Common\Collections\ArrayCollection();
        $this->telephones = new \Doctrine\Common\Collections\ArrayCollection();
        $this->urls = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add addresses
     *
     * @param \Nsm\Bundle\ContactCardBundle\Entity\Address $addresses
     * @return ContactCard
     */
    public function addAddress(\Nsm\Bundle\ContactCardBundle\Entity\Address $addresses)
    {
        $this->addresses[] = $addresses;
        $addresses->setContactCard($this);

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
        $addresses->setContactCard(null);
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
     * @return ContactCard
     */
    public function addEmail(\Nsm\Bundle\ContactCardBundle\Entity\Email $emails)
    {
        $this->emails[] = $emails;
        $emails->setContactCard($this);

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
        $emails->setContactCard(null);
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
     * Add impps
     *
     * @param \Nsm\Bundle\ContactCardBundle\Entity\Impp $impps
     * @return ContactCard
     */
    public function addImpp(\Nsm\Bundle\ContactCardBundle\Entity\Impp $impps)
    {
        $this->impps[] = $impps;
        $impps->setContactCard($this);

        return $this;
    }

    /**
     * Remove impps
     *
     * @param \Nsm\Bundle\ContactCardBundle\Entity\Impp $impps
     */
    public function removeImpp(\Nsm\Bundle\ContactCardBundle\Entity\Impp $impps)
    {
        $this->impps->removeElement($impps);
        $impps->setContactCard(null);
    }

    /**
     * Get impps
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImpps()
    {
        return $this->impps;
    }

    /**
     * Add telephones
     *
     * @param \Nsm\Bundle\ContactCardBundle\Entity\Telephone $telephones
     * @return ContactCard
     */
    public function addTelephone(\Nsm\Bundle\ContactCardBundle\Entity\Telephone $telephones)
    {
        $this->telephones[] = $telephones;
        $telephones->setContactCard($this);

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
        $telephones->setContactCard(null);
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
     * @return ContactCard
     */
    public function addUrl(\Nsm\Bundle\ContactCardBundle\Entity\Url $urls)
    {
        $this->urls[] = $urls;
        $urls->setContactCard($this);

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
        $urls->setContactCard(null);
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
