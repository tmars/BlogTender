<?php

namespace mh\BTBundle\Command;

if ( ! defined('STDIN')) {
	define('STDIN',fopen("php://stdin","r"));
}
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
			->addArgument('host', InputArgument::REQUIRED, 'Host for generation urls?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$em = $this->getEntityManager(null);
		$router = $this->getContainer()->get('router');
		$countReader = $this->getContainer()->get('like_count_reader');
		$posts = $em->getRepository('BTBundle:Post')->findAll();
			
		foreach ($posts as $post) {
			$url = sprintf("%s%s", $input->getArgument('host'), $router->generate('show_post', array('id' => $post->getId())));
			
			$output->writeln($url);
			
			$facebook 		= $countReader::facebook($url);
			$vk 			= $countReader::vk($url);
			$twitter 		= $countReader::twitter($url);
			$odn 			= $countReader::odn($url);
			$mail 			= $countReader::mail($url);
			$gp 			= $countReader::gp($url);
			
			$output->writeln("facebook=".$facebook);
			$output->writeln("vk=".$vk);
			$output->writeln("twitter=".$twitter);
			$output->writeln("odn=".$odn);
			$output->writeln("mail=".$mail);
			$output->writeln("gp=".$gp);
			$output->writeln("\r\n");
			break;
		}
	}

}