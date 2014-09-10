<?php

namespace Nsm\Bundle\AppBundle\Form\Type;

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
        $projectFormEventHandler = function (FormEvent $event, $eventName) {

            $form = $event->getForm();
            $data = $event->getData();

            // Base options
            $options = array(
                'label' => 'Project - Remote',
                'class' => 'NsmAppBundle:Project',
                'multiple' => true,
                'endpoint_index' => '/projects.json',
                'template' => 'NsmAppBundle:Projects:_selectize/default.html.twig'
            );

            $projects = isset($data['project']) ? $data['project'] : null;

            // If the projects are null then load empty choices
            if (null === $projects) {
                $options['choices'] = array();

                // Otherwise load a query builder
            } else {

                // Add the query builder to limit the valid options
                $options['query_builder'] = function (ProjectRepository $repo) use ($projects) {
                    $qb = $repo->createQueryBuilder('Project');
                    $qb->where($qb->expr()->in('Project.id', ':projects'));
                    $qb->setParameter('projects', $projects);

                    return $qb;
                };
            }

            // Add the form type
            $form->add(
                'project',
                'entity_search',
                $options
            );
        };

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
                'template' => 'NsmAppBundle:Projects:_selectize/default.html.twig'
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
                'template' => 'NsmAppBundle:Projects:_selectize/default.html.twig'
            )
        );

        $builder->addEventListener(FormEvents::PRE_SET_DATA, $projectFormEventHandler);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, $projectFormEventHandler);

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
                'csrf_protection' => false
            )
        );
    }

    public function getName()
    {
        return 'filter';
    }
}
