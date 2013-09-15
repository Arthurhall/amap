<?php

namespace Amap\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * PanierMaxi
 *
 * @ORM\Table(name="panier_maxi")
 * @ORM\Entity
 */
class PanierMaxi extends PanierAbstract
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
     * @var float
     *
     * @ORM\Column(name="discounted_price", type="float", nullable=true)
     */
    private $discountedPrice;

    /**
     * @ORM\ManyToMany(targetEntity="Product", inversedBy="panierMaxi", cascade={"persist", "detach"})
     * @ORM\JoinTable(name="panier_maxi_has_product",
     *   joinColumns={
     *     @ORM\JoinColumn(name="panier_maxi_id", referencedColumnName="id")
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
     * @return PanierMaxi
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
     * @return PanierMaxi
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
     * @return PanierMaxi
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
     * @return PanierMaxi
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
     * @return PanierMaxi
     */
    public function addProduct(\Amap\MainBundle\Entity\Product $product)
    {
        $this->product[] = $product;
    	$product->addPanierMaxi($this);
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
		$product->removePanierMaxi($this);
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
}