<?php

namespace Nsm\Bundle\GeneratorBundle\Manipulator;

use Symfony\Component\DependencyInjection\Container;

/**
 * Changes the PHP code of a YAML services file.
 *
 * Not Originally Sensio, based on RoutingManipulator though
 */
class ServicesManipulator extends Manipulator
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
     * Adds a services resource at the bottom of the existing ones.
     *
     * @param string $service The service yml file to import
     *
     * @return Boolean true if it worked, false otherwise
     *
     * @throws \RuntimeException If service is already imported
     */
    public function addResource($service)
    {
        $current = '';
        if (file_exists($this->file)) {
            $current = file_get_contents($this->file);

            if (false !== strpos($current, $service)) {
                throw new \RuntimeException(sprintf('Service "%s" is already imported.', $service));
            }
        } else {
            // If the directory doesn't exist create it
            if (!is_dir($dir = dirname($this->file))) {
                mkdir($dir, 0777, true);
            }

            // Add imports to the top of the new file.
            $current = "\n# Put any custom code ABOVE this comment\nimports:\n";
        }

        $current .= sprintf("  - { resource: %s.yml }\n", $service);

        if (false === file_put_contents($this->file, $current)) {
            return false;
        }

        return true;
    }
}
