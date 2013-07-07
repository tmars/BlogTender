<?php

namespace mh\BTBundle\Controller\Backend;

use mh\BTBundle\Controller\Base\BaseController;
use mh\BTBundle\DBAL\ModerationStatusType;
use Symfony\Component\HttpFoundation\Response;

class InternalController extends BaseController
{
	public function indexAction()
	{
		return $this->render("Internal:index.html.twig");
	}

	public function refreshScoresAction()
    {
        $allocator = $this->get('scores_allocator');

        $users = $this->getRepository('User')->findAll();
		foreach ($users as $user) {
			$types = $allocator->getTypes($user);
			$scores = 0;
			foreach ($types as $query) {
				$stmt = $this->getEM()->getConnection()->prepare($query);
				$stmt->execute();
				$rows = $stmt->fetchAll();
				$scores += $rows[0]['scores'];
			}
			$user->setScores($scores);
		}

		$em = $this->getEM();
		$em->flush();

		return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    public function loadFixturesAction()
    {
        $em = $this->getEM();

		$themes = file(__DIR__.'/../../DataFixtures/themes.dat');

		foreach ($themes as $t) {
			$theme = new \mh\BTBundle\Entity\ThemeForPost();
			$theme->setTitle($t);
			$em->persist($theme);
		}
		$em->flush();
    }

    public function authCheckAction($auth_hash)
    {
        $session = $this->getRepository('UserSession')->findOneByHash($auth_hash);
        if ($session) {
            return new Response(1);
        } else {
            return new Response(0);
        }
    }

    public function clearUnusedImagesAction()
    {
        $string = '';
        foreach (glob("./images/*") as $image) {
            $count = count($this->getRepository("Post")->createQueryBuilder('p')
                ->where('p.content LIKE :filename')
                ->setParameter(':filename', "%{$image}%")
                ->getQuery()->getResult())
            ;
            $string .= substr($image, 9).' Найден в '.$count.' постах';
            if ($count == 0) {
                unlink($image);
                $string .=' удален';
            }
            $string .= "<br>";
        }
        if ($string == '') {
            $string = 'картинок нет';
        }
        return new Response($string);
    }
}
