<?php

namespace Amap\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Delivery
 *
 * @ORM\Table(name="delivery")
 * @ORM\Entity(repositoryClass="Amap\MainBundle\Entity\Repository\DeliveryRepository")
 * @UniqueEntity(
 *     fields={"deliveredAt"},
 *     errorPath="deliveredAt",
 *     message="Une livraison est déjà programmé à cette date !"
 * )
 */
class Delivery
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    
    // public static function loadValidatorMetadata(ClassMetadata $metadata)
    // {
        // $metadata->addConstraint(new UniqueEntity(array(
            // 'fields'  => 'deliveredAt',
            // 'message' => '',
        // )));
    // }
	
	/**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="is_sent", type="boolean", nullable=true)
     */
    private $isSent;
    
    /**
     * @var string
     *
     * @ORM\Column(name="sent_to", type="string", nullable=true, length=100)
     * @Assert\Email()
     */
    private $sentTo;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="with_eggs", type="boolean", nullable=true)
     */
    private $withEggs;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="delivered_at", type="datetime", nullable=false, unique=true)
     * @Assert\NotBlank()
     */
    private $deliveredAt;
	
	/**
     * @var \DateTime
     *
     * @ORM\Column(name="sent_at", type="datetime", nullable=true)
     */
    private $sentAt;

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
     * @var \PanierMaxi
     *
     * @ORM\ManyToOne(targetEntity="PanierMaxi")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="panier_maxi_id", referencedColumnName="id")
     * })
     */
    private $panierMaxi;

    /**
     * @var \PanierMini
     *
     * @ORM\ManyToOne(targetEntity="PanierMini")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="panier_mini_id", referencedColumnName="id")
     * })
     */
    private $panierMini;
	
    /**
     * @ORM\ManyToMany(targetEntity="PanierAddon", inversedBy="delivery", cascade={"persist", "detach"})
     * @ORM\JoinTable(name="panier_addon_has_delivery",
     *   joinColumns={
     *     @ORM\JoinColumn(name="panier_addon_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="delivery_id", referencedColumnName="id")
     *   }
     * )
     */
    private $panierAddon;
    
	/**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Image", inversedBy="delivery")
     * @ORM\JoinTable(name="delivery_has_image",
     *   joinColumns={
     *     @ORM\JoinColumn(name="delivery_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     *   }
     * )
     */
    private $image;
	

    /**
     * Constructor
     */
    public function __construct()
    {
		$this->panierAddon = new \Doctrine\Common\Collections\ArrayCollection();
		$this->isSent = false;
        $this->withEggs = false;
	}
    
	public function __toString() 
	{
		if($this->deliveredAt instanceof \DateTime) {
			return (string) 'Panier du '.$this->deliveredAt->format('d/m/Y');
		}
		return (string) 'Nouveau Panier';
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
     * Set deliveredAt
     *
     * @param \DateTime $deliveredAt
     * @return Delivery
     */
    public function setDeliveredAt($deliveredAt)
    {
        $this->deliveredAt = $deliveredAt;
    
        return $this;
    }

    /**
     * Get deliveredAt
     *
     * @return \DateTime 
     */
    public function getDeliveredAt()
    {
        return $this->deliveredAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Delivery
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
     * @return Delivery
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
     * Set panierMaxi
     *
     * @param \Amap\MainBundle\Entity\PanierMaxi $panierMaxi
     * @return Delivery
     */
    public function setPanierMaxi(\Amap\MainBundle\Entity\PanierMaxi $panierMaxi = null)
    {
        $this->panierMaxi = $panierMaxi;
    
        return $this;
    }

    /**
     * Get panierMaxi
     *
     * @return \Amap\MainBundle\Entity\PanierMaxi 
     */
    public function getPanierMaxi()
    {
        return $this->panierMaxi;
    }

    /**
     * Set panierMini
     *
     * @param \Amap\MainBundle\Entity\PanierMini $panierMini
     * @return Delivery
     */
    public function setPanierMini(\Amap\MainBundle\Entity\PanierMini $panierMini = null)
    {
        $this->panierMini = $panierMini;
    
        return $this;
    }

    /**
     * Get panierMini
     *
     * @return \Amap\MainBundle\Entity\PanierMini 
     */
    public function getPanierMini()
    {
        return $this->panierMini;
    }


    /**
     * Add panierAddon
     *
     * @param \Amap\MainBundle\Entity\PanierAddon $panierAddon
     * @return Delivery
     */
    public function addPanierAddon(\Amap\MainBundle\Entity\PanierAddon $panierAddon)
    {
        $this->panierAddon[] = $panierAddon;
    
        return $this;
    }

    /**
     * Remove panierAddon
     *
     * @param \Amap\MainBundle\Entity\PanierAddon $panierAddon
     */
    public function removePanierAddon(\Amap\MainBundle\Entity\PanierAddon $panierAddon)
    {
        $this->panierAddon->removeElement($panierAddon);
    }

    /**
     * Get panierAddon
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPanierAddon()
    {
        return $this->panierAddon;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Delivery
     */
    public function setMessage($message)
    {
        $this->message = $message;
    
        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Add image
     *
     * @param \Amap\MainBundle\Entity\Image $image
     * @return Delivery
     */
    public function addImage(\Amap\MainBundle\Entity\Image $image)
    {
        $this->image[] = $image;
    
        return $this;
    }

    /**
     * Remove image
     *
     * @param \Amap\MainBundle\Entity\Image $image
     */
    public function removeImage(\Amap\MainBundle\Entity\Image $image)
    {
        $this->image->removeElement($image);
    }

    /**
     * Get image
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set isSent
     *
     * @param boolean $isSent
     * @return Delivery
     */
    public function setIsSent($isSent)
    {
        $this->isSent = $isSent;
    
        return $this;
    }

    /**
     * Get isSent
     *
     * @return boolean 
     */
    public function getIsSent()
    {
        return $this->isSent;
    }

    /**
     * Set sentAt
     *
     * @param \DateTime $sentAt
     * @return Delivery
     */
    public function setSentAt($sentAt)
    {
        $this->sentAt = $sentAt;
    
        return $this;
    }

    /**
     * Get sentAt
     *
     * @return \DateTime 
     */
    public function getSentAt()
    {
        return $this->sentAt;
    }

    /**
     * Set withEggs
     *
     * @param boolean $withEggs
     * @return Delivery
     */
    public function setWithEggs($withEggs)
    {
        $this->withEggs = $withEggs;
    
        return $this;
    }

    /**
     * Get withEggs
     *
     * @return boolean 
     */
    public function getWithEggs()
    {
        return $this->withEggs;
    }

    /**
     * Set sentTo
     *
     * @param string $sentTo
     * @return Delivery
     */
    public function setSentTo($sentTo)
    {
        $this->sentTo = $sentTo;
    
        return $this;
    }

    /**
     * Get sentTo
     *
     * @return string 
     */
    public function getSentTo()
    {
        return $this->sentTo;
    }
}