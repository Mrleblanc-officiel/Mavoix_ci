<?php
/**
 * Page de déconnexion
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Suppression des variables de session
session_unset();

// Destruction de la session
session_destroy();

// Redirection vers la page de connexion
header('Location: index.php?action=login');
exit;
?>
