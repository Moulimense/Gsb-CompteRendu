<section class="bg-light py-5">
    <div class="container">
        <h2 class="text-center mb-4">Historique des rapports de visite</h2>

        <div class="card shadow p-4 mx-auto mb-5" style="max-width: 800px;">
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

        <?php if (isset($lesRapports)): ?>
            <?php if (empty($lesRapports)): ?>
                <div class="alert alert-info text-center">Aucun rapport trouvé pour ces critères.</div>
            <?php else: ?>
                <div class="table-responsive shadow">
                    <table class="table table-bordered table-striped text-center align-middle mb-0">
                        <thead class="table-primary">
                            <tr>
                                <th>Numéro</th>
                                <th>Date</th>
                                <th>N° Praticien</th>
                                <th>Praticien</th>
                                <th>Ville</th>
                                <th>Motif</th>
                                <th>Médicaments</th>
                                <th>État</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $currentRegion = null;
                            foreach ($lesRapports as $rapport):
                                if ($rapport['REG_NOM'] !== $currentRegion):
                                    $currentRegion = $rapport['REG_NOM'];
                                    ?>
                                    <tr class="table-secondary text-uppercase fw-bold">
                                        <td colspan="9" class="text-start ps-4">Région : <?= htmlspecialchars($currentRegion ?: 'Non définie') ?></td>
                                    </tr>
                                <?php endif; ?>
                                
                                <tr class="<?= (isset($rapport['etat_code']) && $rapport['etat_code'] == 3) ? 'table-success' : ((isset($rapport['etat_code']) && $rapport['etat_code'] == 2) ? 'table-warning' : '') ?>">
                                    <td><?= htmlspecialchars($rapport['RAP_NUM']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($rapport['RAP_DATEVISITE'])) ?></td>
                                    <td><?= htmlspecialchars($rapport['PRA_NUM']) ?></td>
                                    <td><a href="index.php?uc=gererPraticien&action=consulter&num=<?= $rapport['PRA_NUM'] ?>" class="text-decoration-none"><?= htmlspecialchars($rapport['PRA_NOM'] . ' ' . $rapport['PRA_PRENOM']) ?></a></td>
                                    <td><?= htmlspecialchars($rapport['PRA_VILLE']) ?></td>
                                    <td><?= htmlspecialchars($rapport['MOTIF_LIBELLE'] ?? $rapport['RAP_MOTIF']) ?></td>
                                    <td>
                                        <?php
                                        $meds = [];
                                        if (!empty($rapport['med_depotlegal_presente1'])) {
                                            $meds[] = $rapport['med_depotlegal_presente1'];
                                        }
                                        if (!empty($rapport['med_depotlegal_presente2'])) {
                                            $meds[] = $rapport['med_depotlegal_presente2'];
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
                                        <?php if (!isset($rapport['etat_code']) || $rapport['etat_code'] != 2): ?>
                                            <a href="index.php?uc=rapportVisite&action=modifier&idRapport=<?= $rapport['RAP_NUM'] ?>"
                                                class="btn btn-warning btn-sm">Modifier</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
