<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250414132648 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
    
      
        $this->addSql(<<<'SQL'
            DROP TABLE salle
        SQL);
      
      
      
      
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement ADD date_initiale DATETIME NOT NULL, ADD date_expiration DATETIME NOT NULL, DROP sportif_id, DROP dateInitiale, DROP dateExpiration, CHANGE modePaiement mode_paiement VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement ADD CONSTRAINT FK_351268BBBD2F03 FOREIGN KEY (gym_id) REFERENCES gym (id)
        SQL);
  
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_351268BBBD2F03 ON abonnement (gym_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement ADD CONSTRAINT abonnement_ibfk_2 FOREIGN KEY (gym_id) REFERENCES gym (id) ON DELETE CASCADE
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
            DROP INDEX coach_id ON cours
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX responsableSalle_id ON cours
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cours ADD date_debut DATE NOT NULL, ADD date_fin DATE NOT NULL, ADD places_disponibles INT DEFAULT NULL, DROP dateDebut, DROP dateFin, DROP coach_id, DROP responsableSalle_id, DROP salleId, DROP placesDisponibles, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE prix prix NUMERIC(10, 0) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9CDC304035 FOREIGN KEY (salle_id) REFERENCES salles (id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX salle_id ON cours
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_FDCA8C9CDC304035 ON cours (salle_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE evenement ADD date_debut DATE NOT NULL, ADD date_fin DATE NOT NULL, DROP dateDebut, DROP dateFin, CHANGE description description LONGTEXT NOT NULL, CHANGE frais frais NUMERIC(10, 0) NOT NULL, CHANGE nombreDePlaces nombre_de_places INT DEFAULT NULL
        SQL);
   
        $this->addSql(<<<'SQL'
            DROP INDEX responsable_id ON gym
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX responsable_id_2 ON gym
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gym DROP responsable_id, CHANGE localisation localisation VARCHAR(255) NOT NULL, CHANGE services services LONGTEXT DEFAULT NULL
        SQL);
       
        $this->addSql(<<<'SQL'
            ALTER TABLE participation CHANGE utilisateur_id utilisateur_id INT DEFAULT NULL, CHANGE statutP statut_p VARCHAR(255) NOT NULL, CHANGE nombreDePlacesReservees nombre_de_places_reservees INT DEFAULT NULL
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
            ALTER TABLE produit CHANGE description description VARCHAR(255) DEFAULT NULL, CHANGE photo photo VARCHAR(255) DEFAULT NULL
        SQL);
      
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT FK_42C84955DC304035 FOREIGN KEY (salle_id) REFERENCES salles (id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_salle ON reservation
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_42C84955DC304035 ON reservation (salle_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT fk_salle FOREIGN KEY (salle_id) REFERENCES salle (id) ON UPDATE CASCADE ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation_creneaux CHANGE choix choix VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE salles CHANGE numero numero VARCHAR(255) DEFAULT NULL, CHANGE specialite specialite VARCHAR(255) DEFAULT NULL, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE nom nom VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX telephone ON user
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX email ON user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE role role VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(255) NOT NULL, CHANGE prenom prenom VARCHAR(255) NOT NULL, CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE telephone telephone VARCHAR(255) NOT NULL, CHANGE adresse adresse LONGTEXT DEFAULT NULL, CHANGE specialite specialite VARCHAR(255) DEFAULT NULL
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
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE salle (id INT AUTO_INCREMENT NOT NULL, gym_id INT DEFAULT NULL, numero VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, specialite VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, capacite INT NOT NULL, description TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, nom VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, INDEX gym_id (gym_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE salle ADD CONSTRAINT salle_ibfk_1 FOREIGN KEY (gym_id) REFERENCES gym (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
     
        $this->addSql(<<<'SQL'
            ALTER TABLE abonnement ADD sportif_id INT NOT NULL, ADD dateInitiale DATE NOT NULL, ADD dateExpiration DATE NOT NULL, DROP date_initiale, DROP date_expiration, CHANGE mode_paiement modePaiement VARCHAR(255) NOT NULL
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
            ALTER TABLE achat_produit ADD CONSTRAINT achat_produit_ibfk_1 FOREIGN KEY (utilisateur_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE achat_produit ADD CONSTRAINT achat_produit_ibfk_2 FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE
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
            ALTER TABLE commande CHANGE date date DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE telephone telephone VARCHAR(20) NOT NULL, CHANGE mail mail VARCHAR(100) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_6eeaa67dfb88e14f ON commande
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX utilisateur_id ON commande (utilisateur_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_6eeaa67df347efb ON commande
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX produit_id ON commande (produit_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)
        SQL);

        $this->addSql(<<<'SQL'
            ALTER TABLE cours ADD dateDebut DATE NOT NULL, ADD dateFin DATE NOT NULL, ADD responsableSalle_id INT DEFAULT NULL, ADD salleId INT DEFAULT NULL, ADD placesDisponibles INT DEFAULT 20, DROP date_debut, DROP date_fin, CHANGE description description TEXT DEFAULT NULL, CHANGE prix prix NUMERIC(10, 2) DEFAULT '0.00' NOT NULL, CHANGE places_disponibles coach_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX coach_id ON cours (coach_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX responsableSalle_id ON cours (responsableSalle_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_fdca8c9cdc304035 ON cours
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX salle_id ON cours (salle_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9CDC304035 FOREIGN KEY (salle_id) REFERENCES salles (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE evenement ADD dateDebut DATE NOT NULL, ADD dateFin DATE NOT NULL, DROP date_debut, DROP date_fin, CHANGE description description TEXT NOT NULL, CHANGE frais frais NUMERIC(10, 2) NOT NULL, CHANGE nombre_de_places nombreDePlaces INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gym ADD responsable_id INT DEFAULT NULL, CHANGE localisation localisation VARCHAR(255) DEFAULT NULL, CHANGE services services VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gym ADD CONSTRAINT gym_ibfk_1 FOREIGN KEY (responsable_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX responsable_id ON gym (responsable_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX responsable_id_2 ON gym (responsable_id)
        SQL);
        
        $this->addSql(<<<'SQL'
            ALTER TABLE participation CHANGE utilisateur_id utilisateur_id INT NOT NULL, CHANGE statut_p statutP VARCHAR(255) NOT NULL, CHANGE nombre_de_places_reservees nombreDePlacesReservees INT DEFAULT NULL
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
            ALTER TABLE produit CHANGE description description VARCHAR(500) DEFAULT NULL, CHANGE photo photo VARCHAR(500) DEFAULT NULL
        SQL);
    
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT fk_salle FOREIGN KEY (salle_id) REFERENCES salle (id) ON UPDATE CASCADE ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_42c84955dc304035 ON reservation
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX fk_salle ON reservation (salle_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT FK_42C84955DC304035 FOREIGN KEY (salle_id) REFERENCES salles (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation_creneaux CHANGE choix choix VARCHAR(50) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE salles CHANGE numero numero VARCHAR(50) DEFAULT NULL, CHANGE specialite specialite VARCHAR(100) DEFAULT NULL, CHANGE description description TEXT DEFAULT NULL, CHANGE nom nom VARCHAR(100) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user CHANGE role role VARCHAR(50) NOT NULL, CHANGE email email VARCHAR(100) NOT NULL, CHANGE prenom prenom VARCHAR(50) NOT NULL, CHANGE nom nom VARCHAR(50) NOT NULL, CHANGE telephone telephone VARCHAR(20) NOT NULL, CHANGE adresse adresse TEXT DEFAULT NULL, CHANGE specialite specialite VARCHAR(100) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX telephone ON user (telephone)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX email ON user (email)
        SQL);
    }
}