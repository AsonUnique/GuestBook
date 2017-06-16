<?php

namespace Application\Controller;

use Application\Form\SignInForm;
use Application\Module;
use Application\Service\PostService;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class AdminController extends AbstractActionController
{
    /** @var PostService $postService */
    private $postService;

    /** @var SignInForm $signInForm */
    private $signInForm;

    /** @var array $adminConfig */
    private $adminConfig;

    /**
     * Главная страница админа
     *
     * @return ViewModel
     */
    public function adminAction()
    {
        $page = $this->params()->fromQuery('page', 0);
        $paginator = $this->getPostService()->getPaginator($page, 0);

        return new ViewModel([
            'avatarUploadPath' => Module::AVATAR_UPLOAD_PATH_REL,
            'paginator'        => $paginator,
        ]);
    }

    public function signInAction()
    {
        $prg = $this->postRedirectGet();
        if ($prg instanceof Response) {
            return $prg;
        } elseif (false === $prg) {
            return new ViewModel([
                'form' => $this->signInForm,
            ]);
        }

        $this->signInForm->setData($prg);
        if ($this->signInForm->isValid()) {
            $data = $this->signInForm->getData();
            if ($data['login'] === $this->adminConfig['login'] && $data['password'] === $this->adminConfig['password']) {
                $container = new Container('auth');
                $container->offsetSet('admin', true);

                return $this->redirect()->toRoute('admin');
            } else {
                $this->flashMessenger()->addErrorMessage('Wrong credentials!');

                return $this->redirect()->refresh();
            }
        } else {
            return new ViewModel([
                'form' => $this->signInForm,
            ]);
        }
    }

    /**
     * @return \Zend\Http\Response
     */
    public function publicAction()
    {
        if (!$this->validateRequest()) {
            return $this->redirect()->toRoute('admin');
        }

        try {
            $this->getPostService()->update(
                $this->params()->fromRoute('id'),
                ['isPublic' => true]
            );

            $this->flashMessenger()->addSuccessMessage('Post is published');
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage($e->getMessage());
        }

        return $this->redirect()->toRoute('admin');
    }

    /**
     * @return \Zend\Http\Response
     */
    public function hideAction()
    {
        if (!$this->validateRequest()) {
            return $this->redirect()->toRoute('admin');
        }

        try {
            $this->getPostService()->update(
                $this->params()->fromRoute('id'),
                ['isPublic' => false]
            );

            $this->flashMessenger()->addSuccessMessage('Post was hidden');
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage($e->getMessage());
        }

        return $this->redirect()->toRoute('admin');
    }

    /**
     * @return \Zend\Http\Response
     */
    public function deleteAction()
    {
        if (!$this->validateRequest()) {
            return $this->redirect()->toRoute('admin');
        }

        try {
            $this->getPostService()->delete($this->params()->fromRoute('id'));

            $this->flashMessenger()->addSuccessMessage('Post was deleted');
        } catch (\Exception $e) {
            $this->flashMessenger()->addErrorMessage($e->getMessage());
        }

        return $this->redirect()->toRoute('admin');
    }

    /**
     * Проверка запроса, переданы ли все необходимые параметры
     *
     * @return bool
     */
    private function validateRequest()
    {
        $id = $this->params()->fromRoute('id');

        if (empty($id)) {
            $this->flashMessenger()->addErrorMessage('Post id is not provided');

            return false;
        }

        return true;
    }

    /**
     * @return PostService
     */
    public function getPostService()
    {
        return $this->postService;
    }

    /**
     * @param PostService $postService
     */
    public function setPostService($postService)
    {
        $this->postService = $postService;
    }

    /**
     * @return SignInForm
     */
    public function getSignInForm()
    {
        return $this->signInForm;
    }

    /**
     * @param SignInForm $signInForm
     */
    public function setSignInForm($signInForm)
    {
        $this->signInForm = $signInForm;
    }

    /**
     * @return array
     */
    public function getAdminConfig()
    {
        return $this->adminConfig;
    }

    /**
     * @param array $adminConfig
     */
    public function setAdminConfig($adminConfig)
    {
        $this->adminConfig = $adminConfig;
    }
}