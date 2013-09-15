<?php

namespace Amap\MainBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\SecurityContext;

class Builder 
{
    private $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }
    
    
    public function createMainMenu(Request $request, EntityManager $em, SecurityContext $sc)
    {
        $user = $sc->getToken()->getUser();
        
        $menu = $this->factory->createItem('root');
		
		$menu->setCurrentUri($request->getRequestUri());
        $menu->setChildrenAttribute('class', 'nav');
        
        $menu->addChild('Home', array('route' => 'home'))
			->setAttribute('divider_append', true);
            
        
		$this->addMenuDeroulant($menu, 'Blog');
		
        $categories = $em->getRepository('AmapMainBundle:Category')->findAll();
        
        $menu['Blog']
            ->addChild('Tout afficher', array(
                'route' => 'blog',
            ));
            
        foreach ($categories as $key => $category) 
        {
            $menu['Blog']
                ->addChild($category->getTitle(), array(
                	'route' => 'blog_category',
                	'routeParameters' => array('slug' => $category->getSlug()),
				));
        }
        
        if($sc->isGranted('ROLE_MEMBER')) {
            $menu['Blog']
                ->addChild('Ecrire dans le Blog', array(
                    'route' => 'blog_new',
                ));
        }
        
        $item = $menu->addChild('Nos Paniers', array(
            'route' => 'delivery'
        ));
		
		$item = $menu->addChild('Nos Produits', array(
            'route' => 'nos_produits'
        ));
		
		
		if($sc->isGranted('ROLE_MEMBER')) {
			$menu->addChild('Mon Compte', array('route' => 'sonata_user_profile_show'))
			->setAttribute('divider_prepend', true);
		}
		
		$menu->addChild('Contact', array('route' => 'contact'))
			->setAttribute('divider_prepend', true);
        
        return $menu;
    }
	
	public function createSidebarMenu(Request $request, EntityManager $em)
	{
		$menu = $this->factory->createItem('root');
		
		$menu->setCurrentUri($request->getRequestUri());
        $menu->setChildrenAttribute('class', 'nav nav-list');
		
		$articles = $em->getRepository('AmapMainBundle:Article')->findAllByMonth();
        
		foreach ($articles as $date => $arr) 
		{
			$item = $menu->addChild( $date );
			foreach($arr as $id => $article)
			{
				$item->addChild('.icon-book '.$article['title'], array(
					'route' => 'blog_show',
					'routeParameters' => array('slug' => $article['slug']),
				));
			}
			$dropdown = $menu->addChild( '' );
			$dropdown->addChild('d1', array('attributes' => array('divider' => true)));
		}
		
        return $menu;
	}

    public function createSidebarPanierMenu(Request $request, EntityManager $em)
    {
        $menu = $this->factory->createItem('root');
        
        $menu->setCurrentUri($request->getRequestUri());
        $menu->setChildrenAttribute('class', 'nav nav-list');
        
        $deliveries = $em->getRepository('AmapMainBundle:Delivery')->findAllByMonth();
        // echo "<pre>";
        // print_r($deliveries);
        // die();
        foreach ($deliveries as $date => $arr) 
        {
            $item = $menu->addChild( $date );
            foreach($arr as $id => $delivery)
            {
                $dt = new \DateTime($delivery['delivered_at']);
                // icon-shopping-cart
                $item->addChild('.icon-shopping-cart Mardi '.$dt->format('d'), array(
                    'route' => 'delivery_show',
                    'routeParameters' => array('id' => $delivery['id']),
                ));
            }
            $dropdown = $menu->addChild( '' );
            $dropdown->addChild('d1', array('attributes' => array('divider' => true)));
        }
        
        return $menu;
    }
	
	public function createSidebarNextPanierMenu(Request $request, EntityManager $em)
    {
        $menu = $this->factory->createItem('root');
        
        $menu->setCurrentUri($request->getRequestUri());
        $menu->setChildrenAttribute('class', 'nav nav-list');
        
        $delivery = $em->getRepository('AmapMainBundle:Delivery')->findNextOneWithJoin();
        
		if(!$delivery) {
			return $menu;
		}
		
		$item = $menu->addChild( 'Notre prochain Panier du '.$delivery->getDeliveredAt()->format('d/m/Y') );
		foreach ($delivery->getPanierMaxi()->getProduct() as $key => $product) 
		{
			$item->addChild('.icon-leaf '.$product->getTitle(), array());	
		}
		
        return $menu;
    }
	
	private function addMenuDeroulant($menu,$titre) 
	{
    	$menu->addChild($titre, array('uri' => '#'))
	        ->setLinkattribute('class', 'dropdown-toggle')
	        ->setLinkattribute('data-toggle', 'dropdown')
	        ->setAttributes(array('class'=>'dropdown'))
	        ->setChildrenAttribute('divider_append', true)
	        ->setChildrenAttribute('class', 'dropdown-menu')
		;
		
       	return $menu;
    }
}