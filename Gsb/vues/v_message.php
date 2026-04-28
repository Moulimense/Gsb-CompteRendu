<section class="bg-light py-5">
    <div class="container text-center">
        <div class="card shadow-sm p-4 mx-auto" style="max-width: 600px;">
            <h2 class="mb-3 text-success">Confirmation</h2>
            <p class="fs-5"><?= htmlspecialchars($message) ?></p>

            <a href="index.php?uc=rapportVisite&action=nouveau" class="btn btn-primary mt-3 me-2">
                Saisir un nouveau rapport
            </a>
            <a href="index.php?uc=rapportVisite&action=liste" class="btn btn-outline-primary mt-3 me-2">
                Voir mes rapports
            </a>
            <a href="index.php?uc=accueil" class="btn btn-outline-secondary mt-3">
                Retour à l’accueil
            </a>
        </div>
    </div>
</section>
