<?php

namespace mh\BTBundle\Controller\Frontend;

class PostController extends Base\BaseUserController
{
	public function listAction()
	{
		$posts = $this->getRepository('Post')
			->getList($this->container->getParameter('count_post_per_page'), $this->getRequest()->get('page', 1));

		return $this->render('Post:list.html.twig', array(
            'posts' => $posts,
        ));
	}

	public function listByTagAction($tag)
	{
		$posts = $this->getRepository('Post')
			->getListByTag($tag, $this->container->getParameter('count_post_per_page'), $this->getRequest()->get('page', 1));

		return $this->render('Post:list_by_tag.html.twig', array(
            'posts' => $posts,
			'tag' => $tag,
        ));
	}

	public function listByCategoryAction($category_id)
	{
		$category = $this->getRepository('Category')->find($category_id);

		if (!$category) {
			throw $this->createNotFoundException('Нет категории.');
		}

		$posts = $this->getRepository('Post')
			->getListByCategory($category_id, $this->container->getParameter('count_post_per_page'), $this->getRequest()->get('page', 1));

		return $this->render('Post:list_by_category.html.twig', array(
            'posts' => $posts,
			'category' => $category,
        ));
	}

    public function listByLoginAction($login)
	{
		$user = $this->getRepository('User')->findOneByLogin($login);
		if ( ! $user) {
			throw $this->createNotFoundException('Такой пользователь не зарегистрирован.');
		}

		$posts = $this->getRepository('Post')
			->getListByUser($user, $this->container->getParameter('count_post_per_page'), $this->getRequest()->get('page', 1));

		return $this->render('Post:list.html.twig', array(
            'posts' => $posts,
			'profile' => $user,
			'profile_categories' => $this->getRepository('User')->getUserCategories($user),
        ));
	}

	public function showAction($login, $post_slug)
    {
		//$user = $this->getCached('user_'.$login);

		//if (!$user) {
			$user = $this->getRepository('User')->findOneByLogin($login);
		//	$this->setCached('user_'.$login, $user);
		//}

		if (!$user) {
			throw $this->createNotFoundException('Пользователя такого нет.');
		}

		//$post = $this->getCached('post_show_'.$login.'_'.$post_slug);
		//if (!$post) {
			$post = $this->getRepository('Post')->getPost($user, $post_slug);
		//	$this->setCached('post_show_'.$login.'_'.$post_slug, $post);
		//}

		if (!$post) {
			throw $this->createNotFoundException('Пост отсутствует.');
		}

		if (!$post->getIsPublished()) {
			throw $this->createNotFoundException('Ошибка доступа.');
		}

		$form = $this->createForm(new \mh\BTBundle\Form\Frontend\CommentType());
		$form->setData(array('post_id' => $post->getId()));

		$query = $this->getEM()->createQuery(
			'SELECT c FROM BTBundle:PostComment c WHERE c.post = :post ORDER BY c.createdDate DESC'
		)->setParameter('post', $post)->setMaxResults(5);
		$comments = $query->getResult();

		return $this->render('Post:show.html.twig', array(
            'post' => $post,
			'similar_posts' => $this->getRepository('Post')->getSimilarPosts($post, 6),
			'comments' => $comments,
			'form' => $form->createView(),
        ));
    }
}
