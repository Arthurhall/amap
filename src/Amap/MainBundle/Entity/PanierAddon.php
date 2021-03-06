<?php

namespace Amap\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PanierAddon
 *
 * @ORM\Table(name="panier_addon")
 * @ORM\Entity(repositoryClass="Amap\MainBundle\Entity\Repository\PanierAddonRepository")
 */
class PanierAddon extends PanierAbstract
{
	const NEW_TITLE = 'Nouveau Panier Exceptionnel';
	
	public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('title', new Assert\Regex(array(
            'pattern' => '/'.self::NEW_TITLE.'/',
            'match'   => false,
            'message' => 'Donnez un titre au panier',
        )));
    }
	
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=200, nullable=false)
     */
    private $title;
	
    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    private $price;

    /**
     * @var float
     *
     * @ORM\Column(name="discounted_price", type="float", nullable=true)
     */
    private $discountedPrice;
	
	/**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Delivery", mappedBy="panierAddon")
     */
    private $delivery;
	
    /**
     * @ORM\ManyToMany(targetEntity="Product", inversedBy="panierAddon", cascade={"persist", "detach"})
     * @ORM\JoinTable(name="panier_addon_has_product",
     *   joinColumns={
     *     @ORM\JoinColumn(name="panier_addon_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     *   }
     * )
     */
    private $product;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->product = new \Doctrine\Common\Collections\ArrayCollection();
		$this->delivery = new \Doctrine\Common\Collections\ArrayCollection();
		
		$this->title = self::NEW_TITLE;
    }
	
	public function __toString() 
	{
		return $this->title;
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return PanierAddon
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
     * @return PanierAddon
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
     * Set message
     *
     * @param string $message
     * @return PanierAddon
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
     * Set price
     *
     * @param float $price
     * @return PanierAddon
     */
    public function setPrice($price)
    {
        $this->price = $price;
    
        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set discountedPrice
     *
     * @param float $discountedPrice
     * @return PanierAddon
     */
    public function setDiscountedPrice($discountedPrice)
    {
        $this->discountedPrice = $discountedPrice;
    
        return $this;
    }

    /**
     * Get discountedPrice
     *
     * @return float 
     */
    public function getDiscountedPrice()
    {
        return $this->discountedPrice;
    }
	
	public function setProduct($products)
	{
		$this->product = new \Doctrine\Common\Collections\ArrayCollection();
		foreach($products as $product){
			$this->addProduct($product);
		}
	}
	
    /**
     * Add product
     *
     * @param \Amap\MainBundle\Entity\Product $product
     * @return PanierAddon
     */
    public function addProduct(\Amap\MainBundle\Entity\Product $product)
    {
        $this->product[] = $product;
    	$product->addPanierAddon($this);
        return $this;
    }

    /**
     * Remove product
     *
     * @param \Amap\MainBundle\Entity\Product $product
     */
    public function removeProduct(\Amap\MainBundle\Entity\Product $product)
    {
        $this->product->removeElement($product);
		$product->removePanierAddon($this);
    }

    /**
     * Get product
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Add delivery
     *
     * @param \Amap\MainBundle\Entity\Delivery $delivery
     * @return PanierAddon
     */
    public function addDelivery(\Amap\MainBundle\Entity\Delivery $delivery)
    {
        $this->delivery[] = $delivery;
    
        return $this;
    }

    /**
     * Remove delivery
     *
     * @param \Amap\MainBundle\Entity\Delivery $delivery
     */
    public function removeDelivery(\Amap\MainBundle\Entity\Delivery $delivery)
    {
        $this->delivery->removeElement($delivery);
    }

    /**
     * Get delivery
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDelivery()
    {
        return $this->delivery;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return PanierAddon
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }
}