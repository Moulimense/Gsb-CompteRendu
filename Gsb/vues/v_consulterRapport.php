<section class="bg-light py-5">
    <div class="container">
        <div class="card shadow p-4 mx-auto" style="max-width: 700px;">
            <h2 class="text-center mb-4">Consultation du rapport</h2>

            <div class="card mb-3">
                <div class="card-header">
                    Praticien :
                    <strong><a href="index.php?uc=gererPraticien&action=consulter&num=<?= $rapport['PRA_NUM'] ?>"
                            class="text-decoration-none"><?= htmlspecialchars($rapport['PRA_NOM'] . ' ' . $rapport['PRA_PRENOM']) ?></a></strong>
                    <button class="btn btn-sm btn-outline-primary float-end" type="button" data-bs-toggle="collapse"
                        data-bs-target="#praticienDetails">
                        Voir détails
                    </button>
                </div>
                <div class="collapse" id="praticienDetails">
                    <div class="card-body">
                        <p><strong>Numéro :</strong> <?= htmlspecialchars($rapport['PRA_NUM']) ?></p>
                        <p><strong>Adresse :</strong>
                            <?= htmlspecialchars($rapport['PRA_ADRESSE'] ?? 'Non renseigné') ?></p>
                        <p><strong>Ville :</strong>
                            <?= htmlspecialchars(($rapport['PRA_CP'] ?? '') . ' ' . ($rapport['PRA_VILLE'] ?? '')) ?>
                        </p>
                        <p><strong>Coef. Notoriété :</strong>
                            <?= htmlspecialchars($rapport['PRA_COEFNOTORIETE'] ?? 'N/A') ?></p>
                        <a href="index.php?uc=gererPraticien&action=consulter&num=<?= $rapport['PRA_NUM'] ?>"
                            class="btn btn-link">Page complète du praticien</a>
                    </div>
                </div>
            </div>

            <?php if (!empty($rapport['PRA_NUM_remplacant'])): ?>
                <p><strong>Remplaçant :</strong>
                    <?= htmlspecialchars(($rapport['REMP_NOM'] ?? '') . ' ' . ($rapport['REMP_PRENOM'] ?? '')) ?></p>
            <?php endif; ?>

            <div class="mb-3">
                <p><strong>Date de visite :</strong> <?= htmlspecialchars($rapport['RAP_DATEVISITE']) ?></p>
                <p><strong>Motif :</strong>
                    <?= htmlspecialchars($rapport['MOTIF_LIBELLE'] ?? $rapport['RAP_MOTIF']) ?><?= (!empty($rapport['RAP_AUTRE']) ? ' : ' . htmlspecialchars($rapport['RAP_AUTRE']) : '') ?>
                </p>
                <?php if (isset($rapport['PRA_COEFF_CONFIANCE']) && $rapport['PRA_COEFF_CONFIANCE'] !== null): ?>
                    <p><strong>Coefficient de Confiance :</strong> <?= htmlspecialchars($rapport['PRA_COEFF_CONFIANCE']) ?>
                        %</p>
                <?php endif; ?>
                <p><strong>Bilan :</strong><br><?= nl2br(htmlspecialchars($rapport['RAP_BILAN'])) ?></p>
                <p><strong>État :</strong>
                    <?php
                    $code = $rapport['etat_code'] ?? 1;
                    if ($code == 3) {
                        echo '<span class="badge bg-success">Validé</span>';
                    } elseif ($code == 2) {
                        echo '<span class="badge bg-warning text-dark">À vérifier</span>';
                    } else {
                        echo '<span class="badge bg-secondary">En cours</span>';
                    }
                    ?>
                </p>
            </div>

            <h4>Médicaments présentés</h4>
            <?php
            $meds = [
                ['name' => $rapport['MED1_NOM'] ?? null, 'id' => $rapport['MEDICAMENT_PRESENTE'], 'comp' => $rapport['MED1_COMP'] ?? '', 'eff' => $rapport['MED1_EFFETS'] ?? '', 'ci' => $rapport['MED1_CONTREINDIC'] ?? ''],
                ['name' => $rapport['MED2_NOM'] ?? null, 'id' => $rapport['MEDICAMENT_PRESCRIT'], 'comp' => $rapport['MED2_COMP'] ?? '', 'eff' => $rapport['MED2_EFFETS'] ?? '', 'ci' => $rapport['MED2_CONTREINDIC'] ?? '']
            ];
            foreach ($meds as $k => $m):
                if ($m['id']):
                    ?>
                    <div class="card mb-2">
                        <div class="card-body">
                            <a href="index.php?uc=medicaments&action=affichermedoc&medicament=<?= htmlspecialchars($m['id']) ?>"
                                class="text-decoration-none fw-bold h5">
                                <?= htmlspecialchars($m['name'] ?? $m['id']) ?>
                            </a>
                        </div>
                    </div>
                    <?php
                endif;
            endforeach;
            ?>

            <?php if (!empty($lesOffres)): ?>
                <h4>Échantillons offerts</h4>
                <?php foreach ($lesOffres as $offre): ?>
                    <div class="card mb-2">
                        <div class="card-body">
                            <a href="index.php?uc=medicaments&action=affichermedoc&medicament=<?= htmlspecialchars($offre['MED_DEPOTLEGAL']) ?>"
                                class="text-decoration-none fw-bold h5">
                                <?= htmlspecialchars($offre['MED_NOMCOMMERCIAL']) ?>
                                <?php if (!empty($offre['OFF_QTE'])): ?> (Qté :
                                    <?= htmlspecialchars($offre['OFF_QTE']) ?>)<?php endif; ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="text-center mt-4">
                <a href="index.php?uc=rapportVisite&action=liste" class="btn btn-secondary">Retour à la liste</a>
            </div>
        </div>
    </div>
</section>