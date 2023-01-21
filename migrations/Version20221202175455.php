<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221202175455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, timeline_id INT NOT NULL, title VARCHAR(255) NOT NULL, duration VARCHAR(255) NOT NULL, presentation VARCHAR(2500) DEFAULT NULL, timeline_order SMALLINT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL, INDEX IDX_3BAE0AA7EDBEDD37 (timeline_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE timeline (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE timeline_category (timeline_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_567C42CAEDBEDD37 (timeline_id), INDEX IDX_567C42CA12469DE2 (category_id), PRIMARY KEY(timeline_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE timeline_portal (timeline_id INT NOT NULL, portal_id INT NOT NULL, INDEX IDX_943119D8EDBEDD37 (timeline_id), INDEX IDX_943119D8B887E1DD (portal_id), PRIMARY KEY(timeline_id, portal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7EDBEDD37 FOREIGN KEY (timeline_id) REFERENCES timeline (id)');
        $this->addSql('ALTER TABLE timeline_category ADD CONSTRAINT FK_567C42CAEDBEDD37 FOREIGN KEY (timeline_id) REFERENCES timeline (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE timeline_category ADD CONSTRAINT FK_567C42CA12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE timeline_portal ADD CONSTRAINT FK_943119D8EDBEDD37 FOREIGN KEY (timeline_id) REFERENCES timeline (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE timeline_portal ADD CONSTRAINT FK_943119D8B887E1DD FOREIGN KEY (portal_id) REFERENCES portal (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7EDBEDD37');
        $this->addSql('ALTER TABLE timeline_category DROP FOREIGN KEY FK_567C42CAEDBEDD37');
        $this->addSql('ALTER TABLE timeline_portal DROP FOREIGN KEY FK_943119D8EDBEDD37');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE timeline');
        $this->addSql('DROP TABLE timeline_category');
        $this->addSql('DROP TABLE timeline_portal');
    }
}
