<?php

namespace Nsm\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class TemplatingCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('templating.helper.assets');
        $definition->setClass('Nsm\Bundle\CoreBundle\Templating\Helper\CoreAssetsHelper');

        $assets = array();

        if($container->hasParameter('app.assetsPath')) {
            $assetMapPath = $container->getParameterBag()->resolveValue($container->getParameter('app.assetsPath'));
            $assets = json_decode(file_get_contents($assetMapPath), true);
        }

        $definition->addArgument($assets);
    }
}
