<?
namespace mh\BTBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;
use mh\Common\Random;

use mh\BTBundle\Entity as Entity;

define('DATA_DIR', __DIR__.'/../Data/');
define('WEB_DIR', __DIR__.'/../../../../../web/');

class FixtureLoader implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $fs = new Filesystem();
        $fs->mkdir(WEB_DIR.'images/user_foto/original');
        $fs->mkdir(WEB_DIR.'images/user_foto/miniview');
        $fs->mkdir(WEB_DIR.'images/user_foto/microview');
        $fs->mkdir(WEB_DIR.'images/user_foto/q122');
        $fs->mkdir(WEB_DIR.'images/user_foto/q190');

        $themes = $this->getThemes();
        $categories = $this->getCategories();
        $users = $this->getUsers(20);

        $this->persistArray($manager, $themes);
        $this->persistArray($manager, $categories);
        $this->persistArray($manager, $users);
        $manager->flush();
    }

    private function persistArray(ObjectManager $manager, array $data)
    {
        foreach ($data as $d) {
            $manager->persist($d);
        }
    }

    private function getThemes()
    {
        $themes = array();

        $titles = file(DATA_DIR."themes.txt");
        foreach ($titles as $title) {
            $t = new Entity\ThemeForPost();
            $t->setTitle($title);
            $themes[] = $t;
        }

        return $themes;
    }

    private function getCategories()
    {
        $categories = array();

        $labels = file(DATA_DIR."categories.txt");
        foreach ($labels as $label) {
            $l = new Entity\Category();
            $l->setLabel($label);
            $categories[] = $l;
        }

        return $categories;
    }

    private function getUsers($count)
    {
        $users = array();

        $m_names = file(DATA_DIR."m_names.txt", FILE_IGNORE_NEW_LINES);
        $m_second_names = file(DATA_DIR."m_second_names.txt", FILE_IGNORE_NEW_LINES);

        $f_names = file(DATA_DIR."f_names.txt");
        $f_second_names = file(DATA_DIR."f_second_names.txt", FILE_IGNORE_NEW_LINES);

        $nicknames = file(DATA_DIR."nicknames.txt", FILE_IGNORE_NEW_LINES);

        for ($i = 0; $i < $count; $i++) {

            if (mt_rand(0, 1) == 0) {
                $name = Random::getArElement($m_names);
                $sname = Random::getArElement($m_second_names);
            } else {
                $name = Random::getArElement($f_names);
                $sname = Random::getArElement($f_second_names);
            }

            $screenName = Random::popArElement($nicknames);

            $user = new Entity\User();
            $user->setSource(Entity\User::SOURCE_INTERNAL);
            $user->setName(sprintf("%s %s", $name, $sname));
            $user->setEmail(sprintf("%s@mail.ru", $screenName));
            $user->setEmailConfirmed(true);
            $user->setScreenName($screenName);
            $user->setPassword('password');

            $users[] = $user;
        }

        return $users;
    }
}