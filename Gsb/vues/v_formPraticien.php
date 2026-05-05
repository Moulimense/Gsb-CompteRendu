<section class="bg-light py-4">
    <div class="container">
        <h1 class="text-center mb-4">
            <?php echo ($mode == 'ajouter') ? "Ajouter un praticien" : "Modifier un praticien"; ?></h1>

        <?php if (!empty($erreur)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <?php if (!empty($info)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($info) ?></div>
        <?php endif; ?>

        <?php
        // For form repopulation on error
        $praticien = $praticien ?? [];
        ?>

        <form
            action="index.php?uc=gererPraticien&action=<?php echo ($mode == 'ajouter') ? 'validerAjout' : 'validerModif'; ?>"
            method="post" id="formPraticien">
            <div class="card">
                <div class="card-header">
                    Informations praticien
                </div>
                <div class="card-body">
                    <?php if ($mode == 'modifier' && isset($praticien['PRA_NUM'])) { ?>
                        <input type="hidden" name="num" value="<?php echo $praticien['PRA_NUM']; ?>" />
                    <?php } ?>

                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom : </label>
                        <input type="text" id="nom" name="nom" class="form-control" maxlength="30"
                            value="<?php echo htmlspecialchars($praticien['PRA_NOM'] ?? ''); ?>" />
                    </div>
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom : </label>
                        <input type="text" id="prenom" name="prenom" class="form-control" maxlength="30"
                            value="<?php echo htmlspecialchars($praticien['PRA_PRENOM'] ?? ''); ?>" />
                    </div>
                    <div class="mb-3">
                        <label for="adresse" class="form-label">Adresse : </label>
                        <input type="text" id="adresse" name="adresse" class="form-control" maxlength="50"
                            value="<?php echo htmlspecialchars($praticien['PRA_ADRESSE'] ?? ''); ?>" />
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="cp" class="form-label">CP : </label>
                            <input type="text" id="cp" name="cp" class="form-control" maxlength="5"
                                value="<?php echo htmlspecialchars($praticien['PRA_CP'] ?? ''); ?>" />
                        </div>
                        <div class="col-md-8 mb-3">
                            <label for="ville" class="form-label">Ville : </label>
                            <input type="text" id="ville" name="ville" class="form-control" maxlength="25"
                                value="<?php echo htmlspecialchars($praticien['PRA_VILLE'] ?? ''); ?>" />
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="coef" class="form-label">Coef. Notoriété : </label>
                        <input type="text" id="coef" name="coef" class="form-control"
                            value="<?php echo htmlspecialchars($praticien['PRA_COEFNOTORIETE'] ?? ''); ?>" />
                    </div>
                    <div class="mb-3">
                        <label for="typeCode" class="form-label">Type : </label>
                        <select id="typeCode" name="typeCode" class="form-select">
                            <option value="">Aucun</option>
                            <?php foreach ($lesTypes as $unType) {
                                $selected = (isset($praticien['TYP_CODE']) && $praticien['TYP_CODE'] == $unType['TYP_CODE']) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $unType['TYP_CODE'] ?>" <?php echo $selected; ?>>
                                    <?php echo $unType['TYP_LIBELLE'] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Spécialité(s) : </label>
                        <div class="card p-2" style="max-height: 200px; overflow-y: auto;">
                            <?php foreach ($lesSpecialites as $uneSpe) {
                                $checked = '';
                                if (isset($praticien['SPE_CODES']) && is_array($praticien['SPE_CODES'])) {
                                    if (in_array($uneSpe['SPE_CODE'], $praticien['SPE_CODES'])) {
                                        $checked = 'checked';
                                    }
                                }
                                ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="speCode[]" 
                                           value="<?php echo $uneSpe['SPE_CODE']; ?>" 
                                           id="spe_<?php echo $uneSpe['SPE_CODE']; ?>" 
                                           <?php echo $checked; ?>>
                                    <label class="form-check-label" for="spe_<?php echo $uneSpe['SPE_CODE']; ?>">
                                        <?php echo $uneSpe['SPE_LIBELLE']; ?>
                                    </label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex gap-2">
                    <input type="submit" value="Valider" class="btn btn-primary" />
                    <a href="index.php?uc=gererPraticien&action=liste&filtre=region" class="btn btn-secondary">Annuler</a>
                </div>
            </div>
        </form>
        <script>
            document.getElementById('formPraticien').addEventListener('submit', function(e) {
                var typeCode = document.getElementById('typeCode').value;
                var checkboxes = document.querySelectorAll('input[name="speCode[]"]:checked');

                // Exception 2-a: No type selected
                if (typeCode === "") {
                    if (!confirm("Vous n'avez pas choisi de type de praticien. Voulez-vous confirmer l'enregistrement sans type ?")) {
                        e.preventDefault();
                        return false;
                    }
                }

                // Exception 4-b: No specialties selected
                if (checkboxes.length === 0) {
                    if (!confirm("Vous n'avez pas sélectionné de spécialité. Voulez-vous confirmer l'enregistrement sans spécialité ?")) {
                        e.preventDefault();
                        return false;
                    }
                }
            });
        </script>
    </div>
</section>