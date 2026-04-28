<section class="bg-light py-4">
    <div class="container">
        <h1 class="text-center mb-4">Gérer les praticiens</h1>

        <?php if (isset($erreur)) { ?>
            <div class="alert alert-danger">
                <?php echo $erreur; ?>
            </div>
        <?php } ?>

        <?php if (!isset($_SESSION['habilitation']) || $_SESSION['habilitation'] !== 'Visiteur'): ?>
            <div class="mb-4 text-center">
                <a href="index.php?uc=gererPraticien&action=ajouter" class="btn btn-success" title="Ajouter un praticien">
                    <i class="fa fa-plus"></i> Ajouter un praticien
                </a>
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
                                <td><?php echo $unPraticien['PRA_NOM']; ?></td>
                                <td><?php echo $unPraticien['PRA_PRENOM']; ?></td>
                                <td><?php echo $unPraticien['PRA_VILLE']; ?></td>
                                <td><?php echo !empty($unPraticien['TYP_LIBELLE']) ? $unPraticien['TYP_LIBELLE'] : '-'; ?>
                                </td>
                                <td><?php echo !empty($unPraticien['SPE_LIBELLE']) ? $unPraticien['SPE_LIBELLE'] : '-'; ?>
                                </td>
                                <td>
                                    <a href="index.php?uc=gererPraticien&action=consulter&num=<?php echo $unPraticien['PRA_NUM']; ?>"
                                        class="btn btn-info btn-sm text-white">Consulter</a>
                                    <?php if (!isset($_SESSION['habilitation']) || $_SESSION['habilitation'] !== 'Visiteur'): ?>
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