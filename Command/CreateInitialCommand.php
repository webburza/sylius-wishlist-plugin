<?php

namespace Webburza\Sylius\WishlistBundle\Command;

use Sylius\Bundle\UserBundle\Doctrine\ORM\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webburza\Sylius\WishlistBundle\Model\WishlistInterface;
use Webburza\Sylius\WishlistBundle\Model\WishlistRepositoryInterface;

class CreateInitialCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('webburza:sylius-wishlist-bundle:create-initial')
            ->setDescription("Create initial wishlists for existing customers.")
            ->setHelp("Usage:  <info>$ bin/console webburza:sylius-wishlist-bundle:create-initial</info>")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Creating wishlists for existing customers...</info>');

        /** @var \Doctrine\ORM\EntityManager $manager */
        $manager = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        /** @var CustomerRepository $customerRepository */
        /** @var WishlistRepositoryInterface $wishlistRepository */
        $customerRepository = $this->getContainer()->get('sylius.repository.customer');
        $wishlistRepository = $this->getContainer()->get('webburza.repository.wishlist');

        $customers = $customerRepository->findAll();
        $createdCount = 0;

        // For each customer, check if a wishlist exists and create it if not
        foreach ($customers as $customer) {
            if ($wishlistRepository->getCountForCustomer($customer) == 0) {
                /** @var WishlistInterface $wishlist */
                $wishlist =
                    $this->getContainer()
                         ->get('webburza.factory.wishlist')
                         ->createDefault($customer);

                $manager->persist($wishlist);
                $createdCount++;
            }
        }

        // Flush changes
        $manager->flush();

        $output->writeln('<info>Created '. $createdCount .' wishlists.</info>');
    }
}
