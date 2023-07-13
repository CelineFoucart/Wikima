<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230713150917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD enable_comment TINYINT(1) DEFAULT NULL, ADD is_archived TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE note ADD is_archived TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE person ADD is_archived TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE place ADD is_archived TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP enable_comment, DROP is_achived');
        $this->addSql('ALTER TABLE note DROP is_achived');
        $this->addSql('ALTER TABLE person DROP is_achived');
        $this->addSql('ALTER TABLE place DROP is_achived');
    }
}
