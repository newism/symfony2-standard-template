<?php

namespace Nsm\Bundle\ContactCardBundle\Form\DataTransformer;

use Nsm\Bundle\ContactCardBundle\Entity\ContactCard;
use Nsm\Bundle\ContactCardBundle\Entity\ContactCardManager;
use Nsm\Bundle\ContactCardBundle\Entity\Email;
use Nsm\Bundle\ContactCardBundle\Entity\Telephone;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Transforms a Contact Card to a preferred values.
 */
class ContactCardToPreferredValuesTransformer implements DataTransformerInterface
{
    /**
     * @var ContactCardManager
     */
    protected $contactCardManager;

    /**
     * @param ContactCardManager $contactCardManager
     */
    public function __construct(ContactCardManager $contactCardManager)
    {
        $this->contactCardManager = $contactCardManager;
    }

    /**
     * @param ContactCard|null $contactCard
     *
     * @return array|null
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function transform($contactCard)
    {
        if (null === $contactCard) {
            return null;
        }

        if (!$contactCard instanceof ContactCard) {
            throw new UnexpectedTypeException($contactCard, 'Nsm\Bundle\ContactCard\Bundle\Entity\ContactCard');
        }

        /** @var Telephone $telephone */
        $telephone = $this->contactCardManager->getPreferredElementFromCollection($contactCard->getTelephones());
        /** @var Email $email */
        $email = $this->contactCardManager->getPreferredElementFromCollection($contactCard->getEmails());

        $arrayRepresentation = array(
            'id' => $contactCard->getId(),
            'telephone' => $telephone ? $telephone->getValue() : null,
            'email' => $email ? $email->getValue() : null,

        );

        return $arrayRepresentation;
    }

    /**
     * @param array|null $arrayRepresentation
     *
     * @return ContactCard|null
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function reverseTransform($arrayRepresentation)
    {
        if (null === $arrayRepresentation || empty($arrayRepresentation)) {
            return null;
        }

        if (!is_array($arrayRepresentation)) {
            throw new UnexpectedTypeException($arrayRepresentation, 'array');
        }

        /** @var ContactCardManager $contactCardManager */
        $contactCardManager = $this->contactCardManager;

        // Get contact card if it is existing or create a new one
        /** @var ContactCard $contactCard */
        if (null != $arrayRepresentation['id']) {
            $contactCard = $contactCardManager->getRepository()->find($arrayRepresentation['id']);
        } else {
            $contactCard = $this->contactCardManager->create();
        }

        if (!empty($arrayRepresentation['telephone'])) {
            /** @var Telephone $telephone */
            $telephone = $this->contactCardManager->getPreferredElementFromCollection($contactCard->getTelephones());
            if (!$telephone instanceof Telephone) {
                $telephone = new Telephone();
                $telephone->setPreferred(true);
                $telephone->setType('Mobile');
                $contactCard->addTelephone($telephone);
            }

            $telephone->setValue($arrayRepresentation['telephone']);
        }

        if (!empty($arrayRepresentation['email'])) {
            /** @var Email $email */
            $email = $this->contactCardManager->getPreferredElementFromCollection($contactCard->getEmails());
            if (!$email instanceof Email) {
                $email = new Email();
                $email->setPreferred(true);
                $email->setType('Home');
                $contactCard->addEmail($email);
            }

            $email->setValue($arrayRepresentation['email']);
        }

        return $contactCard;
    }
}
