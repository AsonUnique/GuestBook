<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Form\PostForm;
use Application\Module;
use Application\Service\PostService;
use Doctrine\Common\Util\Debug;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    /** @var PostForm $postForm */
    private $postForm;

    /** @var PostService $postService */
    private $postService;

    public function indexAction()
    {
        $page = $this->params()->fromQuery('page', 0);
        $paginator = $this->getPostService()->getPaginator($page);
        
        return new ViewModel([
            'form'             => $this->postForm,
            'avatarUploadPath' => Module::AVATAR_UPLOAD_PATH_REL,
            'paginator'        => $paginator,
        ]);
    }

    public function postAction()
    {
        $jsonResponse = new JsonModel(['success' => false]);

        /** @var Request $request */
        $request = $this->getRequest();

        if (!$request->isPost()) {
            $jsonResponse->setVariable('reason', 'Request is not POST');

            return $jsonResponse;
        }

        $post = array_merge_recursive(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray()
        );

        $this->postForm->setData($post);

        if ($this->postForm->isValid()) {
            try {
                $this->getPostService()->save($this->postForm->getData());
                
                $jsonResponse->setVariable('success', true);
            } catch (\Exception $e) {
                $jsonResponse->setVariable('reason', $e->getMessage());
            }
        } else {
            $jsonResponse->setVariable('reason', $this->postForm->getMessages());
        }
        
        return $jsonResponse;
    }

    /**
     * @return PostForm
     */
    public function getPostForm()
    {
        return $this->postForm;
    }

    /**
     * @param PostForm $postForm
     */
    public function setPostForm($postForm)
    {
        $this->postForm = $postForm;
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
}

