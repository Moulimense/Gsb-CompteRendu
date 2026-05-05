<section class="bg-light py-4">
    <div class="container">
        <h1 class="text-center mb-4">Rapports en cours de saisie</h1>

        <div class="alert alert-info text-center">
            Vous avez des rapports de visite en cours de saisie (non validés). 
            Souhaitez-vous reprendre la saisie d'un de ces rapports ou en créer un nouveau ?
        </div>

        <div class="table-responsive mb-4">
            <table class="table table-bordered table-striped table-hover align-middle bg-white">
                <thead class="table-dark">
                    <tr>
                        <th>Date de la visite</th>
                        <th>Praticien</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rapportsEnCours as $rapport): ?>
                        <tr>
                            <td><?= htmlspecialchars($rapport['RAP_DATEVISITE'] ?? '') ?></td>
                            <td><?= htmlspecialchars($rapport['PRA_NOM'] . ' ' . $rapport['PRA_PRENOM']) ?></td>
                            <td class="text-center">
                                <a href="index.php?uc=rapportVisite&action=modifier&idRapport=<?= htmlspecialchars($rapport['RAP_NUM']) ?>" class="btn btn-sm btn-primary">Reprendre la saisie</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="text-center mt-4">
            <a href="index.php?uc=rapportVisite&action=nouveau&forceNouveau=1" class="btn btn-outline-primary">Créer un nouveau rapport (vierge)</a>
            <a href="index.php?uc=rapportVisite&action=liste" class="btn btn-outline-secondary">Annuler</a>
        </div>
    </div>
</section>
