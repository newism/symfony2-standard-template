<?php

namespace Nsm\Bundle\CoreBundle;

use Nsm\Bundle\CoreBundle\DependencyInjection\Compiler\TemplatingCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NsmCoreBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TemplatingCompilerPass());
    }
}
