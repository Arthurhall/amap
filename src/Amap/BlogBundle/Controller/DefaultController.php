<?php

namespace Amap\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('AmapBlogBundle:Default:index.html.twig', array('name' => $name));
    }
}
