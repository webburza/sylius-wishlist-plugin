<?php

namespace Webburza\Sylius\WishlistBundle\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Webburza\Sylius\WishlistBundle\Model\WishlistInterface;

class UserListener
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ArrayCollection|WishlistInterface[]
     */
    protected $wishlistQueue;

    /**
     * Event listeners cause circular reference exception
     * when using multiple database connections. Injecting the
     * entire container to access repositories inside listeners
     * seems to be the only working solution.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->wishlistQueue = new ArrayCollection();
    }

    /**
     * @param OnFlushEventArgs $event
     */
    public function onFlush(OnFlushEventArgs $event)
    {
        $entities = $event->getEntityManager()->getUnitOfWork()->getScheduledEntityInsertions();

        foreach ($entities as $entity) {
            if ($entity instanceof ShopUserInterface) {
                $this->wishlistQueue->add(
                    $this->container->get('webburza_wishlist.factory.wishlist')->createDefault($entity)
                );
            }
        }
    }

    /**
     * @param PostFlushEventArgs $event
     */
    public function postFlush(PostFlushEventArgs $event)
    {
        if ($this->wishlistQueue->count()) {
            foreach ($this->wishlistQueue as $wishlist) {
                $this->container->get('doctrine.orm.entity_manager')->persist($wishlist);
            }
            $this->wishlistQueue->clear();

            $event->getEntityManager()->flush();
        }
    }
}
