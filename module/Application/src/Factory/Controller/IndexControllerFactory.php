<?php

namespace Application\Factory\Controller;

use Application\Controller\IndexController;
use Application\Form\PostForm;
use Application\Service\PostService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class IndexControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return IndexController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = new IndexController();
        $controller->setPostForm($container->get(PostForm::class));
        $controller->setPostService($container->get(PostService::class));

        return $controller;
    }
}
