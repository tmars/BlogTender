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

	public function listByCategoryAction($slug)
	{
		$category = $this->getRepository('Category')->findOneBySlug($slug);

		if (!$category) {
			throw $this->createNotFoundException('Нет категории.');
		}

		$posts = $this->getRepository('Post')->getListByCategory(
				$category,
				$this->container->getParameter('count_post_per_page'),
				$this->getRequest()->get('page', 1));

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

		return $this->render('Post:list_by_login.html.twig', array(
            'posts' => $posts,
			'profile' => $user,
		));
	}

	public function showAction($id)
    {
		$post = $this->getRepository('Post')->find($id);
		
		if (!$post) {
			throw $this->createNotFoundException('Пост отсутствует.');
		}

		if (!$post->getIsPublished()) {
			throw $this->createNotFoundException('Ошибка доступа.');
		}

		$form = $this->createForm(new \mh\BTBundle\Form\Frontend\CommentType());
		$form->setData(array('post_id' => $post->getId()));

		$comments = $this->getRepository('Post')->getComments($post, 5);

		return $this->render('Post:show.html.twig', array(
            'post' => $post,
			'similar_posts' => $this->getRepository('Post')->getSimilarPosts($post, 6),
			'comments' => $comments,
			'form' => $form->createView(),
        ));
    }
}
