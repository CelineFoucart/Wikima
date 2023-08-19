<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230819142001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE image_image_tag (image_id INT NOT NULL, image_tag_id INT NOT NULL, INDEX IDX_83E1BC843DA5256D (image_id), INDEX IDX_83E1BC8415CF3DD9 (image_tag_id), PRIMARY KEY(image_id, image_tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image_tag (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE image_image_tag ADD CONSTRAINT FK_83E1BC843DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image_image_tag ADD CONSTRAINT FK_83E1BC8415CF3DD9 FOREIGN KEY (image_tag_id) REFERENCES image_tag (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image_image_tag DROP FOREIGN KEY FK_83E1BC843DA5256D');
        $this->addSql('ALTER TABLE image_image_tag DROP FOREIGN KEY FK_83E1BC8415CF3DD9');
        $this->addSql('DROP TABLE image_image_tag');
        $this->addSql('DROP TABLE image_tag');
    }
}
