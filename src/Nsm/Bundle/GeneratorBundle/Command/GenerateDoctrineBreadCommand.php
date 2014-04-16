<?php

namespace Nsm\Bundle\GeneratorBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Mapping\DisconnectedMetadataFactory;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

use Nsm\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use Nsm\Bundle\GeneratorBundle\Manipulator\RoutingManipulator;
use Nsm\Bundle\GeneratorBundle\Manipulator\ServicesManipulator;
use Nsm\Bundle\GeneratorBundle\Manipulator\ValidationsManipulator;


/**
 * Generates a BREAD for an Doctrine entity.
 */
class GenerateDoctrineBreadCommand extends ContainerAwareCommand
{
    protected $skeletonDirs;

    protected $templateVariables;

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

        $entity = $dialog->askAndValidate($output, $dialog->getQuestion('The Entity shortcut name', $input->getOption('entity')), array('Nsm\Bundle\GeneratorBundle\Command\Validators', 'validateEntityName'), false, $input->getOption('entity'));
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
        $overwrite = $dialog->ask($output, $dialog->getQuestion('Overwrite:', $overwrite ? "true": "false"), $overwrite);
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

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null
     * @throws \RuntimeException
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

        $entityName = Validators::validateEntityName($input->getOption('entity'));
        list($bundleName, $entityName) = $this->parseShortcutNotation($entityName);

        $entityNamespaced = $this->getContainer()->get('doctrine')->getAliasNamespace($bundleName).'\\'.$entityName;

        /** @var BundleInterface $bundle */
        $bundle = $this->getContainer()->get('kernel')->getBundle($bundleName);
        $metadata = $this->getEntityMetadata($entityNamespaced)[0];
        $this->skeletonDirs = $this->getSkeletonDirs($bundle);
        $this->forceOverwrite = $input->getOption('overwrite');

        $this->templateVariables = array(
            'entity_name' => $entityName,
            'entity_namespaced' => $entityNamespaced,
            'entity_variable_name' => lcfirst($entityName),
            'entity_service_name' => $this->transformToLowerCaseUnderScoredStyle($entityName),
            'entity_english_name' => $this->transformToEnglishStyle($entityName),
            'entity_url_name' => $input->getOption('entityUrlName') ? $input->getOption('entityUrlName') : $this->transformToUrlStyle($entityName),
            'bundle_name' => $bundleName,
            'bundle_service_name' => Container::underscore(substr($bundleName, 0, -6)),
            'bundle_namespace' => $bundle->getNamespace(),
            'actions' => array('browse', 'read', 'edit', 'add', 'delete'),
            'meta_data' => $metadata,
        );

        if (count($metadata->identifier) > 1) {
            throw new \RuntimeException('The BREAD generator does not support entity classes with multiple primary keys.');
        }

        if (!in_array('id', $metadata->identifier)) {
            throw new \RuntimeException('The BREAD generator expects the entity object has a primary key field named "id" with a getId() method.');
        }


        $this->renderFile(
            'Entity/EntityManager.php.twig',
            sprintf(
                '%s/Entity/%sManager.php',
                $bundle->getPath(),
                $entityName
            ),
            'Manager',
            $input,
            $output
        );

        $this->renderFile(
            'Entity/EntityQueryBuilder.php.twig',
            sprintf(
                '%s/Entity/%sQueryBuilder.php',
                $bundle->getPath(),
                $entityName
            ),
            'Query Builder',
            $input,
            $output
        );

        $this->renderFile(
            'Entity/EntityRepository.php.twig',
            sprintf(
                '%s/Entity/%sRepository.php',
                $bundle->getPath(),
                $entityName
            ),
            'Repository',
            $input,
            $output
        );

        $this->renderFile(
            'Controller/Controller.php.twig',
            sprintf(
                '%s/Controller/%sController.php',
                $bundle->getPath(),
                $entityName
            ),
            'Controller',
            $input,
            $output
        );

        $this->renderFile(
            'EventSubscriber/EventSubscriber.php.twig',
            sprintf(
                '%s/EventSubscriber/%sSubscriber.php',
                $bundle->getPath(),
                $entityName
            ),
            'Event Subscriber',
            $input,
            $output
        );

