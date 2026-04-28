<?php
if (!isset($_SESSION['login'])) {
    header('Location: index.php?uc=connexion&action=connexion');
    exit();
}

require_once(__DIR__ . "/../modele/bd.rapportVisite.inc.php");
require_once(__DIR__ . "/../modele/bd.praticien.inc.php");
require_once(__DIR__ . "/../modele/medicament.modele.inc.php");
require_once(__DIR__ . "/../modele/bd.motif.inc.php");

$action = $_REQUEST['action'] ?? 'liste';
$idVisiteur = $_SESSION['matricule'];

// Normalisation de l'habilitation
// Normalisation de l'habilitation
$hab = $_SESSION['habilitation'] ?? 1; // Default to Visitor (1)
$estVisiteur = ($hab == 1);
$isResponsable = ($hab == 3); // Permission 3 is Responsibility
$estDelegue = ($hab == 2);
$habilitation = $estVisiteur ? 'Visiteur' : 'Delegue';

switch ($action) {
    case 'liste':
        if ($isResponsable) {
            header('Location: index.php?uc=rapportVisite&action=recherche');
            exit();
        }
        $typeRapport = $_GET['type_rapport'] ?? 'mes_rapports';

        if ($typeRapport === 'region') {
            $infosCompte = getAllInformationCompte($idVisiteur);
            $codeRegion = $infosCompte['code_region'];
            $codeSecteur = $infosCompte['code_secteur'] ?? null;

            if ($isResponsable && $codeSecteur) {
                // If Responsible has Sector, show Sector reports
                $rapports = getRapportsSecteur($codeSecteur);
            } else {
                // Delegate shows Region
                $rapports = getRapportsRegion($codeRegion);
            }
        } else {
            $rapports = getRapportsVisiteurTous($idVisiteur);
        }
        include("vues/v_listeRapports.php");
        break;

    case 'nouveau':
        if ($isResponsable) {
            header('Location: index.php?uc=rapportVisite&action=liste');
            exit();
        }
        $lesPraticiens = getPraticiens();
        $lesMedicaments = getAllNomMedicament();
        $lesMotifs = getAllMotifs();
        include("vues/v_saisirRapport.php");
        break;

    case 'modifier':
        if ($isResponsable) {
            header('Location: index.php?uc=rapportVisite&action=liste');
            exit();
        }
        if (!isset($_GET['idRapport'])) {
            header('Location: index.php?uc=rapportVisite&action=liste');
            exit();
        }
        $idRapport = $_GET['idRapport'];
        $rapport = getRapportById($idVisiteur, $idRapport);
        if (!$rapport) {
            $erreur = "Le rapport demandé est introuvable.";
            $rapports = getRapportsVisiteurTous($idVisiteur);
            include("vues/v_listeRapports.php");
            break;
        }

        if (isset($rapport['etat_code']) && $rapport['etat_code'] != 1) {

            $rapports = getRapportsVisiteurTous($idVisiteur);
            $erreur = "Ce rapport ne peut plus être modifié car il a été validé ou consulté.";
            include("vues/v_listeRapports.php");
            break;
        }
        $lesPraticiens = getPraticiens();
        $lesMedicaments = getAllNomMedicament();
        $lesMotifs = getAllMotifs();
        $lesOffres = getOffresRapport($idVisiteur, $idRapport);
        include("vues/v_saisirRapport.php");
        break;

    case 'recherche':
        if ($estVisiteur) {
            header('Location: index.php?uc=rapportVisite&action=liste');
            exit();
        }

        $infosCompte = getAllInformationCompte($idVisiteur);
        $codeRegion = $infosCompte['code_region'] ?? null;
        $codeSecteur = $infosCompte['code_secteur'] ?? null;


        $lesPraticiens = getPraticiensByRegion($codeRegion);

        $dateDebut = $_POST['dateDebut'] ?? date('Y-m-d', strtotime('-1 month'));
        $dateFin = $_POST['dateFin'] ?? date('Y-m-d');
        $praNum = $_POST['praNum'] ?? null;

        $filtreType = $_POST['filtreType'] ?? 'region'; // Default filter

        // Always fetch reports. If POST, use posted values. If GET, use defaults (-1 month).
        if ($isResponsable && $filtreType === 'secteur' && $codeSecteur) {
            $lesRapports = getRapportsSecteurFiltres($codeSecteur, $dateDebut, $dateFin, $praNum);
        } else {
            $lesRapports = getRapportsRegionFiltres($codeRegion, $dateDebut, $dateFin, $praNum);
        }

        include("vues/v_rechercheRapports.php");
        break;

    case 'nouveauxRapportsRegion':
        $infosCompte = getAllInformationCompte($idVisiteur);
        $codeRegion = $infosCompte['code_region'];
        $codeSecteur = $infosCompte['code_secteur'] ?? null;

        if (!$estVisiteur) {
            if ($isResponsable && $codeSecteur) {
                $rapports = getRapportsSecteurNouveaux($codeSecteur);
            } else {
                $rapports = getRapportsRegionNouveaux($codeRegion);
            }
            include("vues/v_listeNouveauxRapports.php");
        } else {
            header('Location: index.php?uc=rapportVisite&action=liste');
        }
        break;

    case 'consulter':
        if (!isset($_GET['idRapport'])) {
            header('Location: index.php?uc=rapportVisite&action=liste');
            exit();
        }
        $idRapport = $_GET['idRapport'];

        $targetVisiteur = $_GET['idVisiteur'] ?? $idVisiteur;

        // Verify permission: If target != me, must be delegate/resp, OR (if Visitor) must be same region
        if ($targetVisiteur !== $idVisiteur && $estVisiteur) {
            $infosCompte = getAllInformationCompte($idVisiteur);
            $codeRegion = $infosCompte['code_region'];
            $infosTarget = getAllInformationCompte($targetVisiteur);
            $targetRegion = $infosTarget['code_region'];

            if ($codeRegion !== $targetRegion) {
                header('Location: index.php?uc=rapportVisite&action=liste');
                exit();
            }
        }

        $rapport = getRapportById($targetVisiteur, $idRapport);

        if (!$rapport) {
            header('Location: index.php?uc=rapportVisite&action=liste');
            exit();
        }

        // Update status if it's a new report and I'm a delegate
        if (!$estVisiteur && isset($rapport['etat_code']) && $rapport['etat_code'] == 2) {
            setRapportConsulte($targetVisiteur, $idRapport);
            // Refresh report data to show updated status
            $rapport['etat_code'] = 3;
        }

        $lesOffres = getOffresRapport($targetVisiteur, $idRapport);
        include("vues/v_consulterRapport.php");
        break;

    case 'consulterPraticien':
        $idPraticien = $_GET['idPraticien'] ?? null;
        if ($idPraticien) {
            $praticien = getPraticienByNum($idPraticien);
            include("vues/v_afficherPraticien.php");
        } else {
            header('Location: index.php?uc=rapportVisite&action=liste');
        }
        break;

    case 'validerSaisie':
        if ($isResponsable) {
            header('Location: index.php?uc=rapportVisite&action=liste');
            exit();
        }
        $erreurs = [];
        $idPraticien = $_POST['idPraticien'] ?? null;
        $praticienRemplacant = !empty($_POST['praticienRemplacant']) ? $_POST['praticienRemplacant'] : null;
        $dateVisite = $_POST['dateVisite'] ?? null;
        $motif = !empty($_POST['motif']) ? $_POST['motif'] : null;
        $autreMotif = trim($_POST['autreMotif'] ?? '');
        $bilan = trim($_POST['bilan'] ?? '');
        $medicamentPresente = !empty($_POST['medicamentPresente']) ? $_POST['medicamentPresente'] : null;
        $medicamentPrescrit = !empty($_POST['medicamentPrescrit']) ? $_POST['medicamentPrescrit'] : null;
        $medicamentEchantillon = $_POST['medicamentEchantillon'] ?? [];
        $quantiteEchantillon = $_POST['quantiteEchantillon'] ?? [];


        if (!is_array($medicamentEchantillon))
            $medicamentEchantillon = [];
        if (!is_array($quantiteEchantillon))
            $quantiteEchantillon = [];

        $coeffConfiance = $_POST['coeffConfiance'] ?? null;
        $etat = isset($_POST['saisieDefinitive']) ? 2 : 1;
        $idRapport = $_POST['idRapport'] ?? null;

        if (!empty($idRapport)) {
            $oldRapport = getRapportById($idVisiteur, $idRapport);
            // If rapport exists and state is not 1 (En cours), block modification
            if ($oldRapport && isset($oldRapport['etat_code']) && $oldRapport['etat_code'] != 1) {
                header('Location: index.php?uc=rapportVisite&action=liste');
                exit();
            }
        }

        if (empty($dateVisite) || empty($motif) || empty($bilan) || empty($idPraticien)) {
            $erreurs[] = "Tous les champs obligatoires doivent être remplis.";
        }

        if ($motif == 5 && empty($autreMotif)) {
            $erreurs[] = "Veuillez préciser le motif autre.";
        }

        if ($coeffConfiance !== null && $coeffConfiance !== '' && ($coeffConfiance < 0 || $coeffConfiance > 100)) {
            $erreurs[] = "Le coefficient de confiance doit être compris entre 0 et 100.";
        }

        // Validation des échantillons
        $hasSample = false;
        foreach ($medicamentEchantillon as $index => $med) {
            $qte = $quantiteEchantillon[$index] ?? '';
            if (!empty($med) && empty($qte)) {
                $erreurs[] = "Ligne échantillon " . ($index + 1) . " : Médicament sélectionné sans quantité.";
            }
            if (empty($med) && !empty($qte)) {
                $erreurs[] = "Ligne échantillon " . ($index + 1) . " : Quantité sélectionnée sans médicament.";
            }
            if (!empty($med) && !empty($qte)) {
                $hasSample = true;
            }
        }



        // Validation globale des médicaments (Présenté, prescrit ou échantillon)
        if (empty($medicamentPresente) && empty($medicamentPrescrit) && !$hasSample) {
            // Check for confirmation override
            if (!isset($_POST['confirmerSansMedicament'])) {
                // We add a specific error that might trigger a UI element or just text
                $erreurs[] = "Aucun médicament (présenté, prescrit ou échantillon) n'a été sélectionné.";
            }
        }

        if (!empty($erreurs)) {
            $lesPraticiens = getPraticiens();
            $lesMedicaments = getAllNomMedicament();
            $lesMotifs = getAllMotifs();

            // Reconstruct offers for the view
            $lesOffres = [];
            foreach ($medicamentEchantillon as $index => $medId) {
                if (!empty($medId) && !empty($quantiteEchantillon[$index])) {
                    $lesOffres[] = [
                        'MED_DEPOTLEGAL' => $medId,
                        'OFF_QTE' => $quantiteEchantillon[$index]
                    ];
                }
            }

            $rapport = [
                'RAP_NUM' => $idRapport,
                'PRA_NUM' => $idPraticien,
                'PRA_NUM_remplacant' => $praticienRemplacant,
                'RAP_DATEVISITE' => $dateVisite,
                'RAP_MOTIF' => $motif,
                'RAP_AUTRE' => $autreMotif,
                'RAP_BILAN' => $bilan,
                'etat_code' => $etat,
                'MEDICAMENT_PRESENTE' => $medicamentPresente,
                'MEDICAMENT_PRESCRIT' => $medicamentPrescrit,
                'PRA_COEFF_CONFIANCE' => $coeffConfiance
            ];
            $erreur = implode('<br>', $erreurs);
            include("vues/v_saisirRapport.php");
            break;
        }

        if (!empty($idRapport)) {
            updateRapportVisite($idVisiteur, $idRapport, $idPraticien, $dateVisite, $motif, $bilan, $etat, $medicamentPresente, $medicamentPrescrit, $praticienRemplacant, $motif, $autreMotif, $coeffConfiance);
            deleteOffrir($idVisiteur, $idRapport);
        } else {
            $idRapport = insertRapportVisite($idVisiteur, $idPraticien, $dateVisite, $motif, $bilan, $etat, $medicamentPresente, $medicamentPrescrit, $praticienRemplacant, $motif, $autreMotif, $coeffConfiance);
        }

        // Insert offers
        foreach ($medicamentEchantillon as $index => $med) {
            $qte = $quantiteEchantillon[$index] ?? 0;
            if (!empty($med) && !empty($qte)) {
                insertOffrir($idVisiteur, $idRapport, $med, $qte);
            }
        }

        $info = "Le rapport a été enregistré avec succès";
        // Fetch fresh data including offers
        $rapport = getRapportById($idVisiteur, $idRapport);
        $lesOffres = getOffresRapport($idVisiteur, $idRapport);

        $lesPraticiens = getPraticiens();
        $lesMedicaments = getAllNomMedicament();
        $lesMotifs = getAllMotifs();
        include("vues/v_saisirRapport.php");
        break;

    default:
        header('Location: index.php?uc=rapportVisite&action=liste');
        break;
}
?>