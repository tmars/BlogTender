<?php

namespace mh\BTBundle\Repository;

use mh\BTBundle\Repository\Base\BaseRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class PostRepository extends BaseRepository
{
	public function getSimilarPosts($post, $count = 1)
	{
		$category_ids = array();
		foreach ($post->getCategories() as $category) {
			$category_ids[] = $category->getId();
		}

		$tag_ids = array();
		foreach ($post->getTags() as $tag) {
			$tag_ids[] = $tag->getId();
		}

		$q = $this->_em->getConnection();
		$stmt= $q->prepare("SELECT
			DISTINCT p.id
			FROM post__post p
			INNER JOIN category_post cp ON (cp.post_id = p.id)
			INNER JOIN tag_post tp ON (tp.post_id = p.id)
			WHERE p.is_published = 1
			AND cp.category_id in (?)
			OR (
				cp.category_id in (?)
				AND tp.tag_id in (?)
			)
			AND p.id <> ?
			ORDER BY RAND() LIMIT $count"
		);
		$stmt->bindValue(1, implode(',', $category_ids));
		$stmt->bindValue(2, implode(',', $category_ids));
		$stmt->bindValue(3, implode(',', $tag_ids));
		$stmt->bindValue(4, $post->getId());
		$stmt->execute();
		$rows = $stmt->fetchAll();

		$ids = array();
		foreach ($rows as $row) $ids[] = $row['id'];
		return $ids ? $this->findById($ids) : array();
	}

	public function getList($count, $page)
    {
        $query = $this->createQueryBuilder('p')
			->select(array('p', 'u', 'o'))
            ->innerJoin('p.user', 'u')
            ->innerJoin('p.contentObject', 'o')
            ->where('p.isPublished = true')
			->orderBy('p.createdDate', 'DESC')
			->getQuery()
		;
		//$query->useResultCache(true, 60, 'post_list_page_'.$page);

		return $this->getPaginated($query, $count, $page);
    }

    public function getListByCategory($cat_id, $count, $page)
    {
        $query = $this->createQueryBuilder('p')
			->select(array('p', 'u', 'o'))
            ->innerJoin('p.contentObject', 'o')
            ->innerJoin('p.categories', 'c', 'WITH', 'c.id = :id')
            ->innerJoin('p.user', 'u')
            ->where('p.isPublished = true')
            ->setParameter(':id', $cat_id)
			->orderBy('p.createdDate', 'DESC')
            ->getQuery()
		;
		//$query->useResultCache(true, 60, 'post_list_by_category_page_'.$page);

		return $this->getPaginated($query, $count, $page);
    }

    public function getListByTag($tag, $count, $page)
    {
        $query = $this->createQueryBuilder('p')
			->select(array('p', 'u', 'o'))
            ->innerJoin('p.contentObject', 'o')
            ->innerJoin('p.tags', 't')
            ->innerJoin('p.user', 'u')
			->where('t.label = :tag AND p.isPublished = true')
			->orderBy('p.createdDate', 'DESC')
            ->setParameter(':tag', $tag)
            ->getQuery()
        ;
		//$query->useResultCache(true, 60, 'post_list_by_tag_'.$tag.'_page_'.$page);

		return $this->getPaginated($query, $count, $page);
    }

    public function getListByUser($user, $count, $page)
    {
        $query = $this->createQueryBuilder('p')
			->select(array('p', 'u', 'o'))
            ->innerJoin('p.contentObject', 'o')
            ->innerJoin('p.user', 'u')
			->where('p.user = :user AND p.isPublished = true')
			->setParameter(':user', $user)
            ->getQuery()
        ;
		//$query->useResultCache(true, 60, 'post_list_by_user_'.$user->getId().'_page_'.$page);

		return $this->getPaginated($query, $count, $page);
    }

    public function getPost($user, $slug)
    {
        $query = $this->createQueryBuilder('p')
			->select(array('p', 'o'))
            ->innerJoin('p.contentObject', 'o')
			->where('p.user = :user AND p.slug = :slug')
            ->setParameter(':user', $user)
			->setParameter(':slug', $slug)
            ->getQuery()
        ;

		//$query->useResultCache(true, 60, 'posts_show_'.$user->getId().'_'.$slug);

        $post = $query->getSingleResult();
        $post->getCategories();
        $post->getTags();

        return $post;
    }

    public function getLast($count = 1)
    {
        $query = $this->createQueryBuilder('p')
			->select('p')
			->where('p.isPublished = true')
            ->getQuery()
            ->setMaxResults($count);
        //$query->useResultCache(true, 60, 'post_last_'.$count);

        return $query->getResult();
    }

	public function getRandomMarked($count = 1)
    {
		$q = $this->_em->getConnection();
		$stmt= $q->executeQuery("SELECT
			p.id
			FROM post__post p
			WHERE p.is_published = 1 AND p.show_on_main = 1
			ORDER BY RAND() LIMIT $count"
		);
		$result = $stmt->fetchAll();

		$ids = array();
		foreach ($result as $row) $ids[] = (int)$row['id'];
		return $ids ? $this->findById($ids) : array();
    }

    public function getRandom($count = 1)
    {
        // mapper для результата
        $rsm = new ResultSetMapping;
        $rsm->addEntityResult('mh\BTBundle\Entity\Post', 'p');
        $rsm->addFieldResult('p', 'id', 'id');
        $rsm->addFieldResult('p', 'slug', 'slug');
        $rsm->addFieldResult('p', 'title', 'title');
        $rsm->addJoinedEntityResult('mh\BTBundle\Entity\User', 'u', 'p', 'user');
        $rsm->addFieldResult('u', 'user_id', 'id');
        $rsm->addFieldResult('u', 'screen_name', 'screenName');
        $rsm->addJoinedEntityResult('mh\BTBundle\Entity\ContentObject', 'o', 'p', 'contentObject');
        $rsm->addFieldResult('o', 'content_object_id', 'id');
        //$rsm->addFieldResult('o', 'likes_count', 'likes_count');

        // тут я использую NativeQuery - перевести этот запрос в DQL для меня сейчас слабо )
        // также примем допущение что user не содержит "дыр" в ID
        $query = $this->_em
            ->createNativeQuery('SELECT
                p.id, p.slug, p.title, p.content, p.user_id, p.content_object_id,
                u.screen_name
                FROM post__post p
                INNER JOIN content_object__object o ON p.content_object_id = o.id
                INNER JOIN user__user u ON p.user_id = u.id
                WHERE p.is_published = 1
				ORDER BY RAND() LIMIT 0,:count', $rsm);
		/*$query = $this->_em
            ->createNativeQuery('SELECT
                p.id, p.slug, p.title, p.content, p.user_id, p.content_object_id,
                o.likes_count,
                u.screen_name
                FROM post__post p
                INNER JOIN content_object__object o ON p.content_object_id = o.id
                INNER JOIN user__user u ON p.user_id = u.id
                ORDER BY RAND() LIMIT 0,:count', $rsm);*/
        $query->setParameter(':count', $count);

        return $query->getResult();
    }
}