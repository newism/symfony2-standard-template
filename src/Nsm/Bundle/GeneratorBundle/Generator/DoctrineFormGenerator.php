<?php

namespace Nsm\Bundle\GeneratorBundle\Generator;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Generates a form class based on a Doctrine entity.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Hugo Hamon <hugo.hamon@sensio.com>
 */
class DoctrineFormGenerator extends Generator
{
    private $filesystem;
    private $className;
    private $classPath;

    /**
     * Constructor.
     *
     * @param Filesystem $filesystem A Filesystem instance
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function getClassPath()
    {
        return $this->classPath;
    }

    /**
     * Generates the entity form class if it does not exist.
     *
     * @param BundleInterface   $bundle
     * @param string            $entity
     * @param ClassMetadataInfo $metadata
     * @throws \RuntimeException
     */
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata)
    {
        $parts       = explode('\\', $entity);
        $entityClass = array_pop($parts);

        // Generate FormType

        $this->className = $entityClass . 'Type';
        $dirPath         = $bundle->getPath() . '/Form/Type';
        $this->classPath = $dirPath . '/' . str_replace('\\', '/', $entity) . 'Type.php';

        if (file_exists($this->classPath)) {
            throw new \RuntimeException(sprintf(
                'Unable to generate the %s form class as it already exists under the %s file',
                $this->className,
                $this->classPath
            ));
        }

        if (count($metadata->identifier) > 1) {
            throw new \RuntimeException('The form generator does not support entity classes with multiple primary keys.');
        }

        $parts = explode('\\', $entity);
        array_pop($parts);

        $variables = array(
            'fields'           => $this->getFieldsFromMetadata($metadata),
            'namespace'        => $bundle->getNamespace(),
            'entity_namespace' => implode('\\', $parts),
            'entity_class'     => $entityClass,
            'bundle'           => $bundle->getName(),
            'form_class'       => $this->className,
            'form_type_name'   => strtolower(
                str_replace('\\', '_', $bundle->getNamespace()) . ($parts ? '_' : '') . implode(
                    '_',
                    $parts
                ) . '_' . substr($this->className, 0, -4)
            ),
        );

        $this->renderFile('form/FormType.php.twig', $this->classPath, $variables);
    }

    /**
     * Generates the entity formFilter class if it does not exist.
     *
     * @param BundleInterface   $bundle
     * @param string            $entity
     * @param ClassMetadataInfo $metadata
     *
     * @throws \RuntimeException
     */
    public function generateFilter(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata)
    {
        $parts       = explode('\\', $entity);
        $entityClass = array_pop($parts);

        // Generate FilterType

        $this->className = $entityClass . 'FilterType';
        $dirPath         = $bundle->getPath() . '/Form/Type';
        $this->classPath = $dirPath . '/' . str_replace('\\', '/', $entity) . 'FilterType.php';

        if (file_exists($this->classPath)) {
            throw new \RuntimeException(sprintf(
                'Unable to generate the %s filter class as it already exists under the %s file',
                $this->className,
                $this->classPath
            ));
        }

        if (count($metadata->identifier) > 1) {
            throw new \RuntimeException('The form generator does not support entity classes with multiple primary keys.');
        }

        $parts = explode('\\', $entity);
        array_pop($parts);

        $variables = array(
            'fields'           => $this->getFieldsFromMetadata($metadata),
            'namespace'        => $bundle->getNamespace(),
            'entity_namespace' => implode('\\', $parts),
            'entity_class'     => $entityClass,
            'bundle'           => $bundle->getName(),
            'form_class'       => $this->className,
            'form_type_name'   => strtolower(
                str_replace('\\', '_', $bundle->getNamespace()) . ($parts ? '_' : '') . implode(
                    '_',
                    $parts
                ) . '_' . substr($this->className, 0, -4)
            ),
        );

        $this->renderFile('form/FormFilterType.php.twig', $this->classPath, $variables);
    }

    /**
     * Returns an array of fields. Fields can be both column fields and
     * association fields.
     *
     * @param ClassMetadataInfo $metadata
     *
     * @return array             $fields
     */
    private function getFieldsFromMetadata(ClassMetadataInfo $metadata)
    {
        $fields = (array) $metadata->fieldNames;

        // Remove the primary key field if it's not managed manually
        if (!$metadata->isIdentifierNatural()) {
            $fields = array_diff($fields, $metadata->identifier);
        }

        foreach ($metadata->associationMappings as $fieldName => $relation) {
            if ($relation['type'] !== ClassMetadataInfo::ONE_TO_MANY) {
                $fields[] = $fieldName;
            }
        }

        return $fields;
    }
}
