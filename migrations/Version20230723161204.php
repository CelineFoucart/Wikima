<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230723161204 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE template (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(2000) DEFAULT NULL, content LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE template_template_group (template_id INT NOT NULL, template_group_id INT NOT NULL, INDEX IDX_FB0769105DA0FB8 (template_id), INDEX IDX_FB076910A102E934 (template_group_id), PRIMARY KEY(template_id, template_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE template_group (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(2000) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE template_template_group ADD CONSTRAINT FK_FB0769105DA0FB8 FOREIGN KEY (template_id) REFERENCES template (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE template_template_group ADD CONSTRAINT FK_FB076910A102E934 FOREIGN KEY (template_group_id) REFERENCES template_group (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE template_template_group DROP FOREIGN KEY FK_FB0769105DA0FB8');
        $this->addSql('ALTER TABLE template_template_group DROP FOREIGN KEY FK_FB076910A102E934');
        $this->addSql('DROP TABLE template');
        $this->addSql('DROP TABLE template_template_group');
        $this->addSql('DROP TABLE template_group');
    }
}
