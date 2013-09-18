<?php

namespace mh\BTBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use mh\Common\HabraParser\API as HabraAPI;
use mh\BTBundle\Entity as Entity;

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
		$pageLimit = ($input->getArgument('count') / 10);
		$list_src = new HabraAPI();
		$items = array();
		
		for ($p = 0; $p < $pageLimit; ++$p) {
			$list_src->change_page("http://habrahabr.ru/");
			$list = $list_src->get_article_list();
			$items += $list['items'];
		}
		
		//var_dump($items);
		
		$posts = array();
		$currentCount = 0;
		while ($currentCount < $input->getArgument('count')) {
			$output->writeln("http://habrahabr.ru/post/".$items[$currentCount]['id']);
			
			$post = new Entity\Post();
			
			
			$posts[] = $post;
			
			++$currentCount;
		}
		
		
		/*
		$users = $this->generate();
		$em = $this->getEntityManager(null);

		foreach ($users as $user) {
			$output->writeln($user->getLogin());
			$em->persist($user);
		}

		$em->flush();*/
	}
}