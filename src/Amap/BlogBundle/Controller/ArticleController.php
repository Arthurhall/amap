<?php

namespace Amap\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Amap\MainBundle\Entity\Article;
use Amap\MainBundle\Entity\Category;

use Amap\BlogBundle\Form\ArticleType;

/**
 * Article controller.
 *
 */
class ArticleController extends Controller
{

    /**
     * Lists all Article entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AmapMainBundle:Article')->findAllPublished();

        return $this->render('AmapBlogBundle:Article:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    
    /**
     * Creates a new Article entity.
     *
     */
    public function createAction(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
		$user = $this->getUser();
		
		$entity  = new Article();
		$img_ids = $request->request->get('image_entity');
		
		if($img_ids && !empty($img_ids)) 
		{
			foreach ($img_ids as $key => $id) {
				$img = $em->getRepository('AmapMainBundle:Image')->find($id);
				$entity->addImage($img);
			}
		}
		
		$entity->setUser($user);
        
        $form = $this->createForm(new ArticleType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('blog_show', array('slug' => $entity->getSlug())));
        }

        return $this->render('AmapBlogBundle:Article:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new Article entity.
     *
     */
    public function newAction()
    {
        $entity = new Article();
        $form   = $this->createForm(new ArticleType(), $entity);

        return $this->render('AmapBlogBundle:Article:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'user' => $this->getUser(),
        ));
    }

    /**
     * Finds and displays a Article entity.
     *
     */
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AmapMainBundle:Article')->findOneBy(array('slug' => $slug));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Article entity.');
        }
        
        $user= $this->get('security.context')->getToken()->getUser();
        //echo $user->getUsername(); die();
        $deleteForm = $this->createDeleteForm($entity);

        return $this->render('AmapBlogBundle:Article:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'user' 		=> $user,        
		));
    }
	
	/**
     * Finds and displays Articles entity.
     *
     */
    public function showByCategoryAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AmapMainBundle:Article')->findByCategory(array('slug' => $slug));

        if (!$entities) {
            throw $this->createNotFoundException('Unable to find Articles entities.');
        }
        
        $category = $entities[0]->getCategory();
        
        return $this->render('AmapBlogBundle:Article:showByCategory.html.twig', array(
            'entities'      => $entities,
            'category'      => $category,
        ));
    }

    /**
     * Displays a form to edit an existing Article entity.
     *
     */
    public function editAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AmapMainBundle:Article')->findOneBy(array('slug' => $slug));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Article entity.');
        }
        
		$user = $this->getUser();
        $id = $entity->getId();

        $editForm = $this->createForm(new ArticleType(), $entity);
        $deleteForm = $this->createDeleteForm($entity);

        return $this->render('AmapBlogBundle:Article:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'user' => $user,
       	));
    }

    /**
     * Edits an existing Article entity.
     *
     */
    public function updateAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AmapMainBundle:Article')->findOneBy(array('slug' => $slug));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Article entity.');
        }
        
        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createForm(new ArticleType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('blog_edit', array('slug' => $entity->getSlug())));
        }

        return $this->render('AmapBlogBundle:Article:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
	
	
    /**
     * Deletes a Article entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteFormWithId($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AmapMainBundle:Article')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Article entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('blog'));
    }

    /**
     * Creates a form to delete a Article entity.
     *
     * @param Article $article The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Article $article)
    {
        return $this->createFormBuilder(array('id' => $article->getId()))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
	private function createDeleteFormWithId($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
