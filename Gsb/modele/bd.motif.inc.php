<?php
require_once("bd.inc.php");

function getAllMotifs()
{
    $connexion = connexionPDO();
    $sql = "SELECT MO_Code, MO_Libelle FROM motif ORDER BY MO_Code";
    $req = $connexion->prepare($sql);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}
?>