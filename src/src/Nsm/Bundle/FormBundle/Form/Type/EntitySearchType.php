<?php

namespace Nsm\Bundle\FormBundle\Form\Type;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerInterface;
use Nsm\Bundle\FormBundle\Form\ChoiceList\ORMQueryBuilderLoader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\Router;
use Twig_Template;

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

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var array
     */
    protected $serializationGroups = array('Default', 'entitySearch');

    /**
     * @param \Twig_Environment   $twig
     * @param Router              $router
     * @param SerializerInterface $serializer
     * @param array               $serializerGroups
     */
    public function __construct(
        \Twig_Environment $twig,
        Router $router,
        SerializerInterface $serializer,
        array $serializerGroups = null
    ) {
        $this->twig = $twig;
        $this->router = $router;
        $this->serializer = $serializer;
        $this->serializationGroups = $serializerGroups;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        /** @var ChoiceListInterface $choiceList */
        $choiceList = $options['choice_list'];
        $choices = $choiceList->getChoices();
        $view->vars['choices'] = null;

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups($this->serializationGroups);
        $serializationContext->setSerializeNull(true);

        $choiceData = $this->serializer->serialize(array_values($choices), 'json', $serializationContext);

        $widgetOptions = array(
            'remote' => $options['remote'],
            'entityName' => $options['class'],
            'selectedOptions' => (array)$view->vars['value'],
            'selectizeOptions' => array(
                'valueField' => 'id',
                'labelField' => 'title',
                'searchField' => 'title',
                'options' => json_decode($choiceData, true),
            )
        );

        // Generate the endpoint index url
        if (null !== $options['endpoint_index']) {
            if (false === isset($options['endpoint_index'][1])) {
                $options['endpoint_index'][1] = array();
            }
            $options['endpoint_index'][1]['serialization_groups'] = $this->serializationGroups;
            $widgetOptions['endpointIndex'] = call_user_func_array(
                array($this->router, "generate"),
                $options['endpoint_index']
            );
        }

        // Generate the modal url
        if (null !== $options['endpoint_modal']) {
            $widgetOptions['endpointModal'] = call_user_func_array(
                array($this->router, "generate"),
                $options['endpoint_modal']
            );
        }

        // If there is a template defined
        // Load the template and pull out the blocks
        if (null !== $options['template']) {
            /** @var Twig_Template $template */
            $template = $this->twig->loadTemplate($options['template']);
            $widgetOptions['templates'] = array(
                'item' => $template->renderBlock('item', array()),
                'option' => $template->renderBlock('option', array()),
                'option_create' => $template->renderBlock('option_create', array()),
                'optgroup_header' => $template->renderBlock('optgroup_header', array()),
                'optgroup' => $template->renderBlock('optgroup', array())
            );
        }

        $view->vars['attr']['data-widget'] = 'entitySearch';
        $view->vars['attr']['data-entity-search-options'] = json_encode(
            $widgetOptions,
            JSON_HEX_QUOT | JSON_PRETTY_PRINT
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $type = $this;

        $loader = function (Options $options) use ($type) {
            if (null !== $options['query_builder']) {
                return new ORMQueryBuilderLoader(
                    $options['query_builder'],
                    $options['em'],
                    $options['class'],
                    $options['load_entities']
                );
            }
        };

        $resolver->setDefaults(
            array(
                'remote' => true,
                'endpoint_index' => null,
                'endpoint_modal' => null,
                'template' => null,
                'selectize_options' => array(),
                'load_entities' => true,
                'loader' => $loader
            )
        );

    }

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
