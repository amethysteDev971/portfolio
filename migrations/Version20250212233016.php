<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250212233016 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE projets ADD cover_photo_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE projets ADD CONSTRAINT FK_B454C1DBA69B8AD7 FOREIGN KEY (cover_photo_id) REFERENCES photo (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B454C1DBA69B8AD7 ON projets (cover_photo_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE projets DROP FOREIGN KEY FK_B454C1DBA69B8AD7');
        $this->addSql('DROP INDEX UNIQ_B454C1DBA69B8AD7 ON projets');
        $this->addSql('ALTER TABLE projets DROP cover_photo_id');
    }
}
