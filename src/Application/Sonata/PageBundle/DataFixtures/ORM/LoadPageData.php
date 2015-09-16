<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ocd\Bundle\CorporateBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

use Sonata\PageBundle\Model\SiteInterface;
use Sonata\PageBundle\Model\PageInterface;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadPageData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    private $container;

    public function getOrder()
    {
        return 10;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        if(!isset($_SERVER['REMOTE_ADDR'])||$_SERVER['REMOTE_ADDR']=='127.0.0.1') $site = $this->createSiteLocal();
		else $site = $this->createSite();
        // $site_dev = $this->createSiteLocal();
		/* template page */
        $this->createGlobalPage($site);
		/* Home Page */
        $this->createHomePage($site);
        $this->create404ErrorPage($site);
        $this->create500ErrorPage($site);
        $this->createBlogIndex($site);
        $this->createGalleryIndex($site);
        // $this->createMediaPage($site);
        // $this->createProductPage($site);
        // $this->createBasketPage($site);
        // $this->createUserPage($site);
        // $this->createApiPage($site);
        $this->createLegalNotesPage($site);
        // $this->createTermsPage($site);

        // Create footer pages/**/
        $this->createWhoWeArePage($site);
        $this->createClientTestimonialsPage($site);
        $this->createPressPage($site);
        $this->createFAQPage($site);
        $this->createContactUsPage($site);
        // $this->createBundlesPage($site);

        // $this->createSubSite();
        $this->createStrategiePage($site);
        $this->createReferencementPage($site);
        $this->createTechnologiePage($site);
    }

    /**
     * @return SiteInterface $site
     */
    public function createSite()
    {
        $site = $this->getSiteManager()->create();

        $site->setHost('o-c-d.fr');
        $site->setEnabled(true);
        $site->setName('o-c-d');
        $site->setEnabledFrom(new \DateTime('now'));
        $site->setEnabledTo(new \DateTime('+10 years'));
        $site->setRelativePath("");
        $site->setIsDefault(true);

        $this->getSiteManager()->save($site);

        return $site;
    }

    /**
     * @return SiteInterface $site
     */
    public function createSiteLocal()
    {
        $site = $this->getSiteManager()->create();

        $site->setHost('o-c-d.dev');
        $site->setEnabled(true);
        $site->setName('localhost');
        $site->setEnabledFrom(new \DateTime('now'));
        $site->setEnabledTo(new \DateTime('+10 years'));
        $site->setRelativePath("");
        $site->setIsDefault(true);

        $this->getSiteManager()->save($site);

        return $site;
    }

    public function createSubSite()
    {
        if (!$this->container->hasParameter('sonata.fixtures.page.create_subsite')) {
            return;
        }

        if ($this->container->getParameter('sonata.fixtures.page.create_subsite') !== true) {
            return;
        }

        $site = $this->getSiteManager()->create();

        $site->setHost('localhost');
        $site->setEnabled(true);
        $site->setName('sub site');
        $site->setEnabledFrom(new \DateTime('now'));
        $site->setEnabledTo(new \DateTime('+10 years'));
        $site->setRelativePath("/sub-site");
        $site->setIsDefault(false);

        $this->getSiteManager()->save($site);

        return $site;
    }

    /**
     * @param SiteInterface $site
     */
    public function createBlogIndex(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();

        $blogIndex = $pageManager->create();
        $blogIndex->setSlug('blog');
        $blogIndex->setUrl('/blog');
        $blogIndex->setName('News');
        $blogIndex->setTitle('News');
        $blogIndex->setEnabled(true);
        $blogIndex->setDecorate(1);
        $blogIndex->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $blogIndex->setTemplateCode('default');
        $blogIndex->setRouteName('sonata_news_home');
        $blogIndex->setParent($this->getReference('page-homepage'));
        $blogIndex->setSite($site);

        $pageManager->save($blogIndex);
    }

    /**
     * @param SiteInterface $site
     */
    public function createGalleryIndex(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();
        $blockManager = $this->getBlockManager();
        $blockInteractor = $this->getBlockInteractor();

        $galleryIndex = $pageManager->create();
        $galleryIndex->setSlug('gallery');
        $galleryIndex->setUrl('/media/gallery');
        $galleryIndex->setName('Gallery');
        $galleryIndex->setTitle('Gallery');
        $galleryIndex->setEnabled(true);
        $galleryIndex->setDecorate(1);
        $galleryIndex->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $galleryIndex->setTemplateCode('default');
        $galleryIndex->setRouteName('sonata_media_gallery_index');
        $galleryIndex->setParent($this->getReference('page-homepage'));
        $galleryIndex->setSite($site);

        // CREATE A HEADER BLOCK
        $galleryIndex->addBlocks($content = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page' => $galleryIndex,
            'code' => 'content_top',
        )));

        $content->setName('The content_top container');

        // add the breadcrumb
        $content->addChildren($breadcrumb = $blockManager->create());
        $breadcrumb->setType('sonata.page.block.breadcrumb');
        $breadcrumb->setPosition(0);
        $breadcrumb->setEnabled(true);
        $breadcrumb->setPage($galleryIndex);

        // add a block text
        $content->addChildren($text = $blockManager->create());
        $text->setType('sonata.block.service.text');
        $text->setSetting('content', <<<CONTENT
<h1>Gallery List</h1>
<p>
    This current text is defined in a <code>text block</code> linked to a custom symfony action <code>GalleryController::indexAction</code>
    the SonataPageBundle can encapsulate an action into a dedicated template. <br /><br />
    If you are connected as an admin you can click on <code>Show Zone</code> to see the different editable areas. Once
    areas are displayed, just double click on one to edit it.
</p>
CONTENT
        );
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($galleryIndex);

        $pageManager->save($galleryIndex);
    }

    /**
     * @param SiteInterface $site
     */
    public function createTermsPage(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();
        $blockManager = $this->getBlockManager();
        $blockInteractor = $this->getBlockInteractor();

        $terms = $pageManager->create();
        $terms->setSlug('shop-payment-terms-and-conditions');
        $terms->setUrl('/shop/payment/terms-and-conditions');
        $terms->setName('Terms and conditions');
        $terms->setTitle('Terms and conditions');
        $terms->setEnabled(true);
        $terms->setDecorate(1);
        $terms->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $terms->setTemplateCode('2columns');
        $terms->setRouteName('sonata_payment_terms');
        $terms->setParent($this->getReference('page-homepage'));
        $terms->setSite($site);

        // CREATE A HEADER BLOCK
        $terms->addBlocks($content = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page' => $terms,
            'code' => 'content_top',
        )));
        $content->setName('The content_top container');

        // add the breadcrumb
        $content->addChildren($breadcrumb = $blockManager->create());
        $breadcrumb->setType('sonata.page.block.breadcrumb');
        $breadcrumb->setPosition(0);
        $breadcrumb->setEnabled(true);
        $breadcrumb->setPage($terms);

        $pageManager->save($terms);
    }

    /**
     * @param SiteInterface $site
     */
    public function createHomePage(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();
        $blockManager = $this->getBlockManager();
        $blockInteractor = $this->getBlockInteractor();

        $this->addReference('page-homepage', $homepage = $pageManager->create());
        $homepage->setSlug('/');
        $homepage->setUrl('/');
        $homepage->setName('sonata_seo_homepage_breadcrumb');
        $homepage->setTitle('Accueil');
        $homepage->setEnabled(true);
        $homepage->setDecorate(0);
        $homepage->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $homepage->setTemplateCode('default');
        $homepage->setRouteName(PageInterface::PAGE_ROUTE_CMS_NAME);
        $homepage->setSite($site);

        $pageManager->save($homepage);

        // CREATE A HEADER BLOCK
        $homepage->addBlocks($contentTop = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page' => $homepage,
            'code' => 'content_top',
        )));
        $contentTop->setName('The container top container');
        $blockManager->save($contentTop);
        // Add media gallery block
		/*
        $contentTop->addChildren($gallery = $blockManager->create());
        $gallery->setType('sonata.media.block.gallery');
        $gallery->setSetting('galleryId', $this->getReference('media-gallery')->getId());
        $gallery->setSetting('context', 'default');
        $gallery->setSetting('format', 'big');
        $gallery->setPosition(1);
        $gallery->setEnabled(true);
        $gallery->setPage($homepage);
		*/

		
		
        // CREATE A content BLOCK
        $homepage->addBlocks($content = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page' => $homepage,
            'code' => 'content',
        )));
        $content->setName('The content container');
        $blockManager->save($content);
		
        // add a block text
		/* 
        $content->addChildren($text = $blockManager->create());
        $text->setType('sonata.block.service.text');
        $text->setSetting('content', <<<CONTENT
<h1>Agence de communication pour TPE</h1>
<div class="col-md-3 welcome">
  <article id="viewport">
    <section id="tetrahedron">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
    </section>
  </article>
</div>
<div class="col-md-9">
	<p>OCD met à la disposition des TPE les outils de communication corporate des grands comptes.</p>
    <p>Mettez les technologies du web au service de votre business</p>
	<ul>
		<li>Formalisez votre stratégie pour harmoniser vos communications</li>
		<li>Optez pour des outils de publication web performants et innovants</li>
		<li>Référencez vos contenus dans les moteurs de recherche</li>
		<li>Utilisez les réseaux sociaux pour développer votre communauté</li>
		<li></li>
	</ul>
</div>
CONTENT
        );
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($homepage);
		*/

        // add a block text
		/*
        $content->addChildren($text = $blockManager->create());
        $text->setType('sonata.block.service.text');
        $text->setSetting('content', <<<CONTENT
<h1>Agence de communication Web</h1>
<p>
Bientôt disponible...
</p>
CONTENT
        );
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($homepage);
		*/
		
		/**/
        $content->addChildren($text = $blockManager->create());
        $text->setType('sonata.block.service.text');
        $text->setSetting('content', <<<CONTENT
<h1>Agence de communication Web</h1>
<div class="col-md-3 welcome">
	<div class="gizeh">
	  <div class="pyramid-axis">
		  <div class="pyramid">
			<div class="pyramid-wall front"></div>
			<div class="pyramid-wall back"></div>
			<div class="pyramid-wall left"></div>
			<div class="pyramid-wall right"></div>    
			<div class="pyramid-bottom"></div>
		  </div>
		  <div class="pyramid-shadow"></div>
	  </div>
	</div>
</div>
<div class="col-md-9">
	<p>OCD met à la disposition des TPE les outils de communication corporate des grands comptes.</p>
    <p>Mettez les technologies du web au service de votre business</p>
	<ul>
		<li>Formalisez votre <a href="/strategie" class="pyramid_front_link">stratégie</a> pour harmoniser vos communications</li>
		<li>Optez pour des <a href="/" class="pyramid_right_link">outils de publication web</a> performants et innovants</li>
		<li>Référencez vos contenus dans les <a href="/" class="pyramid_back_link">moteurs de recherche</a></li>
		<li>Utilisez les <a href="/" class="pyramid_left_link">réseaux sociaux</a> pour développer votre communauté</li>
		<li>Tenez-vous informé des dernières tendances du web grâce à notre <a href="/" class="pyramid_bottom_link">veille permanente</a>.</li>
	</ul>
</div>
CONTENT
        );
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($homepage);
		


        // Add recent products block
		/*  
        $content->addChildren($newProductsBlock = $blockManager->create());
        $newProductsBlock->setType('sonata.product.block.recent_products');
        $newProductsBlock->setSetting('number', 4);
        $newProductsBlock->setSetting('title', 'New products');
        $newProductsBlock->setPosition(2);
        $newProductsBlock->setEnabled(true);
        $newProductsBlock->setPage($homepage);
		*/

        // Add homepage bottom container
        $homepage->addBlocks($bottom = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page'    => $homepage,
            'code'    => 'content_bottom',
        ), function ($container) {
            $container->setSetting('layout', '{{ CONTENT }}');
        }));
        $bottom->setName('The bottom content container');

        // Add homepage newsletter container
        // $bottom->addChildren($bottomNewsletter = $blockInteractor->createNewContainer(array(
            // 'enabled' => true,
            // 'page'    => $homepage,
            // 'code'    => 'bottom_newsletter',
        // ), function ($container) {
            // $container->setSetting('layout', '<div class="block-newsletter col-sm-6 well">{{ CONTENT }}</div>');
        // }));
        // $bottomNewsletter->setName('The bottom newsetter container');
        // $bottomNewsletter->addChildren($newsletter = $blockManager->create());
        // $newsletter->setType('sonata.demo.block.newsletter');
        // $newsletter->setPosition(1);
        // $newsletter->setEnabled(true);
        // $newsletter->setPage($homepage);

        // Add homepage embed tweet container
		/*
        $bottom->addChildren($bottomEmbed = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page'    => $homepage,
            'code'    => 'bottom_embed',
        ), function ($container) {
            $container->setSetting('layout', '<div class="col-sm-6">{{ CONTENT }}</div>');
        }));
        $bottomEmbed->setName('The bottom embedded tweet container');
        $bottomEmbed->addChildren($embedded = $blockManager->create());
        $embedded->setType('sonata.seo.block.twitter.embed');
        $embedded->setPosition(1);
        $embedded->setEnabled(true);
        $embedded->setSetting('tweet', "https://twitter.com/dunglas/statuses/438337742565826560");
        $embedded->setSetting('lang', "en");
        $embedded->setPage($homepage);
		*/

        $pageManager->save($homepage);
    }

    /**
     * @param SiteInterface $site
     */
    public function createStrategiePage(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();
        $blockManager = $this->getBlockManager();
        $blockInteractor = $this->getBlockInteractor();

        $this->addReference('page-strategie', $page = $pageManager->create());
        $page->setSlug('strategie');
        $page->setUrl('/strategie');
        $page->setName('Strategie');
        $page->setTitle('Strategie web');
        $page->setEnabled(true);
        $page->setDecorate(0);
        $page->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $page->setTemplateCode('default');
        $page->setRouteName('page_slug');
        $page->setSite($site);

        $pageManager->save($page);

        // CREATE A HEADER BLOCK
        $page->addBlocks($contentTop = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page' => $page,
            'code' => 'content_top',
        )));
        $contentTop->setName('The container top container');
        $blockManager->save($contentTop);
        
		// add the breadcrumb
        $contentTop->addChildren($breadcrumb = $blockManager->create());
        $breadcrumb->setType('sonata.page.block.breadcrumb');
        $breadcrumb->setPosition(0);
        $breadcrumb->setEnabled(true);
        $breadcrumb->setPage($page);
		
        // CREATE A content BLOCK
        $page->addBlocks($content = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page' => $page,
            'code' => 'content',
        )));
        $content->setName('The content container');
        $blockManager->save($content);
		
		/**/
        $content->addChildren($text = $blockManager->create());
        $text->setType('sonata.block.service.text');
        $text->setSetting('content', <<<CONTENT
<h2>Stratégies de communication Web</h2>
<div class="col-md-6">
  <div class="tetrahedron_container">
		<div class='polyhedron polyhedron--tetrahedron'>
		  <div class='polyhedron__face polyhedron--tetrahedron__face'></div>
		  <div class='polyhedron__face polyhedron--tetrahedron__face'></div>
		  <div class='polyhedron__face polyhedron--tetrahedron__face'></div>
		</div>
  </div>
  <!--
  <article id="viewport">
    <section id="tetrahedron">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
    </section>
  </article>
  -->
</div>
<div class="col-md-6">
	<p>Définir des objectifs précis et une stratégie adaptée est un prérequis essentiel pour toute communication.</p>
	<p>La difficulté pour le chef d'une PME/TPE est souvent de partager cette stratégie avec ses collaborateurs et prestataires.</p>
    <p>Nous vous aidons définir votre stratégie web et à rédiger des documents adpatés à votre activité.</p>
	<ul>
		<li>Charte de communication web</li>
		<li>Charte graphique</li>
		<li>Politique de confidentialité</li>
		<li>Dossier de presse</a></li>
	</ul>
</div>
CONTENT
        );
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($page);
		
        $page->setParent($this->getReference('page-homepage'));
        $pageManager->save($page);
    }

    /**
     * @param SiteInterface $site
     */
    public function createTechnologiePage(SiteInterface $site)
    {
        $this->createTextContentPage($site, 'technologie', 'Technologies web', <<<CONTENT
<div class="col-md-6">
	<div class="polyhedron_transform">
		<div class='polyhedron polyhedron--truncated polyhedron--tetrahedron'>
		  <div class='polyhedron__face polyhedron--tetrahedron__face'>
			<div class='polygon polygon--triangle outermorph'>
			  <div class='polygon polygon--triangle__b'>
				<div class='transformer transformer--scaler-reverse'>
				  <div class='polygon polygon--triangle'></div>
				</div>
			  </div>
			</div>
		  </div>
		  <div class='polyhedron__face polyhedron--tetrahedron__face'>
			<div class='polygon polygon--triangle outermorph'>
			  <div class='polygon polygon--triangle__b'>
				<div class='transformer transformer--scaler-reverse'>
				  <div class='polygon polygon--triangle'></div>
				</div>
			  </div>
			</div>
		  </div>
		  <div class='polyhedron__face polyhedron--tetrahedron__face'>
			<div class='polygon polygon--triangle outermorph'>
			  <div class='polygon polygon--triangle__b'>
				<div class='transformer transformer--scaler-reverse'>
				  <div class='polygon polygon--triangle'></div>
				</div>
			  </div>
			</div>
		  </div>
		  <div class='polyhedron__face polyhedron--tetrahedron__face'>
			<div class='polygon polygon--triangle outermorph'>
			  <div class='polygon polygon--triangle__b'>
				<div class='transformer transformer--scaler-reverse'>
				  <div class='polygon polygon--triangle'></div>
				</div>
			  </div>
			</div>
		  </div>
		  <div class='polyhedron__face'>
			<div class='transformer transformer--scaler-shifter'>
			  <div class='polygon polygon--triangle'></div>
			</div>
		  </div>
		  <div class='polyhedron__face'>
			<div class='transformer transformer--scaler-shifter'>
			  <div class='polygon polygon--triangle'></div>
			</div>
		  </div>
		  <div class='polyhedron__face'>
			<div class='transformer transformer--scaler-shifter'>
			  <div class='polygon polygon--triangle'></div>
			</div>
		  </div>
		  <div class='polyhedron__face'>
			<div class='transformer transformer--scaler-shifter'>
			  <div class='polygon polygon--triangle'></div>
			</div>
		  </div>
		</div>
	</div>
</div>
<div class="col-md-6">
<p>
Les technologies du web sont en perpétuelle évolution, choisir la plus adpatée à vos besoin est souvent un défi, mais notre expertise est là pour vous guider.
<br/>
Nous mettons à votre disposition les technologies les plus innovantes pour que vos clients bénéficient en permanence d'une expérience de navigation optimale.
</p>
<dl>
	<dt>Blogs</dt>
		<dd>Wordpress est la référence en matière de blog, nous vous proposons de l'adapter entièrement à vos besoins, en développant des modules sur mesure.</dd>
	<dt>CMS</dt>
		<dd>Symfony2 est plebiscité par les grands comptes pour  leurs développements métiers, nous mettons à votre disposition les meilleurs CMS conçus sous Symfony2.</dd>
	<dt>E-commerce</dt>
		<dd>Prestashop est la plateforme e-commerce la plus utilisée, nous mettons ne ligne votre eboutique, nous vous aidons à créer et gérer les produits.</dd>
	<dt></dt>
		<dd></dd>
	<dt></dt>
		<dd></dd>
</dl>
</div>

CONTENT
        );
    }

    /**
     * @param SiteInterface $site
     */
    public function createReferencementPage(SiteInterface $site)
    {
        $this->createTextContentPage($site, 'referencement', 'Référencement de votre activité', <<<CONTENT
<div class="col-md-6">
  <section class="mappemonde">
    <figure class="earth"><span class="shadow"></span></figure>
  </section>
<!--
  <div class="sphere_container">
		<div class="sphere">
		  <div class="m circle"></div>
		  <div class="m circle"></div>
		  <div class="m circle"></div>
		  <div class="cube">
			<div class="i"></div>
		  </div>
		  <div class="p circle"></div>
		  <div class="p circle"></div>
		  <div class="p circle"></div>
		  <div class="p circle"></div>
		  <div class="p circle"></div>
		</div>
	</div>
-->
</div>
<div class="col-md-6">
<p>
Contrairement à une idée répandue, le référencement n'est pas qu'une question de budget. Nous vous aidons à optimiser votre présence sur le web en fonciton de votre investissement.
</p>
<dl>
	<dt>SEO</dt>
		<dd>L'optimisation de votre site pour les moteurs de recherche doit être pris en compte dès sa conception.</dd>
	<dt>SEA/TF/PI</dt>
		<dd>L'achat de positionnement sur les moters de recherche nécessite une stratégie précise pour vous permettre d'optimiser votre budget.</dd>
	<dt>SMO/SMM</dt>
		<dd>Les réseaux sociaux sont une formidable opportunité de développer votre communauté en même temps que votre visibilité.</dd>
</dl>

</div>

CONTENT
        );
    }

    /**
     * @param SiteInterface $site
     */
    public function createProductPage(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();

        $category = $pageManager->create();

        $category->setSlug('shop-category');
        $category->setUrl('/shop/category');
        $category->setName('Shop');
        $category->setTitle('Shop');
        $category->setEnabled(true);
        $category->setDecorate(1);
        $category->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $category->setTemplateCode('default');
        $category->setRouteName('sonata_catalog_index');
        $category->setSite($site);
        $category->setParent($this->getReference('page-homepage'));

        $pageManager->save($category);
    }

    /**
     * @param SiteInterface $site
     */
    public function createBasketPage(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();

        $basket = $pageManager->create();

        $basket->setSlug('shop-basket');
        $basket->setUrl('/shop/basket');
        $basket->setName('Basket');
        $basket->setEnabled(true);
        $basket->setDecorate(1);
        $basket->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $basket->setTemplateCode('default');
        $basket->setRouteName('sonata_basket_index');
        $basket->setSite($site);
        $basket->setParent($this->getReference('page-homepage'));

        $pageManager->save($basket);
    }

    /**
     * @param SiteInterface $site
     */
    public function createMediaPage(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();
        $blockManager = $this->getBlockManager();
        $blockInteractor = $this->getBlockInteractor();

        $this->addReference('page-media', $media = $pageManager->create());
        $media->setSlug('/media');
        $media->setUrl('/media');
        $media->setName('Media & Seo');
        $media->setTitle('Media & Seo');
        $media->setEnabled(true);
        $media->setDecorate(1);
        $media->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $media->setTemplateCode('default');
        $media->setRouteName('sonata_demo_media');
        $media->setSite($site);
        $media->setParent($this->getReference('page-homepage'));

        // CREATE A HEADER BLOCK
        $media->addBlocks($content = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page' => $media,
            'code' => 'content_top',
        )));

        $content->setName('The content_top container');

        // add the breadcrumb
        $content->addChildren($breadcrumb = $blockManager->create());
        $breadcrumb->setType('sonata.page.block.breadcrumb');
        $breadcrumb->setPosition(0);
        $breadcrumb->setEnabled(true);
        $breadcrumb->setPage($media);

        $pageManager->save($media);
    }

    /**
     * @param SiteInterface $site
     */
    public function createUserPage(SiteInterface $site)
    {
        $this->createTextContentPage($site, 'user', 'Admin', <<<CONTENT
<div>

    <h3>Available accounts</h3>
    You can connect to the <a href="/admin/dashboard">admin section</a> by using two different accounts:<br>

    <ul>
        <li><em>Standard</em> user:
            <ul>
                <li> Login - <strong>johndoe</strong></li>
                <li> Password - <strong>johndoe</strong></li>
            </ul>
        </li>
        <li><em>Admin</em> user:
            <ul>
                <li> Login - <strong>admin</strong></li>
                <li> Password - <strong>admin</strong></li>
            </ul>
        </li>
        <li><em>Two-step Verification admin</em> user:
            <ul>
                <li> Login - <strong>secure</strong></li>
                <li> Password - <strong>secure</strong></li>
                <li> Key - <strong>4YU4QGYPB63HDN2C</strong></li>
            </ul>
        </li>
    </ul>

    <h3>Two-Step Verification</h3>
    The <strong>secure</strong> account is a demo of the Two-Step Verification provided by
    the <a href="https://sonata-project.org/bundles/user/2-0/doc/reference/two_step_validation.html">Sonata User Bundle</a>

    <br />
    <br />
    <center>
        <img src="/bundles/sonatademo/images/secure_qr_code.png" class="img-polaroid" />
        <br />
        <em>Take a shot of this QR Code with <a href="https://support.google.com/accounts/bin/answer.py?hl=en&answer=1066447">Google Authenticator</a></em>
    </center>

</div>

CONTENT
        );
    }

    /**
     * @param SiteInterface $site
     */
    public function createApiPage(SiteInterface $site)
    {
        $this->createTextContentPage($site, 'api-landing', 'API', <<<CONTENT
<div>

    <h3>Available account</h3>
    You can connect to the <a href="/api/doc">api documentation</a> by using the following account:<br>

    <ul>
        <li> Login - <strong>admin</strong></li>
        <li> Password - <strong>admin</strong></li>
    </ul>

    <br />
    <br />
    <center>
        <img src="/bundles/sonatademo/images/api.png" class="img-rounded" />
    </center>

</div>

CONTENT
        );
    }

    /**
     * @param SiteInterface $site
     */
    public function createLegalNotesPage(SiteInterface $site)
    {
        $this->createTextContentPage($site, 'informations-legales', 'Informations légales', <<<CONTENT
<h3>Responsables de la publication</h3>

    Olivier CAMARD, gérant d'Open Code Development

<h3>Hébergement</h3>
<p>
1&1 Internet SARL<br/>
7, place de la Gare<br/>
BP 70109<br/>
57201 Sarreguemines Cedex <br/>
0970 808 911

</p>

<h3>Données personnelles</h3>

<p>Les données personnelles collectées par OcD sont uniquement destinées à un usage interne. En aucun cas ces données ne seront communiquées ou vendues à des tiers. Conformément à la législation française, vous disposez d'un droit d'accès, de modification, de rectification et de suppression des données qui vous concernent.</p>

<h3>Cookies</h3>

<p>Les "cookies" sont des petits fichiers que l'administrateur d'un serveur installe sur votre ordinateur et qui permettent de mémoriser des données relatives à l'internaute lorsque celui-ci se connecte au site. o-c-d.fr utilise quelques cookies pour mémoriser les préférences de votre profil, sans retenir aucune autre donnée masquée.</p>

<h3>Liens</h3>

<p>
Les sites reliés directement ou indirectement au site o-c-d.fr, ne sont pas sous son contrôle. En conséquence, OcD n'assume aucune responsabilité quant aux informations publiées sur ces sites. Les liens avec des sites extérieurs ne sont fournis qu'à titre de commodité et n'impliquent aucune caution quant à leur contenu.
</p>

<h3>Contenus et outils</h3>

<p>OcD ne propose aucune garantie quant à la fiabilité ou au fonctionnement de ce service. OcD ne peut en aucun cas être tenu pour responsable de tous dommages quels qu'ils soient, y compris mais non de façon limitative, des dommages directs, indirects, accessoires ou incidents, des pertes de bénéfices ou de l'interruption d'activité, résultant de l'utilisation ou de l'impossibilité d'utilisation de ce service.</p>

CONTENT
        );
    }

    /**
     * Creates the "Who we are" content page (link available in footer)
     *
     * @param SiteInterface $site
     *
     * @return void
     */
    public function createWhoWeArePage(SiteInterface $site)
    {
        $this->createTextContentPage($site, 'qui-sommes-nous', 'Qui sommes nous ?', <<<CONTENT
		<p>Open Code Developpment est enregistrée au Registe du Commerce et des Sociétés d'Evry sous le N°805 135 183.</p>
		<p>Avec plus de 10 années d'exprience dans le dévelopement web, notre équipe met à votre service son expertise.</p>
CONTENT
        );
    }

    /**
     * Creates the "Client testimonials" content page (link available in footer)
     *
     * @param SiteInterface $site
     *
     * @return void
     */
    public function createClientTestimonialsPage(SiteInterface $site)
    {
        $this->createTextContentPage($site, 'client-testimonials', 'Client testimonials', <<<CONTENT
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut quis sapien gravida, eleifend diam id, vehicula erat. Aenean ultrices facilisis tellus. Vivamus vitae molestie diam. Donec quis mi porttitor, lobortis ipsum quis, fermentum dui. Donec nec nibh nec risus porttitor pretium et et lorem. Nullam mauris sapien, rutrum sed neque et, convallis ullamcorper lacus. Nullam vehicula a lectus vel suscipit. Nam gravida faucibus fermentum.</p>
<p>Pellentesque dapibus eu nisi quis adipiscing. Phasellus adipiscing turpis nunc, sed interdum ante porta eu. Ut tempus, purus posuere molestie cursus, quam nisi fermentum est, dictum gravida nulla turpis vel nunc. Maecenas eget sem quam. Nam condimentum mi id lectus venenatis, sit amet semper purus convallis. Nunc ullamcorper magna mi, non adipiscing velit semper quis. Duis vel justo libero. Suspendisse laoreet hendrerit augue cursus congue. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae;</p>
<p>Nullam dignissim sapien vestibulum erat lobortis, sed imperdiet elit varius. Fusce nisi eros, feugiat commodo scelerisque a, lacinia et quam. In neque risus, dignissim non magna non, ultricies faucibus elit. Vivamus in facilisis enim, porttitor volutpat justo. Praesent placerat feugiat nibh et fermentum. Vivamus eu fermentum metus. Sed mattis volutpat quam a suscipit. Donec blandit sagittis est, ac tristique arcu venenatis sed. Fusce vel libero id lectus aliquet sollicitudin. Fusce ultrices porta est, non pellentesque lorem accumsan eget. Fusce id libero sit amet nulla venenatis dapibus. Maecenas fermentum tellus eu magna mollis gravida. Nam non nibh magna.</p>
CONTENT
        );
    }

    /**
     * Creates the "Press" content page (link available in footer)
     *
     * @param SiteInterface $site
     *
     * @return void
     */
    public function createPressPage(SiteInterface $site)
    {
        $this->createTextContentPage($site, 'press', 'Press', <<<CONTENT
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut quis sapien gravida, eleifend diam id, vehicula erat. Aenean ultrices facilisis tellus. Vivamus vitae molestie diam. Donec quis mi porttitor, lobortis ipsum quis, fermentum dui. Donec nec nibh nec risus porttitor pretium et et lorem. Nullam mauris sapien, rutrum sed neque et, convallis ullamcorper lacus. Nullam vehicula a lectus vel suscipit. Nam gravida faucibus fermentum.</p>
<p>Pellentesque dapibus eu nisi quis adipiscing. Phasellus adipiscing turpis nunc, sed interdum ante porta eu. Ut tempus, purus posuere molestie cursus, quam nisi fermentum est, dictum gravida nulla turpis vel nunc. Maecenas eget sem quam. Nam condimentum mi id lectus venenatis, sit amet semper purus convallis. Nunc ullamcorper magna mi, non adipiscing velit semper quis. Duis vel justo libero. Suspendisse laoreet hendrerit augue cursus congue. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae;</p>
<p>Nullam dignissim sapien vestibulum erat lobortis, sed imperdiet elit varius. Fusce nisi eros, feugiat commodo scelerisque a, lacinia et quam. In neque risus, dignissim non magna non, ultricies faucibus elit. Vivamus in facilisis enim, porttitor volutpat justo. Praesent placerat feugiat nibh et fermentum. Vivamus eu fermentum metus. Sed mattis volutpat quam a suscipit. Donec blandit sagittis est, ac tristique arcu venenatis sed. Fusce vel libero id lectus aliquet sollicitudin. Fusce ultrices porta est, non pellentesque lorem accumsan eget. Fusce id libero sit amet nulla venenatis dapibus. Maecenas fermentum tellus eu magna mollis gravida. Nam non nibh magna.</p>
CONTENT
        );
    }

    /**
     * Creates the "FAQ" content page (link available in footer)
     *
     * @param SiteInterface $site
     *
     * @return void
     */
    public function createFAQPage(SiteInterface $site)
    {
        $this->createTextContentPage($site, 'faq', 'FAQ', <<<CONTENT
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut quis sapien gravida, eleifend diam id, vehicula erat. Aenean ultrices facilisis tellus. Vivamus vitae molestie diam. Donec quis mi porttitor, lobortis ipsum quis, fermentum dui. Donec nec nibh nec risus porttitor pretium et et lorem. Nullam mauris sapien, rutrum sed neque et, convallis ullamcorper lacus. Nullam vehicula a lectus vel suscipit. Nam gravida faucibus fermentum.</p>
<p>Pellentesque dapibus eu nisi quis adipiscing. Phasellus adipiscing turpis nunc, sed interdum ante porta eu. Ut tempus, purus posuere molestie cursus, quam nisi fermentum est, dictum gravida nulla turpis vel nunc. Maecenas eget sem quam. Nam condimentum mi id lectus venenatis, sit amet semper purus convallis. Nunc ullamcorper magna mi, non adipiscing velit semper quis. Duis vel justo libero. Suspendisse laoreet hendrerit augue cursus congue. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae;</p>
<p>Nullam dignissim sapien vestibulum erat lobortis, sed imperdiet elit varius. Fusce nisi eros, feugiat commodo scelerisque a, lacinia et quam. In neque risus, dignissim non magna non, ultricies faucibus elit. Vivamus in facilisis enim, porttitor volutpat justo. Praesent placerat feugiat nibh et fermentum. Vivamus eu fermentum metus. Sed mattis volutpat quam a suscipit. Donec blandit sagittis est, ac tristique arcu venenatis sed. Fusce vel libero id lectus aliquet sollicitudin. Fusce ultrices porta est, non pellentesque lorem accumsan eget. Fusce id libero sit amet nulla venenatis dapibus. Maecenas fermentum tellus eu magna mollis gravida. Nam non nibh magna.</p>
CONTENT
        );
    }

    /**
     * Creates the "Contact us" content page (link available in footer)
     *
     * @param SiteInterface $site
     *
     * @return void
     */
    public function createContactUsPage(SiteInterface $site)
    {
        $this->createTextContentPage($site, 'contact', 'Contactez nous', <<<CONTENT
<p>Vous pouvez nous contacter par mail à l'adresse : <a href="mailto:contact@o-c-d.fr">contact@o-c-d.fr</a></p>
CONTENT
        );
    }

    public function createBundlesPage(SiteInterface $site)
    {
        $this->createTextContentPage($site, 'bundles', 'Sonata Bundles', <<<CONTENT
<div class="row">
<div class="col-md-6">

    <div class="panel panel-success">
      <div class="panel-heading">
        <h3 class="panel-title">Admin bundles</h3>
      </div>
      <div class="panel-body">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut quis sapien gravida, eleifend diam id, vehicula erat.
      </div>
      <ul class="list-group">
        <li class="list-group-item">
          <div class="media">
            <a class="pull-left" href="#">
              <img class="media-object" src="/bundles/sonatademo/images/sonata-logo.png">
            </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="https://sonata-project.org/bundles/admin">Admin</a></h4>
              The missing Symfony2 Admin Generator.
            </div>
          </div>
        </li>
        <li class="list-group-item">
          <div class="media">
            <a class="pull-left" href="#">
              <img class="media-object" src="/bundles/sonatademo/images/sonata-logo.png">
            </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="https://sonata-project.org/bundles/doctrine-orm-admin">Doctrine2 ORM Admin</a></h4>
              Integrates the Doctrine2 ORM into the Admin Bundle.
            </div>
          </div>
        </li>
        <li class="list-group-item">
          <div class="media">
            <a class="pull-left" href="#">
              <img class="media-object" src="/bundles/sonatademo/images/sonata-logo.png">
            </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="https://sonata-project.org/bundles/propel-admin">Propel Admin</a></h4>
              Integrates the Propel into the Admin Bundle.
            </div>
          </div>
        </li>
      </ul>
    </div>

    <div class="panel panel-info">
      <div class="panel-heading">
        <h3 class="panel-title">Foundation bundles</h3>
      </div>
      <div class="panel-body">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut quis sapien gravida, eleifend diam id, vehicula erat.
      </div>
      <ul class="list-group">
        <li class="list-group-item">
          <div class="media">
            <a class="pull-left" href="#">
              <img class="media-object" src="/bundles/sonatademo/images/sonata-logo.png">
            </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="https://sonata-project.org/bundles/core">Core</a></h4>
              Provides base classes used by Sonata's Bundles.
            </div>
          </div>
        </li>
        <li class="list-group-item">
          <div class="media">
            <a class="pull-left" href="#">
              <img class="media-object" src="/bundles/sonatademo/images/sonata-logo.png">
            </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="https://sonata-project.org/bundles/notification">Notification</a></h4>
              Message Queue Solution with Abstracted Backends.
            </div>
          </div>
        </li>
        <li class="list-group-item">
          <div class="media">
            <a class="pull-left" href="#">
              <img class="media-object" src="/bundles/sonatademo/images/sonata-logo.png">
            </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="https://sonata-project.org/bundles/formatter">Formatter</a></h4>
              Add text helpers.
            </div>
          </div>
        </li>
        <li class="list-group-item">
          <div class="media">
            <a class="pull-left" href="#">
              <img class="media-object" src="/bundles/sonatademo/images/sonata-logo.png">
            </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="https://sonata-project.org/bundles/intl">Internationalization (i18n)</a></h4>
              Integrate the PHP Intl extension into a Symfony2 Project.
            </div>
          </div>
        </li>
        <li class="list-group-item">
          <div class="media">
            <a class="pull-left" href="#">
              <img class="media-object" src="/bundles/sonatademo/images/sonata-logo.png">
            </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="https://sonata-project.org/bundles/cache">Cache</a></h4>
              Cache handlers&nbsp;: ESI, Memcached, APC and more…
            </div>
          </div>
        </li>
        <li class="list-group-item">
          <div class="media">
            <a class="pull-left" href="#">
              <img class="media-object" src="/bundles/sonatademo/images/sonata-logo.png">
            </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="https://sonata-project.org/bundles/seo">SEO</a></h4>
              Integrates a shareable object to handle all SEO requirements&nbsp;: title, meta, Open Graph and more…
            </div>
          </div>
        </li>
        <li class="list-group-item">
          <div class="media">
            <a class="pull-left" href="#">
              <img class="media-object" src="/bundles/sonatademo/images/sonata-logo.png">
            </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="https://sonata-project.org/bundles/easy-extends">EasyExtends</a></h4>
              EasyExtends is a tool for generating a valid bundle structure from a Vendor Bundle.
            </div>
          </div>
        </li>
      </ul>
    </div>

</div>
<div class="col-md-6">

    <div class="panel panel-warning">
      <div class="panel-heading">
        <h3 class="panel-title">E-commerce bundles</h3>
      </div>
      <div class="panel-body">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut quis sapien gravida, eleifend diam id, vehicula erat.
      </div>
      <ul class="list-group">
        <li class="list-group-item">
          <div class="media">
            <a class="pull-left" href="#">
              <img class="media-object" src="/bundles/sonatademo/images/sonata-logo.png">
            </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="https://sonata-project.org/bundles/ecommerce">Ecommerce</a></h4>
              Implements base tools for integrated e-commerce features
            </div>
          </div>
        </li>
      </ul>
    </div>

    <div class="panel panel-danger">
      <div class="panel-heading">
        <h3 class="panel-title">Features bundles</h3>
      </div>
      <div class="panel-body">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut quis sapien gravida, eleifend diam id, vehicula erat.
      </div>
      <ul class="list-group">
        <li class="list-group-item">
          <div class="media">
            <a class="pull-left" href="#">
              <img class="media-object" src="/bundles/sonatademo/images/sonata-logo.png">
            </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="https://sonata-project.org/bundles/page">Page</a></h4>
              A Symfony2 friendly CMS.
            </div>
          </div>
        </li>
        <li class="list-group-item">
          <div class="media">
            <a class="pull-left" href="#">
              <img class="media-object" src="/bundles/sonatademo/images/sonata-logo.png">
            </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="https://sonata-project.org/bundles/media">Media</a></h4>
              Media management bundle on steroid for Symfony2.
            </div>
          </div>
        </li>
        <li class="list-group-item">
          <div class="media">
            <a class="pull-left" href="#">
              <img class="media-object" src="/bundles/sonatademo/images/sonata-logo.png">
            </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="https://sonata-project.org/bundles/news">News</a></h4>
              A simple blog/news platform.
            </div>
          </div>
        </li>
        <li class="list-group-item">
          <div class="media">
            <a class="pull-left" href="#">
              <img class="media-object" src="/bundles/sonatademo/images/sonata-logo.png">
            </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="https://sonata-project.org/bundles/user">User</a></h4>
              FOS/User integration.
            </div>
          </div>
        </li>
        <li class="list-group-item">
          <div class="media">
            <a class="pull-left" href="#">
              <img class="media-object" src="/bundles/sonatademo/images/sonata-logo.png">
            </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="https://sonata-project.org/bundles/block">Block</a></h4>
              Handle rendering of block element. A block is a small unit with its own logic and templates. A block can be inserted anywhere in a current template.
            </div>
          </div>
        </li>
        <li class="list-group-item">
          <div class="media">
            <a class="pull-left" href="#">
              <img class="media-object" src="/bundles/sonatademo/images/sonata-logo.png">
            </a>
            <div class="media-body">
              <h4 class="media-heading"><a href="https://sonata-project.org/bundles/timeline">Timeline</a></h4>
              Integrates SpyTimelineBundle into Sonata's bundles.
            </div>
          </div>
        </li>
      </ul>
    </div>

</div>
</div>
CONTENT
        );
    }

    /**
     * Creates simple content pages
     *
     * @param SiteInterface $site    A Site entity instance
     * @param string        $url     A page URL
     * @param string        $title   A page title
     * @param string        $content A text content
     *
     * @return void
     */
    public function createTextContentPage(SiteInterface $site, $url, $title, $content)
    {
        $pageManager = $this->getPageManager();
        $blockManager = $this->getBlockManager();
        $blockInteractor = $this->getBlockInteractor();

        $page = $pageManager->create();
        $page->setSlug(sprintf('/%s', $url));
        $page->setUrl(sprintf('/%s', $url));
        $page->setName($title);
        $page->setTitle($title);
        $page->setEnabled(true);
        $page->setDecorate(1);
        $page->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $page->setTemplateCode('2columns');
        $page->setRouteName('page_slug');
        $page->setSite($site);
        $page->setParent($this->getReference('page-homepage'));

        $page->addBlocks($block = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page'    => $page,
            'code'    => 'content_top',
        )));

        
		// add the breadcrumb
        $block->addChildren($breadcrumb = $blockManager->create());
        $breadcrumb->setType('sonata.page.block.breadcrumb');
        $breadcrumb->setPosition(0);
        $breadcrumb->setEnabled(true);
        $breadcrumb->setPage($page);

        // Add text content block
        $block->addChildren($text = $blockManager->create());
        $text->setType('sonata.block.service.text');
        $text->setSetting('content', sprintf('<h2>%s</h2><div>%s</div>', $title, $content));
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($page);

        $pageManager->save($page);
    }

    public function create404ErrorPage(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();
        $blockManager = $this->getBlockManager();
        $blockInteractor = $this->getBlockInteractor();

        $page = $pageManager->create();
        $page->setName('_page_internal_error_not_found');
        $page->setTitle('Error 404');
        $page->setEnabled(true);
        $page->setDecorate(1);
        $page->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $page->setTemplateCode('default');
        $page->setRouteName('_page_internal_error_not_found');
        $page->setSite($site);

        $page->addBlocks($block = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page'    => $page,
            'code'    => 'content_top',
        )));

        // add the breadcrumb
        $block->addChildren($breadcrumb = $blockManager->create());
        $breadcrumb->setType('sonata.page.block.breadcrumb');
        $breadcrumb->setPosition(0);
        $breadcrumb->setEnabled(true);
        $breadcrumb->setPage($page);

        // Add text content block
        $block->addChildren($text = $blockManager->create());
        $text->setType('sonata.block.service.text');
        $text->setSetting('content', '<h2>Error 404</h2><div>Page not found.</div>');
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($page);

        $pageManager->save($page);
    }

    public function create500ErrorPage(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();
        $blockManager = $this->getBlockManager();
        $blockInteractor = $this->getBlockInteractor();

        $page = $pageManager->create();
        $page->setName('_page_internal_error_fatal');
        $page->setTitle('Error 500');
        $page->setEnabled(true);
        $page->setDecorate(1);
        $page->setRequestMethod('GET|POST|HEAD|DELETE|PUT');
        $page->setTemplateCode('default');
        $page->setRouteName('_page_internal_error_fatal');
        $page->setSite($site);

        $page->addBlocks($block = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page'    => $page,
            'code'    => 'content_top',
        )));

        // add the breadcrumb
        $block->addChildren($breadcrumb = $blockManager->create());
        $breadcrumb->setType('sonata.page.block.breadcrumb');
        $breadcrumb->setPosition(0);
        $breadcrumb->setEnabled(true);
        $breadcrumb->setPage($page);

        // Add text content block
        $block->addChildren($text = $blockManager->create());
        $text->setType('sonata.block.service.text');
        $text->setSetting('content', '<h2>Error 500</h2><div>Internal error.</div>');
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($page);

        $pageManager->save($page);
    }

    /**
     * @param SiteInterface $site
     */
    public function createGlobalPage(SiteInterface $site)
    {
        $pageManager = $this->getPageManager();
        $blockManager = $this->getBlockManager();
        $blockInteractor = $this->getBlockInteractor();

        $global = $pageManager->create();
        $global->setName('global');
        $global->setRouteName('_page_internal_global');
        $global->setSite($site);

        $pageManager->save($global);

        // CREATE A HEADER BLOCK
        $global->addBlocks($header = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page' => $global,
            'code' => 'header',
        )));
        $header->setName('The header container');
		//	ajout block texte
        $header->addChildren($text = $blockManager->create());
        $text->setType('sonata.block.service.text');
        $text->setSetting('content', '
					<section class="logo_container">
						<div class="cube">
							<figure class="front">O</figure>
							<figure class="left"><i class="fa fa-qrcode"></i></figure>
							<figure class="right">C</figure>
							<figure class="top"><i class="fa fa-cloud"></i></figure>
							<figure class="bottom"><i class="fa fa-search"></i></figure>
							<figure class="back">D</figure>
						</div>
					</section>
					<section class="title_container">
                        <h1><a href="/" title="homepage">Open Code Development</a></h1>
						<h2>Conseil, Stratégie, Dévelopment en communication Web</h2>
					</section>
		');
        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($global);

        // CREATE A HEADER BLOCK
        $global->addBlocks($headerTop = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page' => $global,
            'code' => 'header-top',
        ), function ($container) {
            $container->setSetting('layout', '<div class="pull-right">{{ CONTENT }}</div>');
        }));
        $headerTop->setPosition(1);
        $header->addChildren($headerTop);
		// Ajout block user
        $headerTop->addChildren($account = $blockManager->create());
        $account->setType('sonata.user.block.account');
        $account->setPosition(1);
        $account->setEnabled(true);
        $account->setPage($global);

		// Ajout block panier
        // $headerTop->addChildren($basket = $blockManager->create());
        // $basket->setType('sonata.basket.block.nb_items');
        // $basket->setPosition(2);
        // $basket->setEnabled(true);
        // $basket->setPage($global);

        // CREATE A HEADER MENU
        $global->addBlocks($headerMenu = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page' => $global,
            'code' => 'header-menu',
        )));
        $headerMenu->setPosition(2);
        $header->addChildren($headerMenu);
        $headerMenu->setName('The header menu container');
        $headerMenu->setPosition(3);
		// Ajout du menu
        $headerMenu->addChildren($menu = $blockManager->create());
        $menu->setType('sonata.block.service.menu');
        $menu->setSetting('menu_name', "ApplicationSonataPageBundle:Builder:mainMenu");
        $menu->setSetting('safe_labels', true);
        $menu->setPosition(3);
        $menu->setEnabled(true);
        $menu->setPage($global);

        /**/
		// CREATE A FOOTER BLOCK
        $global->addBlocks($footer = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page'    => $global,
            'code'    => 'footer'
        ), function ($container) {
            $container->setSetting('layout', '<div class="row page-footer well">{{ CONTENT }}</div>');
        }));

        $footer->setName('The footer container');

        // Footer : add 3 children block containers (left, center, right)
        $footer->addChildren($footerLeft = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page'    => $global,
            'code'    => 'content'
        ), function ($container) {
            $container->setSetting('layout', '<div class="col-sm-6">{{ CONTENT }}</div>');
        }));

        $footer->addChildren($footerLinksLeft = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page'    => $global,
            'code'    => 'content',
        ), function ($container) {
            $container->setSetting('layout', '<div class="col-sm-2">{{ CONTENT }}</div>');
        }));

        $footer->addChildren($footerLinksCenter = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page'    => $global,
            'code'    => 'content'
        ), function ($container) {
            $container->setSetting('layout', '<div class="col-sm-2">{{ CONTENT }}</div>');
        }));

        $footer->addChildren($footerLinksRight = $blockInteractor->createNewContainer(array(
            'enabled' => true,
            'page'    => $global,
            'code'    => 'content'
        ), function ($container) {
            $container->setSetting('layout', '<div class="col-sm-2">{{ CONTENT }}</div>');
        }));

        // Footer left: add a simple text block
        $footerLeft->addChildren($text = $blockManager->create());

        $text->setType('sonata.block.service.text');
        $text->setSetting('content', '<h2>Communication Web</h2><p class="handcraft">Fabrication artisanale française avec beaucoup d&#039;amour<br /> et un peu d&#039;humour</p>');

        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($global);

        // Footer left links
        $footerLinksLeft->addChildren($text = $blockManager->create());

        $text->setType('sonata.block.service.text');
        $text->setSetting('content', <<<CONTENT
CONTENT
        );

        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($global);

        // Footer middle links
        $footerLinksCenter->addChildren($text = $blockManager->create());

        $text->setType('sonata.block.service.text');
        $text->setSetting('content', <<<CONTENT
CONTENT
        );

        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($global);

        // Footer right links
        $footerLinksRight->addChildren($text = $blockManager->create());

        $text->setType('sonata.block.service.text');
        $text->setSetting('content', <<<CONTENT

<h4>A propos</h4>
<ul class="links">
    <li><a href="/informations-legales">informations-legales</a></li>
    <li><a href="/qui-sommes-nous">Qui sommes nous ?</a></li>
    <li><a href="/contact">Contactez nous</a></li>
</ul>

CONTENT
        );

        $text->setPosition(1);
        $text->setEnabled(true);
        $text->setPage($global);
		

        $pageManager->save($global);
    }

    /**
     * @return \Sonata\PageBundle\Model\SiteManagerInterface
     */
    public function getSiteManager()
    {
        return $this->container->get('sonata.page.manager.site');
    }

    /**
     * @return \Sonata\PageBundle\Model\PageManagerInterface
     */
    public function getPageManager()
    {
        return $this->container->get('sonata.page.manager.page');
    }

    /**
     * @return \Sonata\BlockBundle\Model\BlockManagerInterface
     */
    public function getBlockManager()
    {
        return $this->container->get('sonata.page.manager.block');
    }

    /**
     * @return \Faker\Generator
     */
    public function getFaker()
    {
        return $this->container->get('faker.generator');
    }

    /**
     * @return \Sonata\PageBundle\Entity\BlockInteractor
     */
    public function getBlockInteractor()
    {
        return $this->container->get('sonata.page.block_interactor');
    }
}
