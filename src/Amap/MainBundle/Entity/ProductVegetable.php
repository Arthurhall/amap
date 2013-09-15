<?php

namespace Amap\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * ProductVegetable
 *
 * @ORM\Table(name="product_vegetable")
 * @ORM\Entity(repositoryClass="Amap\MainBundle\Entity\Repository\ProductVegetableRepository")
 * 
 * @UniqueEntity(fields={"title"})
 */
class ProductVegetable
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
	 * @ORM\OneToMany(targetEntity="Product", mappedBy="productVegetable")
	 */
	protected $products;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Image", inversedBy="productVegetable")
     * @ORM\JoinTable(name="product_vegetable_has_image",
     *   joinColumns={
     *     @ORM\JoinColumn(name="product_vegetable_id", referencedColumnName="id")
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
        
    }
    
	public function __toString() {
		return (string) $this->title;
	}
	
	
	public function getAvgPrices()
	{
		$pricesByQuantity = array();
		
		if(!$this->products) {
			return $pricesByQuantity;
		}
		
		foreach ($this->products as $key => $product) 
		{
			$price = $product->getPrice();
			$quantityLab = $product->getQuantityType()->getTitle();
			$quantityVal = $product->getQuantity();
			
			// On convertit en Kg les grammes :
			if(strtolower($quantityLab) == 'g') {
				$quantityVal = $quantityVal /1000;
				$quantityLab = 'Kg';
			}
			
			$pricesByQuantity[ $quantityLab ][] = $price / $quantityVal; 
		}
		
		foreach ($pricesByQuantity as $quantity => $prices) 
		{
			$count = count($prices);
			$sum = array_sum($prices);
			$avg = $sum / $count;
			
			$pricesByQuantity[$quantity]['avg'] = $avg;
		}
		
		return $pricesByQuantity;
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
     * Add products
     *
     * @param \Amap\MainBundle\Entity\Product $products
     * @return ProductVegetable
     */
    public function addProduct(\Amap\MainBundle\Entity\Product $products)
    {
        $this->products[] = $products;
    
        return $this;
    }

    /**
     * Remove products
     *
     * @param \Amap\MainBundle\Entity\Product $products
     */
    public function removeProduct(\Amap\MainBundle\Entity\Product $products)
    {
        $this->products->removeElement($products);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Add image
     *
     * @param \Amap\MainBundle\Entity\Image $image
     * @return ProductVegetable
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
}