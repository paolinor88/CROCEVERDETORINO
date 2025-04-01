<nav class="navbar navbar-expand-lg navbar-cv shadow-sm mb-4">
    <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
        <div class="d-flex align-items-center flex-grow-1">

            <?php
            $page = basename($_SERVER['PHP_SELF']);
            switch ($page) {
                case 'gestione_corsi.php':
                    $breadcrumbLabel = 'Gestione Corsi';
                    break;
                case 'gestisci_corso.php':
                    $breadcrumbLabel = 'Gestione Corso';
                    break;
                case 'gestisci_edizioni.php':
                    $breadcrumbLabel = 'Gestione Edizioni';
                    break;
                case 'gestisci_edizione.php':
                    $breadcrumbLabel = 'Gestione Edizione';
                    break;
                case 'gestione_domande.php':
                    $breadcrumbLabel = 'Gestione Test';
                    break;
                case 'gestione_discenti.php':
                    $breadcrumbLabel = 'Gestione Discenti';
                    break;
                case 'lezioni.php':
                    $breadcrumbLabel = 'FAD';
                    break;
                default:
                    $breadcrumbLabel = '';
            }
            ?>
            <nav class="navbar-breadcrumb ms-3">
                <!-- Desktop breadcrumb completo -->
                <ol class="breadcrumb mb-0 d-none d-sm-flex">
                    <li class="breadcrumb-item">
                        <a href="/strumenti/index.php"><i class="fas fa-home"></i></a>
                    </li>
                    <?php if ($page !== 'lezioni.php'): ?>
                        <li class="breadcrumb-item">
                            <a href="area_formatori.php">Area Formatori</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $breadcrumbLabel ?></li>
                    <?php else: ?>
                        <li class="breadcrumb-item">
                            <a href="corsi.php">Elenco corsi</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $breadcrumbLabel ?></li>
                    <?php endif; ?>
                </ol>

                <!-- Mobile breadcrumb accorciato -->
                <ol class="breadcrumb mb-0 d-sm-none">
                    <?php if ($page !== 'lezioni.php'): ?>
                        <li class="breadcrumb-item">
                            <a href="area_formatori.php"><i class="fas fa-arrow-left me-1"></i>Area Formatori</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $breadcrumbLabel ?></li>
                    <?php else: ?>
                        <li class="breadcrumb-item">
                            <a href="corsi.php"><i class="fas fa-arrow-left me-1"></i> Elenco corsi</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $breadcrumbLabel ?></li>
                    <?php endif; ?>
                </ol>
            </nav>
        </div>

        <div class="d-flex align-items-center">
            <?php if (isset($_SESSION['NomeOperatore'])): ?>
                <span class="navbar-text me-2 d-none d-sm-inline">
                    <i class="fas fa-user-circle me-1"></i> <?= $_SESSION['NomeOperatore']; ?>
                </span>
                <a href="../logout.php" class="btn btn-outline-light btn-icon" title="Logout" data-bs-toggle="tooltip">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            <?php else: ?>
                <a href="#" data-bs-toggle="modal" data-bs-target="#modal3" class="btn btn-sm btn-outline-light">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
