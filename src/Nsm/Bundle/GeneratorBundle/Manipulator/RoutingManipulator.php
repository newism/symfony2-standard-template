<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nsm\Bundle\GeneratorBundle\Manipulator;

use Symfony\Component\DependencyInjection\Container;

/**
 * Changes the PHP code of a YAML routing file.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class RoutingManipulator extends Manipulator
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
     * Adds a routing resource at the top of the existing ones.
     *
     * @param string $serviceName
     * @param string $resource
     *
     * @return Boolean true if it worked, false otherwise
     *
     * @throws \RuntimeException If bundle is already imported
     */
    public function addResource($serviceName, $resource)
    {
        $code = $serviceName.":\n";
        $current = '';
        if (file_exists($this->file)) {
            $current = file_get_contents($this->file);

            if (false !== strpos($current, $code)) {
                throw new \RuntimeException(sprintf('Service "%s" is already imported.', $serviceName));
            }
        } elseif (!is_dir($dir = dirname($this->file))) {
            mkdir($dir, 0777, true);
        }

        $code .= sprintf("    resource: \"%s\"\n", $resource);
        $code .= "    prefix:   /\n";
        $code .= "\n";
        $code .= $current;

        if (false === file_put_contents($this->file, $code)) {
            return false;
        }

        return true;
    }
}
