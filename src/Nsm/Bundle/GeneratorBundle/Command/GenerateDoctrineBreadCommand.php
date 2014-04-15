<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nsm\Bundle\GeneratorBundle\Command;

use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Nsm\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use Nsm\Bundle\GeneratorBundle\Manipulator\RoutingManipulator;
use Nsm\Bundle\GeneratorBundle\Manipulator\ServicesManipulator;
use Nsm\Bundle\GeneratorBundle\Manipulator\ValidationsManipulator;


/**
 * Generates a BREAD for a Doctrine entity.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class GenerateDoctrineBreadCommand extends GenerateDoctrineCommand
{
    protected $skeletonDirs;
    protected $formGenerator;
    protected $filesystem;

    protected $variables;

    protected $entity;
    /** @var ClassMetadata $bundle */
    protected $bundle;
    protected $metadata;
    protected $actions;

    protected $forceOverwrite;


    /**************************
     *
     * Command based Methods
     *
     **************************/

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputOption('entity', '', InputOption::VALUE_REQUIRED, 'The entity class name to initialize (shortcut notation)'),
                new InputOption('entityUrlName', '', InputOption::VALUE_REQUIRED, 'The route for-this-entity'),
                new InputOption('overwrite', '', InputOption::VALUE_REQUIRED, 'Do not stop the generation if bread controller already exist, thus overwriting all generated files', true),
            ))
            ->setDescription('Generates a BREAD based on a Doctrine entity')
            ->setHelp(<<<EOT
The <info>doctrine:generate:bread</info> command generates a BREAD based on a Doctrine entity.

<info>TODO: This documentation is not up to date</info>

The default command only generates the list and show actions.

<info>php app/console doctrine:generate:bread --entity=AcmeBlogBundle:Post --route-prefix=post_admin</info>

Using the --with-write option allows to generate the new, edit and delete actions.

<info>php app/console doctrine:generate:bread --entity=AcmeBlogBundle:Post --route-prefix=post_admin --with-write</info>

Every generated file is based on a template. There are default templates but they can be overriden by placing custom templates in one of the following locations, by order of priority:

<info>BUNDLE_PATH/Resources/SensioGeneratorBundle/skeleton/bread
APP_PATH/Resources/SensioGeneratorBundle/skeleton/bread</info>

And

<info>__bundle_path__/Resources/SensioGeneratorBundle/skeleton/form
__project_root__/app/Resources/SensioGeneratorBundle/skeleton/form</info>

You can check https://github.com/sensio/SensioGeneratorBundle/tree/master/Resources/skeleton
in order to know the file structure of the skeleton
EOT
            )
            ->setName('nsm:generate:bread');
        ;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Welcome to the Doctrine2 BREAD generator');

        // namespace
        $output->writeln(array(
                '',
                'This command helps you generate BREAD controllers and templates.',
                '',
                'First, you need to give the entity for which you want to generate a BREAD.',
                'You can give an entity that does not exist yet and the wizard will help',
                'you defining it.',
                '',
                'You must use the shortcut notation like <comment>AcmeBlogBundle:Post</comment>.',
                '',
            ));

        $entity = $dialog->askAndValidate($output, $dialog->getQuestion('The Entity shortcut name', $input->getOption('entity')), array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateEntityName'), false, $input->getOption('entity'));
        $input->setOption('entity', $entity);
        list($bundle, $entity) = $this->parseShortcutNotation($entity);

        // entity url name
        $entityUrlName = $this->transformToUrlStyle($entity);
        $output->writeln(array(
                '',
                'Determine the url name for this entity',
                '',
            ));
        $entityUrlName = $dialog->ask($output, $dialog->getQuestion('Url:', '/'.$entityUrlName), '/'.$entityUrlName);
        $input->setOption('entityUrlName', $entityUrlName);

        // overwrite
        $overwrite = true;
        $output->writeln(array(
                '',
                'Overwrite existing files?',
                '',
            ));
        $overwrite = $dialog->ask($output, $dialog->getQuestion('Overwrite:', $overwrite), $overwrite);
        $input->setOption('overwrite', $overwrite);

        // summary
        $output->writeln(array(
                '',
                $this->getHelper('formatter')->formatBlock('Summary before generation', 'bg=blue;fg=white', true),
                '',
                sprintf("You are going to generate a BREAD controller for \"<info>%s:%s</info>\"", $bundle, $entity),
                sprintf("using the \"<info>%s</info>\" url.", $entityUrlName),
                sprintf("with overwrite set to \"<info>%s</info>\".", $overwrite ? "True": "False"),
                '',
            ));
    }

    protected function transformToUrlStyle($text)
    {
        $text = strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $text));

        // Remove prepended slash if it exists
        if ($text && '/' === $text[0]) {
            $text = substr($text, 1);
        }

        return $text;
    }

    protected function transformToLowerCaseUnderScoredStyle($text)
    {
        $text = Container::underscore($text);

        // Remove prepended slash if it exists
        if ($text && '/' === $text[0]) {
            $text = substr($text, 1);
        }

        return $text;
    }

    protected function transformToEnglishStyle($text)
    {
        $text = preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1 ', $text);

        // Remove prepended slash if it exists
        if ($text && '/' === $text[0]) {
            $text = substr($text, 1);
        }

        return $text;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $dialog = $this->getDialogHelper();

        if ($input->isInteractive()) {
            if (!$dialog->askConfirmation($output, $dialog->getQuestion('Do you confirm generation', 'yes', '?'), true)) {
                $output->writeln('<error>Command aborted</error>');

                return 1;
            }
        }

        // BREAD Generation... Taken from the generator and merged in
        $dialog->writeSection($output, 'BREAD generation');

        $entity = Validators::validateEntityName($input->getOption('entity'));
        list($bundle, $entity) = $this->parseShortcutNotation($entity);

        $entityNamespaced = $this->getContainer()->get('doctrine')->getAliasNamespace($bundle).'\\'.$entity;

        $this->bundle = $this->getContainer()->get('kernel')->getBundle($bundle);
        $this->skeletonDirs = $this->getSkeletonDirs($this->bundle);
        $this->metadata = $this->getEntityMetadata($entityNamespaced)[0];
        $this->forceOverwrite = $input->getOption('overwrite');

        $this->variables = array(
            'entity_name' => $entity,
            'entity_namespaced' => $entityNamespaced,
            'entity_variable_name' => lcfirst($entity),
            'entity_service_name' => $this->transformToLowerCaseUnderScoredStyle($entity),
            'entity_english_name' => $this->transformToEnglishStyle($entity),
            'entity_url_name' => $input->getOption('entityUrlName') ? $input->getOption('entityUrlName') : $this->transformToUrlStyle($entity),
            'bundle_name' => $bundle,
            'bundle_service_name' => Container::underscore(substr($this->bundle->getName(), 0, -6)),
            'bundle_namespace' => $this->bundle->getNamespace(),
            'actions' => array('browse', 'read', 'edit', 'add', 'delete'),
            'fields' => $this->metadata->fieldMappings,
        );

        if (count($this->metadata->identifier) > 1) {
            throw new \RuntimeException('The CRUD generator does not support entity classes with multiple primary keys.');
        }

        if (!in_array('id', $this->metadata->identifier)) {
            throw new \RuntimeException('The CRUD generator expects the entity object has a primary key field named "id" with a getId() method.');
        }

        // Generate Controller
        $this->renderFile(
            'bread/controller.php.twig',
            sprintf(
                '%s/Controller/%sController.php',
                $this->bundle->getPath(),
                $this->variables['entity_name']
            ),
            $this->variables
        );

        // Generate Subscriber
        $this->renderFile(
            'bread/eventSubscriber.php.twig',
            sprintf(
                '%s/EventSubscriber/%sSubscriber.php',
                $this->bundle->getPath(),
                $this->variables['entity_name']
            ),
            $this->variables
        );

        $this->generateSubscriberClass();

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



        $output->writeln('Generating the BREAD code: <info>OK</info>');

        $errors = array();
        $runner = $dialog->getRunner($output, $errors);

        // form
        if ($withWrite) {
            $this->generateForm($bundle, $entity, $metadata);
            $output->writeln('Generating the Form code: <info>OK</info>');
        }

        // configurations
        $runner($this->updateRouting($dialog, $input, $output, $bundle, $format, $entity, $prefix));
        $runner($this->updateServices($dialog, $input, $output, $bundle, $format, $entity, $prefix));
        $runner($this->updateValidations($dialog, $input, $output, $bundle, $format, $entity, $prefix));

        $dialog->writeGeneratorSummary($output, $errors);
    }

    /**************************
     *
     * Config Generation Methods
     *
     **************************/

    /**
     * @param DialogHelper    $dialog
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param BundleInterface $bundle
     * @param                 $format
     * @param                 $entity
     * @param                 $prefix
     *
     * @return array
     */
    protected function updateRouting(DialogHelper $dialog, InputInterface $input, OutputInterface $output, BundleInterface $bundle, $format, $entity, $prefix)
    {
        $auto = true;
        if ($input->isInteractive()) {
            $auto = $dialog->askConfirmation($output, $dialog->getQuestion('Confirm automatic update of the Routing', 'yes', '?'), true);
        }

        $output->write('Importing the BREAD routes: ');
        $this->getContainer()->get('filesystem')->mkdir($bundle->getPath().'/Resources/config/');
        $routing = new RoutingManipulator($bundle->getPath().'/Resources/config/routing.yml');
        try {
            $ret = $auto ? $routing->addResource($bundle->getName(), $format, '/'.$prefix, 'routing/'.str_replace('\\', '_', $entity)) : false;
        } catch (\RuntimeException $exc) {
            $ret = false;
        }

        if (!$ret) {
            $help = sprintf("        <comment>resource: \"@%s/Resources/config/routing/%s.%s\"</comment>\n", $bundle->getName(), str_replace('\\', '_', $entity), $format);
            $help .= sprintf("        <comment>prefix:   /%s</comment>\n", "");

            return array(
                '- Import the bundle\'s routing resource in the bundle routing file if it doesn\'t currently exist',
                sprintf('  (%s).', $bundle->getPath().'/Resources/config/routing.yml'),
                '',
                sprintf('    <comment>%s:</comment>', $bundle->getName().('' !== $prefix ? '_'.str_replace('/', '_', $prefix) : '')),
                $help,
                '',
            );
        }
    }

    /**
     * @param DialogHelper    $dialog
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param BundleInterface $bundle
     * @param                 $format
     * @param                 $entity
     * @param                 $prefix
     *
     * @return array
     */
    protected function updateServices(DialogHelper $dialog, InputInterface $input, OutputInterface $output, BundleInterface $bundle, $format, $entity, $prefix)
    {
        $auto = true;
        if ($input->isInteractive()) {
            $auto = $dialog->askConfirmation($output, $dialog->getQuestion('Confirm automatic update of Services', 'yes', '?'), true);
        }

        $output->write('Importing the BREAD services: ');
        $this->getContainer()->get('filesystem')->mkdir($bundle->getPath().'/Resources/config/');
        $services = new ServicesManipulator($bundle->getPath().'/Resources/config/services.yml');
        try {
            $ret = $auto ? $services->addResource('services/entities/'.str_replace('\\', '_', $entity)) : false;
        } catch (\RuntimeException $exc) {
            $ret = false;
        }

        if (!$ret) {
            $help = sprintf("  - { resource: %s.yml }\n", 'services/entities/'.str_replace('\\', '_', $entity));

            return array(
                '- Import the entities service resource in the bundle services file if it doesn\'t currently exist',
                sprintf('  (%s).', $bundle->getPath().'/Resources/config/services.yml'),
                '',
                sprintf('<comment>%s</comment>', $help),
                '',
            );
        }
    }

    /**
     * @param DialogHelper    $dialog
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param BundleInterface $bundle
     * @param                 $format
     * @param                 $entity
     * @param                 $prefix
     *
     * @return array
     */
    protected function updateValidations(DialogHelper $dialog, InputInterface $input, OutputInterface $output, BundleInterface $bundle, $format, $entity, $prefix)
    {
        $auto = true;
        if ($input->isInteractive()) {
            $auto = $dialog->askConfirmation($output, $dialog->getQuestion('Confirm automatic update of Validations', 'yes', '?'), true);
        }

        $output->write('Importing the BREAD validations: ');
        $this->getContainer()->get('filesystem')->mkdir($bundle->getPath().'/Resources/config/');
        $validations = new ValidationsManipulator($bundle->getPath().'/Resources/config/validations.yml');
        try {
            $ret = $auto ? $validations->addResource('config/validations/'.str_replace('\\', '_', $entity)) : false;
        } catch (\RuntimeException $exc) {
            $ret = false;
        }

        if (!$ret) {
            $help = "    - '%kernel.root_dir%/../src/Nsm/Bundle/ApiBundle/Resources/config/".str_replace('\\', '_', $entity).".yml'\n";

            return array(
                '- Import the entities validation resource in the bundle validations file if it doesn\'t currently exist',
                sprintf('  (%s).', $bundle->getPath().'/Resources/config/validations.yml'),
                '',
                sprintf('<comment>%s</comment>', $help),
                '',
            );
        }
    }

    /**************************
     *
     * Form Generation Methods
     *
     **************************/

    /**
     * Tries to generate forms if they don't exist yet and if we need write operations on entities.
     */
    protected function generateForm($bundle, $entity, $metadata)
    {
        try {
            $this->getFormGenerator($bundle)->generate($bundle, $entity, $metadata[0]);
        } catch (\RuntimeException $e ) {
            // form already exists
        }

        try {
            $this->getFormGenerator($bundle)->generateFilter($bundle, $entity, $metadata[0]);
        } catch (\RuntimeException $e ) {
            // filter already exists
        }
    }

    protected function createGenerator($bundle = null)
    {
        return new DoctrineBreadGenerator($this->getContainer()->get('filesystem'));
    }

    protected function getFormGenerator($bundle = null)
    {
        if (null === $this->formGenerator) {
            $this->formGenerator = new DoctrineFormGenerator($this->getContainer()->get('filesystem'));
            $this->formGenerator->setSkeletonDirs($this->getSkeletonDirs($bundle));
        }

        return $this->formGenerator;
    }

    public function setFormGenerator(DoctrineFormGenerator $formGenerator)
    {
        $this->formGenerator = $formGenerator;
    }









    /**
     * Generates the subscriber class only.
     * @param $forceOverwrite
     *
     * @throws \RuntimeException
     */
    protected function generateSubscriberClass()
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

        if (!$this->forceOverwrite && file_exists($target)) {
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

    /**************************
     *
     * File Manipulation Methods
     *
     **************************/

    protected function renderFile($template, $target, $parameters)
    {
        if (!$this->forceOverwrite && file_exists($target)) {
            throw new \RuntimeException(sprintf('Unable to generate %s as it already exists.', $target));
        }

        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0777, true);
        }

        return file_put_contents($target, $this->render($template, $parameters));
    }

    protected function render($template, $parameters)
    {
        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem($this->skeletonDirs), array(
            'debug'            => true,
            'cache'            => false,
            'strict_variables' => true,
            'autoescape'       => false,
        ));

        return $twig->render($template, $parameters);
    }
}
