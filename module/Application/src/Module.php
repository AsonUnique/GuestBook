<?php

namespace Application;

use Application\Controller\AdminController;
use Application\Controller\IndexController;
use Application\Factory\Controller\AdminControllerFactory;
use Application\Factory\Controller\IndexControllerFactory;
use Application\Factory\Service\PostServiceFactory;
use Application\Form\PostForm;
use Application\Form\SignInForm;
use Application\Service\PostService;
use Application\View\Helper\AuthenticationHelper;
use Application\View\Helper\NotificationHelper;
use Zend\EventManager\EventInterface;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\Mvc\MvcEvent;
use Zend\Router\Http\RouteMatch;
use Zend\Router\Http\TreeRouteStack;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Container;

class Module implements
    ConfigProviderInterface,
    ControllerProviderInterface,
    ServiceProviderInterface,
    ViewHelperProviderInterface,
    BootstrapListenerInterface
{
    const AVATAR_UPLOAD_PATH_ABS = './public/img/upload/avatar';
    const AVATAR_UPLOAD_PATH_REL = 'img/upload/avatar';
    
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
    
    public function getControllerConfig()
    {
        return [
            'factories' => [
                IndexController::class => IndexControllerFactory::class,
                AdminController::class => AdminControllerFactory::class,
            ],
        ];
    }
    
    public function getServiceConfig()
    {
        return [
            'factories' => [
                PostForm::class    => InvokableFactory::class,
                SignInForm::class  => InvokableFactory::class,
                PostService::class => PostServiceFactory::class,
            ],
        ];
    }

    public function getViewHelperConfig()
    {
        return [
            'factories' => [
                NotificationHelper::class   => InvokableFactory::class,
                AuthenticationHelper::class => InvokableFactory::class,
            ],
            'aliases'   => [
                'getNotificationHelper'   => NotificationHelper::class,
                'getAuthenticationHelper' => AuthenticationHelper::class,
            ],
        ];
    }

    public function onBootstrap(EventInterface $e)
    {
        if ($e instanceof MvcEvent) {
            /** @var ServiceManager $sm */
            $sm = $e->getApplication()->getServiceManager();

            /** @var TreeRouteStack $router */
            $router = $sm->get('router');
            /** @var Request $request */
            $request = $sm->get('request');
            /** @var RouteMatch $matchedRoute */
            $matchedRoute = $router->match($request);

            // Проверяем, авторизовался ли администратор
            $matchedRouteName = $matchedRoute->getMatchedRouteName();
            if (in_array('admin', explode('/', $matchedRouteName)) && 'admin/sign-in' !== $matchedRouteName) {
                $session = new Container('auth');
                if (empty($session->admin)) {
                    $url = $router->assemble([], ['name' => 'admin/sign-in']);

                    /** @var Response $response */
                    $response = $e->getResponse();
                    $response->setStatusCode(301);
                    $response
                        ->getHeaders()
                        ->addHeaderLine('Location', $url);
                    $e->setResponse($response);
                }
            }
        }
    }
}
