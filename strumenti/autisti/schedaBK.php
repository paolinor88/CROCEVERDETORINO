<?php
header('Access-Control-Allow-Origin: *');
session_start();

if (!isset($_SESSION['Livello']) && isset($_GET['livello'])) {
    $_SESSION['Livello'] = intval($_GET['livello']);
}

include "../config/config.php";

if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $select = $db->query("SELECT * FROM rubrica WHERE Codice='$id'");

    if ($select && $select->num_rows > 0) {
        while ($ciclo = $select->fetch_array()) {
            echo "<h4 align='center'>" . htmlspecialchars($ciclo['Codice']) . " " . htmlspecialchars($ciclo['Cognome']) . " " . htmlspecialchars($ciclo['Nome']) . "</h4>";

            if ($ciclo['SOSP'] == 72) {
                $scadenza = DateTime::createFromFormat('d/m/Y', $ciclo['ScadenzaSOSP']);
                $oggi = new DateTime();
                if ($scadenza && $scadenza > $oggi) {
                    echo "<h3 align='center' style='color:red; font-weight:bold;'><i class='fas fa-exclamation-triangle'></i> GUIDA SOSPESA <i class='fas fa-exclamation-triangle'></i></h3>";
                }
            }
            echo "<hr>";

            echo '
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Abilitazioni</button>
                    <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Corsi</button>
                    <button class="nav-link" id="nav-patente-tab" data-bs-toggle="tab" data-bs-target="#nav-patente" type="button" role="tab" aria-controls="nav-patente" aria-selected="false">Patente</button>';
            if ($_SESSION['Livello'] != 30 ) {
                echo '<button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Contatto</button>';
            }

            echo '
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">';

            echo '<form class="row g-3">';
            mostraDatiAutisti($db, $id, 'AUTISTI_RIENTRI', 'Rientri');
            mostraDatiAutisti($db, $id, 'AUTISTI_NORMALI', 'Normali');
            mostraDatiAutisti($db, $id, 'AUTISTI_URGENZE', 'Urgenze');
            mostraDatiAutisti($db, $id, 'AUTISTI_OVER', 'Over');
            echo '</form>';

            echo '
                </div>
                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
                <form class="row g-3">';
            mostraDataInizioFormazione($db, $id, 39);
            mostraDataInizioFormazione($db, $id, 40);
            mostraDataInizioFormazione($db, $id, 50);
            mostraDataInizioFormazione($db, $id, 62);
            echo '
                </form>
                </div>';

            echo '
                <div class="tab-pane fade" id="nav-patente" role="tabpanel" aria-labelledby="nav-patente-tab" tabindex="0">
                <form class="row g-3">';
            mostraDatiPatente($db, $id);
            echo '
                </form>
                </div>';

            if ($_SESSION['Livello'] != 30) {
                echo '
                <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab" tabindex="0">
                <br>
                    <div class="col-md-12">
                        <label for="Cellulare" class="form-label">Cellulare</label>
                        <input type="text" class="form-control" id="Cellulare" value="'.  $ciclo["Cellulare"] .'" disabled>
                    </div>
                    <div class="col-md-12">
                        <label for="Mail" class="form-label">Mail</label>
                        <input type="text" class="form-control" id="Mail" value="'.  $ciclo["Mail"] .'" disabled>
                    </div>
                    <div class="col-md-12">
                        <label for="Nascita" class="form-label">Data di nascita</label>
                        <input type="text" class="form-control" id="Nascita" value="'.  $ciclo["DataNascita"] .'" disabled>
                    </div>
                </div>';
            }

            echo '
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

function mostraDataInizioFormazione($db, $codice, $idQualifica) {
    $query = $db->query("SELECT DataInizio FROM AUTISTI_FORMAZIONE WHERE Codice='$codice' AND IDQualifica='$idQualifica'");

    switch ($idQualifica) {
        case 39:
            $nomeCorso = "1° Modulo Corso Teorico Autisti";
            break;
        case 40:
            $nomeCorso = "2° Modulo Corso Teorico Autisti";
            break;
        case 50:
            $nomeCorso = "Corso Pratico Base";
            break;
        case 62:
            $nomeCorso = "Corso Pratico Plus";
            break;
        default:
            $nomeCorso = "Corso Non Specificato";
            break;
    }

    if ($query && $query->num_rows > 0) {
        $row = $query->fetch_array();
        if (!empty($row['DataInizio']) && strtotime($row['DataInizio']) !== false) {
            $formattedDate = date('d/m/Y', strtotime($row['DataInizio']));
            echo "<div class='col-md-6'><label for='formazione{$idQualifica}' class='form-label'>{$nomeCorso}</label><input type='text' class='form-control' id='formazione{$idQualifica}' value='".htmlspecialchars($formattedDate)."' disabled></div>";
        }
    } else {
        echo "<div class='col-md-6'><p class='text-danger'>Formazione {$nomeCorso} non effettuata</p></div>";
    }
}

function mostraDatiPatente($db, $codice) {
    $query = $db->query("SELECT * FROM AUTISTI_PATENTI WHERE Codice='$codice' AND IDQualifica IN (67, 68, 70, 71)");

    if ($query && $query->num_rows > 0) {
        while ($row = $query->fetch_array()) {

            switch ($row['IDQualifica']) {
                case 67:
                    $categoria = "Patente B";
                    break;
                case 68:
                    $categoria = "Patente C";
                    break;
                case 70:
                    $categoria = "Patente D";
                    break;
                case 71:
                    $categoria = "Patente A";
                    break;
                default:
                    $categoria = "Categoria Sconosciuta";
            }

            echo "<div class='card mb-3'>";
            echo "<div class='card-header'>$categoria</div>";
            echo "<div class='card-body'>";
            echo "<div class='row'>";
            echo "<div class='col-md-6'><label for='DataInizio' class='form-label'>Data Rilascio</label><input type='text' class='form-control' id='DataInizio' value='".htmlspecialchars(date('d/m/Y', strtotime($row['DataInizio'])))."' disabled></div>";
            echo "<div class='col-md-6'><label for='ScadenzaAutUltimoRetraining' class='form-label'>Scadenza Patente</label><input type='text' class='form-control' id='ScadenzaAutUltimoRetraining' value='".htmlspecialchars(date('d/m/Y', strtotime($row['ScadenzaAutUltimoRetraining'])))."' disabled></div>";
            echo "<div class='col-md-6'><label for='Naut' class='form-label'>Numero Patente</label><input type='text' class='form-control' id='Naut' value='".htmlspecialchars($row['NAut'])."' disabled></div>";
            echo "<div class='col-md-6'><label for='RilasciataDa' class='form-label'>Ente Rilascio</label><input type='text' class='form-control' id='RilasciataDa' value='".htmlspecialchars($row['RilasciataDa'])."' disabled></div>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<p class='text-danger'>Nessuna patente registrata</p>";
    }
}

?>
