<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260622130551 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de la colonne created_at dans la table categories avec gestion des données existantes';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE categories ADD created_at DATETIME DEFAULT NULL');

        $this->addSql('UPDATE categories SET created_at = NOW() WHERE created_at IS NULL');

        $this->addSql('ALTER TABLE categories MODIFY created_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE categories DROP created_at');
    }
}