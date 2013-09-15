<?php

namespace Amap\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Commande
 *
 * @ORM\Table(name="commande")
 * @ORM\Entity(repositoryClass="Amap\MainBundle\Entity\Repository\CommandeRepository")
 */
class Commande
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
     * @var float
     *
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    private $price;
	
	/**
     * @var string
     *
     * @ORM\Column(name="product_detail", type="json", nullable=true)
     */
    private $productDetail;
	
	/**
     * @var \PanierAddon
     *
     * @ORM\ManyToOne(targetEntity="PanierAddon")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="panier_addon_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $panierAddon;
	
	/**
     * @var \Delivery
     *
     * @ORM\ManyToOne(targetEntity="Delivery")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="delivery_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $delivery;
    
	/**
     * @ORM\ManyToMany(targetEntity="Product", inversedBy="commande", cascade={"persist", "detach"})
     * @ORM\JoinTable(name="commande_has_product",
     *   joinColumns={
     *     @ORM\JoinColumn(name="commande_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     *   }
     * )
     */
    private $product;
	
	/**
     * @var \Amap\UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Amap\UserBundle\Entity\User", inversedBy="commandes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $user;
	
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->product = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    
    public function __toString() {
    	if($this->panierAddon && $this->delivery) {
			return (string) 'Commande "'.$this->panierAddon->getTitle().'" le '.$this->delivery->getDeliveredAt()->format('d/m/Y');
		} else {
			return (string) 'Nouvelle Commande';
		}
	}
    
	
	public function getPriceFormat()
	{
		return number_format($this->price, 2, ',', ' ');
	}
	
	public function getDevise()
	{
		return '€';
	}
	
	
	private $productDetailToStr;
	
	public function getProductDetailToStr()
	{
		$str = '';
		$detail = $this->productDetail;
		foreach ($this->product as $key => $product) 
		{
			$quantity = $detail[ $product->getId() ];
			$total = number_format( ($product->getPrice() * $quantity) , 2, ',', ' ') . $this->getDevise();
			
			$str .= $quantity.' x '.$product.' = '.$total . " || \r\n"; // '&#10;' // '\n' // chr(10)
		}
		
		return $str;
	}
	public function setProductDetailToStr($str)
	{
		$this->productDetailToStr = $str;
		return $this;
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
     * @return Commande
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
     * @return Commande
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
     * Set price
     *
     * @param float $price
     * @return Commande
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
     * Set productDetail
     *
     * @param json $productDetail
     * @return Commande
     */
    public function setProductDetail($productDetail)
    {
        $this->productDetail = $productDetail;
    
        return $this;
    }

    /**
     * Get productDetail
     *
     * @return json 
     */
    public function getProductDetail()
    {
        return $this->productDetail;
    }

    /**
     * Set panierAddon
     *
     * @param \Amap\MainBundle\Entity\PanierAddon $panierAddon
     * @return Commande
     */
    public function setPanierAddon(\Amap\MainBundle\Entity\PanierAddon $panierAddon)
    {
        $this->panierAddon = $panierAddon;
    
        return $this;
    }

    /**
     * Get panierAddon
     *
     * @return \Amap\MainBundle\Entity\PanierAddon 
     */
    public function getPanierAddon()
    {
        return $this->panierAddon;
    }

    /**
     * Add product
     *
     * @param \Amap\MainBundle\Entity\Product $product
     * @return Commande
     */
    public function addProduct(\Amap\MainBundle\Entity\Product $product)
    {
        $this->product[] = $product;
    
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
     * Set user
     *
     * @param \Amap\UserBundle\Entity\User $user
     * @return Commande
     */
    public function setUser(\Amap\UserBundle\Entity\User $user)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Amap\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set delivery
     *
     * @param \Amap\MainBundle\Entity\Delivery $delivery
     * @return Commande
     */
    public function setDelivery(\Amap\MainBundle\Entity\Delivery $delivery)
    {
        $this->delivery = $delivery;
    
        return $this;
    }

    /**
     * Get delivery
     *
     * @return \Amap\MainBundle\Entity\Delivery 
     */
    public function getDelivery()
    {
        return $this->delivery;
    }
}