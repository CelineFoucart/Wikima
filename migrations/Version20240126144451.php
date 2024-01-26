<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240126144451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE person_person (person_source INT NOT NULL, person_target INT NOT NULL, INDEX IDX_A879E1C0C32F4FC5 (person_source), INDEX IDX_A879E1C0DACA1F4A (person_target), PRIMARY KEY(person_source, person_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE section_article (section_id INT NOT NULL, article_id INT NOT NULL, INDEX IDX_D07DC369D823E37A (section_id), INDEX IDX_D07DC3697294869C (article_id), PRIMARY KEY(section_id, article_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE person_person ADD CONSTRAINT FK_A879E1C0C32F4FC5 FOREIGN KEY (person_source) REFERENCES person (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE person_person ADD CONSTRAINT FK_A879E1C0DACA1F4A FOREIGN KEY (person_target) REFERENCES person (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE section_article ADD CONSTRAINT FK_D07DC369D823E37A FOREIGN KEY (section_id) REFERENCES section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE section_article ADD CONSTRAINT FK_D07DC3697294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE person_person DROP FOREIGN KEY FK_A879E1C0C32F4FC5');
        $this->addSql('ALTER TABLE person_person DROP FOREIGN KEY FK_A879E1C0DACA1F4A');
        $this->addSql('ALTER TABLE section_article DROP FOREIGN KEY FK_D07DC369D823E37A');
        $this->addSql('ALTER TABLE section_article DROP FOREIGN KEY FK_D07DC3697294869C');
        $this->addSql('DROP TABLE person_person');
        $this->addSql('DROP TABLE section_article');
    }
}
