<?php

namespace Nsm\Bundle\FormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\Router;

class EntitySearchType extends AbstractType
{
    /**
     * @var Router $router
     */
    private $router;

    /**
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /** @var ChoiceListInterface $choiceList */
        $choiceList = $options['choice_list'];
        $choices = $choiceList->getChoices();

        $view->vars['attr']['data-widget'] = 'entitySearch';
        $view->vars['attr']['data-entity-search-options'] = json_encode(
            array(
                'entityName' => '',
                'endpointIndex' => '',
                'endpointDetails' => '',
                'endpointModal' => '',
                'selectizeOptions' => array(
                    'valueField' => 'id',
                    'labelField' => 'title',
                    'searchField' => 'title',
                    // Avoid an unnessecary api call on page load
                    // by providing the required entity data.
                    'options' => array_map(
                        function ($item) {
                            return array(
                                "id" => $item->getId(),
                                "title" => (string)$item
                            );
                        },
                        $choices
                    )
                )
            ),
            JSON_HEX_QUOT
        );
    }


    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(
            array(
                'required' => true,
                'read_only' => false,
                'class' => '',
                'entity_name' => null,
                'endpoint_index' => null,
                'endpoint_details' => null,
                'endpoint_modal' => null,
                'selectize_options' => array(),
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'entity';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'entity_search';
    }
} 
