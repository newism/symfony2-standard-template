<?php

namespace Nsm\Bundle\FormBundle\Form\Type;

use Nsm\Bundle\ApiBundle\Form\Model\Criteria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Custom TimeZone type that replaces the default timezone type with correct naming
 */
class TimeZoneType extends AbstractType
{

    /**
     * @inheritdoc
     */
    public function getParent() {
        return 'timezone';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'time_zone';
    }
}
