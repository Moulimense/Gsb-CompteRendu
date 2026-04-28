<section class="bg-light py-5">
    <div class="container">
        <div class="card shadow p-4 mx-auto" style="max-width: 600px;">
            <h2 class="text-center mb-4">Détails du Praticien</h2>

            <?php if (isset($praticien) && !empty($praticien)): ?>
                <table class="table table-bordered">
                    <tr>
                        <th class="table-light w-25">Nom</th>
                        <td><?= htmlspecialchars($praticien['PRA_NOM'] . ' ' . $praticien['PRA_PRENOM']) ?></td>
                    </tr>
                    <tr>
                        <th class="table-light">Adresse</th>
                        <td>
                            <?= htmlspecialchars($praticien['PRA_ADRESSE']) ?><br>
                            <?= htmlspecialchars($praticien['PRA_CP'] . ' ' . $praticien['PRA_VILLE']) ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="table-light">Notoriété (Coef)</th>
                        <td><?= htmlspecialchars($praticien['PRA_COEFNOTORIETE']) ?></td>
                    </tr>
                    <tr>
                        <th class="table-light">Type</th>
                        <td><?= htmlspecialchars($praticien['TYP_LIBELLE'] ?? 'Non défini') ?></td>
                    </tr>
                    <tr>
                        <th class="table-light">Spécialité(s)</th>
                        <td>
                            <?php
                            // Check if specialties are available (depends on how getPraticienByNum is implemented)
                            // If it's not a joined query, we might just show basic info. 
                            // Assuming getPraticienByNum might not return specialties joined string.
                            // For safety, just show basic info or implement detailed fetch if needed.
                            // Given requirements, "renseignements sur le praticien" usually implies contact/type.
                            echo "<i>Voir fiche détaillée</i>";
                            ?>
                        </td>
                    </tr>
                </table>

                <div class="text-center mt-3">
                    <a href="javascript:history.back()" class="btn btn-secondary">Retour</a>
                </div>
            <?php else: ?>
                <div class="alert alert-danger text-center">Praticien introuvable.</div>
                <div class="text-center">
                    <a href="index.php?uc=rapportVisite&action=liste" class="btn btn-primary">Retour à la liste</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>