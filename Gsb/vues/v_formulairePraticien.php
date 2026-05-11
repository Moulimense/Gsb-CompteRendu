<section class="bg-light">
    <div class="container">
        <div class="structure-hero pt-lg-5 pt-4">
            <h1 class="titre text-center">Consulter un praticien</h1>
            <p class="text text-center">
                Sélectionnez un praticien dans la liste déroulante
                pour afficher toutes ses informations détaillées.
            </p>
        </div>
        <div class="row align-items-center justify-content-center">
            <div class="test col-12 col-sm-8 col-lg-6 col-xl-5 col-xxl-4 py-lg-5">
                <img class="img-fluid size-img-page" src="assets/img/praticien.jpg">
            </div>
            <div class="test col-12 col-sm-8 col-lg-6 col-xl-5 col-xxl-4 py-lg-5 py-3">
                <?php if (isset($_SESSION['erreur_praticien']) && $_SESSION['erreur_praticien']) {
                    echo '<p class="alert alert-danger text-center w-100">Un problème est survenu lors de la sélection du praticien</p>';
                    $_SESSION['erreur_praticien'] = false;
                } ?>
                <form action="index.php?uc=praticiens&action=afficher" method="post" class="formulaire-recherche col-12 m-0">
                    <label class="titre-formulaire" for="listepraticien">Praticiens disponibles :</label>
                    <select required name="praticien" id="listepraticien" class="form-select mt-3">
                        <option value class="text-center">- Choisissez un praticien -</option>
                        <?php
                        foreach ($lesPraticiens as $unPraticien) {
                            echo '<option value="' . $unPraticien['PRA_NUM'] . '" class="form-control">' . htmlspecialchars($unPraticien['PRA_NOM'] . ' ' . $unPraticien['PRA_PRENOM'] . ' - ' . $unPraticien['PRA_VILLE']) . '</option>';
                        }
                        ?>
                    </select>
                    <input class="btn btn-info text-light valider" type="submit" value="Afficher les informations">
                </form>
            </div>
        </div>
    </div>
</section>
