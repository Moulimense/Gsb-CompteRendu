<?php
ob_start();
session_start();
require_once('modele/medicament.modele.inc.php');

require_once('modele/connexion.modele.inc.php');

if (!isset($_REQUEST['uc']) || empty($_REQUEST['uc']))
    $uc = 'accueil';
else {
    $uc = $_REQUEST['uc'];
}
?>
<?php
if (empty($_SESSION['login'])) {
    include("vues/v_headerDeconnexion.php");
} else {
    include("vues/v_header.php");
}
switch ($uc) {
    case 'accueil': {
        include("vues/v_accueil.php");
        break;
    }
    case 'medicaments': {
        if (!empty($_SESSION['login'])) {
            include("controleur/c_medicaments.php");
        } else {
            include("vues/v_accesInterdit.php");
        }
        break;
    }
    case 'praticiens': {
        if (!empty($_SESSION['login'])) {
            include('controleur/c_praticiens.php');
        } else {
            include('vues/v_accesInterdit.php');
        }
        break;
    }

    case 'gererPraticien': {
        if (!empty($_SESSION['login'])) {
            // Seuls les délégués et responsables peuvent gérer les praticiens
            $habCheck = $_SESSION['habilitation'] ?? 0;
            $estVisiteurCheck = ($habCheck == 1);
            if ($estVisiteurCheck) {
                include('vues/v_accesInterdit.php');
            } else {
                include('controleur/c_gererPraticien.php');
            }
        } else {
            include('vues/v_accesInterdit.php');
        }
        break;
    }

    case 'connexion': {
        include("controleur/c_connexion.php");
        break;
    }
    case 'rapportVisite':
        include("controleur/c_rapportVisite.php");
        break;
    default: {

        include("vues/v_accueil.php");
        break;
    }
}
?>
<?php include("vues/v_footer.php"); ?>
</body>

</html>