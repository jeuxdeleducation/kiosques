=== JDE Kiosques ===
Contributors: Samuel Lavoie
Tags: kiosques, réservation, événement, académique
Requires at least: 5.5
Tested up to: 6.4
Stable tag: 1.2.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==
JDE Kiosques est un plugin de gestion des kiosques pour les événements académiques. 
Il permet aux entreprises et partenaires de réserver des emplacements via un widget interactif, tout en offrant aux administrateurs une interface intuitive pour gérer les réservations, logs et statistiques.

== Installation ==
1. Téléchargez le plugin depuis WordPress.org ou ajoutez-le via "Ajouter un plugin" dans l'administration WordPress.
2. Activez le plugin via le menu "Extensions".
3. Accédez aux réglages via "JDE Kiosques" dans le menu admin pour configurer le nombre de kiosques et gérer les accès.
4. Ajoutez le shortcode `[jde_kiosques]` dans vos pages ou widgets pour afficher les kiosques.

== Frequently Asked Questions ==
= Comment afficher les kiosques sur une page ? =
Ajoutez simplement le shortcode `[jde_kiosques]` dans l'éditeur WordPress.

= Comment puis-je personnaliser l'affichage ? =
Utilisez le fichier `style.css` dans `/assets/` ou ajoutez vos propres styles via votre thème.

= Comment gérer les utilisateurs ayant accès au plugin ? =
Dans les paramètres du plugin, sélectionnez les utilisateurs qui auront accès aux fonctionnalités d'administration de JDE Kiosques.

= Comment réinitialiser les réservations ? =
Accédez aux réglages du plugin et utilisez l'option de réinitialisation pour vider la base de données des réservations.

== Changelog ==
= 1.2.4 =
* Amélioration de la gestion des accès : les pages restent visibles, mais affichent un message d'erreur si l'utilisateur n'a pas les permissions.
* Correction et réintégration du cache pour l'affichage des kiosques.
* Optimisation de la gestion des rôles et permissions pour plus de flexibilité.

= 1.2.3 =
* Ajout d'une fonctionnalité permettant de gérer les utilisateurs autorisés à accéder aux paramètres du plugin.
* Sécurisation des accès aux pages d'administration.
* Amélioration de la gestion des paramètres avec une interface plus intuitive.

== Upgrade Notice ==
= 1.2.4 =
Cette mise à jour améliore la flexibilité d'affichage et optimise la gestion des accès. Mettez à jour immédiatement.
