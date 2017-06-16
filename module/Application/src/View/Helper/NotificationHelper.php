<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class NotificationHelper extends AbstractHelper
{
    public function __invoke($autoEscape = true)
    {
        $fm = $this->view->flashMessenger();
        $fm()->setMessageOpenFormat('<div%s>
                                         <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                             &times;
                                         </button>
                                         <ul class="notification-flash-list"><li>')
            ->setMessageSeparatorString('</li><li>')
            ->setMessageCloseString('</li></ul></div>');
        if ($fm()->hasCurrentErrorMessages())
            echo $fm()->setAutoEscape($autoEscape)->render('error', ['alert', 'alert-dismissible', 'alert-danger']);
        if ($fm()->hasCurrentSuccessMessages())
            echo $fm()->setAutoEscape($autoEscape)->render('success', ['alert', 'alert-dismissible', 'alert-success']);
    }
}