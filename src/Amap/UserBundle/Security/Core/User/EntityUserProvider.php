<?php

namespace Amap\UserBundle\Security\Core\User;


class EntityUserProvider implements UserProviderInterface 
{
	
	/**
    * Constructeur du service security.user.provider.entity
    * @param EntityManager       $em
    * @param string              $class Entité qui sert de user de connexion
    * @param string              $property Nom du champ qui sert de login sur l'entité $class
    */
   public function __construct(EntityManager $em, $class, $property = null)
   {
      $this->class = $class;
 
      if (false !== strpos($this->class, ':'))
      {
         $this->class = $em->getClassMetadata($class)->name;
      }
 
      $this->repository = $em->getRepository($class);
      //$this->repository = new UserRepository($em, $em->getClassMetadata($class));
 
      $this->property = $property;
   }
 
  /**
    * Charge un user à partir de son username
    * @param string $username
    * @return UserInterface
    */
   public function loadUserByUsername($username)
   {
   $user = $this->repository->findOneByMyWay($username);
 
      if (null === $user)
      {
         throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username));
      }
 
      return $user;
   }

}