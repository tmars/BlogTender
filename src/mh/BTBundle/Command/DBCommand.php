<?php

namespace mh\BTBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use mh\Common\Slug;

class DBCommand extends DoctrineCommand
{
	protected function configure()
    {
		
        $this
            ->setName('mh:db:refresh')
            ->setDescription('Apply patches')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$container = $this->getApplication()->getKernel()->getContainer();
		
		$em = $this->getEntityManager(null);
		$allocator = $container->get('scores_allocator');

        $users = $em->getRepository('BTBundle:User')->findAll();
		foreach ($users as $user) {
			$types = $allocator->getTypes($user);
			$scores = 0;
			foreach ($types as $query) {
				$stmt = $em->getConnection()->prepare($query);
				$stmt->execute();
				$rows = $stmt->fetchAll();
				$scores += $rows[0]['scores'];
			}
			$user->setScores($scores);
		}

		$em->flush();
		
	}
}