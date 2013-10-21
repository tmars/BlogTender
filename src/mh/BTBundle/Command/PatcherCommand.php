<?php

namespace mh\BTBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use mh\Common\Slug;
use mh\BTBundle\Entity as Entity;

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

        /*$categories = $em->getRepository('BTBundle:Category')->findAll();
		
		foreach ($categories as $category) {
			$slug = Slug::getSlug($category->getLabel());
			echo $slug."\r\n";
			$category->setSlug($slug);
		}*/

        $posts = $em->getRepository('BTBundle:Post')->findAll();
        $answers = $em->getRepository('BTBundle:Answer')->findAll();
        $questions = $em->getRepository('BTBundle:Question')->findAll();

        $items = array(
            'post' => $posts,
            'answer' => $answers,
            'question' => $questions,
        );

        foreach ($items as $mode => $vals) {
            foreach ($vals as $item) {
                if ($item->getScoreObject() === NULL) {
                    $obj = new Entity\ScoreObject();
                    $obj->setUser($item->getUser());
                    $obj->setObjectType($mode);
                    $obj->setScores(10);
                    $obj->setCreatedDate($item->getCreatedDate());

                    $item->setScoreObject($obj);
                    $em->persist($obj);
                }
            }
        }

		$em->flush();
	}
}