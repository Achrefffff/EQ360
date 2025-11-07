<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251107144221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE objectifs (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, sppa_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, domaine_vie VARCHAR(255) NOT NULL, horizon VARCHAR(255) NOT NULL, priorite VARCHAR(255) NOT NULL, statut VARCHAR(255) NOT NULL, date_debut DATE NOT NULL, date_fin DATE DEFAULT NULL, progression DOUBLE PRECISION NOT NULL, image VARCHAR(255) DEFAULT NULL, doc VARCHAR(255) DEFAULT NULL, INDEX IDX_7805601A76ED395 (user_id), INDEX IDX_7805601A8867482 (sppa_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projets (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, sppa_id INT DEFAULT NULL, objectif_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, type_projet VARCHAR(255) NOT NULL, categorie VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, date_debut DATE NOT NULL, date_fin DATE DEFAULT NULL, budget DOUBLE PRECISION NOT NULL, statut VARCHAR(255) NOT NULL, piece_jointes VARCHAR(255) DEFAULT NULL, INDEX IDX_B454C1DBA76ED395 (user_id), INDEX IDX_B454C1DBA8867482 (sppa_id), INDEX IDX_B454C1DB157D1AD4 (objectif_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sppas (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, avatar VARCHAR(255) DEFAULT NULL, couleur VARCHAR(255) DEFAULT NULL, competences JSON NOT NULL, valeurs JSON NOT NULL, niveau DOUBLE PRECISION NOT NULL, heures_accumulees DOUBLE PRECISION NOT NULL, experience_xp DOUBLE PRECISION NOT NULL, INDEX IDX_F274DFAEA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE taches (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, objectif_id INT DEFAULT NULL, projet_id INT DEFAULT NULL, sppa_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, livrable LONGTEXT DEFAULT NULL, type VARCHAR(255) NOT NULL, domaine VARCHAR(255) NOT NULL, valeur_ajoutee VARCHAR(255) NOT NULL, date_echeance DATE DEFAULT NULL, priorite VARCHAR(255) NOT NULL, duree_estimee DOUBLE PRECISION NOT NULL, difficulte INT NOT NULL, enthousiasme INT NOT NULL, statut VARCHAR(255) NOT NULL, piece_joint VARCHAR(255) DEFAULT NULL, INDEX IDX_3BF2CD98A76ED395 (user_id), INDEX IDX_3BF2CD98157D1AD4 (objectif_id), INDEX IDX_3BF2CD98C18272 (projet_id), INDEX IDX_3BF2CD98A8867482 (sppa_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, date_naissance DATE NOT NULL, email VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE objectifs ADD CONSTRAINT FK_7805601A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE objectifs ADD CONSTRAINT FK_7805601A8867482 FOREIGN KEY (sppa_id) REFERENCES sppas (id)');
        $this->addSql('ALTER TABLE projets ADD CONSTRAINT FK_B454C1DBA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE projets ADD CONSTRAINT FK_B454C1DBA8867482 FOREIGN KEY (sppa_id) REFERENCES sppas (id)');
        $this->addSql('ALTER TABLE projets ADD CONSTRAINT FK_B454C1DB157D1AD4 FOREIGN KEY (objectif_id) REFERENCES objectifs (id)');
        $this->addSql('ALTER TABLE sppas ADD CONSTRAINT FK_F274DFAEA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE taches ADD CONSTRAINT FK_3BF2CD98A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE taches ADD CONSTRAINT FK_3BF2CD98157D1AD4 FOREIGN KEY (objectif_id) REFERENCES objectifs (id)');
        $this->addSql('ALTER TABLE taches ADD CONSTRAINT FK_3BF2CD98C18272 FOREIGN KEY (projet_id) REFERENCES projets (id)');
        $this->addSql('ALTER TABLE taches ADD CONSTRAINT FK_3BF2CD98A8867482 FOREIGN KEY (sppa_id) REFERENCES sppas (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE objectifs DROP FOREIGN KEY FK_7805601A76ED395');
        $this->addSql('ALTER TABLE objectifs DROP FOREIGN KEY FK_7805601A8867482');
        $this->addSql('ALTER TABLE projets DROP FOREIGN KEY FK_B454C1DBA76ED395');
        $this->addSql('ALTER TABLE projets DROP FOREIGN KEY FK_B454C1DBA8867482');
        $this->addSql('ALTER TABLE projets DROP FOREIGN KEY FK_B454C1DB157D1AD4');
        $this->addSql('ALTER TABLE sppas DROP FOREIGN KEY FK_F274DFAEA76ED395');
        $this->addSql('ALTER TABLE taches DROP FOREIGN KEY FK_3BF2CD98A76ED395');
        $this->addSql('ALTER TABLE taches DROP FOREIGN KEY FK_3BF2CD98157D1AD4');
        $this->addSql('ALTER TABLE taches DROP FOREIGN KEY FK_3BF2CD98C18272');
        $this->addSql('ALTER TABLE taches DROP FOREIGN KEY FK_3BF2CD98A8867482');
        $this->addSql('DROP TABLE objectifs');
        $this->addSql('DROP TABLE projets');
        $this->addSql('DROP TABLE sppas');
        $this->addSql('DROP TABLE taches');
        $this->addSql('DROP TABLE users');
    }
}
