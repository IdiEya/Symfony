<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250421202942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE produit_categorie DROP FOREIGN KEY produit_categorie_ibfk_2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit_categorie DROP FOREIGN KEY produit_categorie_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE salle DROP FOREIGN KEY salle_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE migration_versions
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE produit_categorie
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE salle
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement DROP FOREIGN KEY abonnement_ibfk_2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement DROP FOREIGN KEY abonnement_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX sportif_id ON abonnement
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement DROP FOREIGN KEY abonnement_ibfk_2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement ADD date_initiale DATE NOT NULL, ADD date_expiration DATE NOT NULL, ADD mode_paiement VARCHAR(255) NOT NULL, DROP sportif_id, DROP dateInitiale, DROP dateExpiration, DROP type, DROP modePaiement, CHANGE gym_id gym_id INT DEFAULT NULL
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
            ALTER TABLE abonnement ADD CONSTRAINT abonnement_ibfk_2 FOREIGN KEY (gym_id) REFERENCES gym (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE categorie DROP produits, CHANGE nom nom VARCHAR(100) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE coach CHANGE id id INT AUTO_INCREMENT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande DROP FOREIGN KEY commande_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande DROP FOREIGN KEY commande_ibfk_2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande CHANGE date date DATETIME DEFAULT NULL, CHANGE telephone telephone VARCHAR(255) NOT NULL, CHANGE mail mail VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX utilisateur_id ON commande
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6EEAA67DFB88E14F ON commande (utilisateur_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX produit_id ON commande
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6EEAA67DF347EFB ON commande (produit_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande ADD CONSTRAINT commande_ibfk_1 FOREIGN KEY (utilisateur_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande ADD CONSTRAINT commande_ibfk_2 FOREIGN KEY (produit_id) REFERENCES produit (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cours ADD date_debut DATE NOT NULL, ADD date_fin DATETIME NOT NULL, ADD places_disponibles INT DEFAULT NULL, DROP dateDebut, DROP dateFin, DROP coach_id, DROP responsableSalle_id, DROP salleId, DROP placesDisponibles, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE prix prix NUMERIC(10, 2) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9CDC304035 FOREIGN KEY (salle_id) REFERENCES salles (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_FDCA8C9CDC304035 ON cours (salle_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE evenement CHANGE description description LONGTEXT NOT NULL, CHANGE frais frais DOUBLE PRECISION NOT NULL, CHANGE photo photo VARCHAR(255) DEFAULT NULL, CHANGE statut statut VARCHAR(50) DEFAULT 'A_VENIR' NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gym DROP FOREIGN KEY gym_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gym DROP FOREIGN KEY gym_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gym CHANGE responsable_id responsable_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gym ADD CONSTRAINT FK_7F27DBED53C59D72 FOREIGN KEY (responsable_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX responsable_id ON gym
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_7F27DBED53C59D72 ON gym (responsable_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gym ADD CONSTRAINT gym_ibfk_1 FOREIGN KEY (responsable_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification CHANGE id id INT AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participation DROP FOREIGN KEY participation_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participation CHANGE statutP statutP VARCHAR(50) NOT NULL, CHANGE nombreDePlacesReservees nombreDePlacesReservees INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participation ADD CONSTRAINT FK_AB55E24FFD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_AB55E24FFD02F13 ON participation (evenement_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX utilisateur_id ON participation
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_AB55E24FFB88E14F ON participation (utilisateur_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participation ADD CONSTRAINT participation_ibfk_1 FOREIGN KEY (utilisateur_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit ADD categorie_id INT NOT NULL, DROP categorie, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE photo photo VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_29A5EC27BCF5E72D ON produit (categorie_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_salle ON reservation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD adresse VARCHAR(255) DEFAULT NULL, DROP heure_reservation, DROP salle_id, DROP salle_nom
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation_creneaux CHANGE choix choix VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE salles ADD date_debut DATETIME DEFAULT NULL, CHANGE numero numero VARCHAR(255) DEFAULT NULL, CHANGE specialite specialite VARCHAR(255) DEFAULT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE nom nom VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX email ON user
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX telephone ON user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE role role VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(255) NOT NULL, CHANGE prenom prenom VARCHAR(255) NOT NULL, CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE telephone telephone VARCHAR(255) NOT NULL, CHANGE adresse adresse LONGTEXT DEFAULT NULL, CHANGE specialite specialite VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE achat_produit DROP FOREIGN KEY achat_produit_ibfk_2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE achat_produit DROP FOREIGN KEY achat_produit_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE achat_produit DROP FOREIGN KEY achat_produit_ibfk_2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE achat_produit ADD CONSTRAINT FK_C26FA378FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE achat_produit ADD CONSTRAINT FK_C26FA378F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX produit_id ON achat_produit
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C26FA378F347EFB ON achat_produit (produit_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE achat_produit ADD CONSTRAINT achat_produit_ibfk_2 FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_event_vote DROP FOREIGN KEY FK_USER_EVENT_VOTE_USER
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_event_vote DROP FOREIGN KEY FK_USER_EVENT_VOTE_EVENT
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_event_vote DROP FOREIGN KEY FK_USER_EVENT_VOTE_EVENT
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_event_vote CHANGE vote vote SMALLINT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_event_vote ADD CONSTRAINT FK_E2BC0F23A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_event_vote ADD CONSTRAINT FK_E2BC0F2371F7E88B FOREIGN KEY (event_id) REFERENCES evenement (id)
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
        $this->addSql(<<<'SQL'
            ALTER TABLE user_event_vote ADD CONSTRAINT FK_USER_EVENT_VOTE_EVENT FOREIGN KEY (event_id) REFERENCES evenement (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE votes CHANGE id id INT AUTO_INCREMENT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messenger_messages CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE migration_versions (version VARCHAR(191) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, executed_at DATETIME DEFAULT NULL, execution_time INT DEFAULT NULL, PRIMARY KEY(version)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE produit_categorie (produit_id INT NOT NULL, categorie_id INT NOT NULL, INDEX categorie_id (categorie_id), INDEX IDX_CDEA88D8F347EFB (produit_id), PRIMARY KEY(produit_id, categorie_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE salle (id INT AUTO_INCREMENT NOT NULL, gym_id INT NOT NULL, numero INT DEFAULT NULL, specialite VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, capacite INT NOT NULL, description TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, INDEX gym_id (gym_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit_categorie ADD CONSTRAINT produit_categorie_ibfk_2 FOREIGN KEY (categorie_id) REFERENCES categorie (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit_categorie ADD CONSTRAINT produit_categorie_ibfk_1 FOREIGN KEY (produit_id) REFERENCES produit (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE salle ADD CONSTRAINT salle_ibfk_1 FOREIGN KEY (gym_id) REFERENCES gym (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement DROP FOREIGN KEY FK_351268BBBD2F03
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement DROP FOREIGN KEY FK_351268BBBD2F03
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement ADD sportif_id INT NOT NULL, ADD dateInitiale DATE NOT NULL, ADD dateExpiration DATE NOT NULL, ADD modePaiement VARCHAR(255) NOT NULL, DROP date_initiale, DROP date_expiration, CHANGE gym_id gym_id INT NOT NULL, CHANGE mode_paiement type VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement ADD CONSTRAINT abonnement_ibfk_2 FOREIGN KEY (gym_id) REFERENCES gym (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement ADD CONSTRAINT abonnement_ibfk_1 FOREIGN KEY (sportif_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX sportif_id ON abonnement (sportif_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_351268bbbd2f03 ON abonnement
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX gym_id ON abonnement (gym_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement ADD CONSTRAINT FK_351268BBBD2F03 FOREIGN KEY (gym_id) REFERENCES gym (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE achat_produit DROP FOREIGN KEY FK_C26FA378FB88E14F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE achat_produit DROP FOREIGN KEY FK_C26FA378F347EFB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE achat_produit DROP FOREIGN KEY FK_C26FA378F347EFB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE achat_produit ADD CONSTRAINT achat_produit_ibfk_2 FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE achat_produit ADD CONSTRAINT achat_produit_ibfk_1 FOREIGN KEY (utilisateur_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_c26fa378f347efb ON achat_produit
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX produit_id ON achat_produit (produit_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE achat_produit ADD CONSTRAINT FK_C26FA378F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE categorie ADD produits TEXT DEFAULT NULL, CHANGE nom nom VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE coach CHANGE id id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DFB88E14F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DF347EFB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande CHANGE date date DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE telephone telephone VARCHAR(20) NOT NULL, CHANGE mail mail VARCHAR(100) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_6eeaa67df347efb ON commande
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX produit_id ON commande (produit_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_6eeaa67dfb88e14f ON commande
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX utilisateur_id ON commande (utilisateur_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9CDC304035
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_FDCA8C9CDC304035 ON cours
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cours ADD dateFin DATE NOT NULL, ADD responsableSalle_id INT DEFAULT NULL, ADD salleId INT DEFAULT NULL, ADD placesDisponibles INT DEFAULT 20, DROP date_fin, CHANGE description description TEXT DEFAULT NULL, CHANGE prix prix NUMERIC(10, 2) DEFAULT '0.00' NOT NULL, CHANGE date_debut dateDebut DATE NOT NULL, CHANGE places_disponibles coach_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE evenement CHANGE description description TEXT NOT NULL, CHANGE frais frais NUMERIC(10, 2) NOT NULL, CHANGE photo photo VARCHAR(255) NOT NULL, CHANGE statut statut VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gym DROP FOREIGN KEY FK_7F27DBED53C59D72
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gym DROP FOREIGN KEY FK_7F27DBED53C59D72
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gym CHANGE responsable_id responsable_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gym ADD CONSTRAINT gym_ibfk_1 FOREIGN KEY (responsable_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX uniq_7f27dbed53c59d72 ON gym
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX responsable_id ON gym (responsable_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gym ADD CONSTRAINT FK_7F27DBED53C59D72 FOREIGN KEY (responsable_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messenger_messages MODIFY id BIGINT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `primary` ON messenger_messages
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_75EA56E0FB7336F0 ON messenger_messages
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_75EA56E0E3BD61CE ON messenger_messages
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_75EA56E016BA31DB ON messenger_messages
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messenger_messages CHANGE id id BIGINT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification MODIFY id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `primary` ON notification
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification CHANGE id id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24FFD02F13
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_AB55E24FFD02F13 ON participation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24FFB88E14F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participation CHANGE statutP statutP VARCHAR(255) NOT NULL, CHANGE nombreDePlacesReservees nombreDePlacesReservees INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_ab55e24ffb88e14f ON participation
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX utilisateur_id ON participation (utilisateur_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participation ADD CONSTRAINT FK_AB55E24FFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27BCF5E72D
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_29A5EC27BCF5E72D ON produit
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit ADD categorie VARCHAR(255) NOT NULL, DROP categorie_id, CHANGE description description VARCHAR(500) DEFAULT NULL, CHANGE photo photo VARCHAR(500) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD salle_id INT DEFAULT NULL, ADD salle_nom VARCHAR(255) DEFAULT NULL, CHANGE adresse heure_reservation VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX fk_salle ON reservation (salle_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation_creneaux CHANGE choix choix VARCHAR(50) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE salles DROP date_debut, CHANGE numero numero VARCHAR(50) DEFAULT NULL, CHANGE specialite specialite VARCHAR(100) DEFAULT NULL, CHANGE description description TEXT DEFAULT NULL, CHANGE nom nom VARCHAR(100) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE role role VARCHAR(50) NOT NULL, CHANGE email email VARCHAR(100) NOT NULL, CHANGE prenom prenom VARCHAR(50) NOT NULL, CHANGE nom nom VARCHAR(50) NOT NULL, CHANGE telephone telephone VARCHAR(20) NOT NULL, CHANGE adresse adresse TEXT DEFAULT NULL, CHANGE specialite specialite VARCHAR(100) DEFAULT NULL
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
            DROP INDEX user_event_unique ON user_event_vote
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_event_vote DROP FOREIGN KEY FK_E2BC0F2371F7E88B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_event_vote CHANGE vote vote SMALLINT NOT NULL COMMENT '1 for like, -1 for dislike'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_event_vote ADD CONSTRAINT FK_USER_EVENT_VOTE_USER FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_event_vote ADD CONSTRAINT FK_USER_EVENT_VOTE_EVENT FOREIGN KEY (event_id) REFERENCES evenement (id) ON DELETE CASCADE
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
        $this->addSql(<<<'SQL'
            ALTER TABLE votes CHANGE id id INT NOT NULL
        SQL);
    }
}
