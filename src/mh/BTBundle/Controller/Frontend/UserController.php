<?php

namespace mh\BTBundle\Controller\Frontend;

class UserController extends Base\BaseUserController
{
    public function ratingAction()
    {
        $query = $this->getRepository('User')->createQueryBuilder('u')
			->select('u')
            ->orderBy('u.scores', 'DESC')
        ;

		$users = $this->getPaginated($query, 10);

		return $this->render('User:rating.html.twig', array(
            'users' => $users,
        ));
    }
	
	public function infoAction($login)
	{
		$user = $this->getRepository('User')->findOneByLogin($login);
		if ( ! $user) {
			throw $this->createNotFoundException('Такой пользователь не зарегистрирован.');
		}
		
		return $this->render('User:info.html.twig', array(
			'profile' => $user,
			'profile_categories' => $this->getRepository('User')->getUserCategories($user),
		));	
	}
}
