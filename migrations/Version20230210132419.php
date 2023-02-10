<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230210132419 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD is_sticky TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE person ADD is_sticky TINYINT(1) DEFAULT NULL, ADD children VARCHAR(255) DEFAULT NULL, ADD siblings VARCHAR(255) DEFAULT NULL, ADD partner VARCHAR(255) DEFAULT NULL, ADD physical_description VARCHAR(400) DEFAULT NULL');
        $this->addSql('ALTER TABLE place ADD is_sticky TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE portal ADD position INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP is_sticky');
        $this->addSql('ALTER TABLE person DROP is_sticky, DROP children, DROP siblings, DROP partner, DROP physical_description');
        $this->addSql('ALTER TABLE place DROP is_sticky');
        $this->addSql('ALTER TABLE portal DROP position');
    }
}
