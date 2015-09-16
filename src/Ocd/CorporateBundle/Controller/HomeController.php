<?php

namespace Ocd\CorporateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class HomeController extends Controller
{
    /**
     * @Route("/homepage_corporate", name="homepage_corporate")
     * @Template("OcdCorporateBundle:Home:index.html.twig")
     */
    public function indexAction()
    {
        $name="";
		return array('name' => $name);
    }
}
