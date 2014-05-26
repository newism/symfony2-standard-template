<?php

namespace Nsm\Bundle\ApiBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManager;
use Nsm\Bundle\ApiBundle\Entity\Invitation;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Transforms an Invitation to an invitation code.
 */
class ChoiceToValueTransformer implements DataTransformerInterface
{

    /**
     * Model to Normalised
     *
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

    }

    /**
     * Normalised to Model
     *
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

        return $value;

//        if (!is_array($value)) {
//            throw new UnexpectedTypeException($value, 'array');
//        }

        // This should be an array

    }
}
