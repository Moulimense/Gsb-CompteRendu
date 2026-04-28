<?php
require_once("bd.inc.php");

function getPraticiensByRegion($codeRegion)
{
    $connexion = connexionPDO();
    // Join with departement to filter by region, and LEFT JOIN with posseder/specialite
    $req = $connexion->prepare("
        SELECT p.PRA_NUM, p.PRA_NOM, p.PRA_PRENOM, p.PRA_ADRESSE, p.PRA_CP, p.PRA_VILLE, p.PRA_COEFNOTORIETE, t.TYP_LIBELLE, 
        GROUP_CONCAT(s.SPE_LIBELLE SEPARATOR ', ') as SPE_LIBELLE
        FROM praticien p
        LEFT JOIN type_praticien t ON p.TYP_CODE = t.TYP_CODE
        INNER JOIN departement d ON LEFT(p.PRA_CP, 2) = d.NoDEPT
        LEFT JOIN posseder pos ON p.PRA_NUM = pos.PRA_NUM
        LEFT JOIN specialite s ON pos.SPE_CODE = s.SPE_CODE
        WHERE d.REG_CODE = :codeRegion
        GROUP BY p.PRA_NUM
        ORDER BY p.PRA_NOM ASC
    ");
    $req->bindParam(':codeRegion', $codeRegion, PDO::PARAM_STR);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function getPraticiens()
{
    $connexion = connexionPDO();
    $req = $connexion->prepare("
        SELECT p.PRA_NUM, p.PRA_NOM, p.PRA_PRENOM, p.PRA_ADRESSE, p.PRA_CP, p.PRA_VILLE, p.PRA_COEFNOTORIETE, t.TYP_LIBELLE, 
        GROUP_CONCAT(s.SPE_LIBELLE SEPARATOR ', ') as SPE_LIBELLE
        FROM praticien p
        LEFT JOIN type_praticien t ON p.TYP_CODE = t.TYP_CODE
        LEFT JOIN posseder pos ON p.PRA_NUM = pos.PRA_NUM
        LEFT JOIN specialite s ON pos.SPE_CODE = s.SPE_CODE
        GROUP BY p.PRA_NUM
        ORDER BY p.PRA_NOM ASC
    ");
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function getPraticienByNum($num)
{
    $connexion = connexionPDO();
    // Fetch practitioner details
    $req = $connexion->prepare("
        SELECT p.*
        FROM praticien p
        WHERE p.PRA_NUM = :num
    ");
    $req->bindParam(':num', $num, PDO::PARAM_INT);
    $req->execute();
    $praticien = $req->fetch(PDO::FETCH_ASSOC);

    if ($praticien) {
        // Fetch specialties
        $reqSpe = $connexion->prepare("SELECT SPE_CODE FROM posseder WHERE PRA_NUM = :num");
        $reqSpe->bindParam(':num', $num, PDO::PARAM_INT);
        $reqSpe->execute();
        $specialites = $reqSpe->fetchAll(PDO::FETCH_COLUMN);
        $praticien['SPE_CODES'] = $specialites;
    }

    return $praticien;
}

function getPraticienDetails($num)
{
    $connexion = connexionPDO();
    $req = $connexion->prepare("
        SELECT p.PRA_NUM, p.PRA_NOM, p.PRA_PRENOM, p.PRA_ADRESSE, p.PRA_CP, p.PRA_VILLE, p.PRA_COEFNOTORIETE, 
               t.TYP_LIBELLE, 
               GROUP_CONCAT(s.SPE_LIBELLE SEPARATOR ', ') as SPE_LIBELLE
        FROM praticien p
        LEFT JOIN type_praticien t ON p.TYP_CODE = t.TYP_CODE
        LEFT JOIN posseder pos ON p.PRA_NUM = pos.PRA_NUM
        LEFT JOIN specialite s ON pos.SPE_CODE = s.SPE_CODE
        WHERE p.PRA_NUM = :num
        GROUP BY p.PRA_NUM
    ");
    $req->bindParam(':num', $num, PDO::PARAM_INT);
    $req->execute();
    return $req->fetch(PDO::FETCH_ASSOC);
}

function getTypesPraticien()
{
    $connexion = connexionPDO();
    $req = $connexion->prepare("SELECT * FROM type_praticien ORDER BY TYP_LIBELLE");
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function getSpecialites()
{
    $connexion = connexionPDO();
    $req = $connexion->prepare("SELECT * FROM specialite ORDER BY SPE_LIBELLE");
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function ajouterPraticien($nom, $prenom, $adresse, $cp, $ville, $coef, $typeCode, $speCodes)
{
    $connexion = connexionPDO();

    // Calculate new ID
    $reqId = $connexion->query("SELECT MAX(PRA_NUM) as maxId FROM praticien");
    $resId = $reqId->fetch();
    $newId = $resId['maxId'] + 1;

    $req = $connexion->prepare("
        INSERT INTO praticien (PRA_NUM, PRA_NOM, PRA_PRENOM, PRA_ADRESSE, PRA_CP, PRA_VILLE, PRA_COEFNOTORIETE, TYP_CODE)
        VALUES (:id, :nom, :prenom, :adresse, :cp, :ville, :coef, :typeCode)
    ");
    $req->bindParam(':id', $newId, PDO::PARAM_INT);
    $req->bindParam(':nom', $nom, PDO::PARAM_STR);
    $req->bindParam(':prenom', $prenom, PDO::PARAM_STR);
    $req->bindParam(':adresse', $adresse, PDO::PARAM_STR);
    $req->bindParam(':cp', $cp, PDO::PARAM_STR);
    $req->bindParam(':ville', $ville, PDO::PARAM_STR);
    $req->bindParam(':coef', $coef, PDO::PARAM_STR);
    if (empty($typeCode)) {
        $typeCode = null;
        $req->bindValue(':typeCode', null, PDO::PARAM_NULL);
    } else {
        $req->bindParam(':typeCode', $typeCode, PDO::PARAM_STR);
    }

    $res = $req->execute();

    if ($res && !empty($speCodes) && is_array($speCodes)) {
        $reqSpe = $connexion->prepare("INSERT INTO posseder (PRA_NUM, SPE_CODE, POS_DIPLOME, POS_COEFPRESCRIPTIO) VALUES (:id, :speCode, 'Non défini', 1.0)");
        foreach ($speCodes as $speCode) {
            $reqSpe->bindParam(':id', $newId, PDO::PARAM_INT);
            $reqSpe->bindParam(':speCode', $speCode, PDO::PARAM_STR);
            $reqSpe->execute();
        }
    }

    return $res;
}

function modifierPraticien($num, $nom, $prenom, $adresse, $cp, $ville, $coef, $typeCode, $speCodes)
{
    $connexion = connexionPDO();
    $req = $connexion->prepare("
        UPDATE praticien 
        SET PRA_NOM = :nom, PRA_PRENOM = :prenom, PRA_ADRESSE = :adresse, PRA_CP = :cp, PRA_VILLE = :ville, PRA_COEFNOTORIETE = :coef, TYP_CODE = :typeCode
        WHERE PRA_NUM = :num
    ");
    $req->bindParam(':num', $num, PDO::PARAM_INT);
    $req->bindParam(':nom', $nom, PDO::PARAM_STR);
    $req->bindParam(':prenom', $prenom, PDO::PARAM_STR);
    $req->bindParam(':adresse', $adresse, PDO::PARAM_STR);
    $req->bindParam(':cp', $cp, PDO::PARAM_STR);
    $req->bindParam(':ville', $ville, PDO::PARAM_STR);
    $req->bindParam(':coef', $coef, PDO::PARAM_STR);
    if (empty($typeCode)) {
        $typeCode = null;
        $req->bindValue(':typeCode', null, PDO::PARAM_NULL);
    } else {
        $req->bindParam(':typeCode', $typeCode, PDO::PARAM_STR);
    }

    $res = $req->execute();

    if ($res) {
        // Delete existing specialties
        $reqDel = $connexion->prepare("DELETE FROM posseder WHERE PRA_NUM = :num");
        $reqDel->bindParam(':num', $num, PDO::PARAM_INT);
        $reqDel->execute();

        // Insert new specialties if selected
        if (!empty($speCodes) && is_array($speCodes)) {
            $reqSpe = $connexion->prepare("INSERT INTO posseder (PRA_NUM, SPE_CODE, POS_DIPLOME, POS_COEFPRESCRIPTIO) VALUES (:num, :speCode, 'Non défini', 1.0)");
            foreach ($speCodes as $speCode) {
                $reqSpe->bindParam(':num', $num, PDO::PARAM_INT);
                $reqSpe->bindParam(':speCode', $speCode, PDO::PARAM_STR);
                $reqSpe->execute();
            }
        }
    }

    return $res;
}

function supprimerPraticien($num)
{
    $connexion = connexionPDO();
    try {
        // First delete from posseder (foreign key constraint might not cascade)
        $reqPos = $connexion->prepare("DELETE FROM posseder WHERE PRA_NUM = :num");
        $reqPos->bindParam(':num', $num, PDO::PARAM_INT);
        $reqPos->execute();

        $req = $connexion->prepare("DELETE FROM praticien WHERE PRA_NUM = :num");
        $req->bindParam(':num', $num, PDO::PARAM_INT);
        $req->execute();
        return true;
    } catch (PDOException $e) {
        return false; // Likely constraint violation from rapport_visite
    }
}
?>