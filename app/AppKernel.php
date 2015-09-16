<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            // new AppBundle\AppBundle(),
			// Customization starts here
			// Sonata Core
            new Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),
			new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
			new Sonata\CoreBundle\SonataCoreBundle(),
			new Sonata\BlockBundle\SonataBlockBundle(),
			// KnpMenuBundle, version 2.0 required for SonataAdminBundle and required for html5.html.twig (layout) in OcdCorporateBundle
			new Knp\Bundle\MenuBundle\KnpMenuBundle(),
			// add the mysql storage bundle
			new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
			// add SonataAdminBundle
			new Sonata\AdminBundle\SonataAdminBundle(),
			
			//SonataUserBundle\FOSUserBundle
			new FOS\UserBundle\FOSUserBundle(),
			new Sonata\UserBundle\SonataUserBundle('FOSUserBundle'),
			new Application\Sonata\UserBundle\ApplicationSonataUserBundle(),
			// new Ocd\Sonata\UserBundle\OcdSonataUserBundle(),
	        new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
			
			//SonataPageBundle
			new Sonata\CacheBundle\SonataCacheBundle(),
			new Sonata\SeoBundle\SonataSeoBundle(),
			new Sonata\NotificationBundle\SonataNotificationBundle(),
			new Application\Sonata\NotificationBundle\ApplicationSonataNotificationBundle(),
			// new Ocd\Sonata\NotificationBundle\OcdSonataNotificationBundle(),
			new Sonata\PageBundle\SonataPageBundle(),
			new Application\Sonata\PageBundle\ApplicationSonataPageBundle(),
			// new Ocd\Sonata\PageBundle\OcdSonataPageBundle(),
			
			//SonataFormatterBundle
			new Sonata\MarkItUpBundle\SonataMarkItUpBundle(),
			new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
			new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
			new Sonata\FormatterBundle\SonataFormatterBundle(),
			
			//SonataClassificationBundle
			new JMS\SerializerBundle\JMSSerializerBundle(),
			new Sonata\ClassificationBundle\SonataClassificationBundle(),
			new Application\Sonata\ClassificationBundle\ApplicationSonataClassificationBundle(),
			
			//SonataMediaBundle
            new Sonata\IntlBundle\SonataIntlBundle(),
			new Sonata\MediaBundle\SonataMediaBundle(),
			new Application\Sonata\MediaBundle\ApplicationSonataMediaBundle(),
			
			//SonataNewsBundle
            new Sonata\NewsBundle\SonataNewsBundle(),
            new Application\Sonata\NewsBundle\ApplicationSonataNewsBundle(),
			
			//TimelineBundle
            new Spy\TimelineBundle\SpyTimelineBundle(),
			new Sonata\TimelineBundle\SonataTimelineBundle(),
			new Application\Sonata\TimelineBundle\ApplicationSonataTimelineBundle(),
			
			new FOS\RestBundle\FOSRestBundle(),
			new Symfony\Cmf\Bundle\CreateBundle\CmfCreateBundle(),
			
			//Ocd
			// new Ocd\CorporateBundle\OcdCorporateBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
			$bundles[] = new CoreSphere\ConsoleBundle\CoreSphereConsoleBundle();
			$bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
