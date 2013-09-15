<?php

namespace Amap\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Amap\MainBundle\Form\ContactType;
use Amap\MainBundle\Form\PermanenceType;
use Symfony\Component\Form\FormError;


class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $accueil = $em->getRepository('AmapMainBundle:Article')->findHome();
		
        return $this->render('AmapMainBundle:Default:index.html.twig', array(
            'accueil' => $accueil
        ));
    }
	
	public function searchAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		
		$search = $request->request->get('search');
		
        $entities = $em->getRepository('AmapMainBundle:Article')->search(array('search' => $search));
        
        return $this->render('AmapMainBundle:Default:search_result.html.twig', array(
            'entities'      => $entities,
        ));
	}
	
	public function nextPanierAction()
	{
		$em = $this->getDoctrine()->getManager();
		
		$delivery = $em->getRepository('AmapMainBundle:Delivery')->findNextOneWithJoin();
        
		return $this->render('AmapMainBundle:Default:next_panier.html.twig', array(
            'delivery'      => $delivery,
        ));
	}
	
	public function pluploadAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		
		$file = $request->files->get('file');
		$post = json_decode( $request->request->get('json') );
		
		$ref = $request->request->get('ref');
		$dir = $request->request->get('dir');
		$article_id = $request->request->get('article_id');
		
		
		if($file->getError() != 0 || !$post) {
			$response = new Response( '{"error": true, "message": "Une erreur est survenue"}' );
	        $response->headers->set('Content-Type', 'application/json');
	        return $response;
		}
		
		$current = null;
		foreach ($post as $key => $arr) 
		{
			if($arr->filename == $file->getClientOriginalName()) {
				//echo $arr->filename .' == '. $file->getClientOriginalName();
				$current = $key;
			}
		}
		// print_r($post);
		// print_r($current); die();
		if($current === null) 
		{
			$debug = array(
				'current' => $current,
				'getClientOriginalName' => $file->getClientOriginalName(),
				'postJSON' => $post,
			);
			$response = new Response( '{"error": true, "message": "Une erreur est survenue", "debug": '.json_encode($debug).'}' );
	        $response->headers->set('Content-Type', 'application/json');
	        return $response;
		}
		
		$image = new \Amap\MainBundle\Entity\Image();
		$image->setFile( $file );
		$image->upload();
		
		$image->setTitle( $post->$current->title )->setAlt( $post->$current->alt );
		
		if($file->getError() != 0) {
			$response = new Response( '{"error": true, "message": "Une erreur est survenue"}' );
	        $response->headers->set('Content-Type', 'application/json');
	        return $response;
		}
		
		if($article_id && $article_id > 0) {
			$article = $em->getRepository('AmapMainBundle:Article')->find( $article_id );
			$article->addImage( $image );
		}

		$avalancheService = $this->get('imagine.cache.path.resolver');
		$cachedImage = $avalancheService->getBrowserPath($image->getWebPath(), 'my_thumb');
		
		$em->persist( $image );
		$em->flush();
		
		$response = new Response( '{"error": false, "message": "Upload OK", "image_id": '.$image->getId().', "web_path": "'.$cachedImage.'"}' );
        $response->headers->set('Content-Type', 'application/json');
        return $response;
			
		// print_r($file); 
		// print_r($post); die();
	}

	public function contactAction(Request $request)
    {
        //if($request->getSession()->has('messageSent')) 
    	$form = $this->createForm(new ContactType());
        
        if ('POST' == $request->getMethod()) 
        {
            $form->bind($request);
            if ($form->isValid()) 
            {
            	$data = $form->getData();
				
				$message = \Swift_Message::newInstance()
					->setSubject('Contact - Amap')
					->setFrom( $data['email'] )
					->setTo( $this->container->getParameter('email') )
					->setCharset('UTF-8')    
					->setContentType('text/html')
					->setBody($this->renderView('AmapMainBundle:Default:contact_mail.html.twig', array('data' => $data)))
				;
				
				$this->get('mailer')->send($message); 
				
				$session = $this->get("session"); //$request->getSession();
				$session->getFlashBag()->add('messageSent', 'Votre message a bien été envoyé.');	
				
				// Redirect - This is important to prevent users re-posting
            	// the form if they refresh the page
				return $this->redirect($this->generateUrl('contact'));
				
			}
		}
		
		return $this->render('AmapMainBundle:Default:contact.html.twig', array(
			'form' => $form->createView(),
		));
	}

	
	public function createPermanenceAction(Request $request)
	{
		if(!$request->isXmlHttpRequest()) {
			throw $this->createNotFoundException('La requête doit être de type XHR.');
		}
		
        $jsonResp = array();
		$form = $this->createForm(new PermanenceType());
		
		$form->bind($request);
		
        if ($form->isValid()) 
        {
        	$em = $this->getDoctrine()->getManager();
			
        	$user = $this->getUser();
        	$post = $request->request->get('amap_mainbundle_permanencetype');
        	
			$date = new \DateTime( $post['deliveryDate'] );
			
			$entity = $em->getRepository('AmapMainBundle:Permanence')->findOneBy( array('deliveryDate' => $date) );
			
			// La permanence n'existe pas, on la créé :
			if(!$entity) 
			{
				$entity = $form->getData();
				$entity->setDeliveryDate( $date );
				$entity->setUserDetail( array( $user->getId() => array($post['heure'])) );
				$em->persist( $entity );
				
				$user->addPermanence( $entity );
                
                $jsonResp = array(
                    'returnState' => 'ok',
                );
			}
			// La permanence existe :
			else 
			{
				if($entity->getIs18Full() && $post['heure'] == 18) 
				{
				    $jsonResp = array( 
				        "error" => true, 
				        "message" => "Il n\'y a plus de places pour 18h.", 
				        "returnState" => "remove"
                    );
					return $this->createJsonResponse( $jsonResp );
				}
				if($entity->getIs19Full() && $post['heure'] == 19) 
				{
				    $jsonResp = array( 
				        "error" => true, 
				        "message" => "Il n\'y a plus de places pour 19h.", 
				        "returnState" => "remove",
                    );
                    return $this->createJsonResponse( $jsonResp );
				}
				
				$detail = $entity->getUserDetail();
				
				// Le user est dèjà rattaché à cette perm :
				if($user->hasPermanence( $entity->getId() ))
				{
				    // Le user fait les 2h de permanences :
					if(!in_array($post['heure'], $detail[$user->getId()]))
					{
						$detail[$user->getId()][] = $post['heure'];
						$entity->setUserDetail( $detail );
                        
                        $jsonResp = array(
                            'returnState' => 'ok',
                        );
					}
					else 
					{
					    $jsonResp = array(
                            'error' => true, 
                            'message' => 'Vous êtes déjà inscrit à '.$post['heure'],
                            'returnState' => 'remove',
                        );
						return $this->createJsonResponse( $jsonResp );
					}
				}
				// Le user n'est pas encore rattaché à cette perm :
				else 
				{
					$user->addPermanence( $entity );
					$detail[$user->getId()][] = $post['heure'];
					$entity->setUserDetail( $detail );
                    
                    $jsonResp = array( 
                        'returnState' => 'ok',
                    );
				}
			}
			
			
			$em->flush();
			
			$inscriptionTxt = $user->getFirstName().' '.$user->getLastName().' à '.$post['heure'].'h';
			$fullInscriptionTxt = 'Vous vous êtes inscrits à la permanence du mardi '.$entity->getDeliveryDate()->format('d/m/Y').' à '.$post['heure'].'h';
            
            $jsonResp = array_merge( array(
                "fullInscription" => trim($fullInscriptionTxt), 
                "inscription" => trim($inscriptionTxt), 
                "usersCountClass" => $entity->getUsersCountClass(), 
                "is18Full" => $entity->getIs18Full('txt'),
                "is19Full" => $entity->getIs19Full('txt'),
                "returnState" => 'remove',
            ), $jsonResp);
            return $this->createJsonResponse( $jsonResp );
		}
		
        // Form is not valid :
        $jsonResp = array(
            "error" => true, 
            "message" => "Une erreur est survenue",
        );
		return $this->createJsonResponse( $jsonResp );
	}
	
	public function mergeJsonResponse($jsonResp)
	{
    	return json_encode( array_merge( array(
            'error' => false,
            'message' => '',
            'fullInscription' => '',
            'inscription' => '',
            'usersCountClass' => 0,
            "is18Full" => false, 
            'is19Full' => false,
            'returnState' => '',
            'showMessage' => false,
        ), $jsonResp));	
	}
	
	public function createJsonResponse($jsonResp)
	{
        $response = new Response( $this->mergeJsonResponse($jsonResp) );
        $response->headers->set('Content-Type', 'application/json');
        return $response;
	}
	
	
	
	public function nosProduitsAction()
	{
		$em = $this->getDoctrine()->getManager();
		$products = $em->getRepository('AmapMainBundle:ProductVegetable')->findAllWithAvgPrices();
		//$products = $em->getRepository('AmapMainBundle:ProductVegetable')->findBy(array(),array('title' => 'ASC'));
		
		return $this->render('AmapMainBundle:Default:nos_produits.html.twig', array(
			'products' => $products,
		));
	}
	
}
