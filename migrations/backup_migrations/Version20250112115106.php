<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250112115106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $schemaManager = $this->connection->createSchemaManager();

        // Vérifie si 'created_at' existe
        if (!$schemaManager->introspectTable('projets')->hasColumn('created_at')) {
            $this->addSql('ALTER TABLE projets ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP');
        }

        // Vérifie si 'updated_at' existe
        if (!$schemaManager->introspectTable('projets')->hasColumn('updated_at')) {
            $this->addSql('ALTER TABLE projets ADD updated_at DATETIME DEFAULT CURRENT_TIMESTAMP');
        }
    }
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE projets DROP created_at, DROP updated_at');
    }
}
