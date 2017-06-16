<?php

namespace Application\Factory\Service;

use Application\Service\PostService;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Factory\FactoryInterface;

class PostServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $em = $container->get('doctrine.entitymanager.orm_default');
        if (!$em instanceof EntityManager) {
            throw new ServiceNotCreatedException(sprintf(
                'Service %s expected %s, got %s instead',
                $requestedName,
                EntityManager::class,
                get_class($em)
            ));
        }
        
        $config = $container->get('Config');
        
        if (empty($config['pagination']) || !is_array($config['pagination'])) {
            throw new ServiceNotCreatedException('Pagination config is not specified!');
        }

        return new PostService($em, $config['pagination']);
    }
}
