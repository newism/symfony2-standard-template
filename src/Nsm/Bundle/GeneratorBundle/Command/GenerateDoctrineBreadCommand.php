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

use Nsm\Bundle\GeneratorBundle\Manipulator\ServicesManipulator;
use Nsm\Bundle\GeneratorBundle\Manipulator\ValidationsManipulator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Nsm\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use Nsm\Bundle\GeneratorBundle\Generator\DoctrineBreadGenerator;
use Nsm\Bundle\GeneratorBundle\Generator\DoctrineFormGenerator;
use Nsm\Bundle\GeneratorBundle\Manipulator\RoutingManipulator;


/**
 * Generates a BREAD for a Doctrine entity.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class GenerateDoctrineBreadCommand extends GenerateDoctrineCommand
{
    private $formGenerator;

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputOption('entity', '', InputOption::VALUE_REQUIRED, 'The entity class name to initialize (shortcut notation)'),
                new InputOption('route-prefix', '', InputOption::VALUE_REQUIRED, 'The route prefix'),
                new InputOption('with-write', '', InputOption::VALUE_REQUIRED, 'Whether or not to generate create, new and delete actions', true),
                new InputOption('format', '', InputOption::VALUE_REQUIRED, 'Use the format for configuration files (php, xml, yml, or annotation)', 'yml'),
                new InputOption('overwrite', '', InputOption::VALUE_REQUIRED, 'Do not stop the generation if bread controller already exist, thus overwriting all generated files', true),
            ))
            ->setDescription('Generates a BREAD based on a Doctrine entity')
            ->setHelp(<<<EOT
The <info>doctrine:generate:bread</info> command generates a BREAD based on a Doctrine entity.

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

        $entity = Validators::validateEntityName($input->getOption('entity'));
        list($bundle, $entity) = $this->parseShortcutNotation($entity);

        $format = Validators::validateFormat($input->getOption('format'));
        $prefix = $this->getRoutePrefix($input, $entity);
        $withWrite = $input->getOption('with-write');
        $forceOverwrite = $input->getOption('overwrite');

        $dialog->writeSection($output, 'BREAD generation');

        $entityClass = $this->getContainer()->get('doctrine')->getAliasNamespace($bundle).'\\'.$entity;
        $metadata    = $this->getEntityMetadata($entityClass);
        $bundle      = $this->getContainer()->get('kernel')->getBundle($bundle);

        $generator = $this->getGenerator($bundle);
        $generator->generate($bundle, $entity, $metadata[0], $format, $prefix, $withWrite, $forceOverwrite);

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

        // write?
        $withWrite = $input->getOption('with-write') ?: true;
        $output->writeln(array(
                '',
                'By default, the generator creates two actions: list and show.',
                'You can also ask it to generate "write" actions: new, update, and delete.',
                '',
            ));
        $withWrite = $dialog->askConfirmation($output, $dialog->getQuestion('Do you want to generate the "write" actions', $withWrite ? 'yes' : 'no', '?'), $withWrite);
        $input->setOption('with-write', $withWrite);

        // format
        $format = $input->getOption('format');
        $output->writeln(array(
                '',
                'Determine the format to use for the generated BREAD.',
                '',
            ));
        $format = $dialog->askAndValidate($output, $dialog->getQuestion('Configuration format (yml, xml, php, or annotation)', $format), array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateFormat'), false, $format);
        $input->setOption('format', $format);

        // route prefix
        $prefix = $this->getRoutePrefix($input, $entity);
        $output->writeln(array(
                '',
                'Determine the routes prefix (all the routes will be "mounted" under this',
                'prefix: /prefix/, /prefix/new, ...).',
                '',
            ));
        $prefix = $dialog->ask($output, $dialog->getQuestion('Routes prefix', '/'.$prefix), '/'.$prefix);
        $input->setOption('route-prefix', $prefix);

        // summary
        $output->writeln(array(
                '',
                $this->getHelper('formatter')->formatBlock('Summary before generation', 'bg=blue;fg=white', true),
                '',
                sprintf("You are going to generate a BREAD controller for \"<info>%s:%s</info>\"", $bundle, $entity),
                sprintf("using the \"<info>%s</info>\" format.", $format),
                '',
            ));
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

    protected function getRoutePrefix(InputInterface $input, $entity)
    {
        $prefix = $input->getOption('route-prefix') ?: strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $entity));;

        if ($prefix && '/' === $prefix[0]) {
            $prefix = substr($prefix, 1);
        }

        return $prefix;
    }

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
}
