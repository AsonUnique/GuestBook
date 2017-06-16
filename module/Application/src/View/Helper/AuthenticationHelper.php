<?php

namespace Application\View\Helper;

use Zend\Session\Container;

class AuthenticationHelper
{
    private $container;

    public function __invoke()
    {
        $this->container = new Container('auth');

        return $this;
    }

    public function isAdmin()
    {
        return !empty($this->container->admin);
    }
}