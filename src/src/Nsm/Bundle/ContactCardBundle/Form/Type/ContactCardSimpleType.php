<?php

namespace Nsm\Bundle\ContactCardBundle\Form\Type;

use Nsm\Bundle\ContactCardBundle\Form\DataTransformer\ContactCardToPreferredValuesTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Contact Card Simple Form Type
 */
class ContactCardSimpleType extends AbstractType
{
    /**
     * @var \ClubEvo\Bundle\AppBundle\Form\DataTransformer\ContactCardToPreferredValuesTransformer
     */
    protected $contactCardToPreferredValuesTransformer;

    /**
     * @param ContactCardToPreferredValuesTransformer $contactCardToPreferredValuesTransformer
     */
    public function __construct(ContactCardToPreferredValuesTransformer $contactCardToPreferredValuesTransformer)
    {
        $this->contactCardToPreferredValuesTransformer = $contactCardToPreferredValuesTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->contactCardToPreferredValuesTransformer);

        $builder->add(
            'id',
            'hidden',
            array(
                'required' => false,
            )
        );

        $builder->add(
            'telephone',
            new TelephoneType(),
            array(
                'label' => 'mobileTelephone',
                'required' => false,
            )
        );

        $builder->add(
            'email',
            new EmailType(),
            array(
                'label' => 'personalEmail',
                'required' => false,
            )
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => null, //'Nsm\Bundle\ContactCardBundle\Entity\ContactCard',
                'cascade_validation' => true,
                'error_bubbling' => false,
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'contact_card_simple';
    }
}
