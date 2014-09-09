<?php

namespace Nsm\Bundle\AppBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use FOS\RestBundle\DependencyInjection\Compiler\ConfigurationCheckPass;

class NsmAppBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ConfigurationCheckPass());
    }

}
