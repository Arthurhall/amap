<?php

namespace Amap\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Amap\MainBundle\Entity\ProductVegetable;
use Amap\MainBundle\Entity\QuantityType;


/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity
 * 
 * @UniqueEntity(fields={"title"})
 * @ORM\HasLifecycleCallbacks()
 */
class Product
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=100, nullable=false, unique=true)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=500, nullable=true)
     */
    private $description;

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
     * @ORM\Column(name="updatetd_at", type="datetime", nullable=true)
	 * @Gedmo\Timestampable(on="update")
     */
    private $updatetdAt;

    /**
     * @var float
     *
     * @ORM\Column(name="quantity", type="float", nullable=true)
     */
    private $quantity;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    private $price;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="PanierMaxi", mappedBy="product")
     */
    private $panierMaxi;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="PanierMini", mappedBy="product")
     */
    private $panierMini;
	
	/**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="PanierAddon", mappedBy="product")
     */
    private $panierAddon;
	
	/**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Commande", mappedBy="product")
     */
    private $commande;

    /**
     * @var \QuantityType
     *
     * @ORM\ManyToOne(targetEntity="QuantityType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="quantity_type_id", referencedColumnName="id")
     * })
     */
    private $quantityType;
    
    /**
     * @var \ProductVegetable
     *
     * @ORM\ManyToOne(targetEntity="ProductVegetable", inversedBy="products")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_vegetable_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $productVegetable;
    

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->panierMaxi = new \Doctrine\Common\Collections\ArrayCollection();
        $this->panierMini = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
	public function __toString() {
		if($this->testTitleValue())
			return (string) $this->productVegetable.' - '.$this->quantity.$this->quantityType.' - '.$this->getPriceFormat().$this->getDevise();
		else 
			return (string) 'Nouveau Produit';
	}
    
	
	public function getPriceFormat()
	{
		return number_format($this->price, 2, ',', ' ');
	}
	
	public function getDevise()
	{
		return '€';
	}
	
	/**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setTitleValue()
    {
        if($this->testTitleValue())
            $this->title = $this->__toString();
    }
    
    public function testTitleValue()
    {
        if( is_object($this->productVegetable) && 
        	$this->productVegetable instanceof ProductVegetable && 
        	$this->quantity && 
        	is_object($this->quantityType) && 
        	$this->quantityType instanceof QuantityType && 
        	$this->price) {
            return true;
        }
        return false;
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
     * Set title
     *
     * @param string $title
     * @return Product
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

    /**
     * Set description
     *
     * @param string $description
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Product
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
     * Set updatetdAt
     *
     * @param \DateTime $updatetdAt
     * @return Product
     */
    public function setUpdatetdAt($updatetdAt)
    {
        $this->updatetdAt = $updatetdAt;
    
        return $this;
    }

    /**
     * Get updatetdAt
     *
     * @return \DateTime 
     */
    public function getUpdatetdAt()
    {
        return $this->updatetdAt;
    }

    /**
     * Set quantity
     *
     * @param float $quantity
     * @return Product
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        $this->setTitleValue();
        return $this;
    }

    /**
     * Get quantity
     *
     * @return float 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;
        $this->setTitleValue();
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
     * Add panierMaxi
     *
     * @param \Amap\MainBundle\Entity\PanierMaxi $panierMaxi
     * @return Product
     */
    public function addPanierMaxi(\Amap\MainBundle\Entity\PanierMaxi $panierMaxi)
    {
        $this->panierMaxi[] = $panierMaxi;
    	//$panierMaxi->addProduct($this);
        return $this;
    }

    /**
     * Remove panierMaxi
     *
     * @param \Amap\MainBundle\Entity\PanierMaxi $panierMaxi
     */
    public function removePanierMaxi(\Amap\MainBundle\Entity\PanierMaxi $panierMaxi)
    {
        $this->panierMaxi->removeElement($panierMaxi);
		//$panierMaxi->removeProduct($this);
   	}

    /**
     * Get panierMaxi
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPanierMaxi()
    {
        return $this->panierMaxi;
    }

    /**
     * Add panierMini
     *
     * @param \Amap\MainBundle\Entity\PanierMini $panierMini
     * @return Product
     */
    public function addPanierMini(\Amap\MainBundle\Entity\PanierMini $panierMini)
    {
        $this->panierMini[] = $panierMini;
    	//$panierMini->addProduct($this);
        return $this;
    }

    /**
     * Remove panierMini
     *
     * @param \Amap\MainBundle\Entity\PanierMini $panierMini
     */
    public function removePanierMini(\Amap\MainBundle\Entity\PanierMini $panierMini)
    {
        $this->panierMini->removeElement($panierMini);
		//$panierMini->removeProduct($this);
    }

    /**
     * Get panierMini
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPanierMini()
    {
        return $this->panierMini;
    }

    /**
     * Set quantityType
     *
     * @param \Amap\MainBundle\Entity\QuantityType $quantityType
     * @return Product
     */
    public function setQuantityType(\Amap\MainBundle\Entity\QuantityType $quantityType = null)
    {
        $this->quantityType = $quantityType;
        $this->setTitleValue();
        return $this;
    }

    /**
     * Get quantityType
     *
     * @return \Amap\MainBundle\Entity\QuantityType 
     */
    public function getQuantityType()
    {
        return $this->quantityType;
    }

    /**
     * Set productVegetable
     *
     * @param \Amap\MainBundle\Entity\ProductVegetable $productVegetable
     * @return Product
     */
    public function setProductVegetable(\Amap\MainBundle\Entity\ProductVegetable $productVegetable = null)
    {
        $this->productVegetable = $productVegetable;
        $this->setTitleValue();
        return $this;
    }

    /**
     * Get productVegetable
     *
     * @return \Amap\MainBundle\Entity\ProductVegetable 
     */
    public function getProductVegetable()
    {
        return $this->productVegetable;
    }

    /**
     * Add panierAddon
     *
     * @param \Amap\MainBundle\Entity\PanierAddon $panierAddon
     * @return Product
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
     * Add commande
     *
     * @param \Amap\MainBundle\Entity\Commande $commande
     * @return Product
     */
    public function addCommande(\Amap\MainBundle\Entity\Commande $commande)
    {
        $this->commande[] = $commande;
    
        return $this;
    }

    /**
     * Remove commande
     *
     * @param \Amap\MainBundle\Entity\Commande $commande
     */
    public function removeCommande(\Amap\MainBundle\Entity\Commande $commande)
    {
        $this->commande->removeElement($commande);
    }

    /**
     * Get commande
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCommande()
    {
        return $this->commande;
    }
}