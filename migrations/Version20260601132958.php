<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260601132958 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adresse_de_livraison (id INT AUTO_INCREMENT NOT NULL, adresse_postale VARCHAR(255) NOT NULL, code_postale VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ligne_de_commande (id INT AUTO_INCREMENT NOT NULL, prix_unitaire DOUBLE PRECISION NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE product_categories (product_id INT NOT NULL, categories_id INT NOT NULL, INDEX IDX_A99419434584665A (product_id), INDEX IDX_A9941943A21214B7 (categories_id), PRIMARY KEY (product_id, categories_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE product_categories ADD CONSTRAINT FK_A99419434584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_categories ADD CONSTRAINT FK_A9941943A21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product ADD relation VARCHAR(255) NOT NULL, ADD ligne_de_commande VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user ADD adresse_mail VARCHAR(255) NOT NULL, ADD numero_de_telephone INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_categories DROP FOREIGN KEY FK_A99419434584665A');
        $this->addSql('ALTER TABLE product_categories DROP FOREIGN KEY FK_A9941943A21214B7');
        $this->addSql('DROP TABLE adresse_de_livraison');
        $this->addSql('DROP TABLE ligne_de_commande');
        $this->addSql('DROP TABLE product_categories');
        $this->addSql('ALTER TABLE product DROP relation, DROP ligne_de_commande');
        $this->addSql('ALTER TABLE user DROP adresse_mail, DROP numero_de_telephone');
    }
}
