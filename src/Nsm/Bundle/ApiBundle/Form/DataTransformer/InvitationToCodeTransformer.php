<?php

namespace Nsm\Bundle\ApiBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManager;
use Nsm\Bundle\ApiBundle\Entity\Invitation;
use Symfony\Component\Form\DataTransformerInterface;
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
            throw new UnexpectedTypeException($value, 'Nsm\Bundle\UserBundle\Entity\Invitation');
        }

        return $value->getCode();
    }

    /**
     * @param mixed $value
     *
     * @return mixed|null|object
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function reverseTransform($value)
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        return new Invitation();

//        return $this->entityManager
//            ->getRepository('Nsm\Bundle\UserBundle\Entity\Invitation')
//            ->findOneBy(
//                array(
//                    'code' => $value,
//                    'claimedBy' => null,
//                )
//            );
    }
}
