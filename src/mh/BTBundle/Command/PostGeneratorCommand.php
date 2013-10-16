<?php

namespace mh\BTBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use mh\Common\HabraParser\API as HabraAPI;
use mh\BTBundle\Entity as Entity;
use mh\Common\Random;

class PostGeneratorCommand extends DoctrineCommand
{
	protected function configure()
    {
        $this
            ->setName('mh:gen:post')
            ->setDescription('Post generator')
            ->addArgument('count', InputArgument::REQUIRED, 'What count of post you want to generate?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$em = $this->getEntityManager(null);
		$users = $em->getRepository('BTBundle:User')->findAll();
		$categories = $em->getRepository('BTBundle:Category')->findAll();
		$allocator = $this->getApplication()->getKernel()->getContainer()->get('scores_allocator');
		$eventsList = $this->getApplication()->getKernel()->getContainer()->get('events_list');
		$avalancheService = $this->getApplication()->getKernel()->getContainer()->get('imagine.cache.path.resolver');
		$cacheDir = $this->getApplication()->getKernel()->getCacheDir();
		$API = new HabraAPI();

		if (count($users) == 0) {
			$output->writeln("Users not exist.");
			return;
		}
		if (count($categories) == 0) {
			$output->writeln("Categories not exist.");
			return;
		}
		
		//var_dump($items);
		$currentCount = 0;
		$items = 0;
		while ($currentCount < $input->getArgument('count')) {
			if ( ! $items) {
				$API->change_page("http://habrahabr.ru/");
				$list = $API->get_article_list();
				$items = $list['items'];
			}
			
			$item = array_pop($items);
			$output->writeln("http://habrahabr.ru/post/".$item['id']);
			
			$post = new Entity\Post();
			$post->setUser(Random::getArrayElement($users));
			$post->setTitle($item['title']);
			$post->setSubtitle($item['content']);
			$em->persist($post);
			
			// ��������� ����������
			$cont = $API->get_article($item['id'], array('content'));
			$imgArray = $API->prepare_content_for_download($cont['content']);
			
			foreach ($imgArray as $ind => $imgSrc) {
				$pathParts = pathinfo($imgSrc);
				
				$local = sprintf("%s/%s.%s", $cacheDir, Random::generate(), $pathParts['extension']);
				file_put_contents($local, file_get_contents($imgSrc));
				$file = new UploadedFile($local, $pathParts['basename'], null, null, null, true);

                // ������ ������ ���� ����������
				if ($ind == 0) {
					$localImage = sprintf("%s/%s.%s", $cacheDir, Random::generate(), $pathParts['extension']);
					file_put_contents($localImage, file_get_contents($imgSrc));
					
					$fileImage = new UploadedFile($localImage, $pathParts['basename'], null, null, null, true);

					$postImage = new Entity\PostImage($fileImage);
					$post->setImage($postImage);
				}
				
				$attachment = new Entity\PostAttachmentImage($file);
				$attachment->setPost($post);
				$em->persist($attachment);
				
				$newSrc = $attachment->getBrowserPath();
				$newSrc = $avalancheService->getBrowserPath($newSrc, 'image_in_post');

				$cont['content'] = str_replace($imgSrc, $newSrc, $cont['content']);
				$output->writeln("\t".$local);
			}
			
			$post->setContent($cont['content']);
			
			// ��������� �����������
			$commentItems = $API->get_comments($item['id'], $params = array("text", "time"));
			foreach ($commentItems as $commentItem) {
				$comment = new Entity\PostComment();
				$comment->setPost($post);
				$comment->setUser(Random::getArrayElement($users));
				$comment->setContent($commentItem['text']);
				//$comment->setCreatedDate(new \DateTime($commentItem['time']));
				$em->persist($comment);
			}
			
			// ��������� ���������
			$catIds = array();
			for ($i = 0; $i < 3; ++$i) {
				do {
					$category = Random::getArrayElement($categories);
				} while (in_array($category->getId(), $catIds));
				$catIds[] = $category->getId();
				$post->addCategorie($category);
				$category->addPost($post);
			}
			
			// ��������� ����
			foreach ($item['tags'] as $tagItem) {
				Entity\Tag::formatLabel($tagItem['name']);

				if (!$tagItem['name']) {
					continue;
				}

				$tag = $em->getRepository('BTBundle:Tag')->findOneByLabel($tagItem['name']);
				if (!$tag) {
					$tag = new Entity\Tag();
					$tag->setLabel($tagItem['name']);
					$em->persist($tag);
				}
				
				$post->addTag($tag);
				$tag->addPost($post);
			}
			
			$allocator->forPost($post);
			$em->flush();

            $eventsList->happened($eventsList::ADDED_POST, $post);

            ++$currentCount;
		}
		
		
	}
}