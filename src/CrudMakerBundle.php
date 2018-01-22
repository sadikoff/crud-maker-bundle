<?php

namespace Koff\Bundle\CrudMakerBundle;

use Koff\Bundle\CrudMakerBundle\DependencyInjection\CompilerPass\MakeCommandRegistrationPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Sadicov Vladimir <sadikoff@gmail.com>
 */
class CrudMakerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        // add a priority so we run before the core command pass
        $container->addCompilerPass(new MakeCommandRegistrationPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 10);
    }
}
