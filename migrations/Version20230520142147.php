<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230520142147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE acc (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cash_transfert (id INT AUTO_INCREMENT NOT NULL, source_id INT NOT NULL, target_id INT NOT NULL, comment LONGTEXT NOT NULL, performed_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_FBF99147953C1C61 (source_id), INDEX IDX_FBF99147158E0B66 (target_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE settings (id INT AUTO_INCREMENT NOT NULL, defaults VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cash_transfert ADD CONSTRAINT FK_FBF99147953C1C61 FOREIGN KEY (source_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE cash_transfert ADD CONSTRAINT FK_FBF99147158E0B66 FOREIGN KEY (target_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE cashier');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE account_movement DROP FOREIGN KEY FK_792B24659D02C3AF');
        $this->addSql('ALTER TABLE account_movement ADD balance DOUBLE PRECISION NOT NULL');
        $this->addSql('DROP INDEX idx_792b24659d02c3af ON account_movement');
        $this->addSql('CREATE INDEX IDX_8C1377419D02C3AF ON account_movement (acct_id)');
        $this->addSql('ALTER TABLE account_movement ADD CONSTRAINT FK_792B24659D02C3AF FOREIGN KEY (acct_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE pay DROP FOREIGN KEY pay_ibfk_1');
        $this->addSql('ALTER TABLE sms ADD content VARCHAR(255) NOT NULL, DROP body');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(255) NOT NULL, CHANGE username username VARCHAR(255) NOT NULL, CHANGE full_name full_name VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cashier (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_45FD6F57E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, headers LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, queue_name VARCHAR(190) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), INDEX IDX_75EA56E0FB7336F0 (queue_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE cash_transfert DROP FOREIGN KEY FK_FBF99147953C1C61');
        $this->addSql('ALTER TABLE cash_transfert DROP FOREIGN KEY FK_FBF99147158E0B66');
        $this->addSql('DROP TABLE acc');
        $this->addSql('DROP TABLE cash_transfert');
        $this->addSql('DROP TABLE settings');
        $this->addSql('ALTER TABLE account_movement DROP FOREIGN KEY FK_8C1377419D02C3AF');
        $this->addSql('ALTER TABLE account_movement DROP balance');
        $this->addSql('DROP INDEX idx_8c1377419d02c3af ON account_movement');
        $this->addSql('CREATE INDEX IDX_792B24659D02C3AF ON account_movement (acct_id)');
        $this->addSql('ALTER TABLE account_movement ADD CONSTRAINT FK_8C1377419D02C3AF FOREIGN KEY (acct_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE pay ADD CONSTRAINT pay_ibfk_1 FOREIGN KEY (transaction_id) REFERENCES transaction (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sms ADD body LONGTEXT NOT NULL, DROP content');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677 ON user');
        $this->addSql('ALTER TABLE user CHANGE full_name full_name VARCHAR(255) DEFAULT NULL, CHANGE username username VARCHAR(255) DEFAULT NULL, CHANGE email email VARCHAR(180) NOT NULL');
    }
}
