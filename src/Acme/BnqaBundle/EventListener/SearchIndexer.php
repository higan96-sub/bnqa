<?php
namespace Acme\BnqaBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Acme\StoreBundle\Entity\Report;

class SearchIndexer
{
  public function postPersist(LifecycleEventArgs $args)
  {
    $entity = $args->getEntity();
    $entityManager = $args->getEntityManager();

    // perhaps you only want to act on some "Product" entity
    if ($entity instanceof Report) {
      $entity->setMisprint();
      $entityManager->persist($entity);
      $entityManager->flush();
    }
  }
}