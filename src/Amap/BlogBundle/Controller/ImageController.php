<?php

namespace Amap\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Amap\BlogBundle\Form\ImageType;

/**
 * Image controller.
 *
 */
class ImageController extends Controller
{
	
	/**
     * Edits an existing Image entity.
     *
     */
    public function updateAction(Request $request)
    {
    	$post = $request->request;
		$user = $this->getUser();
		
		if($post->get('id') <= 0 || !$user || $user->getId() != $post->get('user') || !$user->hasArticle($post->get('article_id'))) {
			return $this->throwError();
		}
		
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AmapMainBundle:Image')->find( $post->get('id') );
		
        if (!$entity) {
	        return $this->throwError('{"error": true, "message": "Unable to find Entity Image."}');
        }
		
		$id = $entity->getId();
		
		$title = $post->get('title'); $title = $title[$id];
		$alt = $post->get('alt'); $alt = $alt[$id];
		$vitrine = $post->get('vitrine'); $vitrine = $vitrine[$id];
		
		$entity->setTitle( $title )
			->setAlt( $alt )
			->setVitrine( $vitrine )
		;
		
        $em->flush();
		
		$msg = array(
			'error' => false,
			'message' => 'Upload OK',
			'vitrine' => null,
		);
		
		if($entity->getVitrine()) {
			$msg['vitrine'] = $entity->getId();
		}
		
		$response = new Response( json_encode($msg) );
        $response->headers->set('Content-Type', 'application/json');
        return $response;
        
    }
	
	public function deleteAction(Request $request)
    {
    	$post = $request->request;
		$user = $this->getUser();
		
		if($post->get('id') <= 0 || !$user || $user->getId() != $post->get('user')) {
	        return $this->throwError();
		}
		if($post->get('article_id') && $post->get('article_id') > 0 && !$user->hasArticle($post->get('article_id'))){
			return $this->throwError();
		}
		
		$em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AmapMainBundle:Image')->find( $post->get('id') );
		
        if (!$entity) {
            return $this->throwError('{"error": true, "message": "Image introuvable."}');
        }

        $em->remove($entity);
        $em->flush();
		
		$response = new Response( '{"error": false, "message": "Delete OK", "is_deleted": true}' );
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

	public function throwError($msg = '{"error": true, "message": "Une erreur est survenue."}')
	{
		$response = new Response( $msg );
        $response->headers->set('Content-Type', 'application/json');
        return $response;
	}
	
}	