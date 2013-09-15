<?php

namespace Amap\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Permanence
 *
 * @ORM\Table(name="permanence")
 * @ORM\Entity(repositoryClass="Amap\MainBundle\Entity\Repository\PermanenceRepository")
 */
class Permanence
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
	
	/**
     * @var \DateTime
     *
     * @ORM\Column(name="delivery_date", type="datetime", nullable=false, unique=true)
     */
    private $deliveryDate;
	
	/**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Amap\UserBundle\Entity\User", mappedBy="permanences")
     */
    private $users;

    /**
     * @var array
     *
     * @ORM\Column(name="user_detail", type="json")
     */
    private $userDetail;
	
	
	/**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
	 * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
	 * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;
	
	
	/**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }
	
	
	private $userDetailToStr;
	
	public function getUserDetailToStr()
	{
		$str = '';
		$detail = $this->userDetail;
		foreach ($this->users as $key => $user) 
		{
			$heures = $detail[ $user->getId() ];
			foreach ($heures as $key => $heure) {
				$str .= $user->getFirstName().' '.$user->getLastName().' à '.$heure. "h || \r\n"; // '&#10;' // '\n' // chr(10)	
			}
		}
		
		return $str;
	}
	public function setUserDetailToStr($str)
	{
		$this->userDetailToStr = $str;
		return $this;
	}
	
	
	public function getUsersCountClass()
	{
		$i=0;
		foreach($this->userDetail as $userId => $horaire) {
			$i += count($horaire);
		}
		if($i == 4) {
			return 'success';
		} else if($i == 0) {
			return 'error';
		} else if($i < 4) {
			return 'warning';
		} else {
			return 'info';
		}
	}
	
	public function getIs18Full($type = null)
	{
		return $this->getIsFull(18, $type);
	}
	
	public function getIs19Full($type = null)
	{
		return $this->getIsFull(19, $type);
	}
	
	public function getIsFull($heure, $type = null)
	{
		$i=0;
		foreach ($this->userDetail as $userId => $detail) 
		{
			if(in_array($heure, $detail))
			{
				$i++;
			}
		}
		if($type=='txt') {
			return ($i>=2) ? 'true' : 'false';
		}
		return ($i>=2) ? true : false;
	}
    
    public function hasUser($id)
    {
        foreach ($this->users as $key => $user) {
            if ($user->getId() == $id) 
                return true;
        }
        return false;
    }
    
    public function hasUserFor18($id)
    {
        $arr = $this->getUserDetail();
        if(in_array($id, array_keys($arr)))
        {
            $hours = $arr[$id];
            if(in_array(18, $hours))
                return true;
        }
    }
    
    public function hasUserFor19($id)
    {
        $arr = $this->getUserDetail();
        if(in_array($id, array_keys($arr)))
        {
            $hours = $arr[$id];
            if(in_array(19, $hours))
                return true;
        }
    }
    
    
    /**
     * Set userDetail
     *
     * @param array $userDetail
     * @return Permanence
     */
    public function setUserDetail($userDetail)
    {
        $this->userDetail = $userDetail;
    
        return $this;
    }

    /**
     * Get userDetail
     *
     * @return array 
     */
    public function getUserDetail()
    {
        return $this->userDetail;
    }
    
    /**
     * Add users
     *
     * @param \Amap\UserBundle\Entity\User $users
     * @return Permanence
     */
    public function addUser(\Amap\UserBundle\Entity\User $users)
    {
        $this->users[] = $users;
    
        return $this;
    }

    /**
     * Remove users
     *
     * @param \Amap\UserBundle\Entity\User $users
     */
    public function removeUser(\Amap\UserBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Permanence
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Permanence
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set deliveryDate
     *
     * @param \DateTime $deliveryDate
     * @return Permanence
     */
    public function setDeliveryDate($deliveryDate)
    {
        $this->deliveryDate = $deliveryDate;
    
        return $this;
    }

    /**
     * Get deliveryDate
     *
     * @return \DateTime 
     */
    public function getDeliveryDate()
    {
        return $this->deliveryDate;
    }
}