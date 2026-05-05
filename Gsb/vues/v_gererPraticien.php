<section class="bg-light py-4">
    <div class="container">
        <h1 class="text-center mb-4">Gérer les praticiens</h1>

        <?php if (isset($erreur)) { ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($erreur); ?>
            </div>
        <?php } ?>

        <?php if (isset($info)) { ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($info); ?>
            </div>
        <?php } ?>

        <?php
        $hab = $_SESSION['habilitation'] ?? 1;
        $estVisiteurLocal = ($hab == 1);
        ?>

        <?php if ($estVisiteurLocal): ?>
            <!-- Visitor: Dropdown to select a practitioner to consult -->
            <div class="card shadow mb-4 p-4 mx-auto" style="max-width: 700px;">
                <h5 class="mb-3">Sélectionner un praticien à consulter</h5>
                <form action="index.php" method="get" class="row align-items-end">
                    <input type="hidden" name="uc" value="gererPraticien">
                    <input type="hidden" name="action" value="consulter">
                    <div class="col-md-8 mb-2">
                        <label for="selectPraticienConsult" class="form-label">Praticien :</label>
                        <select name="num" id="selectPraticienConsult" class="form-select" required>
                            <option value="">-- Sélectionner un praticien --</option>
                            <?php foreach ($lesPraticiens as $unPraticien): ?>
                                <option value="<?= $unPraticien['PRA_NUM'] ?>">
                                    <?= htmlspecialchars($unPraticien['PRA_NOM'] . ' ' . $unPraticien['PRA_PRENOM'] . ' - ' . $unPraticien['PRA_VILLE']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <button type="submit" class="btn btn-info text-white">Consulter</button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <!-- Delegate: Dropdown to select a practitioner to edit -->
            <div class="card shadow mb-4 p-4 mx-auto" style="max-width: 700px;">
                <h5 class="mb-3">Sélectionner un praticien à modifier</h5>
                <form action="index.php" method="get" class="row align-items-end">
                    <input type="hidden" name="uc" value="gererPraticien">
                    <input type="hidden" name="action" value="modifier">
                    <div class="col-md-8 mb-2">
                        <label for="selectPraticien" class="form-label">Praticien :</label>
                        <select name="num" id="selectPraticien" class="form-select" required>
                            <option value="">-- Sélectionner un praticien --</option>
                            <?php foreach ($lesPraticiens as $unPraticien): ?>
                                <option value="<?= $unPraticien['PRA_NUM'] ?>">
                                    <?= htmlspecialchars($unPraticien['PRA_NOM'] . ' ' . $unPraticien['PRA_PRENOM'] . ' - ' . $unPraticien['PRA_VILLE']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Modifier</button>
                        <a href="index.php?uc=gererPraticien&action=ajouter" class="btn btn-success">+ Nouveau</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                Liste des praticiens
                <?php echo (isset($filtre) && $filtre == 'global') ? "" : "(De votre région)"; ?>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Ville</th>
                            <th>Type</th>
                            <th>Spécialité(s)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lesPraticiens as $unPraticien) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($unPraticien['PRA_NOM']); ?></td>
                                <td><?php echo htmlspecialchars($unPraticien['PRA_PRENOM']); ?></td>
                                <td><?php echo htmlspecialchars($unPraticien['PRA_VILLE']); ?></td>
                                <td><?php echo !empty($unPraticien['TYP_LIBELLE']) ? htmlspecialchars($unPraticien['TYP_LIBELLE']) : '-'; ?>
                                </td>
                                <td><?php echo !empty($unPraticien['SPE_LIBELLE']) ? htmlspecialchars($unPraticien['SPE_LIBELLE']) : '-'; ?>
                                </td>
                                <td>
                                    <a href="index.php?uc=gererPraticien&action=consulter&num=<?php echo $unPraticien['PRA_NUM']; ?>"
                                        class="btn btn-info btn-sm text-white">Consulter</a>
                                    <?php if (!$estVisiteurLocal): ?>
                                        <a href="index.php?uc=gererPraticien&action=modifier&num=<?php echo $unPraticien['PRA_NUM']; ?>"
                                            class="btn btn-primary btn-sm">Modifier</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>