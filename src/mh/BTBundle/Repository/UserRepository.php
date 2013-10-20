<?php

namespace mh\BTBundle\Repository;

use mh\BTBundle\Repository\Base\BaseRepository;
use mh\Common\Random;
use mh\BTBundle\Entity as Entity;

class UserRepository extends BaseRepository
{
    public function getUserCategories($user)
	{
		$userCategories = array();
		foreach ($user->getPosts() as $post) {
			foreach ($post->getCategories() as $category) {
				$userCategories[$category->getId()] = $category;
			}
		}
		return $userCategories ;

		/*$q = $this->_em->getConnection();
		$stmt= $q->prepare("SELECT
			DISTINCT p.category_id
			FROM post__post p
			INNER JOIN category_post cp ON (cp.post_id = p.id)
			WHERE p.is_published = 1
			AND p.user_id = :user_id"
		);
		$stmt->bindValue(1, $user->getId());
		$stmt->execute();
		$rows = $stmt->fetchAll();

		$ids = array();
		foreach ($rows as $row) $ids[] = $row['category_id'];

		return $this->createQueryBuilder('u')
			->select(array('c'))
            ->innerJoin('p.posts', '�')
            ->innerJoin('p.categories', 'c', 'WITH', 'c.id = :id')
            ->innerJoin('p.user', 'u')
            ->where('p.isPublished = true')
            ->setParameter(':ids', $ids)
			->orderBy('p.createdDate', 'DESC')
            ->getQuery()->getResult()
		;*/
	}
}