<?php
if (!isset($_REQUEST['action']) || empty($_REQUEST['action'])) {
    $action = "formulaire";
} else {
    $action = $_REQUEST['action'];
}

require_once("modele/bd.praticien.inc.php");

switch ($action) {
    case 'formulaire': {
        $lesPraticiens = getPraticiens();
        include("vues/v_formulairePraticien.php");
        break;
    }

    case 'afficher': {
        if (isset($_REQUEST['praticien']) && !empty($_REQUEST['praticien'])) {
            $num = $_REQUEST['praticien'];
            $praticien = getPraticienDetails($num);
            if ($praticien) {
                include("vues/v_afficherDetailsPraticien.php");
            } else {
                $_SESSION['erreur_praticien'] = true;
                header("Location: index.php?uc=praticiens&action=formulaire");
            }
        } else {
            $_SESSION['erreur_praticien'] = true;
            header("Location: index.php?uc=praticiens&action=formulaire");
        }
        break;
    }

    default: {
        header('Location: index.php?uc=praticiens&action=formulaire');
        break;
    }
}
?>
