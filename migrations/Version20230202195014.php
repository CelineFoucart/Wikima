<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230202195014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE note (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, portal_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, message LONGTEXT DEFAULT NULL, is_processed TINYINT(1) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_CFBDFA1412469DE2 (category_id), INDEX IDX_CFBDFA14B887E1DD (portal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person_person_type (person_id INT NOT NULL, person_type_id INT NOT NULL, INDEX IDX_6BD38C8A217BBB47 (person_id), INDEX IDX_6BD38C8AE7D23F1A (person_type_id), PRIMARY KEY(person_id, person_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person_type (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA1412469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14B887E1DD FOREIGN KEY (portal_id) REFERENCES portal (id)');
        $this->addSql('ALTER TABLE person_person_type ADD CONSTRAINT FK_6BD38C8A217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE person_person_type ADD CONSTRAINT FK_6BD38C8AE7D23F1A FOREIGN KEY (person_type_id) REFERENCES person_type (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA1412469DE2');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14B887E1DD');
        $this->addSql('ALTER TABLE person_person_type DROP FOREIGN KEY FK_6BD38C8A217BBB47');
        $this->addSql('ALTER TABLE person_person_type DROP FOREIGN KEY FK_6BD38C8AE7D23F1A');
        $this->addSql('DROP TABLE note');
        $this->addSql('DROP TABLE person_person_type');
        $this->addSql('DROP TABLE person_type');
    }
}
