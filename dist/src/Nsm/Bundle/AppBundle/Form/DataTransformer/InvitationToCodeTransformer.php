<?php

namespace Nsm\Bundle\AppBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManager;
use Nsm\Bundle\AppBundle\Entity\Invitation;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Transforms an Invitation to an invitation code.
 */
class InvitationToCodeTransformer implements DataTransformerInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param mixed $value
     *
     * @return mixed|null
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof Invitation) {
            throw new UnexpectedTypeException($value, 'Nsm\Bundle\AppBundle\Entity\Invitation');
        }

        return $value->getCode();
    }

    /**
     * @param mixed $value
     *
     * @return mixed|Invitation|null
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function reverseTransform($value)
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $invitation = $this->entityManager
            ->getRepository('Nsm\Bundle\AppBundle\Entity\Invitation')
            ->findOneBy(
                array(
                    'code' => $value,
                    'claimedBy' => null,
                )
            );

        if (null === $invitation) {
            throw new TransformationFailedException(sprintf(
                'An invitation with code "%s" could not be found',
                $value
            ));
        }

        return $invitation;
    }
}
