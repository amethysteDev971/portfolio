<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250103031334 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add updated_at column to photo table';
    }

    public function up(Schema $schema): void
    {
        // Add the updated_at column with a default value for existing rows
        $this->addSql('ALTER TABLE photo ADD updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');

        // Ensure all existing rows have a valid datetime value
        $this->addSql('UPDATE photo SET updated_at = CURRENT_TIMESTAMP');
    }

    public function down(Schema $schema): void
    {
        // Remove the updated_at column
        $this->addSql('ALTER TABLE photo DROP updated_at');
    }
}
