<?php

namespace Amap\UserBundle\Controller;

use Sonata\UserBundle\Controller\ProfileController as Base;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;

class ProfileController extends Base
{
	/**
     * @return Response
     *
     * @throws AccessDeniedException
     */
    public function showAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
		
		$em = $this->container->get('doctrine.orm.entity_manager');
		
		$articles = $em->getRepository('AmapMainBundle:Article')->findBy(
			array('user' => $user),
			array('publishedAt' => 'DESC')
		);
		
		$comments = $em->getRepository('AmapCommentBundle:Comment')->findByAuthorWithArticle($user->getId());
		
		$commandes = $em->getRepository('AmapMainBundle:Commande')->findBy(
			array('user' => $user), 
			array('createdAt' => 'DESC')
		);
		
		
		$permanences = $em->getRepository('AmapMainBundle:Permanence')->findAllWithUsers();
		
		$permanence = new \Amap\MainBundle\Entity\Permanence();
		$permanence->addUser($user);
        $form_permanence   = $this->createForm(new \Amap\MainBundle\Form\PermanenceType(), $permanence);
		
		$dates = $this->container->get('permanence.dates')->getDates();
		//print_r($dates); die();

        return $this->render('SonataUserBundle:Profile:show.html.twig', array(
            'user' => $user,
            'articles' => $articles,
            'comments' => $comments,
            'commandes' => $commandes,
            'permanences' => $permanences,
            'form_permanence' => $form_permanence->createView(),
            'dates' => $dates,
        ));
    }
	
}