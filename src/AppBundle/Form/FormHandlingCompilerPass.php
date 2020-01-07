<?php
/**
 * @copyright 2019
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace AppBundle\Form;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class FormHandlingCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $formBagDefinition = new Definition(FormFlashBag::class);
        $formBagDefinition->setPrivate(true);
        $formBagDefinition->setAutowired(true);
        $formBagDefinition->setShared(false);

        $container->setDefinition(FormFlashBag::SERVICE_NAME, $formBagDefinition);

        $formBagReference = new Reference(FormFlashBag::SERVICE_NAME);
        $container
            ->getDefinition('session')
            ->addMethodCall('registerBag', [$formBagReference]);
    }
}
