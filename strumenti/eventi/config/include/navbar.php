<nav class="navbar navbar-expand-lg navbar-cv shadow-sm mb-4">
    <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
        <div class="d-flex align-items-center flex-grow-1">

            <?php
            $page = basename($_SERVER['PHP_SELF']);
            switch ($page) {
                case 'anagrafica2.php':
                    $breadcrumbLabel = 'Anagrafica Autisti';
                    break;
                case 'autocertificazioni.php':
                    $breadcrumbLabel = 'Autocertificazioni';
                    break;
                case 'report.php':
                    $breadcrumbLabel = 'Report';
                    break;
                case 'listaprove.php':
                    $breadcrumbLabel = 'Lista Prove';
                    break;
                case 'index.php':
                    $breadcrumbLabel = 'Dashboard';
                    break;
                case 'new.php':
                    $breadcrumbLabel = 'Nuovo';
                    break;
                case 'list.php':
                    $breadcrumbLabel = 'Interventi';
                    break;
                case 'schedaintervento.php':
                    $breadcrumbLabel = 'Modifica';
                    break;
                default:
                    $breadcrumbLabel = '';
            }
            ?>
            <nav class="navbar-breadcrumb ms-3">
                <!-- Desktop breadcrumb completo -->
                <ol class="breadcrumb mb-0 d-none d-sm-flex">
                    <li class="breadcrumb-item">
                        <a href="/strumenti/eventi/index.php"><i class="fas fa-home"></i></a>
                    </li>
                    <?php if ($page !== 'index.php'): ?>
                        <li class="breadcrumb-item">
                            <a href="./index.php">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $breadcrumbLabel ?></li>
                    <?php else: ?>
                        <li class="breadcrumb-item active" aria-current="page">Interventi</li>
                    <?php endif; ?>
                </ol>

                <!-- Mobile breadcrumb accorciato -->
                <ol class="breadcrumb mb-0 d-sm-none">
                    <?php if ($page !== 'index.php'): ?>
                        <li class="breadcrumb-item">
                            <a href="./index.php"><i class="fas fa-arrow-left me-1"></i>Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $breadcrumbLabel ?></li>
                    <?php else: ?>
                        <li class="breadcrumb-item">
                            <a href="/strumenti/eventi/index.php"><i class="fas fa-home"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Interventi</li>
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
            <!--<a href="#" data-bs-toggle="modal" data-bs-target="#modal3" class="btn btn-sm btn-outline-light">Login</a>-->
            <?php endif; ?>
        </div>
    </div>
</nav>
