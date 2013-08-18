<?php

namespace mh\BTBundle\Controller\Frontend;

use Symfony\Component\Form as Form;
use mh\BTBundle\Form\Frontend\PostType;
use mh\BTBundle\Entity as Entity;
use mh\BTBundle\DBAL\ModerationStatusType;

class ProfileAdminPostController extends Base\BaseUserController
{
	public function listForeignLinkAction()
	{
		$user = $this->getUserOrException();
		$request = $this->getRequest();
		$form = $this->createForm(new \mh\BTBundle\Form\Frontend\UrlType());

		$query = $this->getRepository('PostForeignLink')->createQueryBuilder('p')
			->select('p')
			->innerJoin('p.post', 'c', 'WITH', 'c.user = :user')
			->setParameter(':user', $user);
		;

		$links = $this->getPaginated($query);

		while (1) {
			if ($request->getMethod() != 'POST') {
				break;
			}
			$form->bindRequest($request);

			if (!$form->isValid()) {
				break;
			}

			$data = $form->getData();
			list($target_url) = explode('/', $data['url']);

			$posts = array();
			$urls = array();
			foreach ($this->getRepository("Post")->findBy(array('user' => $user, 'isPublished' => true)) as $post) {
				$urls[$post->getId()] = $this->genUrl('show_post', $post, true);
				$posts[$post->getId()] = $post;
			}

			$checker = $this->get('link_checker');
			$postIds = $checker->hasLinks($target_url, $urls);

			if (false === $postIds) {
				$form->addError(new
					Form\FormError('На странице ссылок не найдено.'));
				break;
			}

			$em = $this->getEM();
			$allocator = $this->get('scores_allocator');
			foreach ($postIds as $postId) {
				if (!$this->getRepository("PostForeignLink")->findOneBy(array('url' => $target_url, 'post' => $posts[$postId]))) {
					$link = new Entity\PostForeignLink();
					$link->setPost($posts[$postId]);
					$link->setUrl($target_url);

					$allocator->forPostForeignLink($link);

					$em->persist($link);
				}
			}


			$em->flush();

			return $this->redirect($this->generateUrl('profile_admin_list_foreign_link'));
		}

		return $this->render("ProfileAdminPost:list_foreign_link.html.twig", array(
			'links' => $links,
			'form' => $form->createView(),
		));
	}

	public function listAction()
    {
        $user = $this->getUserOrException();

		$query = $this->getRepository('Post')-> createQueryBuilder('p')
			->select('p')
            ->where('p.user = :user')
            ->setParameter(':user', $user)
		;

		$posts = $this->getPaginated($query, $this->container->getParameter('count_post_per_page'));

		return $this->render("ProfileAdminPost:list.html.twig", array(
			'posts' => $posts,
		));
    }

	public function newAction()
    {
		$user = $this->getActiveUserOrException();
		$form = $this->createForm(new PostType());
        $request = $this->getRequest();

		while(1) {
            if ($request->getMethod() != 'POST') {
				break;
            }

			$form->bindRequest($request);
			if ( ! $this->validatePost($form)) {
                break;
            }

			$post = new Entity\Post();
			$post->setUser($user);
			$this->updatePost($form, $post);

			$em = $this->getEM();
			$em->persist($post);
			$em->flush();

			return $this->redirect($this->generateUrl('profile_admin_edit_post', array(
				'id' => $post->getId(),
			)));
		}

        $themes = $this->getRepository('ThemeForPost')->getRandom(5);

		return $this->render('ProfileAdminPost:new.html.twig', array(
            'form' => $form->createView(),
			'themes_for_post' => $themes,
        ));
    }

