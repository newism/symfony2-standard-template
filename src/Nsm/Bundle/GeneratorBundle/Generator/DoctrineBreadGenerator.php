<?php

namespace Nsm\Bundle\GeneratorBundle\Generator;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Generates a CRUD controller.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class DoctrineBreadGenerator extends Generator
{
    protected $filesystem;
    protected $routePrefix;
    protected $routeNamePrefix;
    protected $bundle;
    protected $entity;
    protected $metadata;
    protected $format;
    protected $actions;

    /**
     * Constructor.
     *
     * @param Filesystem $filesystem A Filesystem instance
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem  = $filesystem;
    }

    /**
     * Generate the CRUD controller.
     *
     * @param BundleInterface   $bundle           A bundle object
     * @param string            $entity           The entity relative class name
     * @param ClassMetadataInfo $metadata         The entity class metadata
     * @param string            $format           The configuration format (xml, yaml, annotation)
     * @param string            $routePrefix      The route name prefix
     *
     * @throws \RuntimeException
     */
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, $format, $routePrefix, $needWriteActions, $forceOverwrite)
    {
        $this->routePrefix = $routePrefix;
        $this->routeNamePrefix = str_replace('-', '_', str_replace('/', '_', $routePrefix));
        $this->actions = array('browse', 'read', 'edit', 'add', 'delete');

        if (count($metadata->identifier) > 1) {
            throw new \RuntimeException('The CRUD generator does not support entity classes with multiple primary keys.');
        }

        if (!in_array('id', $metadata->identifier)) {
            throw new \RuntimeException('The CRUD generator expects the entity object has a primary key field named "id" with a getId() method.');
        }

        $this->entity   = $entity;
        $this->bundle   = $bundle;
        $this->metadata = $metadata;
        $this->setFormat($format);

        $this->generateControllerClass($forceOverwrite);
        $this->generateSubscriberClass($forceOverwrite);

        $this->generateRouting();
        $this->generateServices();
        $this->generateValidations();
        $this->generateSerializations();

        // Check for view directories
        $dir = sprintf('%s/Resources/views/%s', $this->bundle->getPath(), str_replace('\\', '/', $this->entity));
        if (!file_exists($dir)) {
            $this->filesystem->mkdir($dir, 0777);
        }
        if (!file_exists($dir."/_partials")) {
            $this->filesystem->mkdir($dir, 0777);
        }

        $this->generateBrowseView($dir);
        $this->generateReadView($dir);
        $this->generateAddView($dir);
        $this->generateEditView($dir);
        $this->generateDeleteView($dir);
        $this->generatePartialView($dir."/_partials");
//        $this->generateTestClass();

    }

    /**
     * Generates the controller class only.
     * @param $forceOverwrite
     *
     * @throws \RuntimeException
     */
    protected function generateControllerClass($forceOverwrite)
    {
        $dir = $this->bundle->getPath();

        $parts = explode('\\', $this->entity);
        $entityClass = array_pop($parts);
        $entityNamespace = implode('\\', $parts);

        $target = sprintf(
            '%s/Controller/%s/%sController.php',
            $dir,
            str_replace('\\', '/', $entityNamespace),
            $entityClass
        );

        if (!$forceOverwrite && file_exists($target)) {
            throw new \RuntimeException('Unable to generate the controller as it already exists.');
        }

        $variables = array(
            'actions'           => $this->actions,
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'bundle'            => $this->bundle->getName(),
            'entity'            => $this->entity,
            'entity_class'      => $entityClass,
            'namespace'         => $this->bundle->getNamespace(),
            'entity_namespace'  => $entityNamespace,
            'format'            => $this->format,
            'variable_name'     => lcfirst($this->entity),
        );

        $this->renderFile('crud/controller.php.twig', $target, $variables);
    }

    /**
     * Generates the subscriber class only.
     * @param $forceOverwrite
     *
     * @throws \RuntimeException
     */
    protected function generateSubscriberClass($forceOverwrite)
    {
        $dir = $this->bundle->getPath();

        $parts = explode('\\', $this->entity);
        $entityClass = array_pop($parts);
        $entityNamespace = implode('\\', $parts);

        $target = sprintf(
            '%s/EventSubscriber/%s/%sSubscriber.php',
            $dir,
            str_replace('\\', '/', $entityNamespace),
            $entityClass
        );

        if (!$forceOverwrite && file_exists($target)) {
            throw new \RuntimeException('Unable to generate the subscriber as it already exists.');
        }

        $variables = array(
            'actions'           => $this->actions,
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'bundle'            => $this->bundle->getName(),
            'entity'            => $this->entity,
            'entity_class'      => $entityClass,
            'namespace'         => $this->bundle->getNamespace(),
            'entity_namespace'  => $entityNamespace,
            'format'            => $this->format,
            'variable_name'     => lcfirst($this->entity),
        );

        $this->renderFile('crud/eventSubscriber.php.twig', $target, $variables);
    }

    /**
     * Sets the configuration format.
     *
     * @param string $format The configuration format
     */
    private function setFormat($format)
    {
        switch ($format) {
            case 'yml':
            case 'xml':
            case 'php':
            case 'annotation':
                $this->format = $format;
                break;
            default:
                $this->format = 'yml';
                break;
        }
    }

    /**
     * Generates the routing configuration.
     *
     */
    protected function generateRouting()
    {
        if (!in_array($this->format, array('yml', 'xml', 'php'))) {
            return;
        }

        $target = sprintf(
            '%s/Resources/config/routing/%s.%s',
            $this->bundle->getPath(),
            strtolower(str_replace('\\', '_', $this->entity)),
            $this->format
        );

        $variables = array(
            'actions'           => $this->actions,
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'bundle'            => $this->bundle->getName(),
            'entity'            => $this->entity,
            'variable_name'     => lcfirst($this->entity),
            'bundle_config_namespace' => Container::underscore(substr($this->bundle->getName(), 0, -6)),
        );

        $this->renderFile('crud/config/routing.'.$this->format.'.twig', $target, $variables);
    }

    /**
     * Generates the service configuration.
     *
     */
    protected function generateServices()
    {
        if (!in_array($this->format, array('yml', 'xml', 'php'))) {
            return;
        }

        $target = sprintf(
            '%s/Resources/config/services/entities/%s.%s',
            $this->bundle->getPath(),
            strtolower(str_replace('\\', '_', $this->entity)),
            $this->format
        );

        $variables = array(
            'actions'           => $this->actions,
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'namespace'         => $this->bundle->getNamespace(),
            'bundle'            => $this->bundle->getName(),
            'entity'            => $this->entity,
            'variable_name'     => lcfirst($this->entity),
            'bundle_config_namespace' => Container::underscore(substr($this->bundle->getName(), 0, -6)),
        );

        $this->renderFile('crud/config/services.'.$this->format.'.twig', $target, $variables);
    }

    /**
     * Generates the validations configuration.
     *
     */
    protected function generateValidations()
    {
        if (!in_array($this->format, array('yml', 'xml', 'php'))) {
            return;
        }

        $target = sprintf(
            '%s/Resources/config/validations/%s.%s',
            $this->bundle->getPath(),
            strtolower(str_replace('\\', '_', $this->entity)),
            $this->format
        );

        $variables = array(
            'actions'           => $this->actions,
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'namespace'         => $this->bundle->getNamespace(),
            'bundle'            => $this->bundle->getName(),
            'entity'            => $this->entity,
            'variable_name'     => lcfirst($this->entity),
            'bundle_config_namespace' => Container::underscore(substr($this->bundle->getName(), 0, -6)),
        );

        $this->renderFile('crud/config/validations.'.$this->format.'.twig', $target, $variables);
    }

    /**
     * Generates the serialization configuration.
     *
     */
    protected function generateSerializations()
    {
        if (!in_array($this->format, array('yml', 'xml', 'php'))) {
            return;
        }

        $target = sprintf(
            '%s/Resources/config/serializations/Entity.%s.%s',
            $this->bundle->getPath(),
            str_replace('\\', '_', $this->entity),
            $this->format
        );

        $variables = array(
            'actions'           => $this->actions,
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'namespace'         => $this->bundle->getNamespace(),
            'bundle'            => $this->bundle->getName(),
            'entity'            => $this->entity,
            'variable_name'     => lcfirst($this->entity),
        );

        $this->renderFile('crud/config/serializations.'.$this->format.'.twig', $target, $variables);
    }

    /**
     * Generates the functional test class only.
     *
     */
    protected function generateTestClass()
    {
        $parts = explode('\\', $this->entity);
        $entityClass = array_pop($parts);
        $entityNamespace = implode('\\', $parts);

        $dir    = $this->bundle->getPath() .'/Tests/Controller';
        $target = $dir .'/'. str_replace('\\', '/', $entityNamespace).'/'. $entityClass .'ControllerTest.php';

        $variables = array(
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'entity'            => $this->entity,
            'bundle'            => $this->bundle->getName(),
            'entity_class'      => $entityClass,
            'namespace'         => $this->bundle->getNamespace(),
            'entity_namespace'  => $entityNamespace,
            'actions'           => $this->actions,
            'form_type_name'    => strtolower(str_replace('\\', '_', $this->bundle->getNamespace()).($parts ? '_' : '').implode('_', $parts).'_'.$entityClass.'Type'),
            'variable_name'     => lcfirst($this->entity),
        );

        $this->renderFile('crud/tests/test.php.twig', $target, $variables);
    }

    /**
     * Generates the browse.html.twig template in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    protected function generateBrowseView($dir)
    {
        $variables = array(
            'bundle'            => $this->bundle->getName(),
            'entity'            => $this->entity,
            'fields'            => $this->metadata->fieldMappings,
            'actions'           => $this->actions,
            'record_actions'    => $this->getRecordActions(),
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'variable_name'     => lcfirst($this->entity),
        );

        $this->renderFile('crud/views/browse.html.twig.twig', $dir.'/browse.html.twig', $variables);
    }

    /**
     * Generates the show.html.twig template in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    protected function generateReadView($dir)
    {
        $variables = array(
            'bundle'            => $this->bundle->getName(),
            'entity'            => $this->entity,
            'fields'            => $this->metadata->fieldMappings,
            'actions'           => $this->actions,
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'variable_name'     => lcfirst($this->entity),
        );

        $this->renderFile('crud/views/read.html.twig.twig', $dir.'/read.html.twig', $variables);
    }

    /**
     * Generates the add.html.twig template in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    protected function generateAddView($dir)
    {
        $variables = array(
            'bundle'            => $this->bundle->getName(),
            'entity'            => $this->entity,
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'actions'           => $this->actions,
            'variable_name'     => lcfirst($this->entity),
        );

        $this->renderFile('crud/views/add.html.twig.twig', $dir.'/add.html.twig', $variables);
    }

    /**
     * Generates the edit.html.twig template in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    protected function generateEditView($dir)
    {
        $variables = array(
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'entity'            => $this->entity,
            'bundle'            => $this->bundle->getName(),
            'actions'           => $this->actions,
            'variable_name'     => lcfirst($this->entity),
        );

        $this->renderFile('crud/views/edit.html.twig.twig', $dir.'/edit.html.twig', $variables);
    }

    /**
     * Generates the delete.html.twig template in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    protected function generateDeleteView($dir)
    {
        $variables = array(
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'entity'            => $this->entity,
            'bundle'            => $this->bundle->getName(),
            'actions'           => $this->actions,
            'variable_name'     => lcfirst($this->entity),
        );

        $this->renderFile('crud/views/delete.html.twig.twig', $dir.'/delete.html.twig', $variables);
    }

    /**
     * Generates the _partial templates in the final bundle.
     *
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    protected function generatePartialView($dir)
    {
        $variables = array(
            'bundle'            => $this->bundle->getName(),
            'entity'            => $this->entity,
            'fields'            => $this->metadata->fieldMappings,
            'actions'           => $this->actions,
            'record_actions'    => $this->getRecordActions(),
            'route_prefix'      => $this->routePrefix,
            'route_name_prefix' => $this->routeNamePrefix,
            'variable_name'     => lcfirst($this->entity),
        );

        $this->renderFile('crud/views/_partials/collection.html.twig.twig', $dir.'/collection.html.twig', $variables);
        $this->renderFile('crud/views/_partials/collectionItem.html.twig.twig', $dir.'/collectionItem.html.twig', $variables);
    }

    /**
     * Returns an array of record actions to generate (edit, show).
     *
     * @return array
     */
    protected function getRecordActions()
    {
        return array_filter($this->actions, function($item) {
            return in_array($item, array('show', 'edit'));
        });
    }
}
