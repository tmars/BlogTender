<?php

namespace mh\BTBundle\Command;

define('STDIN',fopen("php://stdin","r"));

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LikeCountReaderCommand extends DoctrineCommand
{

	protected function configure()
    {
        $this
            ->setName('mh:lcr:read')
            ->setDescription('Like count reader.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$em = $this->getEntityManager(null);
		$router = $this->getContainer()->get('router');
		$countReader = $this->getContainer()->get('like_count_reader');
		
		$posts = $em->getRepository('BTBundle:Post')->findAll();
			
		foreach ($posts as $post) {
			$url = $router->generate('show_post', array(
				'login' => $post->getUser()->getLogin(),
				'post_slug' => $post->getSlug(),
			), true);
			
			$facebook 		= $countReader::facebook($url);
			$vk 			= $countReader::vk($url);
			$twitter 		= $countReader::twitter($url);
			$odn 			= $countReader::odn($url);
			$mail 			= $countReader::mail($url);
			$gp 			= $countReader::gp($url);
			
		}
	}

}