<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240611190446 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE election_option (election_id INT NOT NULL, option_id INT NOT NULL, INDEX IDX_5739FB44A708DAFF (election_id), INDEX IDX_5739FB44A7C41D6F (option_id), PRIMARY KEY(election_id, option_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE election_option ADD CONSTRAINT FK_5739FB44A708DAFF FOREIGN KEY (election_id) REFERENCES election (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE election_option ADD CONSTRAINT FK_5739FB44A7C41D6F FOREIGN KEY (option_id) REFERENCES `option` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE election_option DROP FOREIGN KEY FK_5739FB44A708DAFF');
        $this->addSql('ALTER TABLE election_option DROP FOREIGN KEY FK_5739FB44A7C41D6F');
        $this->addSql('DROP TABLE election_option');
    }
}
