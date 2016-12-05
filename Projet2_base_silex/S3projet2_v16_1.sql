DROP TABLE  IF EXISTS paniers,commandes, produits, users, typeProduits, etats;

-- --------------------------------------------------------
-- Structure de la table typeproduits
--
CREATE TABLE IF NOT EXISTS typeProduits (
  id int(10) NOT NULL,
  libelle varchar(50) DEFAULT NULL,
  PRIMARY KEY (id)
)  DEFAULT CHARSET=utf8;
-- Contenu de la table typeproduits
INSERT INTO typeProduits (id, libelle) VALUES
(1, 'Seinen'),
(2, 'Shojo'),
(3, 'Shonen');

-- --------------------------------------------------------
-- Structure de la table etats

CREATE TABLE IF NOT EXISTS etats (
  id int(11) NOT NULL AUTO_INCREMENT,
  libelle varchar(20) NOT NULL,
  PRIMARY KEY (id)
) DEFAULT CHARSET=utf8 ;
-- Contenu de la table etats
INSERT INTO etats (id, libelle) VALUES
(1, 'A préparer'),
(2, 'Expédié');

-- --------------------------------------------------------
-- Structure de la table produits

CREATE TABLE IF NOT EXISTS produits (
  id int(10) NOT NULL AUTO_INCREMENT,
  typeProduit_id int(10) DEFAULT NULL,
  nom varchar(50) DEFAULT NULL,
  prix float(6,2) DEFAULT NULL,
  photo varchar(50) DEFAULT NULL,
  dispo tinyint(4) NOT NULL,
  stock int(11) NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_produits_typeProduits FOREIGN KEY (typeProduit_id) REFERENCES typeProduits (id)
) DEFAULT CHARSET=utf8 ;

INSERT INTO produits (id,typeProduit_id,nom,prix,photo,dispo,stock) VALUES
(1,1, 'Death Note','100','deathnote.jpg',1,5),
(2,3, 'Naruto','5.5','naruto.jpg',1,4),
(3,1, 'Oyazumi Punpun','8.5','punpun.jpg',1,10),
(4,3, 'Dragon Ball Z','8','dragonballz.jpg',1,5),
(5,2, 'Genkan Shoujo Nozaki Kun','55','genkanshoujo.jpg',1,4),
(6,3, 'Zetsuen no tempest','5','zetsuennotempest.jpg',1,10),
(7,3, 'One Piece','5.45','onepiece .jpg',1,10);


-- --------------------------------------------------------
-- Structure de la table user
-- valide permet de rendre actif le compte (exemple controle par email )

CREATE TABLE IF NOT EXISTS users (
  id int(11) NOT NULL AUTO_INCREMENT,
  email varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  login varchar(255) NOT NULL,
  nom varchar(255),
  code_postal varchar(255),
  ville varchar(255),
  adresse varchar(255),
  valide tinyint NOT NULL,
  droit varchar(255) NOT NULL,
  PRIMARY KEY (id)
) DEFAULT CHARSET=utf8;

-- Contenu de la table users
INSERT INTO users (id,login,password,email,valide,droit) VALUES
(1, 'admin', 'admin', 'admin@gmail.com',1,'DROITadmin'),
(2, 'vendeur', 'vendeur', 'vendeur@gmail.com',1,'DROITadmin'),
(3, 'client', 'client', 'client@gmail.com',1,'DROITclient'),
(4, 'client2', 'client2', 'client2@gmail.com',1,'DROITclient'),
(5, 'client3', 'client3', 'client3@gmail.com',1,'DROITclient');



-- --------------------------------------------------------
-- Structure de la table commandes
CREATE TABLE IF NOT EXISTS commandes (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) NOT NULL,
  prix float(6,2) NOT NULL,
  date_achat  timestamp default CURRENT_TIMESTAMP,
  etat_id int(11) NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_commandes_users FOREIGN KEY (user_id) REFERENCES users (id),
  CONSTRAINT fk_commandes_etats FOREIGN KEY (etat_id) REFERENCES etats (id)
) DEFAULT CHARSET=utf8 ;



-- --------------------------------------------------------
-- Structure de la table paniers
CREATE TABLE IF NOT EXISTS paniers (
  id int(11) NOT NULL AUTO_INCREMENT,
  quantite int(11) NOT NULL,
  prix float(6,2) NOT NULL,
  dateAjoutPanier timestamp default CURRENT_TIMESTAMP,
  user_id int(11) NOT NULL,
  produit_id int(11) NOT NULL,
  commande_id int(11) DEFAULT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_paniers_users FOREIGN KEY (user_id) REFERENCES users (id),
  CONSTRAINT fk_paniers_produits FOREIGN KEY (produit_id) REFERENCES produits (id),
  CONSTRAINT fk_paniers_commandes FOREIGN KEY (commande_id) REFERENCES commandes (id)
) DEFAULT CHARSET=utf8 ;

