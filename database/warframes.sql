-- Warframes metadata storage for Vite & Gourmand
USE `vite_gourmand`;

DROP TABLE IF EXISTS `warframes`;

CREATE TABLE `warframes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `category` VARCHAR(80) NOT NULL,
  `description` TEXT,
  `file_path` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `warframes` (`name`, `category`, `description`, `file_path`) VALUES
('Warframe Page d’accueil', 'Public', 'Design de la page d’accueil avec navigation et zone héros.', 'assets/warframes/warframe-accueil.png'),
('Warframe Connexion', 'Auth', 'Écran de connexion sécurisé pour tous les rôles.', 'assets/warframes/warframe-login.png'),
('Warframe Inscription', 'Auth', 'Formulaire d’inscription avec choix de rôle.', 'assets/warframes/warframe-register.png'),
('Warframe Dashboard client', 'Client', 'Vue du client avec historique des commandes.', 'assets/warframes/warframe-dashboard-client.png'),
('Warframe Nouvelle commande', 'Client', 'Page de sélection des menus et résumé de panier.', 'assets/warframes/warframe-nouvelle-commande.png'),
('Warframe Dashboard admin', 'Admin', 'Vue de gestion pour menus et commandes.', 'assets/warframes/warframe-dashboard-admin.png');
