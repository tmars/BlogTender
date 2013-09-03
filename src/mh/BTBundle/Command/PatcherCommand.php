<?php

namespace mh\BTBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use mh\Common\Slug;

class PatcherCommand extends DoctrineCommand
{

	protected function configure()
    {
        $this
            ->setName('mh:patcher:apply')
            ->setDescription('Apply patches')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$em = $this->getEntityManager(null);
		$categories = $em->getRepository('BTBundle:Category')->findAll();
		
		foreach ($categories as $category) {
			$slug = Slug::getSlug($category->getLabel());
			echo $slug."\r\n";
			$category->setSlug($slug);
		}

		$em->flush();
	}
}