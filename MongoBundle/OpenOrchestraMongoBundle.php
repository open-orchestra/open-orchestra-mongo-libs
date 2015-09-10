<?php

namespace OpenOrchestra\MongoBundle;

use OpenOrchestra\MongoBundle\DependencyInjection\Compiler\FilterTypePaginationCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class OpenOrchestraMongoBundle
 */
class OpenOrchestraMongoBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new FilterTypePaginationCompilerPass());
    }
}
