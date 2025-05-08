<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250429174926 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement DROP FOREIGN KEY FK_abonnement_user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement DROP mode_paiement
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement ADD CONSTRAINT FK_351268BBBD2F03 FOREIGN KEY (gym_id) REFERENCES gym (id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX gym_id ON abonnement
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_351268BBBD2F03 ON abonnement (gym_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_abonnement_user ON abonnement
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_351268BBA76ED395 ON abonnement (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement ADD CONSTRAINT FK_abonnement_user FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cours CHANGE date_fin date_fin DATETIME NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX responsable_id ON gym
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX responsable_id_2 ON gym
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gym DROP responsable_id, CHANGE nom nom VARCHAR(100) NOT NULL, CHANGE horaires horaires VARCHAR(255) NOT NULL, CHANGE contact contact VARCHAR(100) NOT NULL, CHANGE services services LONGTEXT NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24FFD02F13
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participation CHANGE utilisateur_id utilisateur_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participation ADD CONSTRAINT FK_AB55E24FFD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27BCF5E72D
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_29a5ec27bcf5e72d ON produit
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_29A5EC27BCF5E72D ON produit (categorie_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_salle ON reservation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP heure_reservation, DROP salle_id, DROP salle_nom
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation_creneaux CHANGE choix choix VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE salles CHANGE numero numero VARCHAR(255) DEFAULT NULL, CHANGE specialite specialite VARCHAR(255) DEFAULT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE nom nom VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX email ON user
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX telephone ON user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP reset_token, CHANGE role role VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(255) NOT NULL, CHANGE prenom prenom VARCHAR(255) NOT NULL, CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE telephone telephone VARCHAR(255) NOT NULL, CHANGE adresse adresse LONGTEXT DEFAULT NULL, CHANGE specialite specialite VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_event_vote ADD likes INT DEFAULT 0 NOT NULL, ADD dislikes INT DEFAULT 0 NOT NULL, CHANGE vote vote SMALLINT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_event_vote ADD CONSTRAINT FK_E2BC0F23A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_event_vote ADD CONSTRAINT FK_E2BC0F2371F7E88B FOREIGN KEY (event_id) REFERENCES evenement (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E2BC0F23A76ED395 ON user_event_vote (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX user_event_unique ON user_event_vote (user_id, event_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_user_event_vote_event ON user_event_vote
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E2BC0F2371F7E88B ON user_event_vote (event_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement DROP FOREIGN KEY FK_351268BBBD2F03
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement DROP FOREIGN KEY FK_351268BBBD2F03
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement DROP FOREIGN KEY FK_351268BBA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement ADD mode_paiement VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_351268bbbd2f03 ON abonnement
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX gym_id ON abonnement (gym_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_351268bba76ed395 ON abonnement
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX FK_abonnement_user ON abonnement (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement ADD CONSTRAINT FK_351268BBBD2F03 FOREIGN KEY (gym_id) REFERENCES gym (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement ADD CONSTRAINT FK_351268BBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cours CHANGE date_fin date_fin DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gym ADD responsable_id INT DEFAULT NULL, CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE services services VARCHAR(255) DEFAULT NULL, CHANGE horaires horaires VARCHAR(255) DEFAULT NULL, CHANGE contact contact VARCHAR(255) DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX responsable_id ON gym (responsable_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX responsable_id_2 ON gym (responsable_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24FFD02F13
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participation CHANGE utilisateur_id utilisateur_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participation ADD CONSTRAINT FK_AB55E24FFD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27BCF5E72D
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_29a5ec27bcf5e72d ON produit
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX FK_29A5EC27BCF5E72D ON produit (categorie_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD heure_reservation VARCHAR(255) DEFAULT NULL, ADD salle_id INT DEFAULT NULL, ADD salle_nom VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX fk_salle ON reservation (salle_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation_creneaux CHANGE choix choix VARCHAR(50) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE salles CHANGE numero numero VARCHAR(50) DEFAULT NULL, CHANGE specialite specialite VARCHAR(100) DEFAULT NULL, CHANGE description description TEXT DEFAULT NULL, CHANGE nom nom VARCHAR(100) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD reset_token VARCHAR(255) DEFAULT NULL, CHANGE role role VARCHAR(50) DEFAULT NULL, CHANGE email email VARCHAR(100) NOT NULL, CHANGE prenom prenom VARCHAR(50) NOT NULL, CHANGE nom nom VARCHAR(50) NOT NULL, CHANGE telephone telephone VARCHAR(20) NOT NULL, CHANGE adresse adresse TEXT DEFAULT NULL, CHANGE specialite specialite VARCHAR(100) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX email ON user (email)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX telephone ON user (telephone)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_event_vote DROP FOREIGN KEY FK_E2BC0F23A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_event_vote DROP FOREIGN KEY FK_E2BC0F2371F7E88B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_E2BC0F23A76ED395 ON user_event_vote
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX user_event_unique ON user_event_vote
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_event_vote DROP FOREIGN KEY FK_E2BC0F2371F7E88B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_event_vote DROP likes, DROP dislikes, CHANGE vote vote SMALLINT NOT NULL COMMENT '1 for like, -1 for dislike'
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_e2bc0f2371f7e88b ON user_event_vote
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX FK_USER_EVENT_VOTE_EVENT ON user_event_vote (event_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_event_vote ADD CONSTRAINT FK_E2BC0F2371F7E88B FOREIGN KEY (event_id) REFERENCES evenement (id)
        SQL);
    }
}
