<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230426101706 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pay (id INT AUTO_INCREMENT NOT NULL, transaction_id INT NOT NULL, user_id INT NOT NULL, rate NUMERIC(10, 3) NOT NULL, comment LONGTEXT DEFAULT NULL, client_phone VARCHAR(255) DEFAULT NULL, payed_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_FE8F223C2FC0CB0F (transaction_id), INDEX IDX_FE8F223CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
       
        $this->addSql('ALTER TABLE pay ADD CONSTRAINT FK_FE8F223C2FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id)');
        $this->addSql('ALTER TABLE pay ADD CONSTRAINT FK_FE8F223CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE sms ADD content VARCHAR(255) NOT NULL, DROP body');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(255) NOT NULL, CHANGE username username VARCHAR(255) NOT NULL, CHANGE full_name full_name VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, headers LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, queue_name VARCHAR(190) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E016BA31DB (delivered_at), INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE pay DROP FOREIGN KEY FK_FE8F223C2FC0CB0F');
        $this->addSql('ALTER TABLE pay DROP FOREIGN KEY FK_FE8F223CA76ED395');
        $this->addSql('DROP TABLE pay');
        $this->addSql('ALTER TABLE sms ADD body LONGTEXT NOT NULL, DROP content');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677 ON user');
        $this->addSql('ALTER TABLE user CHANGE full_name full_name VARCHAR(255) DEFAULT NULL, CHANGE username username VARCHAR(255) DEFAULT NULL, CHANGE email email VARCHAR(180) NOT NULL');
    }
}
