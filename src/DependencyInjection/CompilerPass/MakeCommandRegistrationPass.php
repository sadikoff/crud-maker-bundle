<?php

namespace Koff\Bundle\CrudMakerBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Sadicov Vladimir <sadikoff@gmail.com>
 */
class MakeCommandRegistrationPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // TODO: Implement process() method.
    }
}