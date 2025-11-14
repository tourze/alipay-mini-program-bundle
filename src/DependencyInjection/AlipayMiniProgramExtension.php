<?php

namespace AlipayMiniProgramBundle\DependencyInjection;

use AlipayMiniProgramBundle\Tests\Utils\MockTokenService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Tourze\AccessTokenContracts\TokenServiceInterface;
use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

class AlipayMiniProgramExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return __DIR__ . '/../Resources/config';
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        parent::load($configs, $container);

        // Register mock TokenServiceInterface for test environment
        if ('test' === $container->getParameter('kernel.environment')) {
            if (!$container->has(TokenServiceInterface::class)) {
                $mockServiceDef = new Definition(MockTokenService::class);
                $container->setDefinition(TokenServiceInterface::class, $mockServiceDef);
            }
        }
    }
}
