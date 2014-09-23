<?php

namespace Nsm\Bundle\AppBundle\Form\Type;

use Nsm\Bundle\AppBundle\Entity\ProjectRepository;
use Nsm\Bundle\AppBundle\Entity\Task;
use Nsm\Bundle\AppBundle\Form\DataTransformer\ChoiceToValueTransformer;
use Nsm\Bundle\ContactCardBundle\Form\Type\ContactCardType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormFactory;

class TestType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $project = $builder->getData();

        // Base options
        $options = array(
            'label' => 'Project - Remote',
            'class' => 'NsmAppBundle:Project',
            'multiple' => true,
            'endpoint_index' => array('project_browse', array('_format' => 'json')),
            'template' => 'NsmAppBundle:Projects:_selectize/default.html.twig',
            'load_entities' => false,
            'mapped' => false
        );

        // Add the query builder to limit the valid options
        $options['query_builder'] = function (ProjectRepository $repo) {
            $qb = $repo->createQueryBuilder('Project');
            return $qb;
        };

        // Add the form type
        $builder->add(
            'project',
            'entity_search',
            $options
        );

        // Add the form type
        $builder->add(
            'project_local_multiple',
            'entity_search',
            array(
                'mapped' => false,
                'label' => 'Project - Local - Multiple',
                'class' => 'NsmAppBundle:Project',
                'multiple' => true,
                'required' => false,
                'template' => 'NsmAppBundle:Projects:_selectize/default.html.twig',
            )
        );

        $builder->add(
            'project_local_single',
            'entity_search',
            array(
                'mapped' => false,
                'label' => 'Project - Local - Single',
                'class' => 'NsmAppBundle:Project',
                'multiple' => false,
                'required' => false,
                'template' => 'NsmAppBundle:Projects:_selectize/default.html.twig',
            )
        );

        $builder->add(
            'text',
            'text',
            array(
                'mapped' => false
            )
        );

        $builder->add(
            'textarea',
            'textarea',
            array(
                'mapped' => false
            )
        );


        $builder->add(
            'choice',
            'choice',
            array(
                'mapped' => false,
                'choices' => [1,2]
            )
        );

        $builder->add(
            'choice-expanded-multiple',
            'choice',
            array(
                'mapped' => false,
                'expanded' => true,
                'multiple' => true,
                'choices' => [1,2]
            )
        );

        $builder->add(
            'choice-expanded',
            'choice',
            array(
                'mapped' => false,
                'expanded' => true,
                'choices' => [1,2]
            )
        );

        $builder->add(
            'date',
            'date',
            array(
                'mapped' => false
            )
        );

        $builder->add(
            'datetime',
            'datetime',
            array(
                'mapped' => false
            )
        );

        $builder->add(
            'datetime-single',
            'datetime',
            array(
                'widget' => 'single_text',
                'mapped' => false
            )
        );

        $builder->add(
            'birthday',
            'birthday',
            array(
                'mapped' => false
            )
        );

        $builder->add(
            'startedAtRange',
            'date_range',
            array(
                'required' => true,
                'help' => 'Dates are inclusive',
                'mapped' => false
            )
        );

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'layout' => 'table',
//                'data_class' => 'Nsm\Bundle\AppBundle\Entity\Project'
            )
        );
    }

    public function getName()
    {
        return 'project';
    }
}
