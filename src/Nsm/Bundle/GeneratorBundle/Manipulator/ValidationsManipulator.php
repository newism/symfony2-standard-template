<?php

namespace Nsm\Bundle\GeneratorBundle\Manipulator;

use Symfony\Component\DependencyInjection\Container;

/**
 * Changes the PHP code of a YAML validations file.
 *
 * Not Originally Sensio, based on RoutingManipulator though
 */
class ValidationsManipulator extends Manipulator
{
    private $file;

    /**
     * Constructor.
     *
     * @param string $file The YAML routing file path
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * Adds a validations resource at the bottom of the existing ones.
     *
     * @param string $validation The validation yml file to import
     *
     * @return Boolean true if it worked, false otherwise
     *
     * @throws \RuntimeException If service is already imported
     */
    public function addResource($validation)
    {
        $current = '';
        if (file_exists($this->file)) {
            $current = file_get_contents($this->file);

            if (false !== strpos($current, $validation)) {
                throw new \RuntimeException(sprintf('Validation "%s" is already imported.', $validation));
            }
        } else {
            // If the directory doesn't exist create it
            if (!is_dir($dir = dirname($this->file))) {
                mkdir($dir, 0777, true);
            }

            // Add imports to the top of the new file.
            $current = "\nparameters:\n    validator.mapping.loader.yaml_files_loader.mapping_files:\n";
        }

        $pattern = '/^    validator.mapping.loader.yaml_files_loader.mapping_files:$/m';
        $replacement = "    validator.mapping.loader.yaml_files_loader.mapping_files:\n        - '%kernel.root_dir%/../src/Nsm/Bundle/ApiBundle/Resources/".$validation."'";

        $count = 0;
        $current = preg_replace($pattern, $replacement, $current, 1, $count);

        if ($count == 2) {
            return false;
        }

        if (false === file_put_contents($this->file, $current)) {
            return false;
        }

        return true;
    }
}
