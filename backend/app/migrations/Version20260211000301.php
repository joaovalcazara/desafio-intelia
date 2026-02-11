<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260211000301 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cadastro (id INT AUTO_INCREMENT NOT NULL, step INT NOT NULL, nome_completo VARCHAR(255) NOT NULL, data_nascimento DATETIME NOT NULL, email VARCHAR(255) NOT NULL, rua VARCHAR(255) NOT NULL, numero VARCHAR(255) DEFAULT NULL, cep VARCHAR(20) DEFAULT NULL, cidade VARCHAR(255) DEFAULT NULL, estado VARCHAR(20) DEFAULT NULL, telefone_fixo VARCHAR(20) DEFAULT NULL, telefone_celular VARCHAR(20) DEFAULT NULL, uuid BINARY(16) NOT NULL, UNIQUE INDEX UNIQ_CBC68492D17F50A6 (uuid), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE cadastro');
    }
}
