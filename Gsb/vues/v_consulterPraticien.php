<section class="bg-light py-4">
    <div class="container">
        <h1 class="text-center mb-4">Détails du praticien</h1>

        <div class="card">
            <div class="card-header">
                Informations : <?php echo $praticien['PRA_NOM'] . ' ' . $praticien['PRA_PRENOM']; ?>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Numéro</th>
                        <td><?php echo $praticien['PRA_NUM']; ?></td>
                    </tr>
                    <tr>
                        <th>Nom</th>
                        <td><?php echo $praticien['PRA_NOM']; ?></td>
                    </tr>
                    <tr>
                        <th>Prénom</th>
                        <td><?php echo $praticien['PRA_PRENOM']; ?></td>
                    </tr>
                    <tr>
                        <th>Adresse</th>
                        <td><?php echo $praticien['PRA_ADRESSE']; ?></td>
                    </tr>
                    <tr>
                        <th>Code Postal</th>
                        <td><?php echo $praticien['PRA_CP']; ?></td>
                    </tr>
                    <tr>
                        <th>Ville</th>
                        <td><?php echo $praticien['PRA_VILLE']; ?></td>
                    </tr>
                    <tr>
                        <th>Coefficient de notoriété</th>
                        <td><?php echo $praticien['PRA_COEFNOTORIETE']; ?></td>
                    </tr>
                    <tr>
                        <th>Type de praticien</th>
                        <td><?php echo $praticien['TYP_LIBELLE']; ?></td>
                    </tr>
                    <tr>
                        <th>Spécialité(s)</th>
                        <td><?php echo !empty($praticien['SPE_LIBELLE']) ? $praticien['SPE_LIBELLE'] : 'Aucune'; ?></td>
                    </tr>
                </table>

                <div class="mt-3 text-center">
                    <a href="index.php?uc=gererPraticien&action=liste" class="btn btn-secondary">Retour à la liste</a>
                </div>
            </div>
        </div>
    </div>
</section>
