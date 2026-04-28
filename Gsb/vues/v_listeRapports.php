<section class="bg-light py-4">
    <div class="container">
        <h2 class="mb-4 text-center">Mes rapports de visite</h2>

        <form action="index.php" method="get" class="mb-4 text-center">
            <input type="hidden" name="uc" value="rapportVisite">
            <input type="hidden" name="action" value="liste">
            <div class="btn-group" role="group" aria-label="Filtre rapports">
                <button type="submit" name="type_rapport" value="mes_rapports"
                    class="btn btn-outline-primary <?= (!isset($_GET['type_rapport']) || $_GET['type_rapport'] == 'mes_rapports') ? 'active' : '' ?>">Mes
                    rapports</button>
                <button type="submit" name="type_rapport" value="region"
                    class="btn btn-outline-primary <?= (isset($_GET['type_rapport']) && $_GET['type_rapport'] == 'region') ? 'active' : '' ?>">
                    <?= $isResponsable ? 'Rapports de mon secteur' : 'Rapports de ma région' ?>
                </button>

                <?php
                $hab = $_SESSION['habilitation'] ?? 1;
                $estVisiteur = ($hab == 1);
                $isResponsable = ($hab == 3);
                ?>

            </div>
        </form>

        <?php if (!empty($erreur)): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <?php if (empty($rapports)): ?>
            <div class="alert alert-info text-center">Aucun rapport trouvé.</div>
        <?php else: ?>
            <table class="table table-bordered table-striped text-center align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>Date</th>
                        <th>Praticien</th>
                        <?php if (isset($_GET['type_rapport']) && $_GET['type_rapport'] == 'region'): ?>
                            <th>Visiteur</th>
                        <?php endif; ?>
                        <th>Motif</th>
                        <th>État</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rapports as $r): ?>
                        <tr class="<?= (isset($r['etat_code']) && $r['etat_code'] == 2) ? 'table-success' : '' ?>">
                            <td><?= htmlspecialchars($r['RAP_DATEVISITE']) ?></td>
                            <td><a href="index.php?uc=gererPraticien&action=consulter&num=<?= $r['PRA_NUM'] ?>"
                                    class="text-decoration-none"><?= htmlspecialchars($r['PRA_NOM'] . ' ' . $r['PRA_PRENOM']) ?></a>
                            </td>
                            <?php if (isset($_GET['type_rapport']) && $_GET['type_rapport'] == 'region'): ?>
                                <td><?= htmlspecialchars($r['VIS_NOM'] . ' ' . $r['VIS_PRENOM']) ?></td>
                            <?php endif; ?>
                            <td><?= htmlspecialchars($r['MOTIF_LIBELLE'] ?? $r['RAP_MOTIF']) ?></td>
                            <td>
                                <?php
                                if (isset($r['etat_code'])) {
                                    if ($r['etat_code'] == 2)
                                        echo '<span class="badge bg-warning text-dark">À vérifier</span>';
                                    elseif ($r['etat_code'] == 3)
                                        echo '<span class="badge bg-success">Validé</span>';
                                    else
                                        echo '<span class="badge bg-secondary">En cours</span>';
                                } else {
                                    echo '<span class="badge bg-secondary">En cours</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <a href="index.php?uc=rapportVisite&action=consulter&idRapport=<?= $r['RAP_NUM'] ?>&idVisiteur=<?= $r['VIS_MATRICULE'] ?? $_SESSION['matricule'] ?>"
                                    class="btn btn-info btn-sm">Consulter</a>
                                <?php if ((!isset($r['etat_code']) || $r['etat_code'] == 1) && !$isResponsable): ?>
                                    <a href="index.php?uc=rapportVisite&action=modifier&idRapport=<?= $r['RAP_NUM'] ?>"
                                        class="btn btn-warning btn-sm">Modifier</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if (!$isResponsable): ?>
            <div class="text-center mt-4">
                <a href="index.php?uc=rapportVisite&action=nouveau" class="btn btn-primary">
                    + Créer un nouveau rapport
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>