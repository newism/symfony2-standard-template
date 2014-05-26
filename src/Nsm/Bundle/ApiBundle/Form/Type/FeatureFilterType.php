<?php

namespace Nsm\Bundle\ApiBundle\Form\Type;

use Nsm\Bundle\ApiBundle\Entity\ProjectRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FeatureFilterType extends AbstractType
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
                'label' => 'Project',
                'class' => 'NsmApiBundle:Project',
                'multiple' => true
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