	public function editAction($id)
	{
		$user = $this->getActiveUserOrException();

		$post = $this->getRepository('Post')->findOneBy(array(
			'id' => $id,
			'user' => $user,
		));

		if (!$post) {
			throw $this->createNotFoundException('Доступа нет.');
		}

		$form = $this->createForm(new Entity\PostType());
        $request = $this->getRequest();

		while (1) {

			if ($request->getMethod() != 'POST') {
                $form->setData(array(
					'title' => $post->getTitle(),
					'subtitle' => $post->getSubtitle(),
					'content' => $post->getContent(),
					'tags' => $post->getTagsString(),
					'categories' => $post->getCategories(),
				));

				break;
            }

            $form->bindRequest($request);

			if ( ! $this->validatePost($form)) {
                break;
            }

			$data = $form->getData();
			$this->updatePost($form, $post);

			switch ($data['pub_flag']) {
			case 1:
				if ($post->getModerationStatus() == ModerationStatusType::NOT_VALID) {
					$post->setIsPublished(false);
					$post->setModerationStatus(ModerationStatusType::NOT_MODERATED);
					$form->addError(new Form\FormError('Пост отправлен на проверку модератору.', array('info' => 1)));
				} else {
					$post->setIsPublished(true);
					$form->addError(new Form\FormError('Пост опубликован.', array('info' => 1)));
				}
				break;
			case -1:
				$post->setIsPublished(false);
				$form->addError(new Form\FormError('Пост снят с публикации.', array('info' => 1)));
				break;
			default:
				$form->addError(new Form\FormError('Пост сохранен.', array('info' => 1)));
				break;
			}

			$this->getEM()->flush();

			break;
		}

		$form->addError(new Form\FormError('Количество баллов за пост: '.$post->getScores(), array('info' => 1)));

		return $this->render('ProfileAdminPost:edit.html.twig', array(
            'form' => $form->createView(),
			'post' => $post,
		));

	}

	public function deleteAction($id)
	{
		$user = $this->getActiveUserOrException();

		$post = $this->getRepository('Post')->findOneBy(array(
			'id' => $id,
			'user' => $user,
		));

		if (!$post) {
			throw $this->createNotFoundException('Доступа нет.');
		}

		$em = $this->getEM();
		$em->remove($post->getImage());
		$em->remove($post);
		$em->flush();

		return $this->redirect($this->generateUrl('profile_admin_list_post'));
	}

	private function validatePost(&$form)
	{
		if ( ! $form->isValid()) {
			return false;
        }

		$data = $form->getData();
		
		// Проверка уникальности
		$uniqueChecker = $this->get('unique_checker');
		if (!$uniqueChecker->isUnique($data['content'])) {
			$form->addError(new
				Form\FormError('Текст не уникален.'));
			return false;
		}

		// Проверка количества категорий
		if (count($data['categories']) == 0 || count($data['categories']) > 3) {
			$form->get('categories')->addError(new
				Form\FormError('количество категорий от 1 до 3'));
			return false;
		}

		// Проверка количества тегов
		if (count($data['tags']) == 0 || count($data['tags']) > 10) {
			$form->get('tags')->addError(new
				Form\FormError('количество тегов от 1 до 10'));
			return false;
		}
		
		// Проверка длины поста
		$text = Entity\Post::clearContent($data['content']);
		if (mb_strlen($text) < 700) {
			$form->addError(new
				Form\FormError('Пост должен содержать как минимум 700 символов'));
			return false;
		}

		return true;
	}

	private function updatePost(&$form, &$post)
	{
		$data = $form->getData();
		$em = $this->getEM();

		$post->setTitle($data['title']);
		$post->setSubtitle($data['subtitle']);
		$post->setContent($data['content']);
		if ($data['image']) {
			$post->setImage(new Entity\PostImage($data['image']));
		}

		// заполняем новые категории
		$newCategories = array();
		foreach ($data['categories'] as $category) {
			$newCategories[$category->getId()] = $category;
		}

		// Удаляем категории которых нет
		foreach ($post->getCategories() as $category) {
			if (!array_key_exists($category->getId(), $newCategories)) {
				$post->removeCategorie($category);
				$category->removePost($post);
			} else {
				unset($newCategories[$category->getId()]);
			}
		}

		// Добавляем новые категории
		foreach ($newCategories as $category) {
			$post->addCategorie($category);
			$category->addPost($post);
		}

		// заполняем новые теги
		$newTags = array();
		foreach (explode(',', $data['tags']) as $tag_label) {
			Tag::formatLabel($tag_label);

			if (!$tag_label) {
				continue;
			}

			$tag = $this->getRepository('Tag')->findOneByLabel($tag_label);
			if (!$tag) {
				$tag = new Entity\Tag();
				$tag->setLabel($tag_label);
			}

			$newTags[$tag_label] = $tag;
		}

		// Удаляем тегов которых больше нет
		foreach ($post->getTags() as $tag) {
			if (!array_key_exists($tag->getLabel(), $newTags)) {
				$post->removeTag($tag);
				$tag->removePost($post);
			} else {
				unset ($newTags[$tag->getLabel()]);
			}
		}

		// Добавляем новые теги
		foreach ($newTags as $tag) {
			$post->addTag($tag);
			$tag->addPost($post);
		}

		$allocator = $this->get('scores_allocator');
		$allocator->forPost($post);
	}
}
