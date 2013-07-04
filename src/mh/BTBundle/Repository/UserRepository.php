<?php

namespace mh\BTBundle\Repository;

use mh\BTBundle\Repository\Base\BaseRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class UserRepository extends BaseRepository
{
	public function getUniqueScreenName($screenName)
	{
        if (strlen($screenName) > 28) {
            $screenName = substr($screenName, 28);
        } else if (strlen($screenName) < 6) {
            $screenName = 'login_';
        }

		$postfix = '';
        $screenName = $this->getFormatedScreenName($screenName);
        while ($this->findOneByScreenName($screenName.$postfix)) {
            $postfix = $postfix ? $postfix + 1 : 2;
        }
        return $screenName.$postfix;
	}

    public function getFormatedScreenName($screenName)
    {
        $screenName = preg_replace('/\W/', '_', $screenName);
        $screenName = preg_replace('/_+/', '_', $screenName);
        $screenName = preg_replace('/^_+/', '', $screenName);
        $screenName = preg_replace('/_+$/', '', $screenName);

        return $screenName;
    }

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
            ->innerJoin('p.posts', 'ç')
            ->innerJoin('p.categories', 'c', 'WITH', 'c.id = :id')
            ->innerJoin('p.user', 'u')
            ->where('p.isPublished = true')
            ->setParameter(':ids', $ids)
			->orderBy('p.createdDate', 'DESC')
            ->getQuery()->getResult()
		;*/
	}
}