<?php 

namespace Amap\UserBundle\Entity;
 
use Sonata\UserBundle\Entity\BaseUser as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

 
/**
 * @ORM\Table(name="fos_user")
 * @ORM\Entity(repositoryClass="Amap\UserBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
	
	/**
     * @ORM\OneToMany(targetEntity="Amap\MainBundle\Entity\Article", mappedBy="user")
     */
    private $articles; 
	
	/**
     * @ORM\OneToMany(targetEntity="Amap\MainBundle\Entity\Commande", mappedBy="user")
     */
    private $commandes; 
	
	/**
     * @ORM\ManyToMany(targetEntity="Amap\MainBundle\Entity\Permanence", inversedBy="users", cascade={"persist", "detach"})
     * @ORM\JoinTable(name="user_has_permanence",
     *   joinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="permanence_id", referencedColumnName="id")
     *   }
     * )
     */
    private $permanences;

    /**
     * Constructor
     */
    public function __construct()
    {
    	parent::__construct();
		
        $this->articles = new \Doctrine\Common\Collections\ArrayCollection();
		$this->commandes = new \Doctrine\Common\Collections\ArrayCollection();
		$this->permanences = new \Doctrine\Common\Collections\ArrayCollection();
    }
	
	public function hasArticle($id)
	{
		if($id > 0)
		{
			foreach ($this->articles as $key => $article) 
			{
				if($id == $article->getId()) {
					return true;
				}
			}
		}
		return false;
	}
	
	public function hasPermanence($id)
	{
		if($id > 0)
		{
			foreach ($this->permanences as $key => $perm) 
			{
				if($id == $perm->getId()) {
					return true;
				}
			}
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
     * Add articles
     *
     * @param \Amap\MainBundle\Entity\Article $articles
     * @return User
     */
    public function addArticle(\Amap\MainBundle\Entity\Article $articles)
    {
        $this->articles[] = $articles;
    
        return $this;
    }

    /**
     * Remove articles
     *
     * @param \Amap\MainBundle\Entity\Article $articles
     */
    public function removeArticle(\Amap\MainBundle\Entity\Article $articles)
    {
        $this->articles->removeElement($articles);
    }

    /**
     * Get articles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Add commandes
     *
     * @param \Amap\MainBundle\Entity\Commande $commandes
     * @return User
     */
    public function addCommande(\Amap\MainBundle\Entity\Commande $commandes)
    {
        $this->commandes[] = $commandes;
    
        return $this;
    }

    /**
     * Remove commandes
     *
     * @param \Amap\MainBundle\Entity\Commande $commandes
     */
    public function removeCommande(\Amap\MainBundle\Entity\Commande $commandes)
    {
        $this->commandes->removeElement($commandes);
    }

    /**
     * Get commandes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCommandes()
    {
        return $this->commandes;
    }
	
    /**
     * Add permanences
     *
     * @param \Amap\MainBundle\Entity\Permanence $permanences
     * @return User
     */
    public function addPermanence(\Amap\MainBundle\Entity\Permanence $permanences)
    {
        $this->permanences[] = $permanences;
    
        return $this;
    }

    /**
     * Remove permanences
     *
     * @param \Amap\MainBundle\Entity\Permanence $permanences
     */
    public function removePermanence(\Amap\MainBundle\Entity\Permanence $permanences)
    {
        $this->permanences->removeElement($permanences);
    }

    /**
     * Get permanences
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPermanences()
    {
        return $this->permanences;
    }

}