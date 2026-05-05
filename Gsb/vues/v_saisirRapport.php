<section class="bg-light py-4">
    <div class="container">
        <h1 class="text-center mb-4">Saisir un rapport de visite</h1>

        <?php if (!empty($erreur)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <?php if (!empty($info)): ?>
            <div class="alert alert-info"><?= htmlspecialchars($info) ?></div>
        <?php endif; ?>

        <?php
        $rapport = $rapport ?? [];
        $lesMedicaments = $lesMedicaments ?? [];
        $lesOffres = $lesOffres ?? [];
        ?>



        <form id="formSaisieRapport" method="post" action="index.php?uc=rapportVisite&action=validerSaisie">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge bg-secondary">État :
                    <?php
                    if (isset($rapport['etat_code'])) {
                        if ($rapport['etat_code'] == 2)
                            echo 'À vérifier';
                        elseif ($rapport['etat_code'] == 3)
                            echo 'Validé';
                        else
                            echo 'En cours';
                    } else {
                        echo 'Saisi en cours';
                    }
                    ?></span>
                <a href="index.php?uc=rapportVisite&action=liste" class="btn btn-outline-secondary btn-sm">Retour à la
                    liste</a>
            </div>

            <div class="mb-3">
                <label class="form-label">Praticien :</label>
                <select name="idPraticien" class="form-select" <?= (isset($rapport['etat_code']) && $rapport['etat_code'] == 2) ? 'disabled' : '' ?>>
                    <option value="">-- Sélectionner un praticien --</option>
                    <?php foreach ($lesPraticiens as $praticien): ?>
                        <option value="<?= $praticien['PRA_NUM'] ?>" <?= ($rapport['PRA_NUM'] ?? '') == $praticien['PRA_NUM'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($praticien['PRA_NOM'] . ' ' . $praticien['PRA_PRENOM']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Praticien remplaçant (optionnel) :</label>
                <select name="praticienRemplacant" class="form-select" <?= (isset($rapport['etat_code']) && $rapport['etat_code'] == 2) ? 'disabled' : '' ?>>
                    <option value="">-- Sélectionner un remplaçant --</option>
                    <?php foreach ($lesPraticiens as $praticien): ?>
                        <option value="<?= $praticien['PRA_NUM'] ?>" <?= ($rapport['PRA_NUM_remplacant'] ?? '') == $praticien['PRA_NUM'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($praticien['PRA_NOM'] . ' ' . $praticien['PRA_PRENOM']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Date de visite :</label>
                <input type="date" name="dateVisite" class="form-control" max="<?= date('Y-m-d') ?>"
                    value="<?= $rapport['RAP_DATEVISITE'] ?? '' ?>" <?= (isset($rapport['etat_code']) && $rapport['etat_code'] == 2) ? 'disabled' : '' ?>>
            </div>

            <div class="mb-3">
                <label class="form-label">Motif :</label>
                <select name="motif" id="motifSelect" class="form-select" <?= (isset($rapport['etat_code']) && $rapport['etat_code'] == 2) ? 'disabled' : '' ?>>
                    <option value="">-- Sélectionner un motif --</option>
                    <?php foreach ($lesMotifs as $motif): ?>
                        <option value="<?= $motif['MO_Code'] ?>" <?= (isset($rapport['mo_num']) && $rapport['mo_num'] == $motif['MO_Code']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($motif['MO_Libelle']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3" id="autreMotifDiv">
                <label class="form-label">Précisez le motif :</label>
                <input type="text" name="autreMotif" id="autreMotifInput" class="form-control"
                    value="<?= isset($rapport['RAP_AUTRE']) ? htmlspecialchars($rapport['RAP_AUTRE']) : '' ?>"
                    <?= (isset($rapport['etat_code']) && $rapport['etat_code'] == 2) ? 'disabled' : '' ?>>
            </div>



            <div class="mb-3">
                <label for="coeffConfiance" class="form-label">Coefficient de Confiance (0-100)</label>
                <input type="number" name="coeffConfiance" id="coeffConfiance" class="form-control"
                    value="<?= htmlspecialchars($rapport['PRA_COEFF_CONFIANCE'] ?? '') ?>" min="0" max="100" step="0.1"
                    <?= (isset($rapport['etat_code']) && $rapport['etat_code'] == 2) ? 'disabled' : '' ?>>
            </div>

            <div class="mb-3">
                <label class="form-label">Bilan :</label>
                <textarea name="bilan" class="form-control" rows="5" <?= (isset($rapport['etat_code']) && $rapport['etat_code'] == 2) ? 'disabled' : '' ?>><?= htmlspecialchars($rapport['RAP_BILAN'] ?? '') ?></textarea>
            </div>

            <h4 class="mt-4">Médicaments présentés</h4>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Médicament 1 :</label>
                    <select name="medicamentPresente" class="form-select" <?= (isset($rapport['etat_code']) && $rapport['etat_code'] == 2) ? 'disabled' : '' ?>>
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($lesMedicaments as $medicament): ?>
                            <option value="<?= htmlspecialchars($medicament['MED_DEPOTLEGAL']) ?>"
                                <?= ($rapport['MEDICAMENT_PRESENTE'] ?? '') === $medicament['MED_DEPOTLEGAL'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($medicament['MED_NOMCOMMERCIAL']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Médicament 2 :</label>
                    <select name="medicamentPrescrit" class="form-select" <?= (isset($rapport['etat_code']) && $rapport['etat_code'] == 2) ? 'disabled' : '' ?>>
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($lesMedicaments as $medicament): ?>
                            <option value="<?= htmlspecialchars($medicament['MED_DEPOTLEGAL']) ?>"
                                <?= ($rapport['MEDICAMENT_PRESCRIT'] ?? '') === $medicament['MED_DEPOTLEGAL'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($medicament['MED_NOMCOMMERCIAL']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <h4 class="mt-4">Échantillons offerts (Max 10)</h4>
            <div id="samples-container">
                <?php
                // Display 10 rows
                for ($i = 0; $i < 10; $i++) {
                    $offer = $lesOffres[$i] ?? null;
                    $medVal = $offer['MED_DEPOTLEGAL'] ?? '';
                    $qteVal = $offer['OFF_QTE'] ?? '';
                    ?>

                    <div class="row sample-row mb-2" id="sample-row-<?= $i ?>">
                        <div class="col-8">
                            <label class="form-label">Médicament <?= $i + 1 ?> :</label>
                            <select name="medicamentEchantillon[]" class="form-select" <?= (isset($rapport['etat_code']) && $rapport['etat_code'] == 2) ? 'disabled' : '' ?>>
                                <option value="">-- Sélectionner un échantillon --</option>
                                <?php foreach ($lesMedicaments as $medicament): ?>
                                    <option value="<?= htmlspecialchars($medicament['MED_DEPOTLEGAL']) ?>"
                                        <?= ($medVal === $medicament['MED_DEPOTLEGAL']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($medicament['MED_NOMCOMMERCIAL']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-4">
                            <label class="form-label">Quantité :</label>
                            <select name="quantiteEchantillon[]" class="form-select" <?= (isset($rapport['etat_code']) && $rapport['etat_code'] == 2) ? 'disabled' : '' ?>>
                                <option value="">Qté</option>
                                <?php for ($q = 1; $q <= 10; $q++): ?>
                                    <option value="<?= $q ?>" <?= ($qteVal == $q) ? 'selected' : '' ?>><?= $q ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <?php
                }

                ?>
            </div>





            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="saisieDefinitive" id="saisieDefinitive"
                    <?= (isset($rapport['etat_code']) && $rapport['etat_code'] == 2) ? 'checked disabled' : '' ?>>
                <label class="form-check-label" for="saisieDefinitive">Saisie définitive</label>
            </div>

            <?php if (isset($rapport['RAP_NUM'])): ?>
                <input type="hidden" name="idRapport" value="<?= htmlspecialchars($rapport['RAP_NUM']) ?>">
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </form>
    </div>

    <script>
    document.getElementById('formSaisieRapport').addEventListener('submit', function(e) {
        let med1 = document.querySelector('select[name="medicamentPresente"]').value;
        let med2 = document.querySelector('select[name="medicamentPrescrit"]').value;
        
        // Exception 3-a
        if (!med1 && !med2) {
            if (!confirm("Vous n'avez pas saisi de médicament présenté. Voulez-vous confirmer l'enregistrement sans médicament ?")) {
                e.preventDefault();
                return false;
            }
        }

        // Exception 3-b
        let qtes = document.querySelectorAll('select[name="quantiteEchantillon[]"]');
        let hasSample = false;
        qtes.forEach(function(select) {
            if (select.value && select.value !== "" && select.value !== "Qté") {
                hasSample = true;
            }
        });

        if (!hasSample) {
            if (!confirm("Vous n'avez pas saisi de quantité d'échantillon. Voulez-vous confirmer l'enregistrement sans échantillon ?")) {
                e.preventDefault();
                return false;
            }
        }
    });
    </script>
</section>