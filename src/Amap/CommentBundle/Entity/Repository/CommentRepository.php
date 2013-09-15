<?php

namespace Amap\CommentBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;


class CommentRepository extends EntityRepository
{
	public function findByAuthorWithArticle($id)
	{
		$sql = "
    		SELECT c.body, c.created_at as createdAt, 
    		a.id, a.slug, a.title
    		
    		FROM comment as c
    		INNER JOIN thread as t 
    		ON c.thread_id = t.id
    		INNER JOIN article as a 
    		ON a.id = t.id
    		INNER JOIN fos_user as u
    		ON u.id = c.author_id 
    		WHERE u.id = $id
    		ORDER BY createdAt DESC
    		";
    	
		$conn = $this->getEntityManager()->getConnection();
		$r = array();
		foreach($conn->query($sql)->fetchAll() as $k => $row)
		{
			$r[$k] = $row;
		}
		
		return $r;
	}
	
}
