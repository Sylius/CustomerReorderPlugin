<?php

declare(strict_types=1);

use Sylius\Bundle\CoreBundle\Application\Kernel;
use Sylius\CustomerReorderPlugin\DependencyInjection\Compiler\RegisterEligibilityCheckerResponseProcessorsPass;
use Sylius\CustomerReorderPlugin\DependencyInjection\Compiler\RegisterEligibilityCheckersPass;
use Sylius\CustomerReorderPlugin\DependencyInjection\Compiler\RegisterReorderProcessorsPass;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class AppKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles(): array
    {
        return array_merge(parent::registerBundles(), [
            new \Sylius\Bundle\AdminBundle\SyliusAdminBundle(),
            new \Sylius\Bundle\ShopBundle\SyliusShopBundle(),

            new \FOS\OAuthServerBundle\FOSOAuthServerBundle(),
            new \Sylius\Bundle\AdminApiBundle\SyliusAdminApiBundle(),

            new \Sylius\CustomerReorderPlugin\SyliusCustomerReorderPlugin(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load($this->getRootDir() . '/config/config.yml');
    }

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterEligibilityCheckersPass());
        $container->addCompilerPass(new RegisterEligibilityCheckerResponseProcessorsPass());
        $container->addCompilerPass(new RegisterReorderProcessorsPass());
    }
}
