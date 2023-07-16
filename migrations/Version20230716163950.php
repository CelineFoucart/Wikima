<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230716163950 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE idiom (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, article_id INT DEFAULT NULL, translated_name VARCHAR(255) NOT NULL, original_name VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, presentation LONGTEXT DEFAULT NULL, banner VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_70241857F675F31B (author_id), INDEX IDX_702418577294869C (article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE idiom_portal (idiom_id INT NOT NULL, portal_id INT NOT NULL, INDEX IDX_A4140AE4B1AA81FD (idiom_id), INDEX IDX_A4140AE4B887E1DD (portal_id), PRIMARY KEY(idiom_id, portal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE idiom_article (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, idiom_id INT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_184F608212469DE2 (category_id), INDEX IDX_184F6082B1AA81FD (idiom_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE idiom_article_image (idiom_article_id INT NOT NULL, image_id INT NOT NULL, INDEX IDX_540D90FE76E3D60 (idiom_article_id), INDEX IDX_540D90FE3DA5256D (image_id), PRIMARY KEY(idiom_article_id, image_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE idiom_category (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, position INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE idiom ADD CONSTRAINT FK_70241857F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE idiom ADD CONSTRAINT FK_702418577294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE idiom_portal ADD CONSTRAINT FK_A4140AE4B1AA81FD FOREIGN KEY (idiom_id) REFERENCES idiom (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE idiom_portal ADD CONSTRAINT FK_A4140AE4B887E1DD FOREIGN KEY (portal_id) REFERENCES portal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE idiom_article ADD CONSTRAINT FK_184F608212469DE2 FOREIGN KEY (category_id) REFERENCES idiom_category (id)');
        $this->addSql('ALTER TABLE idiom_article ADD CONSTRAINT FK_184F6082B1AA81FD FOREIGN KEY (idiom_id) REFERENCES idiom (id)');
        $this->addSql('ALTER TABLE idiom_article_image ADD CONSTRAINT FK_540D90FE76E3D60 FOREIGN KEY (idiom_article_id) REFERENCES idiom_article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE idiom_article_image ADD CONSTRAINT FK_540D90FE3DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE idiom DROP FOREIGN KEY FK_70241857F675F31B');
        $this->addSql('ALTER TABLE idiom DROP FOREIGN KEY FK_702418577294869C');
        $this->addSql('ALTER TABLE idiom_portal DROP FOREIGN KEY FK_A4140AE4B1AA81FD');
        $this->addSql('ALTER TABLE idiom_portal DROP FOREIGN KEY FK_A4140AE4B887E1DD');
        $this->addSql('ALTER TABLE idiom_article DROP FOREIGN KEY FK_184F608212469DE2');
        $this->addSql('ALTER TABLE idiom_article DROP FOREIGN KEY FK_184F6082B1AA81FD');
        $this->addSql('ALTER TABLE idiom_article_image DROP FOREIGN KEY FK_540D90FE76E3D60');
        $this->addSql('ALTER TABLE idiom_article_image DROP FOREIGN KEY FK_540D90FE3DA5256D');
        $this->addSql('DROP TABLE idiom');
        $this->addSql('DROP TABLE idiom_portal');
        $this->addSql('DROP TABLE idiom_article');
        $this->addSql('DROP TABLE idiom_article_image');
        $this->addSql('DROP TABLE idiom_category');
    }
}
