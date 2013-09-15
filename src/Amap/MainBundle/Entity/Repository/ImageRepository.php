<?php

namespace Amap\MainBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;


class ImageRepository extends EntityRepository
{
    public function findAllPath()
    {
        $sql = "
            SELECT i.path
            FROM image as i
            ";
        
        $conn = $this->getEntityManager()->getConnection();
        $r = array();
        foreach($conn->query($sql)->fetchAll() as $k => $row)
        {
            $r[$k] = $row['path'];
        }
        return $r;
    }
}
