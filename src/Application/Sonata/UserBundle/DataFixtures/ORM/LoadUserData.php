<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    private $container;

    function getOrder()
    {
        return 2;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $manager = $this->getUserManager();
//        $faker = $this->getFaker();
//        $admin->setEmail($faker->safeEmail);

        $admin = $manager->createUser();
        $admin->setUsername('admin');
        $admin->setEmail("admin@o-c-d.fr");
        $admin->setPlainPassword('admin');
        $admin->setEnabled(true);
        $admin->setSuperAdmin(true);
		$admin->addGroup($this->getReference('superadmin-group'));
        $admin->setLocked(false);
        $manager->updateUser($admin);
        $this->addReference('user-admin', $admin);
		
        $admin = $manager->createUser();
        $admin->setUsername('onyx');
        $admin->setEmail("olivier.camard@o-c-d.fr");
        $admin->setGplusUid('108059256975131213791');  // 
		$admin->setFacebookUid('10153235697982872');
		$admin->setTwitterUid('1011410880');
        $admin->setPlainPassword('admin');
        $admin->setEnabled(true);
        $admin->setSuperAdmin(true);
		$admin->addGroup($this->getReference('superadmin-group'));
        $admin->setLocked(false);
        $manager->updateUser($admin);
		
        $admin = $manager->createUser();
        $admin->setUsername('lolo6tm');
        $admin->setEmail("lois.puig@kctus.fr");
        $admin->setGplusUid('113678467612325175993');  
// 		$admin->setFacebookUid('');
		$admin->setTwitterUid('80530650');
        $admin->setPlainPassword('admin');
        $admin->setEnabled(true);
        $admin->setSuperAdmin(true);
		$admin->addGroup($this->getReference('superadmin-group'));
        $admin->setLocked(false);
        $manager->updateUser($admin);

        $secure = $manager->createUser();
        $secure->setUsername('secure');
        $secure->setEmail("secure@o-c-d.fr");
        $secure->setPlainPassword('secure');
        $secure->setEnabled(true);
        $secure->setSuperAdmin(true);
		$secure->addGroup($this->getReference('superadmin-group'));
        $secure->setLocked(false);
        // google chart qr code : https://www.google.com/chart?chs=200x200&chld=M|0&cht=qr&chl=otpauth://totp/secure@http://demo.sonata-project.org%3Fsecret%3D4YU4QGYPB63HDN2C
        $secure->setTwoStepVerificationCode('4YU4QGYPB63HDN2C');

        $manager->updateUser($secure);

        $user = $manager->createUser();
        $user->setUsername('johndoe');
        $user->setEmail("johndoe@o-c-d.fr");
        $user->setPlainPassword('johndoe');
        $user->setEnabled(true);
        $user->setSuperAdmin(false);
        $user->setLocked(false);

        $this->setReference('user-johndoe', $user);

        $manager->updateUser($user);

        // Behat testing purpose
        $user = $manager->createUser();
        $user->setUsername('behat_user');
        $user->setEmail("behat_user@o-c-d.fr");
        $user->setEnabled(true);
        $user->setPlainPassword('behat_user');

        $manager->updateUser($user);
    }

    /**
     * @return \FOS\UserBundle\Model\UserManagerInterface
     */
    public function getUserManager()
    {
        return $this->container->get('fos_user.user_manager');
    }

    /**
     * @return \Faker\Generator
     */
    public function getFaker()
    {
        return $this->container->get('faker.generator');
    }
}
