<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260613154043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, montant_total DOUBLE PRECISION NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_6EEAA67DFB88E14F (utilisateur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE order_item');
        $this->addSql('ALTER TABLE ligne_de_commande ADD commande_id INT NOT NULL');
        $this->addSql('ALTER TABLE ligne_de_commande ADD CONSTRAINT FK_7982ACE682EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('CREATE INDEX IDX_7982ACE682EA2E54 ON ligne_de_commande (commande_id)');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY `FK_D34A04ADCA2A78B2`');
        $this->addSql('DROP INDEX IDX_D34A04ADCA2A78B2 ON product');
        $this->addSql('ALTER TABLE product DROP ligne_de_commande_id, CHANGE stock_id stock_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_item (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DFB88E14F');
        $this->addSql('DROP TABLE commande');
        $this->addSql('ALTER TABLE ligne_de_commande DROP FOREIGN KEY FK_7982ACE682EA2E54');
        $this->addSql('DROP INDEX IDX_7982ACE682EA2E54 ON ligne_de_commande');
        $this->addSql('ALTER TABLE ligne_de_commande DROP commande_id');
        $this->addSql('ALTER TABLE product ADD ligne_de_commande_id INT DEFAULT NULL, CHANGE stock_id stock_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT `FK_D34A04ADCA2A78B2` FOREIGN KEY (ligne_de_commande_id) REFERENCES ligne_de_commande (id)');
        $this->addSql('CREATE INDEX IDX_D34A04ADCA2A78B2 ON product (ligne_de_commande_id)');
    }
}
