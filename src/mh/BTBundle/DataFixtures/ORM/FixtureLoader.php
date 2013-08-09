<?
namespace mh\BTBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;

use mh\BTBundle\Entity as Entity;

define('DATA_DIR', __DIR__.'/../Data/');
define('QUERY_DIR', __DIR__.'/../Query/');
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

        $fs->mkdir(WEB_DIR.'images/post_image/original');
        $fs->mkdir(WEB_DIR.'images/post_image/miniview');
        $fs->mkdir(WEB_DIR.'images/post_image/microview');

        $this->execRawQueries($manager);

        $themes = $this->getThemes();
        $categories = $this->getCategories();
        $users = $this->getUsers(20);

        $this->persistArray($manager, $themes);
        $this->persistArray($manager, $categories);
        $this->persistArray($manager, $users);
        $manager->flush();
    }

    private function execRawQueries(ObjectManager $manager)
    {
        $c = $manager->getConnection();
        $l = mysql_connect($c->getHost(), $c->getUsername(), $c->getPassword());
        mysql_select_db($c->getDatabase());

        $d = dir(QUERY_DIR);
        while (false !== ($file = $d->read())) {
            if ($file != "." && $file != "..") {
                $query = file_get_contents(QUERY_DIR.$file);
                var_dump(mysql_query($query));
                var_dump(mysql_error());
            }
        }
        $d->close();

        mysql_close($l);
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
}