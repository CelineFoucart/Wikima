<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240209161101 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE timeline ADD previous_id INT DEFAULT NULL, ADD next_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE timeline ADD CONSTRAINT FK_46FEC6662DE62210 FOREIGN KEY (previous_id) REFERENCES timeline (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE timeline ADD CONSTRAINT FK_46FEC666AA23F6C8 FOREIGN KEY (next_id) REFERENCES timeline (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_46FEC6662DE62210 ON timeline (previous_id)');
        $this->addSql('CREATE INDEX IDX_46FEC666AA23F6C8 ON timeline (next_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE timeline DROP FOREIGN KEY FK_46FEC6662DE62210');
        $this->addSql('ALTER TABLE timeline DROP FOREIGN KEY FK_46FEC666AA23F6C8');
        $this->addSql('DROP INDEX IDX_46FEC6662DE62210 ON timeline');
        $this->addSql('DROP INDEX IDX_46FEC666AA23F6C8 ON timeline');
        $this->addSql('ALTER TABLE timeline DROP previous_id, DROP next_id');
    }
}
