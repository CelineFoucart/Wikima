<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230203181027 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE place_category (place_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_2C17FE42DA6A219 (place_id), INDEX IDX_2C17FE4212469DE2 (category_id), PRIMARY KEY(place_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place_portal (place_id INT NOT NULL, portal_id INT NOT NULL, INDEX IDX_A9609499DA6A219 (place_id), INDEX IDX_A9609499B887E1DD (portal_id), PRIMARY KEY(place_id, portal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE place_category ADD CONSTRAINT FK_2C17FE42DA6A219 FOREIGN KEY (place_id) REFERENCES place (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE place_category ADD CONSTRAINT FK_2C17FE4212469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE place_portal ADD CONSTRAINT FK_A9609499DA6A219 FOREIGN KEY (place_id) REFERENCES place (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE place_portal ADD CONSTRAINT FK_A9609499B887E1DD FOREIGN KEY (portal_id) REFERENCES portal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE place ADD illustration_id INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL, DROP illustration');
        $this->addSql('ALTER TABLE place ADD CONSTRAINT FK_741D53CD5926566C FOREIGN KEY (illustration_id) REFERENCES image (id)');
        $this->addSql('CREATE INDEX IDX_741D53CD5926566C ON place (illustration_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE place_category DROP FOREIGN KEY FK_2C17FE42DA6A219');
        $this->addSql('ALTER TABLE place_category DROP FOREIGN KEY FK_2C17FE4212469DE2');
        $this->addSql('ALTER TABLE place_portal DROP FOREIGN KEY FK_A9609499DA6A219');
        $this->addSql('ALTER TABLE place_portal DROP FOREIGN KEY FK_A9609499B887E1DD');
        $this->addSql('DROP TABLE place_category');
        $this->addSql('DROP TABLE place_portal');
        $this->addSql('ALTER TABLE place DROP FOREIGN KEY FK_741D53CD5926566C');
        $this->addSql('DROP INDEX IDX_741D53CD5926566C ON place');
        $this->addSql('ALTER TABLE place ADD illustration VARCHAR(255) DEFAULT NULL, DROP illustration_id, DROP updated_at');
    }
}
