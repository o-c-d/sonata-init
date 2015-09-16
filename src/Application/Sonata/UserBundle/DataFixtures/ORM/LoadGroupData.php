<?php
 
namespace Application\Sonata\UserBundle\DataFixtures\ORM;
 
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Application\Sonata\UserBundle\Entity\Group;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
 
 
/**
 * Définition de mes groupes
 */
class LoadGroupData extends AbstractFixture
    implements FixtureInterface, OrderedFixtureInterface
{
 
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager ORM Manager
     *
     * @return void
     */
    public function load(ObjectManager $manager)
    {
 
        $superadminsGroup = new Group('Super Administrateur');
        $superadminsGroup->addRole('ROLE_USER');
        $superadminsGroup->addRole('ROLE_SONATA_PAGE_ADMIN_PAGE_EDIT');
        $superadminsGroup->addRole('ROLE_SONATA_PAGE_ADMIN_BLOCK_EDIT');
        $superadminsGroup->addRole('ROLE_ADMIN');
        $superadminsGroup->addRole('ROLE_SONATA_ADMIN');
        $superadminsGroup->addRole('ROLE_ALLOWED_TO_SWITCH');
        $manager->persist($superadminsGroup);
        $manager->flush();
        $this->addReference('superadmin-group', $superadminsGroup);
 
        $adminsGroup = new Group('Administrateur');
        $adminsGroup->addRole('ROLE_USER');
        $adminsGroup->addRole('ROLE_SONATA_PAGE_ADMIN_PAGE_EDIT');
        $adminsGroup->addRole('ROLE_SONATA_PAGE_ADMIN_BLOCK_EDIT');
        $adminsGroup->addRole('ROLE_ADMIN');
        $adminsGroup->addRole('ROLE_SONATA_ADMIN');
        $manager->persist($adminsGroup);
        $manager->flush();
        $this->addReference('admin-group', $adminsGroup);
 
        $moderateursGroup = new Group('Modérateur');
        $moderateursGroup->addRole('ROLE_USER');
        $moderateursGroup->addRole('ROLE_SONATA_PAGE_ADMIN_PAGE_EDIT');
        $moderateursGroup->addRole('ROLE_SONATA_PAGE_ADMIN_BLOCK_EDIT');
        $manager->persist($moderateursGroup);
        $manager->flush();
        $this->addReference('moderateur-group', $moderateursGroup);
    }
 
    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }
 
}