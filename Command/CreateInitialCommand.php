<?php

namespace Webburza\Sylius\WishlistBundle\Command;

use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webburza\Sylius\WishlistBundle\Factory\WishlistFactoryInterface;
use Webburza\Sylius\WishlistBundle\Repository\WishlistRepositoryInterface;

class CreateInitialCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('webburza:sylius-wishlist-bundle:create-initial')
            ->setDescription("Create initial wishlists for existing users.")
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Creating wishlists for existing users...</info>');

        /** @var UserRepositoryInterface $userRepository */
        $userRepository = $this->getContainer()->get('sylius.repository.shop_user');

        /** @var WishlistRepositoryInterface $wishlistRepository */
        $wishlistRepository = $this->getContainer()->get('webburza_wishlist.repository.wishlist');

        /** @var WishlistFactoryInterface $wishlistFactory */
        $wishlistFactory = $this->getContainer()->get('webburza_wishlist.factory.wishlist');

        // Get all users
        $users = $userRepository->findAll();

        // Keep track of created wishlists
        $createdCount = 0;

        // For each user, check if a wishlist exists and create it if not
        foreach ($users as $user) {
            if ($wishlistRepository->getCountForUser($user) == 0) {
                $wishlist = $wishlistFactory->createDefault($user);
                $wishlistRepository->add($wishlist);
                $createdCount++;
            }
        }

        $output->writeln('<info>Created '. $createdCount .' wishlists.</info>');
    }
}
