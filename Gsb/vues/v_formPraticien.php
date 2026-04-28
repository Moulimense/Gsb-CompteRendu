<section class="bg-light py-4">
    <div class="container">
        <h1 class="text-center mb-4">
            <?php echo ($mode == 'ajouter') ? "Ajouter un praticien" : "Modifier un praticien"; ?></h1>

        <form
            action="index.php?uc=gererPraticien&action=<?php echo ($mode == 'ajouter') ? 'validerAjout' : 'validerModif'; ?>"
            method="post" onsubmit="return validerForm();">
            <div class="card">
                <div class="card-header">
                    Informations praticien
                </div>
                <div class="card-body">
                    <?php if ($mode == 'modifier') { ?>
                        <input type="hidden" name="num" value="<?php echo $praticien['PRA_NUM']; ?>" />
                    <?php } ?>

                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom : </label>
                        <input type="text" id="nom" name="nom" class="form-control" maxlength="30"
                            value="<?php echo ($mode == 'modifier') ? $praticien['PRA_NOM'] : ''; ?>" required />
                    </div>
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom : </label>
                        <input type="text" id="prenom" name="prenom" class="form-control" maxlength="30"
                            value="<?php echo ($mode == 'modifier') ? $praticien['PRA_PRENOM'] : ''; ?>" required />
                    </div>
                    <div class="mb-3">
                        <label for="adresse" class="form-label">Adresse : </label>
                        <input type="text" id="adresse" name="adresse" class="form-control" maxlength="50"
                            value="<?php echo ($mode == 'modifier') ? $praticien['PRA_ADRESSE'] : ''; ?>" required />
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="cp" class="form-label">CP : </label>
                            <input type="text" id="cp" name="cp" class="form-control" maxlength="5"
                                value="<?php echo ($mode == 'modifier') ? $praticien['PRA_CP'] : ''; ?>" required />
                        </div>
                        <div class="col-md-8 mb-3">
                            <label for="ville" class="form-label">Ville : </label>
                            <input type="text" id="ville" name="ville" class="form-control" maxlength="25"
                                value="<?php echo ($mode == 'modifier') ? $praticien['PRA_VILLE'] : ''; ?>" required />
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="coef" class="form-label">Coef. Notoriété : </label>
                        <input type="text" id="coef" name="coef" class="form-control"
                            value="<?php echo ($mode == 'modifier') ? $praticien['PRA_COEFNOTORIETE'] : ''; ?>"
                            required />
                    </div>
                    <div class="mb-3">
                        <label for="typeCode" class="form-label">Type : </label>
                        <select id="typeCode" name="typeCode" class="form-select">
                            <option value="">Aucun</option>
                            <?php foreach ($lesTypes as $unType) {
                                $selected = ($mode == 'modifier' && $praticien['TYP_CODE'] == $unType['TYP_CODE']) ? 'selected' : '';
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
                                if ($mode == 'modifier' && isset($praticien['SPE_CODES']) && is_array($praticien['SPE_CODES'])) {
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
                    <input type="button" value="Annuler" class="btn btn-secondary" onclick="history.back()" />
                </div>
            </div>
        </form>
        <script>
            function validerForm() {
                var type = document.getElementById('typeCode').value;
                var checkboxes = document.querySelectorAll('input[name="speCode[]"]:checked');
                
                if (type == "" || checkboxes.length === 0) {
                    return confirm("Vous n'avez pas sélectionné de type ou de spécialité. Voulez-vous vraiment continuer ?");
                }
                return true;
            }
        </script>
    </div>
</section>