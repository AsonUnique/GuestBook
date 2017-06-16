<?php

namespace Application\Service;

use Application\Entity\PostEntity;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as ORMAdapter;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\Paginator\Paginator;

class PostService
{
    /** @var EntityManager $entityManager */
    private $entityManager;

    /** @var array $paginationConfig */
    private $paginationConfig;

    /**
     * PostService constructor.
     * @param EntityManager $entityManager
     * @param array $paginationConfig
     */
    public function __construct(EntityManager $entityManager, array $paginationConfig)
    {
        $this->entityManager = $entityManager;
        $this->paginationConfig = $paginationConfig;
    }

    /**
     * Get a post by id
     *
     * @param $id
     * @return null|PostEntity
     */
    public function get($id)
    {
        return $this->entityManager->getRepository(PostEntity::class)->find($id);
    }

    /**
     * Get all posts from database
     *
     * @param bool $public
     * @return array
     */
    public function getAll($public = true)
    {
        $postEntityRepository = $this->entityManager->getRepository(PostEntity::class);

        if ($public) {
            $postEntityArray = $postEntityRepository->findBy(['isPublic' => true]);
        } else {
            $postEntityArray = $postEntityRepository->findAll();
        }

        return $postEntityArray;
    }

    public function getPaginator($page, $public = true)
    {
        $postsPerPage = $this->paginationConfig['posts_per_page'];

        $query = $this->entityManager->createQueryBuilder();
        $query->select('postEntity')
              ->from(PostEntity::class, 'postEntity')
              ->orderBy('postEntity.date', 'DESC');

        if ($public) {
            $query->where('postEntity.isPublic = 1');
        }

        $ORMPaginator = new ORMPaginator($query, true);
        $adapter = new ORMAdapter($ORMPaginator);

        $paginator = new Paginator($adapter);
        $paginator->setItemCountPerPage($postsPerPage);
        $paginator->setCurrentPageNumber($page);

        return $paginator;
    }

    /**
     * Save new post to database
     *
     * @param array $postData
     * @return PostEntity
     */
    public function save(array $postData)
    {
        if (is_array($postData['avatar'])) {
            $avatar = $postData['avatar'];
            $tmpName = str_replace('\\', '/', $avatar['tmp_name']);
            $fileNameExploded = explode('/', $tmpName);
            $fileName = end($fileNameExploded);
            $postData['avatar'] = $fileName;
        }

        $postEntity = $this->hydrate($postData);
        $this->entityManager->persist($postEntity);
        $this->entityManager->flush();

        return $postEntity;
    }

    /**
     * Update existing post
     *
     * @param int $id
     * @param array $data
     */
    public function update($id, array $data)
    {
        $postEntity = $this->get($id);
        $this->hydrate($data, $postEntity);
        $this->entityManager->flush();
    }

    /**
     * Delete post from database
     *
     * @param $id
     */
    public function delete($id)
    {
        $postEntity = $this->get($id);
        if ($postEntity) {
            $this->entityManager->remove($postEntity);
            $this->entityManager->flush();
        }
    }

    /**
     * Hydrate post entity with data
     *
     * @param PostEntity $postEntity
     * @param array $data
     * @return PostEntity
     */
    private function hydrate(array $data, PostEntity $postEntity = null)
    {
        if (empty($postEntity)) {
            $postEntity = new PostEntity();
        }

        $hydrator = new DoctrineObject($this->entityManager);
        $hydrator->hydrate($data, $postEntity);

        return $postEntity;
    }
}
