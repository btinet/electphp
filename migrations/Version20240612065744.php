<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240612065744 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE option_election_result (option_id INT NOT NULL, election_result_id INT NOT NULL, INDEX IDX_868F801FA7C41D6F (option_id), INDEX IDX_868F801F19FCFB29 (election_result_id), PRIMARY KEY(option_id, election_result_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE option_election_result ADD CONSTRAINT FK_868F801FA7C41D6F FOREIGN KEY (option_id) REFERENCES `option` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE option_election_result ADD CONSTRAINT FK_868F801F19FCFB29 FOREIGN KEY (election_result_id) REFERENCES election_result (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE option_election_result DROP FOREIGN KEY FK_868F801FA7C41D6F');
        $this->addSql('ALTER TABLE option_election_result DROP FOREIGN KEY FK_868F801F19FCFB29');
        $this->addSql('DROP TABLE option_election_result');
    }
}
