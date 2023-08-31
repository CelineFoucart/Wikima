<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230825133824 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE forum (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, title VARCHAR(50) NOT NULL, slug VARCHAR(100) NOT NULL, description VARCHAR(300) DEFAULT NULL, position INT NOT NULL, INDEX IDX_852BBECD12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE forum_forum_group (forum_id INT NOT NULL, forum_group_id INT NOT NULL, INDEX IDX_738061B029CCBAD0 (forum_id), INDEX IDX_738061B0D9A754A0 (forum_group_id), PRIMARY KEY(forum_id, forum_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE forum_category (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(50) NOT NULL, slug VARCHAR(100) NOT NULL, description VARCHAR(300) DEFAULT NULL, position INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE forum_category_forum_group (forum_category_id INT NOT NULL, forum_group_id INT NOT NULL, INDEX IDX_5FBCEA7614721E40 (forum_category_id), INDEX IDX_5FBCEA76D9A754A0 (forum_group_id), PRIMARY KEY(forum_category_id, forum_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE forum_group (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(50) NOT NULL, role_name VARCHAR(100) NOT NULL, colour VARCHAR(15) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, symfony_role TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE forum_group_user (forum_group_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_26FF307CD9A754A0 (forum_group_id), INDEX IDX_26FF307CA76ED395 (user_id), PRIMARY KEY(forum_group_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, topic_id INT NOT NULL, title VARCHAR(150) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_5A8A6C8DF675F31B (author_id), INDEX IDX_5A8A6C8D1F55203D (topic_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, post_id INT NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_C42F7784F675F31B (author_id), INDEX IDX_C42F77844B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE topic (id INT AUTO_INCREMENT NOT NULL, forum_id INT NOT NULL, author_id INT DEFAULT NULL, title VARCHAR(100) NOT NULL, slug VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, sticky TINYINT(1) NOT NULL, INDEX IDX_9D40DE1B29CCBAD0 (forum_id), INDEX IDX_9D40DE1BF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE forum ADD CONSTRAINT FK_852BBECD12469DE2 FOREIGN KEY (category_id) REFERENCES forum_category (id)');
        $this->addSql('ALTER TABLE forum_forum_group ADD CONSTRAINT FK_738061B029CCBAD0 FOREIGN KEY (forum_id) REFERENCES forum (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE forum_forum_group ADD CONSTRAINT FK_738061B0D9A754A0 FOREIGN KEY (forum_group_id) REFERENCES forum_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE forum_category_forum_group ADD CONSTRAINT FK_5FBCEA7614721E40 FOREIGN KEY (forum_category_id) REFERENCES forum_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE forum_category_forum_group ADD CONSTRAINT FK_5FBCEA76D9A754A0 FOREIGN KEY (forum_group_id) REFERENCES forum_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE forum_group_user ADD CONSTRAINT FK_26FF307CD9A754A0 FOREIGN KEY (forum_group_id) REFERENCES forum_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE forum_group_user ADD CONSTRAINT FK_26FF307CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D1F55203D FOREIGN KEY (topic_id) REFERENCES topic (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F77844B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1B29CCBAD0 FOREIGN KEY (forum_id) REFERENCES forum (id)');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1BF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        // Ajout des groupes par défaut
        $this->addSql('INSERT INTO forum_group (title, role_name, colour, description, symfony_role) VALUES ("Administrateurs", "ROLE_ADMIN", "#FF9900", "Administrateurs du site", 1)');
        $this->addSql('INSERT INTO forum_group (title, role_name, colour, description, symfony_role) VALUES ("Modérateurs", "ROLE_MODERARTOR", "#DF2020", "Modérateurs du forum", 1)');
        $this->addSql('INSERT INTO forum_group (title, role_name, description, symfony_role) VALUES ("Editeurs", "ROLE_EDITOR", "Editeurs", 1)');
        $this->addSql('INSERT INTO forum_group (title, role_name, description, symfony_role) VALUES ("Utilisateurs", "ROLE_USER", "Utilisateurs", 1)');
        $this->addSql('INSERT INTO forum_group (title, role_name, description, symfony_role) VALUES ("Invités", "PUBLIC_ACCESS", "Utilisateurs non authentifiés", 1)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE forum DROP FOREIGN KEY FK_852BBECD12469DE2');
        $this->addSql('ALTER TABLE forum_forum_group DROP FOREIGN KEY FK_738061B029CCBAD0');
        $this->addSql('ALTER TABLE forum_forum_group DROP FOREIGN KEY FK_738061B0D9A754A0');
        $this->addSql('ALTER TABLE forum_category_forum_group DROP FOREIGN KEY FK_5FBCEA7614721E40');
        $this->addSql('ALTER TABLE forum_category_forum_group DROP FOREIGN KEY FK_5FBCEA76D9A754A0');
        $this->addSql('ALTER TABLE forum_group_user DROP FOREIGN KEY FK_26FF307CD9A754A0');
        $this->addSql('ALTER TABLE forum_group_user DROP FOREIGN KEY FK_26FF307CA76ED395');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DF675F31B');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D1F55203D');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784F675F31B');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F77844B89032C');
        $this->addSql('ALTER TABLE topic DROP FOREIGN KEY FK_9D40DE1B29CCBAD0');
        $this->addSql('ALTER TABLE topic DROP FOREIGN KEY FK_9D40DE1BF675F31B');
        $this->addSql('DROP TABLE forum');
        $this->addSql('DROP TABLE forum_forum_group');
        $this->addSql('DROP TABLE forum_category');
        $this->addSql('DROP TABLE forum_category_forum_group');
        $this->addSql('DROP TABLE forum_group');
        $this->addSql('DROP TABLE forum_group_user');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE topic');
    }
}
