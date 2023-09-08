<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230908122619 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_group (id INT AUTO_INCREMENT NOT NULL, member_id INT NOT NULL, forum_group_id INT NOT NULL, default_group TINYINT(1) NOT NULL, INDEX IDX_8F02BF9D7597D3FE (member_id), INDEX IDX_8F02BF9DD9A754A0 (forum_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9D7597D3FE FOREIGN KEY (member_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_group ADD CONSTRAINT FK_8F02BF9DD9A754A0 FOREIGN KEY (forum_group_id) REFERENCES forum_group (id)');
        $this->addSql('ALTER TABLE forum_group_user DROP FOREIGN KEY FK_26FF307CA76ED395');
        $this->addSql('ALTER TABLE forum_group_user DROP FOREIGN KEY FK_26FF307CD9A754A0');
        $this->addSql('DROP TABLE forum_group_user');
        $this->addSql('ALTER TABLE user ADD rank VARCHAR(255) DEFAULT NULL, ADD localisation VARCHAR(255) DEFAULT NULL, ADD avatar VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE forum_group_user (forum_group_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_26FF307CD9A754A0 (forum_group_id), INDEX IDX_26FF307CA76ED395 (user_id), PRIMARY KEY(forum_group_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE forum_group_user ADD CONSTRAINT FK_26FF307CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE forum_group_user ADD CONSTRAINT FK_26FF307CD9A754A0 FOREIGN KEY (forum_group_id) REFERENCES forum_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_group DROP FOREIGN KEY FK_8F02BF9D7597D3FE');
        $this->addSql('ALTER TABLE user_group DROP FOREIGN KEY FK_8F02BF9DD9A754A0');
        $this->addSql('DROP TABLE user_group');
        $this->addSql('ALTER TABLE user DROP rank, DROP localisation, DROP avatar');
    }
}
