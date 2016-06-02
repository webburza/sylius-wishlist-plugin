<?php

namespace Webburza\Sylius\WishlistBundle\Command;

use Sylius\Component\Rbac\Model\Permission;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('webburza:sylius-wishlist-bundle:install')
            ->setDescription("Installs the bundle, creates required database tables.")
            ->setHelp("Usage:  <info>$ bin/console webburza:sylius-wishlist-bundle:install</info>")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Doctrine\ORM\EntityManager $manager */
        $manager = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $output->writeln('<info>Creating wishlist tables...</info>');
        $this->createWishlistTables($manager);

        $output->writeln('<info>Creating permissions...</info>');
        $this->createPermissions($manager);

        $output->writeln('<info>Installation complete.</info>');
    }

    /**
     * Create wishlist tables.
     *
     * @param $manager
     */
    private function createWishlistTables($manager)
    {
        // Check if tables already exist
        $schemaManager = $manager->getConnection()->getSchemaManager();

        // Skipp if wishlist table already exist
        if ($schemaManager->tablesExist(['webburza_sylius_wishlist'])) {
            return;
        }

        $queries = [
            'CREATE TABLE webburza_sylius_wishlist (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, public TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_7772F5979395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB',
            'CREATE TABLE webburza_sylius_wishlist_item (id INT AUTO_INCREMENT NOT NULL, wishlist_id INT NOT NULL, product_variant_id INT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_A7AB1B9FFB8E54CD (wishlist_id), INDEX IDX_A7AB1B9FA80EF684 (product_variant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB',
            'ALTER TABLE webburza_sylius_wishlist ADD CONSTRAINT FK_7772F5979395C3F3 FOREIGN KEY (customer_id) REFERENCES sylius_customer (id) ON DELETE CASCADE',
            'ALTER TABLE webburza_sylius_wishlist_item ADD CONSTRAINT FK_A7AB1B9FFB8E54CD FOREIGN KEY (wishlist_id) REFERENCES webburza_sylius_wishlist (id) ON DELETE CASCADE',
            'ALTER TABLE webburza_sylius_wishlist_item ADD CONSTRAINT FK_A7AB1B9FA80EF684 FOREIGN KEY (product_variant_id) REFERENCES sylius_product_variant (id) ON DELETE CASCADE'
        ];

        $manager->beginTransaction();

        foreach ($queries as $query) {
            $statement = $manager->getConnection()->prepare($query);
            $statement->execute();
        }

        $manager->commit();
    }

    /**
     * Create all required permission entries.
     *
     * @param $manager
     */
    private function createPermissions($manager)
    {
        $repository = $this->getContainer()->get('sylius.repository.permission');

        // Get parent node (used for accounts)
        $accountPermission = $repository->findOneBy(['code' => 'sylius.accounts']);

        // Create permissions
        $wishlistManagePermission = $this->createWishlistPermissions($accountPermission);

        // Persist the permissions
        $manager->persist($wishlistManagePermission);
        $manager->flush();
    }

    /**
     * Create permissions for Wishlist resource.
     *
     * @param Permission $parentPermission
     * @return Permission
     */
    private function createWishlistPermissions(Permission $parentPermission)
    {
        // Create main permissions node
        $managePermission = new Permission();
        $managePermission->setCode('webburza.manage.wishlist');
        $managePermission->setDescription('Manage wishlists');
        $managePermission->setParent($parentPermission);

        // Define permissions
        $permissions = [
            'webburza.wishlist.show' => 'Show wishlist',
            'webburza.wishlist.index' => 'List wishlists',
            'webburza.wishlist.create' => 'Create wishlist',
            'webburza.wishlist.update' => 'Update wishlist',
            'webburza.wishlist.delete' => 'Delete wishlist'
        ];

        // Create each permission
        foreach ($permissions as $code => $description) {
            $permission = new Permission();
            $permission->setCode($code);
            $permission->setDescription($description);

            $managePermission->addChild($permission);
        }

        return $managePermission;
    }
}
