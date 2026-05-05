<section class="bg-light py-5">
    <div class="container">
        <h2 class="text-center mb-4">Historique des rapports de visite</h2>

        <?php if (!empty($erreur)): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <div class="card shadow p-4 mx-auto mb-5" style="max-width: 900px;">
            <form action="index.php?uc=rapportVisite&action=recherche" method="post">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="dateDebut" class="form-label">Date Début</label>
                        <input type="date" name="dateDebut" id="dateDebut" class="form-control" max="<?= date('Y-m-d') ?>"
                            value="<?= htmlspecialchars($dateDebut ?? '') ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="dateFin" class="form-label">Date Fin</label>
                        <input type="date" name="dateFin" id="dateFin" class="form-control" max="<?= date('Y-m-d') ?>"
                            value="<?= htmlspecialchars($dateFin ?? '') ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="praticien" class="form-label">Praticien (Visité)</label>
                        <select name="praNum" id="praticien" class="form-select">
                            <option value="">Tous les praticiens</option>
                            <?php foreach ($lesPraticiens as $praticien): ?>
                                <option value="<?= $praticien['PRA_NUM'] ?>"
                                    <?= (isset($praNum) && $praNum == $praticien['PRA_NUM']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($praticien['PRA_NOM'] . ' ' . $praticien['PRA_PRENOM']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if (!$estVisiteur && isset($lesVisiteurs)): ?>
                        <div class="col-md-4 mb-3">
                            <label for="visiteur" class="form-label">Visiteur</label>
                            <select name="visMatricule" id="visiteur" class="form-select">
                                <option value="">Tous les visiteurs</option>
                                <?php foreach ($lesVisiteurs as $vis): ?>
                                    <option value="<?= htmlspecialchars($vis['COL_MATRICULE']) ?>"
                                        <?= (isset($visMatricule) && $visMatricule == $vis['COL_MATRICULE']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($vis['COL_NOM'] . ' ' . $vis['COL_PRENOM']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($isResponsable) && $isResponsable): ?>
                        <div class="col-md-4 mb-3">
                            <label for="filtreType" class="form-label">Zone</label>
                            <select name="filtreType" id="filtreType" class="form-select">
                                <option value="region" <?= (isset($filtreType) && $filtreType == 'region') ? 'selected' : '' ?>>Ma Région</option>
                                <option value="secteur" <?= (isset($filtreType) && $filtreType == 'secteur') ? 'selected' : '' ?>>Mon Secteur</option>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                </div>
            </form>
        </div>

        <?php if (isset($lesRapports) && !empty($lesRapports)): ?>
            <div class="table-responsive shadow">
                <table class="table table-bordered table-striped text-center align-middle mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>Numéro</th>
                            <?php if (!$estVisiteur): ?>
                                <th>Visiteur</th>
                            <?php endif; ?>
                            <th>Date</th>
                            <th>N° Praticien</th>
                            <th>Praticien</th>
                            <th>Motif</th>
                            <th>Médicaments présentés</th>
                            <th>État</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lesRapports as $rapport): ?>
                            <tr class="<?= (isset($rapport['etat_code']) && $rapport['etat_code'] == 3) ? 'table-success' : ((isset($rapport['etat_code']) && $rapport['etat_code'] == 2) ? 'table-warning' : '') ?>">
                                <td><?= htmlspecialchars($rapport['RAP_NUM']) ?></td>
                                <?php if (!$estVisiteur): ?>
                                    <td><?= htmlspecialchars(($rapport['VIS_NOM'] ?? '') . ' ' . ($rapport['VIS_PRENOM'] ?? '')) ?></td>
                                <?php endif; ?>
                                <td><?= date('d/m/Y', strtotime($rapport['RAP_DATEVISITE'])) ?></td>
                                <td><?= htmlspecialchars($rapport['PRA_NUM']) ?></td>
                                <td><a href="index.php?uc=gererPraticien&action=consulter&num=<?= $rapport['PRA_NUM'] ?>" class="text-decoration-none"><?= htmlspecialchars($rapport['PRA_NOM'] . ' ' . $rapport['PRA_PRENOM']) ?></a></td>
                                <td><?= htmlspecialchars($rapport['MOTIF_LIBELLE'] ?? $rapport['RAP_MOTIF']) ?></td>
                                <td>
                                    <?php
                                    $meds = [];
                                    if (!empty($rapport['med_depotlegal_presente1'])) {
                                        $meds[] = $rapport['med_depotlegal_presente1'] . (!empty($rapport['MED1_NOM']) ? ' ('.$rapport['MED1_NOM'].')' : '');
                                    }
                                    if (!empty($rapport['med_depotlegal_presente2'])) {
                                        $meds[] = $rapport['med_depotlegal_presente2'] . (!empty($rapport['MED2_NOM']) ? ' ('.$rapport['MED2_NOM'].')' : '');
                                    }
                                    echo implode(', ', array_map('htmlspecialchars', $meds));
                                    ?>
                                </td>
                                <td><strong><?php 
                                    if (isset($rapport['etat_code'])) {
                                        if ($rapport['etat_code'] == 3) echo 'Validé';
                                        elseif ($rapport['etat_code'] == 2) echo 'À valider';
                                        else echo 'En cours';
                                    } else {
                                        echo 'Inconnu';
                                    }
                                ?></strong></td>
                                <td>
                                    <a href="index.php?uc=rapportVisite&action=consulter&idRapport=<?= $rapport['RAP_NUM'] ?>&idVisiteur=<?= $rapport['VIS_MATRICULE'] ?? $_SESSION['matricule'] ?>"
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
