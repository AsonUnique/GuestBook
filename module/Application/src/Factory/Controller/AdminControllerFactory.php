<?php

namespace Application\Factory\Controller;

use Application\Controller\AdminController;
use Application\Form\SignInForm;
use Application\Service\PostService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Factory\FactoryInterface;

class AdminControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = new AdminController();
        $controller->setPostService($container->get(PostService::class));
        $controller->setSignInForm($container->get(SignInForm::class));

        $config = $container->get('Config');
        if (empty($config['admin']) || !is_array($config['admin'])) {
            throw new ServiceNotCreatedException('Admin config is not specified!');
        }
        $controller->setAdminConfig($config['admin']);

        return $controller;
    }
}