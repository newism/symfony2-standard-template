<?php

namespace Nsm\Bundle\FormBundle\DataTransformer;

use Doctrine\ORM\EntityManager;
use Nsm\Bundle\UserBundle\Entity\Invitation;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Transforms an Invitation to an invitation code.
 */
class DateTimeZoneToStringTransformer implements DataTransformerInterface
{
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

        if (!$value instanceof \DateTimeZone) {
            throw new UnexpectedTypeException($value, '\DateTimeZone');
        }

        return $value->getName();
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

        return new \DateTimeZone($value);
    }
}
