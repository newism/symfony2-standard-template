<?php

namespace Nsm\Bundle\AppBundle\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Nsm\Bundle\AppBundle\Entity\ProjectRepository;
use Nsm\Bundle\AppBundle\Entity\TaskRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TaskFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        // Base options
//        $options = array(
//            'label' => 'Project - Remote',
//            'class' => 'NsmAppBundle:Project',
//            'multiple' => true,
//            'endpoint_index' => array('project_browse', array('_format' => 'json')),
//            'template' => 'NsmAppBundle:Projects:_selectize/default.html.twig',
//            'load_entities' => false
//        );
//
//        // Add the query builder to limit the valid options
//        $options['query_builder'] = function (ProjectRepository $repo) {
//            $qb = $repo->createQueryBuilder('Project');
//            return $qb;
//        };
//
//        // Add the form type
//        $builder->add(
//            'project',
//            'entity_search',
//            $options
//        );
//
//        // Add the form type
//        $builder->add(
//            'project_local_multiple',
//            'entity_search',
//            array(
//                'mapped' => false,
//                'label' => 'Project - Local - Multiple',
//                'class' => 'NsmAppBundle:Project',
//                'multiple' => true,
//                'required' => false,
//                'template' => 'NsmAppBundle:Projects:_selectize/default.html.twig',
//            )
//        );
//
//        $builder->add(
//            'project_local_single',
//            'entity_search',
//            array(
//                'mapped' => false,
//                'label' => 'Project - Local - Single',
//                'class' => 'NsmAppBundle:Project',
//                'multiple' => false,
//                'required' => false,
//                'template' => 'NsmAppBundle:Projects:_selectize/default.html.twig',
//            )
//        );

        $builder->add(
            'id',
            'text',
            array(
                'required' => false
            )
        );

        $builder->add(
            'title',
            'text',
            array(
                'required' => false
            )
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'label' => 'Search Tasks',
                'layout' => 'table',
                'data_class' => null,
                'csrf_protection' => false
            )
        );
    }

    public function getName()
    {
        return 'filter';
    }
}
