<head>
    <title>Projet GSB</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/boxicon.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/gsb.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<body>
    <nav id="main_nav" class="navbar navbar-expand-lg navbar-light bg-white shadow">
        <div class="menuCont container">
            <a class="navbar-brand h1 my-2" href="index.php?uc=accueil">
                <span class="text-dark h4 fw-bold">Projet</span> <span class="text-info h4 fw-bold">GSB</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbar-toggler-success" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="align-self-center collapse navbar-collapse flex-fill  d-lg-flex justify-content-lg-between"
                id="navbar-toggler-success">
                <div class="flex-fill d-flex justify-content-end">
                    <ul class="nav navbar-nav d-flex justify-content-between mx-xl-5 text-center text-dark">
                        <li class="nav-item ">
                            <a class="nav-link btn-outline-info rounded-pill px-3 fw-bold"
                                href="index.php?uc=accueil">Accueil</a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link btn-outline-info rounded-pill px-3 fw-bold"
                                href="index.php?uc=medicaments&action=formulairemedoc">Médicaments</a>
                        </li>


                        <li class="nav-item ">
                            <a class="nav-link btn-outline-info rounded-pill px-3 fw-bold"
                                href="index.php?uc=connexion&action=profil">Profil</a>
                        </li>

                        <?php
                        if (isset($_SESSION['habilitation'])) {
                            $hab = $_SESSION['habilitation'];
                            $estVisiteur = ($hab == 1);
                            $isResponsable = ($hab == 3);

                            // Menu Rapports de visite
                            $active = (isset($_GET['uc']) && $_GET['uc'] === 'rapportVisite') ? ' active' : '';
                            echo '<li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle btn-outline-info rounded-pill px-3 fw-bold' . $active . '" 
               href="#" id="navbarDropdownRapport" role="button" data-bs-toggle="dropdown" aria-expanded="false">
               Rapports de visite
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdownRapport">';
                            if (!$isResponsable) {
                                echo '<li><a class="dropdown-item" href="index.php?uc=rapportVisite&action=liste">Mes brouillons</a></li>';
                                echo '<li><a class="dropdown-item" href="index.php?uc=rapportVisite&action=nouveau">Créer un nouveau rapport</a></li>';
                            }
                            echo '<li><a class="dropdown-item" href="index.php?uc=rapportVisite&action=recherche">Consulter/Rechercher</a></li>';

                            if (!$estVisiteur) {
                                echo '<li><hr class="dropdown-divider"></li>';
                                echo '<li><a class="dropdown-item" href="index.php?uc=rapportVisite&action=nouveauxRapportsRegion">Nouveaux rapports région</a></li>';
                            }
                            echo '</ul></li>';

                            // Menu Consulter praticiens (accessible à tous)
                            $activePraticienConsult = (isset($_GET['uc']) && $_GET['uc'] === 'praticiens') ? ' active' : '';
                            echo '<li class="nav-item">
                                <a class="nav-link btn-outline-info rounded-pill px-3 fw-bold' . $activePraticienConsult . '" 
                                   href="index.php?uc=praticiens&action=formulaire">Praticiens</a>
                            </li>';

                            // Menu Gérer les praticiens (délégué/responsable uniquement)
                            if (!$estVisiteur) {
                                $activePraticien = (isset($_GET['uc']) && $_GET['uc'] === 'gererPraticien') ? ' active' : '';
                                echo '<li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle btn-outline-info rounded-pill px-3 fw-bold' . $activePraticien . '" 
                   href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                   Gérer les praticiens
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="index.php?uc=gererPraticien&action=liste&filtre=region">Par région</a></li>
                    <li><a class="dropdown-item" href="index.php?uc=gererPraticien&action=liste&filtre=global">Afficher praticiens</a></li>
                </ul>
              </li>';
                            }
                        }
                        ?>
                        <li class="nav-item">
                            <a class="nav-link btn-outline-info rounded-pill px-3 fw-bold"
                                href="index.php?uc=connexion&action=deconnexion"
                                onclick="return confirm('Voulez-vous vraiment vous déconnecter ?');">Déconnexion</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>