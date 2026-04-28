<?php
require_once 'bd.inc.php';

/**
 * Returns statistics for a specific visitor
 */
function getStatsVisiteur($matricule, $dateDebut = null, $dateFin = null)
{
    try {
        $monPdo = connexionPDO();
        $sql = "SELECT count(*) as nb_rapports, 
                       MIN(RAP_DATEVISITE) as premiere_visite, 
                       MAX(RAP_DATEVISITE) as derniere_visite 
                FROM rapport_visite 
                WHERE VIS_MATRICULE = :matricule";

        if ($dateDebut)
            $sql .= " AND RAP_DATEVISITE >= :dateDebut";
        if ($dateFin)
            $sql .= " AND RAP_DATEVISITE <= :dateFin";

        $req = $monPdo->prepare($sql);
        $req->bindParam(':matricule', $matricule, PDO::PARAM_STR);
        if ($dateDebut)
            $req->bindParam(':dateDebut', $dateDebut, PDO::PARAM_STR);
        if ($dateFin)
            $req->bindParam(':dateFin', $dateFin, PDO::PARAM_STR);

        $req->execute();
        return $req->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return null;
    }
}

/**
 * Returns reports history for a visitor (last 3 years)
 */
function getHistoriqueVisiteur($matricule)
{
    try {
        $monPdo = connexionPDO();
        $dateLimit = date('Y-m-d', strtotime('-3 years'));

        $sql = "SELECT DATE_FORMAT(RAP_DATEVISITE, '%Y-%m') as mois, count(*) as total 
                FROM rapport_visite 
                WHERE VIS_MATRICULE = :matricule 
                AND RAP_DATEVISITE >= :dateLimit
                GROUP BY mois 
                ORDER BY mois DESC";

        $req = $monPdo->prepare($sql);
        $req->bindParam(':matricule', $matricule, PDO::PARAM_STR);
        $req->bindParam(':dateLimit', $dateLimit, PDO::PARAM_STR);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Returns global stats for a region (Delegue Regional)
 */
function getStatsRegion($regCode, $dateDebut = null, $dateFin = null)
{
    try {
        $monPdo = connexionPDO();
        // Link rapport -> visiteur -> region
        // Actually reports are linked to visitors who are linked to regions
        // But the visitor might have changed region? The user requirement says "activity of visitors attached to his region".
        // We will filter by the visitor's CURRENT region (from collaborateur/visiteur table).

        $sql = "SELECT v.VIS_NOM, v.VIS_PRENOM, count(r.RAP_NUM) as nb_visites 
                FROM rapport_visite r
                INNER JOIN visiteur v ON r.VIS_MATRICULE = v.VIS_MATRICULE
                WHERE v.REG_CODE = :regCode";

        if ($dateDebut)
            $sql .= " AND r.RAP_DATEVISITE >= :dateDebut";
        if ($dateFin)
            $sql .= " AND r.RAP_DATEVISITE <= :dateFin";

        $sql .= " GROUP BY v.VIS_MATRICULE ORDER BY nb_visites DESC";

        $req = $monPdo->prepare($sql);
        $req->bindParam(':regCode', $regCode, PDO::PARAM_STR);
        if ($dateDebut)
            $req->bindParam(':dateDebut', $dateDebut, PDO::PARAM_STR);
        if ($dateFin)
            $req->bindParam(':dateFin', $dateFin, PDO::PARAM_STR);

        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Returns global stats for a sector (Responsable Secteur)
 */
function getStatsSecteurGlobal($secCode, $dateDebut = null, $dateFin = null)
{
    try {
        $monPdo = connexionPDO();
        // Stats aggregated by Region
        $sql = "SELECT reg.REG_NOM, count(r.RAP_NUM) as nb_visites
                FROM rapport_visite r
                INNER JOIN visiteur v ON r.VIS_MATRICULE = v.VIS_MATRICULE
                INNER JOIN region reg ON v.REG_CODE = reg.REG_CODE
                WHERE reg.SEC_CODE = :secCode";

        if ($dateDebut)
            $sql .= " AND r.RAP_DATEVISITE >= :dateDebut";
        if ($dateFin)
            $sql .= " AND r.RAP_DATEVISITE <= :dateFin";

        $sql .= " GROUP BY reg.REG_CODE ORDER BY nb_visites DESC";

        $req = $monPdo->prepare($sql);
        $req->bindParam(':secCode', $secCode, PDO::PARAM_STR);
        if ($dateDebut)
            $req->bindParam(':dateDebut', $dateDebut, PDO::PARAM_STR);
        if ($dateFin)
            $req->bindParam(':dateFin', $dateFin, PDO::PARAM_STR);

        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Returns detailed stats for a sector (Sample/Echantillon counts)
 */
function getStatsEchantillons($secCode, $dateDebut = null, $dateFin = null)
{
    try {
        $monPdo = connexionPDO();
        $sql = "SELECT m.MED_NOMCOMMERCIAL, SUM(o.OFF_QTE) as total_off offert
                FROM offrir o
                INNER JOIN rapport_visite r ON o.RAP_NUM = r.RAP_NUM AND o.VIS_MATRICULE = r.VIS_MATRICULE
                INNER JOIN visiteur v ON r.VIS_MATRICULE = v.VIS_MATRICULE
                INNER JOIN region reg ON v.REG_CODE = reg.REG_CODE
                INNER JOIN medicament m ON o.MED_DEPOTLEGAL = m.MED_DEPOTLEGAL
                WHERE reg.SEC_CODE = :secCode";

        if ($dateDebut)
            $sql .= " AND r.RAP_DATEVISITE >= :dateDebut";
        if ($dateFin)
            $sql .= " AND r.RAP_DATEVISITE <= :dateFin";

        $sql .= " GROUP BY m.MED_DEPOTLEGAL ORDER BY total_off DESC";

        $req = $monPdo->prepare($sql);
        $req->bindParam(':secCode', $secCode, PDO::PARAM_STR);
        if ($dateDebut)
            $req->bindParam(':dateDebut', $dateDebut, PDO::PARAM_STR);
        if ($dateFin)
            $req->bindParam(':dateFin', $dateFin, PDO::PARAM_STR);

        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}
?>