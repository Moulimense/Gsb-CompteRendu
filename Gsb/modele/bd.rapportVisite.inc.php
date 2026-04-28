<?php
require_once("bd.inc.php");

function getRapportsVisiteurTous($idVisiteur)
{
    $connexion = connexionPDO();
    $sql = "
        SELECT rv.RAP_NUM, rv.RAP_DATEVISITE, rv.RAP_MOTIF, rv.RAP_BILAN, rv.etat_code,
               p.PRA_NUM, p.PRA_NOM, p.PRA_PRENOM, m.MO_Libelle as MOTIF_LIBELLE
        FROM rapport_visite rv
        INNER JOIN praticien p ON rv.PRA_NUM = p.PRA_NUM
        LEFT JOIN motif m ON rv.RAP_MOTIF = m.MO_Code
        WHERE rv.VIS_MATRICULE = :vis
        AND rv.etat_code = 1
        ORDER BY rv.RAP_DATEVISITE DESC
    ";

    $req = $connexion->prepare($sql);
    $req->bindValue(':vis', $idVisiteur);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function getRapportsRegion($codeRegion)
{
    $connexion = connexionPDO();
    $sql = "
        SELECT rv.RAP_NUM, rv.RAP_DATEVISITE, rv.RAP_MOTIF, rv.RAP_BILAN, rv.etat_code, rv.VIS_MATRICULE,
               p.PRA_NUM, p.PRA_NOM, p.PRA_PRENOM,
               c.COL_NOM as VIS_NOM, c.COL_PRENOM as VIS_PRENOM,
               m.MO_Libelle as MOTIF_LIBELLE
        FROM rapport_visite rv
        INNER JOIN praticien p ON rv.PRA_NUM = p.PRA_NUM
        INNER JOIN collaborateur c ON rv.VIS_MATRICULE = c.COL_MATRICULE
        LEFT JOIN motif m ON rv.RAP_MOTIF = m.MO_Code
        WHERE c.REG_CODE = :codeRegion
        AND rv.etat_code = 3
        ORDER BY rv.RAP_DATEVISITE DESC
    ";

    $req = $connexion->prepare($sql);
    $req->bindValue(':codeRegion', $codeRegion);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}
// ...
function getRapportsRegionFiltres($codeRegion, $dateDebut, $dateFin, $idPraticien = null)
{
    $connexion = connexionPDO();
    $sql = "
        SELECT rv.RAP_NUM, rv.RAP_DATEVISITE, rv.RAP_MOTIF, rv.RAP_BILAN, rv.etat_code, rv.VIS_MATRICULE,
               p.PRA_NUM, p.PRA_NOM, p.PRA_PRENOM, p.PRA_VILLE,
               r.REG_NOM,
               med1.MED_NOMCOMMERCIAL as MED1_NOM, med2.MED_NOMCOMMERCIAL as MED2_NOM,
               rv.med_depotlegal_presente1, rv.med_depotlegal_presente2,
               m.MO_Libelle as MOTIF_LIBELLE
        FROM rapport_visite rv
        INNER JOIN praticien p ON rv.PRA_NUM = p.PRA_NUM
        INNER JOIN departement d ON LEFT(p.PRA_CP, 2) = d.NoDEPT
        INNER JOIN region r ON d.REG_CODE = r.REG_CODE
        INNER JOIN collaborateur c ON rv.VIS_MATRICULE = c.COL_MATRICULE
        LEFT JOIN medicament med1 ON rv.med_depotlegal_presente1 = med1.MED_DEPOTLEGAL
        LEFT JOIN medicament med2 ON rv.med_depotlegal_presente2 = med2.MED_DEPOTLEGAL
        LEFT JOIN motif m ON rv.RAP_MOTIF = m.MO_Code
        WHERE c.REG_CODE = :codeRegion
        AND rv.etat_code = 3
        AND rv.RAP_DATEVISITE BETWEEN :dateDeb AND :dateFin
    ";

    if ($idPraticien) {
        $sql .= " AND rv.PRA_NUM = :praNum";
    }

    $sql .= " ORDER BY r.REG_NOM, rv.RAP_DATEVISITE DESC";

    $req = $connexion->prepare($sql);
    $req->bindValue(':codeRegion', $codeRegion);
    $req->bindValue(':dateDeb', $dateDebut);
    $req->bindValue(':dateFin', $dateFin);

    if ($idPraticien) {
        $req->bindValue(':praNum', $idPraticien);
    }

    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function getRapportById($idVisiteur, $numRapport)
{
    $connexion = connexionPDO();
    $sql = "
        SELECT rv.RAP_NUM, rv.RAP_DATEVISITE, rv.RAP_MOTIF, rv.RAP_BILAN, rv.etat_code, rv.RAP_AUTRE,
               p.PRA_COEFF_CONFIANCE,
               rv.med_depotlegal_presente1 as MEDICAMENT_PRESENTE,
               rv.med_depotlegal_presente2 as MEDICAMENT_PRESCRIT,
               rv.PRA_NUM_remplacant, rv.mo_num,
               p.PRA_NUM, p.PRA_NOM, p.PRA_PRENOM, p.PRA_ADRESSE, p.PRA_CP, p.PRA_VILLE, p.PRA_COEFNOTORIETE,
               pr.PRA_NOM as REMP_NOM, pr.PRA_PRENOM as REMP_PRENOM,
               med1.MED_NOMCOMMERCIAL as MED1_NOM, med1.MED_COMPOSITION as MED1_COMP, med1.MED_EFFETS as MED1_EFFETS, med1.MED_CONTREINDIC as MED1_CONTREINDIC,
               med2.MED_NOMCOMMERCIAL as MED2_NOM, med2.MED_COMPOSITION as MED2_COMP, med2.MED_EFFETS as MED2_EFFETS, med2.MED_CONTREINDIC as MED2_CONTREINDIC,
               m.MO_Libelle as MOTIF_LIBELLE
        FROM rapport_visite rv
        INNER JOIN praticien p ON rv.PRA_NUM = p.PRA_NUM
        LEFT JOIN praticien pr ON rv.PRA_NUM_remplacant = pr.PRA_NUM
        LEFT JOIN medicament med1 ON rv.med_depotlegal_presente1 = med1.MED_DEPOTLEGAL
        LEFT JOIN medicament med2 ON rv.med_depotlegal_presente2 = med2.MED_DEPOTLEGAL
        LEFT JOIN motif m ON rv.RAP_MOTIF = m.MO_Code
        WHERE rv.VIS_MATRICULE = :vis AND rv.RAP_NUM = :num
    ";
    $req = $connexion->prepare($sql);
    $req->bindValue(':vis', $idVisiteur);
    $req->bindValue(':num', $numRapport);
    $req->execute();
    return $req->fetch(PDO::FETCH_ASSOC);
}

function getOffresRapport($idVisiteur, $idRapport)
{
    $connexion = connexionPDO();
    $sql = "
        SELECT off.MED_DEPOTLEGAL, off.OFF_QTE,
               m.MED_NOMCOMMERCIAL, m.MED_COMPOSITION, m.MED_EFFETS, m.MED_CONTREINDIC
        FROM offrir off
        INNER JOIN medicament m ON off.MED_DEPOTLEGAL = m.MED_DEPOTLEGAL
        WHERE off.VIS_MATRICULE = :vis AND off.RAP_NUM = :num
    ";
    $req = $connexion->prepare($sql);
    $req->bindValue(':vis', $idVisiteur);
    $req->bindValue(':num', $idRapport);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function insertRapportVisite($idVisiteur, $idPraticien, $dateVisite, $motif, $bilan, $etat, $med1 = null, $med2 = null, $remplacant = null, $moNum = null, $autreMotif = null, $coefConf = null)
{
    $connexion = connexionPDO();
    $reqNum = $connexion->prepare("SELECT COALESCE(MAX(RAP_NUM), 0) + 1 AS nextNum FROM rapport_visite WHERE VIS_MATRICULE = :vis");
    $reqNum->bindValue(':vis', $idVisiteur);
    $reqNum->execute();
    $nextNum = $reqNum->fetch(PDO::FETCH_ASSOC)['nextNum'];

    $req = $connexion->prepare("
        INSERT INTO rapport_visite (VIS_MATRICULE, RAP_NUM, PRA_NUM, RAP_DATEVISITE, RAP_MOTIF, RAP_BILAN, etat_code, med_depotlegal_presente1, med_depotlegal_presente2, PRA_NUM_remplacant, mo_num, RAP_AUTRE)
        VALUES (:vis, :num, :pra, :datev, :motif, :bilan, :etat, :med1, :med2, :remp, :monum, :autre)
    ");
    $req->bindValue(':vis', $idVisiteur);
    $req->bindValue(':num', $nextNum);
    $req->bindValue(':pra', $idPraticien);
    $req->bindValue(':datev', $dateVisite);
    $req->bindValue(':motif', $motif);
    $req->bindValue(':bilan', $bilan);
    $req->bindValue(':etat', $etat);
    $req->bindValue(':med1', $med1);
    $req->bindValue(':med2', $med2);
    $req->bindValue(':remp', $remplacant);
    $req->bindValue(':monum', $moNum);
    $req->bindValue(':autre', $autreMotif);
    $req->execute();

    if ($coefConf !== null && $coefConf !== '') {
        $reqP = $connexion->prepare("UPDATE praticien SET PRA_COEFF_CONFIANCE = :coef WHERE PRA_NUM = :pra");
        $reqP->bindValue(':coef', $coefConf);
        $reqP->bindValue(':pra', $idPraticien);
        $reqP->execute();
    }

    return $nextNum;
}

function updateRapportVisite($idVisiteur, $numRapport, $idPraticien, $dateVisite, $motif, $bilan, $etat, $med1 = null, $med2 = null, $remplacant = null, $moNum = null, $autreMotif = null, $coefConf = null)
{
    $connexion = connexionPDO();
    $req = $connexion->prepare("
        UPDATE rapport_visite
        SET PRA_NUM = :pra, RAP_DATEVISITE = :datev, RAP_MOTIF = :motif, RAP_BILAN = :bilan, etat_code = :etat,
            med_depotlegal_presente1 = :med1, med_depotlegal_presente2 = :med2, PRA_NUM_remplacant = :remp, mo_num = :monum, RAP_AUTRE = :autre
        WHERE VIS_MATRICULE = :vis AND RAP_NUM = :num
    ");
    $req->bindValue(':pra', $idPraticien);
    $req->bindValue(':datev', $dateVisite);
    $req->bindValue(':motif', $motif);
    $req->bindValue(':bilan', $bilan);
    $req->bindValue(':etat', $etat);
    $req->bindValue(':med1', $med1);
    $req->bindValue(':med2', $med2);
    $req->bindValue(':remp', $remplacant);
    $req->bindValue(':monum', $moNum);
    $req->bindValue(':autre', $autreMotif);
    $req->bindValue(':vis', $idVisiteur);
    $req->bindValue(':num', $numRapport);
    $req->execute();

    if ($coefConf !== null && $coefConf !== '') {
        $reqP = $connexion->prepare("UPDATE praticien SET PRA_COEFF_CONFIANCE = :coef WHERE PRA_NUM = :pra");
        $reqP->bindValue(':coef', $coefConf);
        $reqP->bindValue(':pra', $idPraticien);
        $reqP->execute();
    }
}

function insertOffrir($idVisiteur, $idRapport, $medDepotLegal, $qte)
{
    $connexion = connexionPDO();
    $req = $connexion->prepare("
        INSERT INTO offrir (VIS_MATRICULE, RAP_NUM, MED_DEPOTLEGAL, OFF_QTE)
        VALUES (:vis, :num, :med, :qte)
    ");
    $req->bindValue(':vis', $idVisiteur);
    $req->bindValue(':num', $idRapport);
    $req->bindValue(':med', $medDepotLegal);
    $req->bindValue(':qte', $qte);
    $req->execute();
}

function deleteOffrir($idVisiteur, $idRapport)
{
    $connexion = connexionPDO();
    $req = $connexion->prepare("DELETE FROM offrir WHERE VIS_MATRICULE = :vis AND RAP_NUM = :num");
    $req->bindValue(':vis', $idVisiteur);
    $req->bindValue(':num', $idRapport);
    $req->execute();
}

function getPraticiensVisites($idVisiteur)
{
    $connexion = connexionPDO();
    $sql = "
        SELECT DISTINCT p.PRA_NUM, p.PRA_NOM, p.PRA_PRENOM
        FROM rapport_visite rv
        INNER JOIN praticien p ON rv.PRA_NUM = p.PRA_NUM
        WHERE rv.VIS_MATRICULE = :vis
        ORDER BY p.PRA_NOM, p.PRA_PRENOM
    ";
    $req = $connexion->prepare($sql);
    $req->bindValue(':vis', $idVisiteur);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function getRapportsFiltres($idVisiteur, $dateDebut, $dateFin, $idPraticien = null)
{
    $connexion = connexionPDO();
    $sql = "
        SELECT rv.RAP_NUM, rv.RAP_DATEVISITE, rv.RAP_MOTIF, rv.RAP_BILAN, rv.etat_code,
               p.PRA_NUM, p.PRA_NOM, p.PRA_PRENOM, p.PRA_VILLE,
               r.REG_NOM,
               med1.MED_NOMCOMMERCIAL as MED1_NOM, med2.MED_NOMCOMMERCIAL as MED2_NOM,
               rv.med_depotlegal_presente1, rv.med_depotlegal_presente2
        FROM rapport_visite rv
        INNER JOIN praticien p ON rv.PRA_NUM = p.PRA_NUM
        INNER JOIN departement d ON LEFT(p.PRA_CP, 2) = d.NoDEPT
        INNER JOIN region r ON d.REG_CODE = r.REG_CODE
        LEFT JOIN medicament med1 ON rv.med_depotlegal_presente1 = med1.MED_DEPOTLEGAL
        LEFT JOIN medicament med2 ON rv.med_depotlegal_presente2 = med2.MED_DEPOTLEGAL
        WHERE rv.VIS_MATRICULE = :vis
        AND rv.RAP_DATEVISITE BETWEEN :dateDeb AND :dateFin
    ";

    if ($idPraticien) {
        $sql .= " AND rv.PRA_NUM = :praNum";
    }

    $sql .= " ORDER BY r.REG_NOM, rv.RAP_DATEVISITE DESC";

    $req = $connexion->prepare($sql);
    $req->bindValue(':vis', $idVisiteur);
    $req->bindValue(':dateDeb', $dateDebut);
    $req->bindValue(':dateFin', $dateFin);

    if ($idPraticien) {
        $req->bindValue(':praNum', $idPraticien);
    }

    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}



function getRapportsRegionNouveaux($codeRegion)
{
    $connexion = connexionPDO();
    $sql = "
        SELECT rv.RAP_NUM, rv.RAP_DATEVISITE, rv.RAP_MOTIF, rv.RAP_BILAN, rv.etat_code, rv.VIS_MATRICULE,
               p.PRA_NUM, p.PRA_NOM, p.PRA_PRENOM, p.PRA_VILLE,
               c.COL_NOM as VIS_NOM, c.COL_PRENOM as VIS_PRENOM,
               med1.MED_NOMCOMMERCIAL as MED1_NOM, med2.MED_NOMCOMMERCIAL as MED2_NOM,
               rv.med_depotlegal_presente1, rv.med_depotlegal_presente2
        FROM rapport_visite rv
        INNER JOIN praticien p ON rv.PRA_NUM = p.PRA_NUM
        INNER JOIN collaborateur c ON rv.VIS_MATRICULE = c.COL_MATRICULE
        LEFT JOIN medicament med1 ON rv.med_depotlegal_presente1 = med1.MED_DEPOTLEGAL
        LEFT JOIN medicament med2 ON rv.med_depotlegal_presente2 = med2.MED_DEPOTLEGAL
        WHERE (c.REG_CODE = :codeRegion OR c.SEC_CODE = :codeRegion) -- Gérer les logiques région/secteur
        AND rv.etat_code = 2
        ORDER BY c.COL_NOM, rv.RAP_DATEVISITE DESC
    ";

    return getRapportsRegionNouveaux_Confirmed($codeRegion);
}

function getRapportsRegionNouveaux_Confirmed($codeRegion)
{
    $connexion = connexionPDO();
    $sql = "
        SELECT rv.RAP_NUM, rv.RAP_DATEVISITE, rv.RAP_MOTIF, rv.RAP_BILAN, rv.etat_code, rv.VIS_MATRICULE,
               p.PRA_NUM, p.PRA_NOM, p.PRA_PRENOM, p.PRA_VILLE,
               c.COL_NOM as VIS_NOM, c.COL_PRENOM as VIS_PRENOM,
               med1.MED_NOMCOMMERCIAL as MED1_NOM, med2.MED_NOMCOMMERCIAL as MED2_NOM,
               rv.med_depotlegal_presente1, rv.med_depotlegal_presente2,
               m.MO_Libelle as MOTIF_LIBELLE
        FROM rapport_visite rv
        INNER JOIN praticien p ON rv.PRA_NUM = p.PRA_NUM
        INNER JOIN collaborateur c ON rv.VIS_MATRICULE = c.COL_MATRICULE
        LEFT JOIN medicament med1 ON rv.med_depotlegal_presente1 = med1.MED_DEPOTLEGAL
        LEFT JOIN medicament med2 ON rv.med_depotlegal_presente2 = med2.MED_DEPOTLEGAL
        LEFT JOIN motif m ON rv.RAP_MOTIF = m.MO_Code
        WHERE c.REG_CODE = :codeRegion
        AND rv.etat_code = 2
        ORDER BY c.COL_NOM, rv.RAP_DATEVISITE DESC
    ";
    $req = $connexion->prepare($sql);
    $req->bindValue(':codeRegion', $codeRegion);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function getRapportsSecteurNouveaux($codeSecteur)
{
    $connexion = connexionPDO();
    $sql = "
        SELECT rv.RAP_NUM, rv.RAP_DATEVISITE, rv.RAP_MOTIF, rv.RAP_BILAN, rv.etat_code, rv.VIS_MATRICULE,
               p.PRA_NUM, p.PRA_NOM, p.PRA_PRENOM, p.PRA_VILLE,
               c.COL_NOM as VIS_NOM, c.COL_PRENOM as VIS_PRENOM,
               med1.MED_NOMCOMMERCIAL as MED1_NOM, med2.MED_NOMCOMMERCIAL as MED2_NOM,
               rv.med_depotlegal_presente1, rv.med_depotlegal_presente2
        FROM rapport_visite rv
        INNER JOIN praticien p ON rv.PRA_NUM = p.PRA_NUM
        INNER JOIN collaborateur c ON rv.VIS_MATRICULE = c.COL_MATRICULE
        LEFT JOIN medicament med1 ON rv.med_depotlegal_presente1 = med1.MED_DEPOTLEGAL
        LEFT JOIN medicament med2 ON rv.med_depotlegal_presente2 = med2.MED_DEPOTLEGAL
        WHERE c.SEC_CODE = :codeSecteur
        AND rv.etat_code = 2
        ORDER BY c.COL_NOM, rv.RAP_DATEVISITE DESC
    ";
    $req = $connexion->prepare($sql);
    $req->bindValue(':codeSecteur', $codeSecteur);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}


function getRapportsSecteur($codeSecteur)
{
    $connexion = connexionPDO();
    $sql = "
        SELECT rv.RAP_NUM, rv.RAP_DATEVISITE, rv.RAP_MOTIF, rv.RAP_BILAN, rv.etat_code, rv.VIS_MATRICULE,
               p.PRA_NOM, p.PRA_PRENOM, p.PRA_VILLE,
               c.COL_NOM as VIS_NOM, c.COL_PRENOM as VIS_PRENOM,
               m.MO_Libelle as MOTIF_LIBELLE
        FROM rapport_visite rv
        INNER JOIN praticien p ON rv.PRA_NUM = p.PRA_NUM
        INNER JOIN collaborateur c ON rv.VIS_MATRICULE = c.COL_MATRICULE
        LEFT JOIN motif m ON rv.RAP_MOTIF = m.MO_Code
        WHERE c.SEC_CODE = :codeSecteur
        AND rv.etat_code = 3
        ORDER BY rv.RAP_DATEVISITE DESC
    ";
    $req = $connexion->prepare($sql);
    $req->bindValue(':codeSecteur', $codeSecteur);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function getRapportsSecteurFiltres($codeSecteur, $dateDebut, $dateFin, $praNum = null)
{
    $connexion = connexionPDO();
    $sql = "
        SELECT rv.RAP_NUM, rv.RAP_DATEVISITE, rv.RAP_MOTIF, rv.RAP_BILAN, rv.etat_code, rv.VIS_MATRICULE,
               p.PRA_NUM, p.PRA_NOM, p.PRA_PRENOM, p.PRA_VILLE,
               c.COL_NOM as VIS_NOM, c.COL_PRENOM as VIS_PRENOM,
               med1.MED_NOMCOMMERCIAL as MED1_NOM, med2.MED_NOMCOMMERCIAL as MED2_NOM,
               rv.med_depotlegal_presente1, rv.med_depotlegal_presente2,
               r.REG_NOM,
               m.MO_Libelle as MOTIF_LIBELLE
        FROM rapport_visite rv
        INNER JOIN praticien p ON rv.PRA_NUM = p.PRA_NUM
        INNER JOIN departement d ON LEFT(p.PRA_CP, 2) = d.NoDEPT
        INNER JOIN region r ON d.REG_CODE = r.REG_CODE
        INNER JOIN collaborateur c ON rv.VIS_MATRICULE = c.COL_MATRICULE
        LEFT JOIN medicament med1 ON rv.med_depotlegal_presente1 = med1.MED_DEPOTLEGAL
        LEFT JOIN medicament med2 ON rv.med_depotlegal_presente2 = med2.MED_DEPOTLEGAL
        LEFT JOIN motif m ON rv.RAP_MOTIF = m.MO_Code
        WHERE c.SEC_CODE = :codeSecteur
        AND rv.etat_code = 3
        AND rv.RAP_DATEVISITE BETWEEN :dateDebut AND :dateFin
    ";

    if ($praNum) {
        $sql .= " AND rv.PRA_NUM = :praNum";
    }

    $sql .= " ORDER BY rv.RAP_DATEVISITE DESC";

    $req = $connexion->prepare($sql);
    $req->bindValue(':codeSecteur', $codeSecteur);
    $req->bindValue(':dateDebut', $dateDebut);
    $req->bindValue(':dateFin', $dateFin);
    if ($praNum) {
        $req->bindValue(':praNum', $praNum);
    }
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function setRapportConsulte($idVisiteur, $idRapport)
{
    $connexion = connexionPDO();
    $sql = "UPDATE rapport_visite SET etat_code = 3 WHERE VIS_MATRICULE = :vis AND RAP_NUM = :num";
    $req = $connexion->prepare($sql);
    $req->bindValue(':vis', $idVisiteur);
    $req->bindValue(':num', $idRapport);
    $req->execute();
}

function getLesMotifs()
{
    $connexion = connexionPDO();
    $req = $connexion->prepare("SELECT * FROM motif ORDER BY MO_Libelle");
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}
?>