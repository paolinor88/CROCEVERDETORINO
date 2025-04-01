<?php
session_start();
include "../config/config.php";

if (isset($_POST["LoginBTN"])) {
    $id = $_POST["matricolaOP"];
    $password = $_POST["passwordOP"];
    $destination = "area_formatori.php";

    $hashedPassword = md5($password);

    $stmt = $db->prepare("SELECT IDOperatore, NomeOperatore, LoginOperatore, Password, Livello FROM Operatori WHERE LoginOperatore = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        list($id, $NomeOperatore, $LoginOperatore, $dbHashedPassword, $Livello) = $result->fetch_array();

        if ($hashedPassword === $dbHashedPassword) {
            if ($Livello == 28 ) {
                $_SESSION["ID"] = $id;
                $_SESSION["NomeOperatore"] = $NomeOperatore;
                $_SESSION["LoginOperatore"] = $LoginOperatore;
                $_SESSION["Livello"] = $Livello;

                header("Location: {$destination}");
                exit();
            } else {

                $_SESSION["errore"] = "Accesso non autorizzato.";
            }
        } else {
            $_SESSION["errore"] = "Matricola o password errata.";
        }
    } else {
        $_SESSION["errore"] = "Matricola o password errata.";
    }

    $stmt->close();
}

header("Location: index.php");
exit();
?>
