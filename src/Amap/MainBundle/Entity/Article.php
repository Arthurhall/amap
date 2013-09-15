<?php

namespace Amap\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Article
 *
 * @ORM\Table(name="article")
 * @ORM\Entity(repositoryClass="Amap\MainBundle\Entity\Repository\ArticleRepository")
 */
class Article
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
     * @ORM\Column(name="title", type="string", length=200, nullable=false)
	 * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
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
     * @var \DateTime
     *
     * @ORM\Column(name="is_home_since", type="datetime", nullable=true)
     */
    private $isHomeSince;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="published_at", type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_published", type="boolean", nullable=true)
     */
    private $isPublished;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_private", type="boolean", nullable=true)
     */
    private $isPrivate;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="is_home", type="boolean", nullable=true)
     */
    private $isHome;
	
	/**
     * @ORM\Column(name="slug", length=245, unique=true)
	 * @Gedmo\Slug(fields={"title"})
     */
    private $slug;

    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Image", inversedBy="article")
     * @ORM\JoinTable(name="article_has_image",
     *   joinColumns={
     *     @ORM\JoinColumn(name="article_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     *   }
     * )
     */
    private $image;

    /**
     * @var \Category
     *
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $category;
    
	/**
     * @var \Amap\UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Amap\UserBundle\Entity\User", inversedBy="articles")
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
        $this->image = new \Doctrine\Common\Collections\ArrayCollection();
        
        $this->isPrivate = false;
        $this->isPublished = false;
		$this->isHome = false;
    }
    
    public function __toString() {
		return (string) $this->title;
	}
    
    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     * @return Article
     */
    public function setIsPublished($isPublished)
    {
        if($isPublished && !$this->publishedAt) {
            $this->setPublishedAt( new \DateTime('now') );
        }
        $this->isPublished = $isPublished;
        
        return $this;
    }
	
	public function hasVitrine()
	{
		foreach ($this->image as $key => $img) {
			if($img->getVitrine())
				return true;
		}
		return false;
	}
    
    private $imageVitrine = null;
	
    public function getImageVitrine()
    {
    	if($this->imageVitrine) 
    		return $this->imageVitrine;
		
    	$first = null;
		$i = 0;
        foreach ($this->image as $key => $img) 
        {
        	if($i == 0) $first = $img;
			if($img->getVitrine())
			{
				$this->imageVitrine = $img;
				return $img;
			}
			$i++;
		}
		$this->imageVitrine = $first;
		return $first;
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
     * @return Article
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
     * Set content
     *
     * @param string $content
     * @return Article
     */
    public function setContent($content)
    {
        $this->content = $content;
    
        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Article
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
     * @return Article
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
     * Set publishedAt
     *
     * @param \DateTime $publishedAt
     * @return Article
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;
    
        return $this;
    }

    /**
     * Get publishedAt
     *
     * @return \DateTime 
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }
    

    /**
     * Get isPublished
     *
     * @return boolean 
     */
    public function getIsPublished()
    {
        return $this->isPublished;
    }

    /**
     * Add image
     *
     * @param \Amap\MainBundle\Entity\Image $image
     * @return Article
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
     * Set slug
     *
     * @param string $slug
     * @return Article
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set category
     *
     * @param \Amap\MainBundle\Entity\Category $category
     * @return Article
     */
    public function setCategory(\Amap\MainBundle\Entity\Category $category)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return \Amap\MainBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set user
     *
     * @param \Amap\UserBundle\Entity\User $user
     * @return Article
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
     * Set isPrivate
     *
     * @param boolean $isPrivate
     * @return Article
     */
    public function setIsPrivate($isPrivate)
    {
        $this->isPrivate = $isPrivate;
    
        return $this;
    }

    /**
     * Get isPrivate
     *
     * @return boolean 
     */
    public function getIsPrivate()
    {
        return $this->isPrivate;
    }

    /**
     * Set isHome
     *
     * @param boolean $isHome
     * @return Article
     */
    public function setIsHome($isHome)
    {
        $this->isHome = $isHome;
    
        return $this;
    }

    /**
     * Get isHome
     *
     * @return boolean 
     */
    public function getIsHome()
    {
        return $this->isHome;
    }

    /**
     * Set isHomeSince
     *
     * @param \DateTime $isHomeSince
     * @return Article
     */
    public function setIsHomeSince($isHomeSince)
    {
        $this->isHomeSince = $isHomeSince;
    
        return $this;
    }

    /**
     * Get isHomeSince
     *
     * @return \DateTime 
     */
    public function getIsHomeSince()
    {
        return $this->isHomeSince;
    }
}