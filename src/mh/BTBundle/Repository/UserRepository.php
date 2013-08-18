<?php

namespace mh\BTBundle\Repository;

use mh\BTBundle\Repository\Base\BaseRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class UserRepository extends BaseRepository
{
	public function getUniqueLogin($login)
	{
        if (strlen($login) > 28) {
            $login = substr($login, 28);
        } else if (strlen($login) < 6) {
            $login = 'login_';
        }

		$postfix = '';
        $login = $this->getFormatedLogin($login);
        while ($this->findOneByLogin($login.$postfix)) {
            $postfix = $postfix ? $postfix + 1 : 2;
        }
        return $login.$postfix;
	}

    public function getFormatedLogin($login)
    {
        $login = preg_replace('/\W/', '_', $login);
        $login = preg_replace('/_+/', '_', $login);
        $login = preg_replace('/^_+/', '', $login);
        $login = preg_replace('/_+$/', '', $login);

        return $login;
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