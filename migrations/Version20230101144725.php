<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230101144725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE person ADD image_id INT DEFAULT NULL, DROP image');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD1763DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('CREATE INDEX IDX_34DCD1763DA5256D ON person (image_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD1763DA5256D');
        $this->addSql('DROP INDEX IDX_34DCD1763DA5256D ON person');
        $this->addSql('ALTER TABLE person ADD image VARCHAR(255) DEFAULT NULL, DROP image_id');
    }
}
