<?php
/**
 *
 * @author     Paolo Randone
 * @author     <paolo.randone@croceverde.org>
 * @version    7.0
 * @note       Powered for Croce Verde Torino. All rights reserved
 *
 */
session_start();
//parametri DB
include "config/config.php";
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="Paolo Randone">
    <title>Guida</title>

    <link rel="stylesheet" href="config/css/bootstrap.min.css">
    <link href='https://use.fontawesome.com/releases/v5.0.6/css/all.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">    <link href='https://use.fontawesome.com/releases/v5.0.6/css/all.css' rel='stylesheet'>
    <link rel="stylesheet" href="config/css/custom.css">

    <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
    <script src="config/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/javascript.util/0.12.12/javascript.util.min.js" integrity="sha512-oHBLR38hkpOtf4dW75gdfO7VhEKg2fsitvHZYHZjObc4BPKou2PGenyxA5ZJ8CCqWytBx5wpiSqwVEBy84b7tw==" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.4.4/umd/popper.min.js" integrity="sha512-eUQ9hGdLjBjY3F41CScH3UX+4JDSI9zXeroz7hJ+RteoCaY+GP/LDoM8AO+Pt+DRFw3nXqsjh9Zsts8hnYv8/A==" crossorigin="anonymous"></script>    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>
<body>
<br>
<div class="container-fluid">
    <div class="jumbotron">
        <div align="center"><a href="https://croceverde.org/gestionale"><img class="img-fluid" src="config/images/logo.png"></div></a>
        <h3 class="text-center" style="color: #078f40">GESTIONALE ONLINE</h3>
        <h3 class="text-center" style="color: #078f40">MANUALE UTENTE</h3>
        <hr>
        <div class="row">
            <div class="col-2">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link active" id="v-pills-accesso-tab" data-toggle="pill" href="#v-pills-accesso" role="tab" aria-controls="v-pills-accesso" aria-selected="true">Accesso</a>
                    <a class="nav-link" id="v-pills-checklist-tab" data-toggle="pill" href="#v-pills-checklist" role="tab" aria-controls="v-pills-checklist" aria-selected="false">Checklist</a>
                    <a class="nav-link" id="v-pills-agenda-tab" data-toggle="pill" href="#v-pills-agenda" role="tab" aria-controls="v-pills-agenda" aria-selected="false">Agenda</a>
                </div>
            </div>
            <div class="col-10">
                <div class="tab-content" id="v-pills-tabContent">
                    <div class="tab-pane fade show active" id="v-pills-accesso" role="tabpanel" aria-labelledby="v-pills-accesso-tab">
                        <p>Al primo accesso premere <button class="btn btn-outline-success btn-sm">Registrati</button> e inserire le informazioni richieste nel form.</p>
                        <p>Ricorda di inserire un indirizzo email valido e di cui hai l'accesso. Se la registrazione è andata a buon fine, riceverai una email contentente le credenziali di accesso. Non cancellarla!</p>
                        <div class="alert alert-warning" role="alert">
                            ATTENZIONE: se non hai ricevuto la mail, controlla nello spam. In caso di problemi, <a href="mailto: mail@paolorandone.it">contatta il supporto</a>
                        </div>
                        <p>Accedi al sistema inserendo la tua matricola (attenzione a rispettare il formato richiesto) e la password che hai ricevuto.</p>
                    </div>
                    <div class="tab-pane fade" id="v-pills-checklist" role="tabpanel" aria-labelledby="v-pills-checklist-tab">
                        Funzione al momento non disponibile
                    </div>
                    <div class="tab-pane fade" id="v-pills-agenda" role="tabpanel" aria-labelledby="v-pills-agenda-tab">
                        <p>L'agenda dello straordinario è accessibile dal relativo pulsante della sezione "Calendario e eventi"</p>
                        <p>La visualizzazione è predefinita sulla settimana corrente. Attraverso i pulsanti freccia posti in alto si può scorrere l'agenda, oppure ottenere la visualizzazione mensile.</p>
                        <p>Per inserire la propria disponibilità allo straordinario, è sufficiente cliccare sul giorno desiderato. Si apre una maschera che invita a selezionare il proprio turno.</p>
                        <p>Per eliminare una disponibilità, è sufficiente cliccare sulla stessa e confermare tramite il popup.</p>
                        <div class="alert alert-warning" role="alert">
                            ATTENZIONE: Non è possibile inserire disponibilità oltre un mese dalla data corrente; sono bloccati anche la modifica è l'inserimento di disponibilità antecedenti e corrispondenti alla data odierna </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<!-- FOOTER -->
<footer class="container-fluid">
    <div class="text-center">
        <font size="-4" style="color: lightgray; "><em>Powered for <a href="mailto:info@croceverde.org">Croce Verde Torino</a>. All rights reserved.<p>V 2.4</p></em></font>
    </div>
</footer>
</html>
