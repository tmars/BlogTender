<?
namespace mh\BTBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use mh\BTBundle\Entity as Entity;

class FixtureLoader implements FixtureInterface
{
	const DATA_DIR = '/../Data/';
	
    public function load(ObjectManager $manager)
    {
        $themes = $this->getThemes();
        $categories = $this->getCategories();

        $this->persistArray($manager, $themes);
        $this->persistArray($manager, $categories);
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

        $titles = file(__DIR__.self::DATA_DIR."themes.txt");
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

        $labels = file(__DIR__.self::DATA_DIR."categories.txt");
        foreach ($labels as $label) {
            $l = new Entity\Category();
            $l->setLabel($label);
            $categories[] = $l;
        }

        return $categories;
    }
}