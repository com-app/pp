<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230514211725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE account_mouvement (id INT AUTO_INCREMENT NOT NULL, acct_id INT NOT NULL, type VARCHAR(255) NOT NULL, amount DOUBLE PRECISION NOT NULL, date DATETIME NOT NULL, comment LONGTEXT NOT NULL, INDEX IDX_792B24659D02C3AF (acct_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cash_transfert (id INT AUTO_INCREMENT NOT NULL, source_id INT NOT NULL, target_id INT NOT NULL, comment LONGTEXT NOT NULL, performed_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_FBF99147953C1C61 (source_id), INDEX IDX_FBF99147158E0B66 (target_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE settings (id INT AUTO_INCREMENT NOT NULL, defaults VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE symfony_demo_comment (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, author_id INT NOT NULL, content LONGTEXT NOT NULL, published_at DATETIME NOT NULL, INDEX IDX_53AD8F834B89032C (post_id), INDEX IDX_53AD8F83F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE symfony_demo_post (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, summary VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, published_at DATETIME NOT NULL, INDEX IDX_58A92E65F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE symfony_demo_post_tag (post_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_6ABC1CC44B89032C (post_id), INDEX IDX_6ABC1CC4BAD26311 (tag_id), PRIMARY KEY(post_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE symfony_demo_tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_4D5855405E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE account_mouvement ADD CONSTRAINT FK_792B24659D02C3AF FOREIGN KEY (acct_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE cash_transfert ADD CONSTRAINT FK_FBF99147953C1C61 FOREIGN KEY (source_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE cash_transfert ADD CONSTRAINT FK_FBF99147158E0B66 FOREIGN KEY (target_id) REFERENCES user (id)');
        
        $this->addSql('DROP TABLE cashier');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A47E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE sms ADD content VARCHAR(255) NOT NULL, DROP body');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(255) NOT NULL, CHANGE username username VARCHAR(255) NOT NULL, CHANGE full_name full_name VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cashier (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_45FD6F57E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, headers LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, queue_name VARCHAR(190) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), INDEX IDX_75EA56E0FB7336F0 (queue_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE account_mouvement DROP FOREIGN KEY FK_792B24659D02C3AF');
        $this->addSql('ALTER TABLE cash_transfert DROP FOREIGN KEY FK_FBF99147953C1C61');
        $this->addSql('ALTER TABLE cash_transfert DROP FOREIGN KEY FK_FBF99147158E0B66');
        $this->addSql('ALTER TABLE symfony_demo_comment DROP FOREIGN KEY FK_53AD8F834B89032C');
        $this->addSql('ALTER TABLE symfony_demo_comment DROP FOREIGN KEY FK_53AD8F83F675F31B');
        $this->addSql('ALTER TABLE symfony_demo_post DROP FOREIGN KEY FK_58A92E65F675F31B');
        $this->addSql('ALTER TABLE symfony_demo_post_tag DROP FOREIGN KEY FK_6ABC1CC44B89032C');
        $this->addSql('ALTER TABLE symfony_demo_post_tag DROP FOREIGN KEY FK_6ABC1CC4BAD26311');
        $this->addSql('DROP TABLE account_mouvement');
        $this->addSql('DROP TABLE cash_transfert');
        $this->addSql('DROP TABLE settings');
        $this->addSql('DROP TABLE symfony_demo_comment');
        $this->addSql('DROP TABLE symfony_demo_post');
        $this->addSql('DROP TABLE symfony_demo_post_tag');
        $this->addSql('DROP TABLE symfony_demo_tag');
        $this->addSql('ALTER TABLE account DROP FOREIGN KEY FK_7D3656A47E3C61F9');
        $this->addSql('ALTER TABLE sms ADD body LONGTEXT NOT NULL, DROP content');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677 ON user');
        $this->addSql('ALTER TABLE user CHANGE full_name full_name VARCHAR(255) DEFAULT NULL, CHANGE username username VARCHAR(255) DEFAULT NULL, CHANGE email email VARCHAR(180) NOT NULL');
    }
}
