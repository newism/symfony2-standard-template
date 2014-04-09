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
 * Generates a CRUD for a Doctrine entity.
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
                new InputOption('with-write', '', InputOption::VALUE_NONE, 'Whether or not to generate create, new and delete actions'),
                new InputOption('format', '', InputOption::VALUE_REQUIRED, 'Use the format for configuration files (php, xml, yml, or annotation)', 'annotation'),
                new InputOption('overwrite', '', InputOption::VALUE_NONE, 'Do not stop the generation if bread controller already exist, thus overwriting all generated files'),
            ))
            ->setDescription('Generates a CRUD based on a Doctrine entity')
            ->setHelp(<<<EOT
The <info>doctrine:generate:bread</info> command generates a CRUD based on a Doctrine entity.

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

        $dialog->writeSection($output, 'CRUD generation');

        $entityClass = $this->getContainer()->get('doctrine')->getAliasNamespace($bundle).'\\'.$entity;
        $metadata    = $this->getEntityMetadata($entityClass);
        $bundle      = $this->getContainer()->get('kernel')->getBundle($bundle);

        $generator = $this->getGenerator($bundle);
        $generator->generate($bundle, $entity, $metadata[0], $format, $prefix, $withWrite, $forceOverwrite);

        $output->writeln('Generating the CRUD code: <info>OK</info>');

        $errors = array();
        $runner = $dialog->getRunner($output, $errors);

        // form
        if ($withWrite) {
            $this->generateForm($bundle, $entity, $metadata);
            $output->writeln('Generating the Form code: <info>OK</info>');
        }

        // routing
        if ('annotation' != $format) {
            $runner($this->updateRouting($dialog, $input, $output, $bundle, $format, $entity, $prefix));
        }

        $dialog->writeGeneratorSummary($output, $errors);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        // Get the given class name
        $class = explode(":", $input->getOption('entity'));
        $class = end($class);

        // Get the url styled class name
        $urlStyled = strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $class));

        // Set default options
        $input->setOption('route-prefix', $urlStyled);
        $input->setOption('with-write', true);
        $input->setOption('format', 'xml');
        $input->setOption('overwrite', true);

        $input->setInteractive(false);
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
    }

    protected function updateRouting(DialogHelper $dialog, InputInterface $input, OutputInterface $output, BundleInterface $bundle, $format, $entity, $prefix)
    {
        $auto = true;
        if ($input->isInteractive()) {
            $auto = $dialog->askConfirmation($output, $dialog->getQuestion('Confirm automatic update of the Routing', 'yes', '?'), true);
        }

        $output->write('Importing the CRUD routes: ');
        $this->getContainer()->get('filesystem')->mkdir($bundle->getPath().'/Resources/config/');
        $routing = new RoutingManipulator($bundle->getPath().'/Resources/config/routing.yml');
        try {
            $ret = $auto ? $routing->addResource($bundle->getName(), $format, '/'.$prefix, 'routing/'.strtolower(str_replace('\\', '_', $entity))) : false;
        } catch (\RuntimeException $exc) {
            $ret = false;
        }

        if (!$ret) {
            $help = sprintf("        <comment>resource: \"@%s/Resources/config/routing/%s.%s\"</comment>\n", $bundle->getName(), strtolower(str_replace('\\', '_', $entity)), $format);
            $help .= sprintf("        <comment>prefix:   /%s</comment>\n", $prefix);

            return array(
                '- Import the bundle\'s routing resource in the bundle routing file',
                sprintf('  (%s).', $bundle->getPath().'/Resources/config/routing.yml'),
                '',
                sprintf('    <comment>%s:</comment>', $bundle->getName().('' !== $prefix ? '_'.str_replace('/', '_', $prefix) : '')),
                $help,
                '',
            );
        }
    }

    protected function getRoutePrefix(InputInterface $input, $entity)
    {
        $prefix = $input->getOption('route-prefix') ?: strtolower(str_replace(array('\\', '/'), '_', $entity));

        if ($prefix && '/' === $prefix[0]) {
            $prefix = substr($prefix, 1);
        }

        return $prefix;
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
