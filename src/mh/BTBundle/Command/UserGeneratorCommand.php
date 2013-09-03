<?php

namespace mh\BTBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use mh\Common\Random;
use mh\BTBundle\Entity as Entity;

define('DATA_DIR', __DIR__.'/Data/');

class UserGeneratorCommand extends DoctrineCommand
{

	protected function configure()
    {
        $this
            ->setName('mh:gen:user')
            ->setDescription('User generator')
            ->addArgument('count', InputArgument::REQUIRED, 'What count of user you want to generate?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$users = $this->generate($input->getArgument('count'));
		$em = $this->getEntityManager(null);

		foreach ($users as $user) {
			$output->writeln($user->getLogin());
			$em->persist($user);
		}

		$em->flush();
	}

	private function generate($count)
    {
        $users = array();

        $m_names = file(DATA_DIR."m_names.txt", FILE_IGNORE_NEW_LINES);
        $m_second_names = file(DATA_DIR."m_second_names.txt", FILE_IGNORE_NEW_LINES);

        $f_names = file(DATA_DIR."f_names.txt");
        $f_second_names = file(DATA_DIR."f_second_names.txt", FILE_IGNORE_NEW_LINES);

        $nicknames = file(DATA_DIR."nicknames.txt", FILE_IGNORE_NEW_LINES);

        for ($i = 0; $i < $count; $i++) {

            if (mt_rand(0, 1) == 0) {
                $name = Random::getArrayElement($m_names);
                $sname = Random::getArrayElement($m_second_names);
            } else {
                $name = Random::getArrayElement($f_names);
                $sname = Random::getArrayElement($f_second_names);
            }

            $login = Random::popArrayElement($login);

            $user = new Entity\User();
            $user->setSource(Entity\User::SOURCE_INTERNAL);
            $user->setName(sprintf("%s %s", $name, $sname));
            $user->setEmail(sprintf("%s@mail.ru", $login));
            $user->setEmailConfirmed(true);
            $user->setLogin($login);
            $user->setPassword('password');

            $users[] = $user;
        }

        return $users;
    }
}