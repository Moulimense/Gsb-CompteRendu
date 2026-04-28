<section class="bg-light py-4">
    <div class="container">
        <h2 class="mb-4 text-center">Nouveaux rapports de visite de ma région</h2>

        <div class="mb-3">
            <a href="index.php?uc=rapportVisite&action=liste" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Retour à mes rapports
            </a>
        </div>

        <?php if (empty($rapports)): ?>
            <div class="alert alert-info text-center">Aucun nouveau rapport à consulter pour votre région.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>Visiteur</th>
                            <th>Numéro Rapport</th>
                            <th>Praticien</th>
                            <th>Date Visite</th>
                            <th>Motif</th>
                            <th>Médicaments Présentés</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rapports as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['VIS_NOM'] . ' ' . $r['VIS_PRENOM']) ?></td>
                                <td><?= htmlspecialchars($r['RAP_NUM']) ?></td>
                                <td><?= htmlspecialchars($r['PRA_NUM'] . ' - ' . $r['PRA_NOM'] . ' ' . $r['PRA_PRENOM']) ?></td>
                                <td><?= htmlspecialchars($r['RAP_DATEVISITE']) ?></td>
                                <td><?= htmlspecialchars($r['MOTIF_LIBELLE'] ?? $r['RAP_MOTIF']) ?></td>
                                <td>
                                    <?php
                                    $meds = [];
                                    if (!empty($r['med_depotlegal_presente1'])) {
                                        $meds[] = $r['med_depotlegal_presente1'] . ' (' . ($r['MED1_NOM'] ?? 'Inconnu') . ')';
                                    }
                                    if (!empty($r['med_depotlegal_presente2'])) {
                                        $meds[] = $r['med_depotlegal_presente2'] . ' (' . ($r['MED2_NOM'] ?? 'Inconnu') . ')';
                                    }
                                    echo implode('<br>', array_map('htmlspecialchars', $meds));
                                    ?>
                                </td>
                                <td>
                                    <!-- Pass the visitor matricule so the controller can find the report -->
                                    <a href="index.php?uc=rapportVisite&action=consulter&idRapport=<?= $r['RAP_NUM'] ?>&idVisiteur=<?= urlencode($r['VIS_MATRICULE']) ?>"
                                        class="btn btn-info btn-sm">Consulter</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>