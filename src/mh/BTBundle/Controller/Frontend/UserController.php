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
	
	public function postsAction($login)
	{
		$user = $this->getUserBySlug($login);
		$posts = $this->getRepository('Post')
			->getListByUser($user, $this->container->getParameter('count_post_per_page'), $this->getRequest()->get('page', 1));

		return $this->render('User:posts.html.twig', array(
            'posts' => $posts,
			'profile' => $user,
		));
	}
	
	public function questionsAction($login)
	{
		$user = $this->getUserBySlug($login);
		
		$query = $this->getRepository('Question')->createQueryBuilder('u')
			->select('u')
			->where('u.user = :user AND u.isPublished = true')
			->setParameter(':user', $user)
            ->orderBy('u.createdDate', 'DESC')
        ;
		$questions = $this->getPaginated($query, 10);
		
		return $this->render('User:questions.html.twig', array(
            'questions' => $questions,
			'profile' => $user,
		));
	}
	
	public function answersAction($login)
	{
		$user = $this->getUserBySlug($login);
		
		$query = $this->getRepository('Answer')->createQueryBuilder('u')
			->select('u')
			->where('u.user = :user AND u.isPublished = true')
			->setParameter(':user', $user)
            ->orderBy('u.createdDate', 'DESC')
        ;
		$answers = $this->getPaginated($query, 10);
		
		return $this->render('User:answers.html.twig', array(
            'answers' => $answers,
			'profile' => $user,
		));
	}
	
	public function infoAction($login)
	{
		$user = $this->getUserBySlug($login);
		
		return $this->render('User:info.html.twig', array(
			'profile' => $user,
			'profile_categories' => $this->getRepository('User')->getUserCategories($user),
		));	
	}
	
	private function getUserBySlug($login)
	{
		$user = $this->getRepository('User')->findOneByLogin($login);
		if ( ! $user) {
			throw $this->createNotFoundException('Такой пользователь не зарегистрирован.');
		}
		
		return $user;
	}
}
