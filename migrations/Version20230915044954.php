<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230915044954 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE election (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE election_code (id INT AUTO_INCREMENT NOT NULL, election_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, used TINYINT(1) DEFAULT NULL, INDEX IDX_EB5A47BEA708DAFF (election_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE election_result (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE election_result_election (election_result_id INT NOT NULL, election_id INT NOT NULL, INDEX IDX_781D91FE19FCFB29 (election_result_id), INDEX IDX_781D91FEA708DAFF (election_id), PRIMARY KEY(election_result_id, election_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE election_result_person (election_result_id INT NOT NULL, person_id INT NOT NULL, INDEX IDX_5FDA5A2219FCFB29 (election_result_id), INDEX IDX_5FDA5A22217BBB47 (person_id), PRIMARY KEY(election_result_id, person_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, election_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_34DCD176A708DAFF (election_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE election_code ADD CONSTRAINT FK_EB5A47BEA708DAFF FOREIGN KEY (election_id) REFERENCES election (id)');
        $this->addSql('ALTER TABLE election_result_election ADD CONSTRAINT FK_781D91FE19FCFB29 FOREIGN KEY (election_result_id) REFERENCES election_result (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE election_result_election ADD CONSTRAINT FK_781D91FEA708DAFF FOREIGN KEY (election_id) REFERENCES election (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE election_result_person ADD CONSTRAINT FK_5FDA5A2219FCFB29 FOREIGN KEY (election_result_id) REFERENCES election_result (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE election_result_person ADD CONSTRAINT FK_5FDA5A22217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176A708DAFF FOREIGN KEY (election_id) REFERENCES election (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE election_code DROP FOREIGN KEY FK_EB5A47BEA708DAFF');
        $this->addSql('ALTER TABLE election_result_election DROP FOREIGN KEY FK_781D91FE19FCFB29');
        $this->addSql('ALTER TABLE election_result_election DROP FOREIGN KEY FK_781D91FEA708DAFF');
        $this->addSql('ALTER TABLE election_result_person DROP FOREIGN KEY FK_5FDA5A2219FCFB29');
        $this->addSql('ALTER TABLE election_result_person DROP FOREIGN KEY FK_5FDA5A22217BBB47');
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176A708DAFF');
        $this->addSql('DROP TABLE election');
        $this->addSql('DROP TABLE election_code');
        $this->addSql('DROP TABLE election_result');
        $this->addSql('DROP TABLE election_result_election');
        $this->addSql('DROP TABLE election_result_person');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
