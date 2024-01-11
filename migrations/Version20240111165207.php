<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240111165207 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE private_message_received (id INT AUTO_INCREMENT NOT NULL, addressee_id INT NOT NULL, author_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', read_status TINYINT(1) NOT NULL, INDEX IDX_A5B1CAFF2261B4C3 (addressee_id), INDEX IDX_A5B1CAFFF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE private_message_sent (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, addressee_id INT DEFAULT NULL, private_message_received_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_452C1972F675F31B (author_id), INDEX IDX_452C19722261B4C3 (addressee_id), UNIQUE INDEX UNIQ_452C197255AA4DA6 (private_message_received_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE private_message_received ADD CONSTRAINT FK_A5B1CAFF2261B4C3 FOREIGN KEY (addressee_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE private_message_received ADD CONSTRAINT FK_A5B1CAFFF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE private_message_sent ADD CONSTRAINT FK_452C1972F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE private_message_sent ADD CONSTRAINT FK_452C19722261B4C3 FOREIGN KEY (addressee_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE private_message_sent ADD CONSTRAINT FK_452C197255AA4DA6 FOREIGN KEY (private_message_received_id) REFERENCES private_message_received (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE private_message_received DROP FOREIGN KEY FK_A5B1CAFF2261B4C3');
        $this->addSql('ALTER TABLE private_message_received DROP FOREIGN KEY FK_A5B1CAFFF675F31B');
        $this->addSql('ALTER TABLE private_message_sent DROP FOREIGN KEY FK_452C1972F675F31B');
        $this->addSql('ALTER TABLE private_message_sent DROP FOREIGN KEY FK_452C19722261B4C3');
        $this->addSql('ALTER TABLE private_message_sent DROP FOREIGN KEY FK_452C197255AA4DA6');
        $this->addSql('DROP TABLE private_message_received');
        $this->addSql('DROP TABLE private_message_sent');
    }
}
