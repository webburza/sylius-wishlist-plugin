<?php

namespace Webburza\Sylius\WishlistBundle\Command;

use Sylius\Component\Rbac\Model\Permission;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UninstallCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('webburza:sylius-wishlist-bundle:uninstall')
            ->setDescription("Uninstalls the bundle, removes bundle-specific database tables and permissions.")
            ->setHelp("Usage:  <info>$ bin/console webburza:sylius-wishlist-bundle:uninstall</info>")
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Doctrine\ORM\EntityManager $manager */
        $manager = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $output->writeln('<info>Removing wishlist tables...</info>');
        $this->removeWishlistTables($manager);

        $output->writeln('<info>Removing permissions...</info>');
        $this->removePermissions($manager);

        $output->writeln('<info>Uninstallation complete.</info>');
    }

    /**
     * Remove wishlist tables.
     *
     * @param $manager
     */
    private function removeWishlistTables($manager)
    {
        // Check if tables exist
        $schemaManager = $manager->getConnection()->getSchemaManager();

        // Skip if product group table does not exist
        if (!$schemaManager->tablesExist(['webburza_sylius_product_group'])) {
            return;
        }

        $queries = [
            'ALTER TABLE webburza_sylius_wishlist_item DROP FOREIGN KEY FK_A7AB1B9FA80EF684',
            'ALTER TABLE webburza_sylius_wishlist_item DROP FOREIGN KEY FK_A7AB1B9FFB8E54CD',
            'ALTER TABLE webburza_sylius_wishlist DROP FOREIGN KEY FK_7772F5979395C3F3',
            'DROP TABLE webburza_sylius_wishlist_item',
            'DROP TABLE webburza_sylius_wishlist'
        ];

        $manager->beginTransaction();

        foreach ($queries as $query) {
            $statement = $manager->getConnection()->prepare($query);
            $statement->execute();
        }

        $manager->commit();
    }

    /**
     * Remove permission entries.
     *
     * @param $manager
     */
    private function removePermissions($manager)
    {
        $repository = $this->getContainer()->get('sylius.repository.permission');

        // Get the main node to remove
        $managePermission = $repository->findOneBy(['code' => 'webburza.manage.wishlist']);

        if ($managePermission) {
            // Remove permissions
            $manager->remove($managePermission);
            $manager->flush();
        }
    }
}
