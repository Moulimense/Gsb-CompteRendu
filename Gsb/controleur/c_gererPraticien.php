<?php
if (!isset($_SESSION['login'])) {
    header('Location: index.php?uc=connexion&action=connexion');
    exit();
}

require_once("modele/bd.praticien.inc.php");
require_once("modele/connexion.modele.inc.php");

$action = $_REQUEST['action'];
$idVisiteur = $_SESSION['matricule'];
$habVar = $_SESSION['habilitation'] ?? 0;
// Check if is visitor (ID 1 or String 'Visiteur')
$isVisitor = ($habVar == 1) || (is_string($habVar) && mb_strtolower($habVar, 'UTF-8') === 'visiteur');

// Security Check for Visitors (id 1)
if ($isVisitor) {
    // Visitor can ONLY list or consult.
    $allowedActions = ['liste', 'consulter'];
    if (!in_array($action, $allowedActions)) {
        // Redirect or error
        header('Location: index.php?uc=gererPraticien&action=liste&filtre=global&error=forbidden');
        exit();
    }
}

switch ($action) {
    case 'liste':
        $filtre = $_GET['filtre'] ?? 'global';
        if ($filtre == 'region') {
            $codeRegion = $_SESSION['code_region'];
            $lesPraticiens = getPraticiensByRegion($codeRegion);
        } else {
            $lesPraticiens = getPraticiens();
        }
        include("vues/v_gererPraticien.php");
        break;

    case 'ajouter':
        $lesTypes = getTypesPraticien();
        $lesSpecialites = getSpecialites();
        $mode = 'ajouter';
        include("vues/v_formPraticien.php");
        break;

    case 'validerAjout':
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $adresse = $_POST['adresse'];
        $cp = $_POST['cp'];
        $ville = $_POST['ville'];
        $coef = $_POST['coef'];
        $typeCode = $_POST['typeCode'];
        $speCodes = $_POST['speCode'] ?? [];

        if (ajouterPraticien($nom, $prenom, $adresse, $cp, $ville, $coef, $typeCode, $speCodes)) {
            $erreur = "Le praticien a été ajouté avec succès."; // Using $erreur as success message for simplicity in view
            // Refresh list
            $lesPraticiens = getPraticiens();
            include("vues/v_gererPraticien.php");
        } else {
            $erreur = "Erreur lors de l'ajout du praticien.";
            $lesTypes = getTypesPraticien();
            $lesSpecialites = getSpecialites();
            $mode = 'ajouter';
            include("vues/v_formPraticien.php");
        }
        break;

    case 'modifier':
        $num = $_GET['num'];
        $praticien = getPraticienByNum($num);
        $lesTypes = getTypesPraticien();
        $lesSpecialites = getSpecialites();
        $mode = 'modifier';
        include("vues/v_formPraticien.php");
        break;

    case 'validerModif':
        $num = $_POST['num'];
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $adresse = $_POST['adresse'];
        $cp = $_POST['cp'];
        $ville = $_POST['ville'];
        $coef = $_POST['coef'];
        $typeCode = $_POST['typeCode'];
        $speCodes = $_POST['speCode'] ?? [];

        if (modifierPraticien($num, $nom, $prenom, $adresse, $cp, $ville, $coef, $typeCode, $speCodes)) {
            $erreur = "Le praticien a été modifié avec succès.";
            $lesPraticiens = getPraticiens();
            include("vues/v_gererPraticien.php");
        } else {
            $erreur = "Erreur lors de la modification du praticien.";
            $praticien = getPraticienByNum($num);
            $lesTypes = getTypesPraticien();
            $lesSpecialites = getSpecialites();
            $mode = 'modifier';
            include("vues/v_formPraticien.php");
        }
        break;

    case 'supprimer':
        $num = $_GET['num'];
        if (supprimerPraticien($num)) {
            $erreur = "Le praticien a été supprimé avec succès.";
        } else {
            $erreur = "Impossible de supprimer ce praticien (il est probablement lié à des rapports de visite).";
        }
        $lesPraticiens = getPraticiens();
        include("vues/v_gererPraticien.php");
        break;

    case 'consulter':
        $num = $_GET['num'];
        $praticien = getPraticienDetails($num);
        include("vues/v_consulterPraticien.php");
        break;

    default:
        header('Location: index.php?uc=gererPraticien&action=liste');
        break;
}
?>