        $this->renderFile(
            'Resources/config/routing/Routing.yml.twig',
            sprintf(
                '%s/Resources/config/routing/%s.yml',
                $bundle->getPath(),
                $entityName
            ),
            'Routings',
            $input,
            $output
        );

        $this->renderFile(
            'Resources/config/services/entities/Service.yml.twig',
            sprintf(
                '%s/Resources/config/services/entities/%s.yml',
                $bundle->getPath(),
                $entityName
            ),
            'Services',
            $input,
            $output
        );

        $this->renderFile(
            'Resources/config/validations/Validation.yml.twig',
            sprintf(
                '%s/Resources/config/validations/%s.yml',
                $bundle->getPath(),
                $entityName
            ),
            'Validations',
            $input,
            $output
        );

        $this->renderFile(
            'Resources/config/serializer/Serializer.yml.twig',
            sprintf(
                '%s/Resources/config/serializer/Entity.%s.yml',
                $bundle->getPath(),
                $entityName
            ),
            'Serializer',
            $input,
            $output
        );

        $this->renderFile(
            'Resources/views/browse.html.twig.twig',
            sprintf(
                '%s/Resources/views/%s/browse.html.twig',
                $bundle->getPath(),
                $entityName
            ),
            'Browse Template',
            $input,
            $output
        );

        $this->renderFile(
            'Resources/views/read.html.twig.twig',
            sprintf(
                '%s/Resources/views/%s/read.html.twig',
                $bundle->getPath(),
                $entityName
            ),
            'Read Template',
            $input,
            $output
        );

        $this->renderFile(
            'Resources/views/add.html.twig.twig',
            sprintf(
                '%s/Resources/views/%s/add.html.twig',
                $bundle->getPath(),
                $entityName
            ),
            'Add Template',
            $input,
            $output
        );

        $this->renderFile(
            'Resources/views/edit.html.twig.twig',
            sprintf(
                '%s/Resources/views/%s/edit.html.twig',
                $bundle->getPath(),
                $entityName
            ),
            'Edit Template',
            $input,
            $output
        );

        $this->renderFile(
            'Resources/views/destroy.html.twig.twig',
            sprintf(
                '%s/Resources/views/%s/destroy.html.twig',
                $bundle->getPath(),
                $entityName
            ),
            'Destroy Template',
            $input,
            $output
        );

        $this->renderFile(
            'Resources/views/_partials/collection.html.twig.twig',
            sprintf(
                '%s/Resources/views/%s/_partials/collection.html.twig',
                $bundle->getPath(),
                $entityName
            ),
            'Collection Template',
            $input,
            $output
        );

        $this->renderFile(
            'Resources/views/_partials/collectionItem.html.twig.twig',
            sprintf(
                '%s/Resources/views/%s/_partials/collectionItem.html.twig',
                $bundle->getPath(),
                $entityName
            ),
            'Collection Item Template',
            $input,
            $output
        );

        $this->renderFile(
            'Form/Type/FormType.php.twig',
            sprintf(
                '%s/Form/Type/%sType.php',
                $bundle->getPath(),
                $entityName
            ),
            'Form Type',
            $input,
            $output
        );

        $this->renderFile(
            'Form/Type/FormFilterType.php.twig',
            sprintf(
                '%s/Form/Type/%sFilterType.php',
                $bundle->getPath(),
                $entityName
            ),
            'Form Filter Type',
            $input,
            $output
        );

        $this->renderFile(
            'Tests/Test.php.twig',
            sprintf(
                '%s/Tests/%sTest.php',
                $bundle->getPath(),
                $entityName
            ),
            'Full Functional Test',
            $input,
            $output
        );

        // update configurations
        $this->getContainer()->get('filesystem')->mkdir($bundle->getPath().'/Resources/config/');

        if (
            $input->isInteractive() && $dialog->askConfirmation($output, $dialog->getQuestion('Confirm update of routing', 'yes', '?'), true)
            ||
            !$input->isInteractive()
        ) {
            $output->write('Updating Routing File: ');
            $routing = new RoutingManipulator($bundle->getPath().'/Resources/config/routing.yml');
            try {
                $ret = $routing->addResource(
                    $this->templateVariables['bundle_service_name']."_".$this->templateVariables['entity_service_name'],
                    $this->templateVariables['bundle_name']."/Resources/config/routing/".$this->templateVariables['entity_name'].".yml"
                ) || false;
            } catch (\RuntimeException $exc) {
                $ret = false;
            }

            if (!$ret) {
                $output->write("<info>ROUTING NOT UPDATED: Import the bundle's routing resource in the bundle routing file if it doesn't currently exist</info>\n");
            }
        }

