<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\DemoBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadCollectionData
 *
 * @package Sonata\Bundle\EcommerceDemoBundle\DataFixtures\ORM
 *
 * @author  Hugo Briand <briand@ekino.com>
 */
class LoadCollectionData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Returns the Sonata CollectionManager.
     *
     * @return \Sonata\CoreBundle\Model\ManagerInterface
     */
    public function getCollectionManager()
    {
        return $this->container->get('sonata.classification.manager.collection');
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $pageContext = $this->getReference('context_cms_page');
		$collectionManager = $this->getCollectionManager();

        // PHP Fan collection
        $php = $collectionManager->create();
        $php->setName("PHP Fan");
        $php->setSlug("php-fan");
        $php->setDescription("Everything a PHP Fan needs.");
        $php->setEnabled(true);
        $php->setContext($pageContext);
        $collectionManager->save($php);

        $this->setReference('php_collection', $php);

        // Travels collection
        $travel = $collectionManager->create();
        $travel->setName("Travels");
        $travel->setSlug("travels");
        $travel->setDescription("Every travels you want");
        $travel->setEnabled(true);
        $travel->setContext($pageContext);
        $collectionManager->save($travel);

        $this->setReference('travel_collection', $travel);

        // Dummy collection
        $dummy = $collectionManager->create();
        $dummy->setName("Dummys");
        $dummy->setSlug("Dummys");
        $dummy->setDescription("Every dummys you want");
        $dummy->setEnabled(true);
        $dummy->setContext($pageContext);
        $collectionManager->save($dummy);

        $this->setReference('dummy_collection', $dummy);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
}
