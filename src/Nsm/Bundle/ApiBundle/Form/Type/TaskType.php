<?php

namespace Nsm\Bundle\ApiBundle\Form\Type;

use Nsm\Bundle\ApiBundle\Entity\Task;
use Nsm\Bundle\ApiBundle\Entity\FeatureRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Util\FormUtil;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TaskType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (true === $options['display_project']) {

            $builder->add(
                'project',
                'entity',
                array(
                    'class' => 'NsmApiBundle:Project',
                    'empty_value' => '',
                    'control_attr' => array(
                        'onchange' => 'document.getElementById(\'task_Refresh\').click();'
                    )
                )
            );
        }

        $builder->add(
            'title',
            'text'
        );

        $builder->add(
            'tags',
            'text'
        );

        $builder->add(
            'description',
            'textarea',
            array(
                'required' => false
            )
        );


        if(true === $options['display_features']) {

            $featureFormEventHandler = function (FormEvent $event, $eventName) {

                $form = $event->getForm();
                $data = $event->getData();

                // Base options
                $options = array(
                    'label' => 'Feature',
                    'class' => 'NsmApiBundle:Feature',
                    'required' => false
                );

                switch(true){
                    // Data on Pre Bind
                    case $data instanceof Task:
                        $project = $data->getProject();
                        break;
                    // Data from Pre Submit
                    case is_array($data):
                        $project = $data['project'];
                        break;
                    // Data if
                    default:
                        $project = null;
                }

                // If the projects are null then load empty choices
                if (FormUtil::isEmpty($project)) {
                    $options['choices'] = array();

                    // Otherwise load a query builder
                } else {

                    // Add the query builder to limit the valid options
                    $options['query_builder'] = function (FeatureRepository $repo) use ($project) {
                        $qb = $repo->createQueryBuilder('Feature');
                        $qb->filterByCriteria(array('project' => $project));

                        return $qb;
                    };
                }

                // Add the form type
                $form->add(
                    'feature',
                    'entity',
                    $options
                );

            };

            $builder->addEventListener(FormEvents::PRE_SET_DATA, $featureFormEventHandler);
            $builder->addEventListener(FormEvents::PRE_SUBMIT, $featureFormEventHandler);

        }


    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Nsm\Bundle\ApiBundle\Entity\Task',
                'display_project' => true,
                'display_features' => true
            )
        );
    }

    public function getName()
    {
        return 'task';
    }
}