        if (
            $input->isInteractive() && $dialog->askConfirmation($output, $dialog->getQuestion('Confirm update of services', 'yes', '?'), true)
            ||
            !$input->isInteractive()
        ) {
            $output->write('Updating Service File: ');
            $services = new ServicesManipulator($bundle->getPath().'/Resources/config/services.yml');
            try {
                $ret = $services->addResource(
                    'services/entities/'.$this->templateVariables['entity_name'].".yml"
                ) || false;
            } catch (\RuntimeException $exc) {
                $ret = false;
            }

            if (!$ret) {
                $output->write("<info>SERVICES NOT UPDATED: Import the entities service resource in the bundle services file if it doesn't currently exist</info>\n");
            }
        }
        if (
            $input->isInteractive() && $dialog->askConfirmation($output, $dialog->getQuestion('Confirm update of validations', 'yes', '?'), true)
            ||
            !$input->isInteractive()
        ) {
            $output->write('Updating Validation File: ');
            $validations = new ValidationsManipulator($bundle->getPath().'/Resources/config/validations.yml');
            try {
                $ret = $validations->addResource(
                    'config/validations/'.$this->templateVariables['entity_name'].".yml"
                ) || false;
            } catch (\RuntimeException $exc) {
                $ret = false;
            }

            if (!$ret) {
                $output->write("<info>VALIDATIONS NOT UPDATED: Import the entities validation resource in the bundle validations file if it doesn't currently exist</info>\n");
            }
            $output->write("<info>Ok</info>\n");
        }

    }


    /**************************
     *
     * File Manipulation Methods
     *
     **************************/

    protected function renderFile($template, $target, $name, InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();

        // If we're running an interactive session and the user wants to proceed, or we're not interactive.
        if (
            $input->isInteractive() && $dialog->askConfirmation($output, $dialog->getQuestion('Confirm generation of '.$name, 'yes', '?'), true)
            ||
            !$input->isInteractive()
        ) {
            $output->write('Exporting ' . $name . ': ');

            if (!$this->forceOverwrite && file_exists($target)) {
                $output->write(sprintf('<info>Unable to generate %s as it already exists.</info>', $target));

                return;
            }

            if (!is_dir(dirname($target))) {
                mkdir(dirname($target), 0777, true);
            }

            file_put_contents($target, $this->render($template, $this->templateVariables));

            $output->write("<info>Ok</info>\n");
        }
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

    /**************************
     *
     * Helper Methods
     *
     **************************/

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

    protected function parseShortcutNotation($shortcut)
    {
        $entity = str_replace('/', '\\', $shortcut);

        if (false === $pos = strpos($entity, ':')) {
            throw new \InvalidArgumentException(sprintf('The entity name must contain a : ("%s" given, expecting something like AcmeBlogBundle:Blog/Post)', $entity));
        }

        return array(substr($entity, 0, $pos), substr($entity, $pos + 1));
    }

    protected function getSkeletonDirs(BundleInterface $bundle = null)
    {
        $skeletonDirs = array();

        if (isset($bundle) && is_dir($dir = $bundle->getPath().'/Resources/SensioGeneratorBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }

        if (is_dir($dir = $this->getContainer()->get('kernel')->getRootdir().'/Resources/SensioGeneratorBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }

        $skeletonDirs[] = __DIR__.'/../Resources/skeleton';
        $skeletonDirs[] = __DIR__.'/../Resources';

        return $skeletonDirs;
    }

    protected function getDialogHelper()
    {
        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog || get_class($dialog) !== 'Nsm\Bundle\GeneratorBundle\Command\Helper\DialogHelper') {
            $this->getHelperSet()->set($dialog = new DialogHelper());
        }

        return $dialog;
    }

    protected function getEntityMetadata($entity)
    {
        $factory = new DisconnectedMetadataFactory($this->getContainer()->get('doctrine'));

        return $factory->getClassMetadata($entity)->getMetadata();
    }
}
