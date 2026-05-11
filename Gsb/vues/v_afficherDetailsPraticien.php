<section class="bg-light">
    <div class="container">
        <div class="structure-hero pt-lg-5 pt-4">
            <h1 class="titre text-center">Informations du praticien <span class="carac"><?php echo htmlspecialchars($praticien['PRA_NOM'] . ' ' . $praticien['PRA_PRENOM']); ?></span></h1>
        </div>
        <div class="row align-items-center justify-content-center">
            <div class="test col-12 col-sm-8 col-lg-6 col-xl-5 col-xxl-4 py-lg-5">
                <img class="img-fluid" src="assets/img/praticien.jpg">
            </div>
            <div class="test col-12 col-sm-8 col-lg-6 col-xl-5 col-xxl-4 py-lg-5 py-3">
                <div class="formulaire">
                    <p><span class="carac">Numéro</span> : <?php echo htmlspecialchars($praticien['PRA_NUM']); ?></p>
                    <p><span class="carac">Nom</span> : <?php echo htmlspecialchars($praticien['PRA_NOM']); ?></p>
                    <p><span class="carac">Prénom</span> : <?php echo htmlspecialchars($praticien['PRA_PRENOM']); ?></p>
                    <p><span class="carac">Adresse</span> : <?php echo htmlspecialchars($praticien['PRA_ADRESSE']); ?></p>
                    <p><span class="carac">Code Postal</span> : <?php echo htmlspecialchars($praticien['PRA_CP']); ?></p>
                    <p><span class="carac">Ville</span> : <?php echo htmlspecialchars($praticien['PRA_VILLE']); ?></p>
                    <p><span class="carac">Coefficient de notoriété</span> : <?php echo htmlspecialchars($praticien['PRA_COEFNOTORIETE']); ?></p>
                    <p><span class="carac">Type de praticien</span> : <?php echo htmlspecialchars($praticien['TYP_LIBELLE'] ?? 'Non défini'); ?></p>
                    <p><span class="carac">Spécialité(s)</span> : <?php echo !empty($praticien['SPE_LIBELLE']) ? htmlspecialchars($praticien['SPE_LIBELLE']) : 'Aucune'; ?></p>
                    <input class="btn btn-info text-light valider col-6 col-sm-5 col-md-4 col-lg-3" type="button" onclick="history.go(-1)" value="Retour">
                </div>
            </div>
        </div>
    </div>
</section>
