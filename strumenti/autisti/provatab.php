<?php
header('Access-Control-Allow-Origin: *');
session_start();

include "../config/config.php";

if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $select = $db->query("SELECT * FROM rubrica WHERE Codice='$id'");

    if ($select && $select->num_rows > 0) {
        while ($ciclo = $select->fetch_array()) {
            echo "<h4 align='center'>" . htmlspecialchars($ciclo['Codice']) . " " . htmlspecialchars($ciclo['Cognome']) . " " . htmlspecialchars($ciclo['Nome']) . "</h4><hr>";

            echo '
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Abilitazioni</button>
                    <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Corsi</button>
                    <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Contatto</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">';

            echo '<form class="row g-3">';
            mostraDatiAutisti($db, $id, 'AUTISTI_RIENTRI', 'Rientri');
            mostraDatiAutisti($db, $id, 'AUTISTI_NORMALI', 'Normali');
            mostraDatiAutisti($db, $id, 'AUTISTI_URGENZE', 'Urgenze');
            mostraDatiAutisti($db, $id, 'AUTISTI_OVER', 'Over');
            mostraDatiAutisti($db, $id, 'AUTISTI_FORMAZIONE', 'Formazione');
            echo '</form>';

            echo '
                </div>
                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">

                </div>
                <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab" tabindex="0">
                    <div class="col-md-6">
                        <label for="Cellulare" class="form-label">Cellulare</label><input type="text" class="form-control" id="Cellulare" value="'.  $ciclo["Cellulare"] .'"disabled>
                        <label for="Mail" class="form-label">Mail</label><input type="text" class="form-control" id="Mail" value="'.  $ciclo["Mail"] .'"disabled>
                    </div>
                </div>
                </div>';
        }
    } else {
        echo "Nessun record trovato nella rubrica.";
    }
}

function mostraDatiAutisti($db, $codice, $tabella, $tipo) {
    $query = $db->query("SELECT * FROM $tabella WHERE Codice='$codice'");
    $dataDisplayed = false;

    if ($query && $query->num_rows > 0) {
        while ($row = $query->fetch_array()) {
            if (!empty($row['DataInizio']) && strtotime($row['DataInizio']) !== false) {
                $formattedDate = date('d/m/Y', strtotime($row['DataInizio']));
                echo "<div class='col-md-6'><label for='{$tipo}' class='form-label'>{$tipo}</label><input type='text' class='form-control' id='{$tipo}' value='".htmlspecialchars($formattedDate)."' disabled></div>";
                $dataDisplayed = true;
            }
            if ($tipo === 'Urgenze' && !empty($row['SCADENZAURGENZE'])) {
                $dataDisplayed |= checkAndDisplayDate($row, 'SCADENZAURGENZE', 'Scadenza Urgenze');
            } elseif ($tipo === 'Over') {
                if (!empty($row['SCADENZAOVER']) && strtotime($row['SCADENZAOVER']) !== false) {
                    $dataDisplayed |= checkAndDisplayDate($row, 'SCADENZAOVER', 'Scadenza abilitazione Over 65');
                }
                if (!empty($row['LIMITEOVER']) && strtotime($row['LIMITEOVER']) !== false) {
                    $limiteOverDate = date('d/m/Y', strtotime($row['LIMITEOVER']));
                    $scaduto = new DateTime($row['LIMITEOVER']) < new DateTime();
                    echo "<div class='col-md-6'><label for='maxRinnovo' class='form-label'>Max rinnovo</label><input type='text' class='form-control ".($scaduto ? "text-danger" : "")."' id='maxRinnovo' value='".htmlspecialchars($limiteOverDate)."' ".($scaduto ? "style='color:red;'" : "")."></div>";
                    $dataDisplayed = true;
                }
            }
        }
    }

    if (!$dataDisplayed && $tipo !== 'Over') {
        echo "<p class='text-danger'>Abilitazione <b>$tipo</b> non presente</p>";
    }
}

function checkAndDisplayDate($dataRow, $dateField, $labelText) {
    if (!empty($dataRow[$dateField]) && strtotime($dataRow[$dateField]) !== false) {
        $scadenzaFormatted = date('d/m/Y', strtotime($dataRow[$dateField]));
        $scaduto = new DateTime($dataRow[$dateField]) < new DateTime();
        echo "<div class='col-md-6'><label for='".strtolower($dateField)."' class='form-label'>$labelText</label><input type='text' class='form-control ".($scaduto ? "text-danger" : "")."' id='".strtolower($dateField)."' value='".htmlspecialchars($scadenzaFormatted)."' ".($scaduto ? "style='color:red;'" : "")." disabled></div>";
        return true;
    }
    return false;
}
?>
