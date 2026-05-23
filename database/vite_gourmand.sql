-- Vite & Gourmand SQL schema and seed data
CREATE DATABASE IF NOT EXISTS `vite_gourmand` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `vite_gourmand`;

DROP TABLE IF EXISTS `ligne_commande`;
DROP TABLE IF EXISTS `commandes`;
DROP TABLE IF EXISTS `menus`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','client','travailleur') NOT NULL DEFAULT 'client',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `menus` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `titre` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `theme` VARCHAR(100) DEFAULT NULL,
  `regime` VARCHAR(100) DEFAULT NULL,
  `personnes_minimum` INT UNSIGNED NOT NULL DEFAULT 1,
  `prix` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `stock` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `commandes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `status` ENUM('en_cours','prete','servie','annulee') NOT NULL DEFAULT 'en_cours',
  `adresse_livraison` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_commandes_user` (`user_id`),
  CONSTRAINT `fk_commandes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ligne_commande` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `commande_id` INT UNSIGNED NOT NULL,
  `menu_id` INT UNSIGNED NOT NULL,
  `quantite` INT UNSIGNED NOT NULL DEFAULT 1,
  `prix_unitaire` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_ligne_commande_commande` (`commande_id`),
  KEY `fk_ligne_commande_menu` (`menu_id`),
  CONSTRAINT `fk_ligne_commande_commande` FOREIGN KEY (`commande_id`) REFERENCES `commandes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ligne_commande_menu` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`email`, `password_hash`, `role`) VALUES
('admi@vite.fr', '$2y$10$uWnU9kr.Foy57teUDEtaBuHrNWZX.YMoJ4gYPQ8Ipe/iYbTcNkBv6', 'admin'),
('client@vite.fr', '$2y$10$tpcMK/upVJZ5nOtNNYvX3eayamKz2Y2fl1PspUdZlFJyBtArnE3YG', 'client'),
('travailleur@vite.fr', '$2y$10$4JY4qreG9.03dqOEJ14PGeOgNvOjTS6NIPxxn.lXpVeaL.9f5wMgS', 'travailleur');

INSERT INTO `menus` (`titre`, `description`, `theme`, `regime`, `personnes_minimum`, `prix`, `stock`) VALUES
('Menu Classique', 'Salade gourmande, plat principal et dessert maison', 'Classique', 'Standard', 2, 35.00, 10),
('Menu Vegan', 'Assiette végétarienne complète, dessert fruité', 'Vegan', 'Sans produit animal', 2, 40.00, 8),
('Menu Premium', 'Entrée festive, plat signature et dessert gourmet', 'Premium', 'Sans gluten disponible', 4, 80.00, 5),
('Menu Express', 'Plat rapide, boisson et dessert léger', 'Express', 'Standard', 1, 18.50, 20);

INSERT INTO `commandes` (`user_id`, `total`, `status`, `adresse_livraison`) VALUES
(2, 75.00, 'en_cours', '123 Rue de la Paix, Lyon');

INSERT INTO `ligne_commande` (`commande_id`, `menu_id`, `quantite`, `prix_unitaire`) VALUES
(1, 1, 1, 35.00),
(1, 2, 1, 40.00);
