<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250501234622 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Pour MySQL :
        $this->addSql("
            UPDATE photo
            SET path = SUBSTRING_INDEX(path, '/', -1)
            WHERE path LIKE 'uploads/photos/%'
        ");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE photo ADD size INT DEFAULT NULL, ADD mime_type VARCHAR(100) DEFAULT NULL');
    }
}
