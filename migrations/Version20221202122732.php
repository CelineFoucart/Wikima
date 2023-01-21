<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221202122732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE page_category (page_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_86D31EE1C4663E4 (page_id), INDEX IDX_86D31EE112469DE2 (category_id), PRIMARY KEY(page_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page_portal (page_id INT NOT NULL, portal_id INT NOT NULL, INDEX IDX_647865EEC4663E4 (page_id), INDEX IDX_647865EEB887E1DD (portal_id), PRIMARY KEY(page_id, portal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE page_category ADD CONSTRAINT FK_86D31EE1C4663E4 FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE page_category ADD CONSTRAINT FK_86D31EE112469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE page_portal ADD CONSTRAINT FK_647865EEC4663E4 FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE page_portal ADD CONSTRAINT FK_647865EEB887E1DD FOREIGN KEY (portal_id) REFERENCES portal (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE page_category');
        $this->addSql('DROP TABLE page_portal');
    }
}
