<?php

namespace Nsm\Bundle\FormBundle\DataTransformer;


use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ArrayToDelimitedStringTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    private $delimiter;

    /**
     * @var string
     */
    private $format;

    /**
     * Constructor
     *
     * @param string $delimiter The delimiter to use when transforming from
     *                           a string to an array and vice-versa
     * @param string $format The format to use when reconstructing the
     *                           string
     */
    public function __construct($delimiter = ',', $format = '%s')
    {
        $this->delimiter = $delimiter;
        $this->format = $format;
    }

    /**
     * Transforms an array into a delimited string
     *
     * @param array $array Array to transform
     *
     * @return string
     *
     * @throws TransformationFailedException If the given value is not an array
     */
    public function transform($array)
    {
        if (null === $array) {
            return '';
        }

        if (!is_array($array)) {
            throw new TransformationFailedException('Expected an array.');
        }

        foreach ($array as &$value) {
            $value = sprintf($this->format, $value);
        }

        $string = trim(implode($this->delimiter, $array));

        return $string;
    }

    /**
     * Transforms a delimited string into an array
     *
     * @param string $string String to transform
     *
     * @return array
     *
     * @throws TransformationFailedException If the given value is not a string
     */
    public function reverseTransform($string)
    {
        if (null !== $string && !is_string($string)) {
            throw new TransformationFailedException('Expected a string.');
        }

        $string = trim($string);

        if (empty($string)) {
            return array();
        }

        $values = explode($this->delimiter, $string);

        if (0 === count($values)) {
            return array();
        }

        foreach ($values as &$value) {
            $value = trim($value);
        }

        return $values;
    }
}

