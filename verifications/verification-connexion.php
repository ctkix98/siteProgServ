<?php
use Exception;
require_once('../db/Database.php');
session_start();

$donneeConnexion = [];

if (filter_has_var(INPUT_POST, 'submit')) {
    $donneeConnexion['email'] = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $donneeConnexion['mdp'] = filter_input(INPUT_POST, 'mdp', FILTER_VALIDATE_REGEXP, ["options" => ["regexp" => "/^[A-Za-z0-9$!€£]{8,20}$/"]]);
} else {
    header('Location: ../pages/errorConnexion.php', true, 303);
    exit();
}

$required = ['email', 'mdp'];
foreach ($required as $champ) {
    if (empty($donneeConnexion[$champ])) {
        header('Location: ../pages/errorConnexion.php', true, 303);
        exit();
    }
}

$donneeConnexion['email'] = strtolower($donneeConnexion['email']);

// Accéder à la base de données
$db = new Database();
$donnesCompletesUtilisateur = $db->verifierAccesEtRecupererUtilisateur($donneeConnexion['email']);

if ($donnesCompletesUtilisateur !== null) {
    // Vérifiez le mot de passe
    if (password_verify($donneeConnexion['mdp'], $donnesCompletesUtilisateur['mdp'])) {
        // Mot de passe correct, établir la session
        $_SESSION['utilisateur'] = $donnesCompletesUtilisateur;
        header('Location: ../pages/confirmationConnexion.php');
        exit();
    } else {
        // Mot de passe incorrect
        header('Location: ../pages/errorConnexion.php', true, 303);
        exit();
    }
} else {
    // Utilisateur non trouvé
    header('Location: ../pages/errorConnexion.php', true, 303);
    exit();
}