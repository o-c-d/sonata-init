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
 * Category fixtures loader.
 *
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
     * Returns the Sonata MediaManager.
     *
     * @return \Sonata\CoreBundle\Model\ManagerInterface
     */
    public function getCategoryManager()
    {
        return $this->container->get('sonata.classification.manager.category');
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $defaultContext = $this->getReference('context_default');

        $defaultCategory = $this->getCategoryManager()->create();
        $defaultCategory->setName('Default Category');
        $defaultCategory->setSlug('products');
        $defaultCategory->setEnabled(true);
        $defaultCategory->setContext($defaultContext);

        $this->setReference('root_default_category', $defaultCategory);

        $this->getCategoryManager()->save($defaultCategory);

        $cmsContext = $this->getReference('context_cms_page');

        $rootPages = $this->getCategoryManager()->create();
        $rootPages->setName('Root Pages');
        $rootPages->setSlug('pages');
        $rootPages->setEnabled(true);
        $rootPages->setContext($cmsContext);
        $this->setReference('pages_category', $rootPages);

        $this->setReference('root_pages_category', $rootPages);

        $this->getCategoryManager()->save($rootPages);


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
