<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Vérifie si l'utilisateur est connecté
 */
function verifierConnexion() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: signin.php");
        exit();
    }
}

/**
 * Vérifie si l'utilisateur possède un des rôles autorisés
 * @param array $roles_autorises
 */
function verifierRole(array $roles_autorises) {
    // Si l'utilisateur n'est pas connecté ou n'a aucun rôle
    if (!isset($_SESSION['role'])) {
        header("Location: signin.php");
        exit();
    }

    // Rôle présent mais pas autorisé
    if (!in_array($_SESSION['role'], $roles_autorises)) {
        if (in_array($_SESSION['role'], ['user', 'membre_d_honneur', 'eleve'])) {
            // Utilisateurs connectés mais rôle insuffisant
            header("Location: index.php");
        } else {
            // Aucun rôle connu
            header("Location: signin.php");
        }
        exit();
    }
}

/**
 * Vérifie si l'utilisateur est admin uniquement
 */
function requireAdmin() {
    verifierRole(['admin']);
}

/**
 * Vérifie si l'utilisateur est membre d'honneur ou admin
 */
function requireHonneurOuAdmin() {
    verifierRole(['membre_d_honneur', 'admin']);
}

/**
 * Vérifie si connecté avec n’importe quel rôle autorisé
 */
function requireAnyRole() {
    verifierRole(['admin', 'user', 'eleve', 'membre_d_honneur']);
}
