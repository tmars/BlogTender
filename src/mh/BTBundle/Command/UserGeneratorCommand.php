<?php

namespace mh\BTBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use mh\Common\Random;
use mh\BTBundle\Entity as Entity;

class UserGeneratorCommand extends DoctrineCommand
{
	const DATA_DIR = '/Data/';

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
		$userHelper = $this->getApplication()->getKernel()->getContainer()->get('user_helper');
        $cacheDir = $this->getApplication()->getKernel()->getCacheDir();
		
        $m_names = file(__DIR__.self::DATA_DIR."m_names.txt", FILE_IGNORE_NEW_LINES);
        $m_second_names = file(__DIR__.self::DATA_DIR."m_second_names.txt", FILE_IGNORE_NEW_LINES);

        $f_names = file(__DIR__.self::DATA_DIR."f_names.txt");
        $f_second_names = file(__DIR__.self::DATA_DIR."f_second_names.txt", FILE_IGNORE_NEW_LINES);

        $nicknames = file(__DIR__.self::DATA_DIR."nicknames.txt", FILE_IGNORE_NEW_LINES);
		
		$avatarsDir = __DIR__.self::DATA_DIR."avatars/";
		if ($handle = opendir($avatarsDir)) {
			while (false !== ($entry = readdir($handle))) {
				if (!is_dir($entry)) {
					$avatars[] = $avatarsDir . $entry;
				}
			}
			closedir($handle);
		}
		
		$em = $this->getEntityManager(null);
		$repo = $em->getRepository('BTBundle:User');
		$currentCount = 0;
		while ($currentCount < $input->getArgument('count')) {

			if (mt_rand(0, 1) == 0) {
                $name = Random::getArrayElement($m_names);
                $sname = Random::getArrayElement($m_second_names);
            } else {
                $name = Random::getArrayElement($f_names);
                $sname = Random::getArrayElement($f_second_names);
            }

			
            $login = $userHelper->getUniqueLogin(Random::popArrayElement($nicknames));
			
            $user = new Entity\User();
            $user->setSource(Entity\User::SOURCE_INTERNAL);
            $user->setName(sprintf("%s %s", $name, $sname));
            $user->setEmail(sprintf("%s@mail.ru", $login));
            $user->setEmailConfirmed(true);
            $user->setLogin($login);
            $user->setPassword('password');
			
			if ($avatars) {
				$avatarSrc = Random::getArrayElement($avatars);
				$pathParts = pathinfo($avatarSrc);
				$local = sprintf("%s/%s.%s", $cacheDir, Random::generate(), $pathParts['extension']);
				file_put_contents($local, file_get_contents($avatarSrc));
				
				$avatar = new UploadedFile($local, $pathParts['basename'], null, null, null, true);
				$user->setFoto(new Entity\UserFoto($avatar));
			}
			
			$em->persist($user);
			$em->flush();	
        
			$output->writeln($user->getLogin());
			$currentCount++;
		}
	}
}