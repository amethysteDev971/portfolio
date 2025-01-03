<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250102183203 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE photo ADD user_id INT NOT NULL, CHANGE alt alt VARCHAR(255) NOT NULL, CHANGE size size INT NOT NULL, CHANGE mime_type mime_type VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B78418A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_14B78418A76ED395 ON photo (user_id)');
        $this->addSql('ALTER TABLE post ADD projet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DC18272 FOREIGN KEY (projet_id) REFERENCES projets (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5A8A6C8DC18272 ON post (projet_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B78418A76ED395');
        $this->addSql('DROP INDEX IDX_14B78418A76ED395 ON photo');
        $this->addSql('ALTER TABLE photo DROP user_id, CHANGE alt alt VARCHAR(255) DEFAULT NULL, CHANGE size size INT DEFAULT NULL, CHANGE mime_type mime_type VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DC18272');
        $this->addSql('DROP INDEX UNIQ_5A8A6C8DC18272 ON post');
        $this->addSql('ALTER TABLE post DROP projet_id');
    }
}
