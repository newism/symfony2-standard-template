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
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var Router
     */
    protected $router;

    public function __construct(\Twig_Environment $twig, Router $router)
    {
        $this->twig = $twig;
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
//        $choiceList = $options['choice_list'];
//        $choices = $choiceList->getChoices();

        $widgetOptions = array(
            'remote' => true,
            'entityName' => $options['class'],
            'endpointIndex' => null,
            'endpointModal' => null,
            'selectizeOptions' => array(
                'valueField' => 'id',
                'labelField' => 'title',
                'searchField' => 'title'
//                    // Avoid an unnessecary api call on page load
//                    // by providing the required entity data.
//                    'options' => array_map(
//                        function ($item) {
//                            return array(
//                                "id" => $item->getId(),
//                                "title" => (string)$item
//                            );
//                        },
//                        $choices
//                    )
            )
        );

        // Generate the endpoint index url
        if (null !== $options['endpoint_index']) {
            $widgetOptions['endpointIndex'] = call_user_func_array(array($this->router, "generate"), $options['endpoint_index']);
        }

        // Generate the modal url
        if (null !== $options['endpoint_modal']) {
            $widgetOptions['endpointModal'] = call_user_func_array(array($this->router, "generate"), $options['endpoint_modal']);
        }

        if (null !== $options['template']) {
            $template = $this->twig->loadTemplate($options['template']);
            $widgetOptions['templates'] = array(
                'item' => $template->renderBlock('item', array()),
                'option' => $template->renderBlock('option', array())
            );
        }

        $view->vars['attr']['data-widget'] = 'entitySearch';
        $view->vars['attr']['data-entity-search-options'] = json_encode($widgetOptions, JSON_HEX_QUOT | JSON_PRETTY_PRINT);
    }


    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(
            array(
                'endpoint_index' => null,
                'endpoint_modal' => null,
                'template' => null,
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
