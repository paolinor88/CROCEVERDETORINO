<?
include "../config/pdo.php";

$data = array();

//$query = "SELECT * FROM programmazione ORDER BY id";
$query = "SELECT id, title, date_format(str_to_date(OraUscitaSede, '%d/%m/%Y %H:%i:%s'), '%H:%i') as uscitaformat, date_format(str_to_date(OraSulPosto, '%d/%m/%Y %H:%i:%s'), '%H:%i') as postoformat, date_format(str_to_date(OraDestinazione, '%d/%m/%Y %H:%i:%s'), '%H:%i') as destinazioneformat, Carico, Destinazione, Convenzione,
       STR_TO_DATE(start_event, '%d/%m/%Y %H:%i:%s') as startformat,
       STR_TO_DATE(end_event, '%d/%m/%Y %H:%i:%s') as endformat
        FROM programmazione
        ORDER BY title";

$statement = $connect->prepare($query);

$statement->execute();

$result = $statement->fetchAll();

foreach($result as $row)
{
    $data[] = array(
        'id' => $row["id"],
        'title' => '['.$row["uscitaformat"]. '] ('. $row["title"] .') ['.$row["postoformat"].'] '.$row["Carico"] .' // ['. $row["destinazioneformat"].'] '.$row["Destinazione"],
        'start' => $row["startformat"],
        'end' => $row["endformat"],
        'OraUscitaSede' => $row["OraUscitaSede"],
        'OraSulPosto' => $row["OraSulPosto"],
        'OraDestinazione' => $row["OraDestinazione"],
        'Carico' => $row["Carico"],
        'Destinazione' => $row["Destinazione"],
        'Convenzione' => $row["Convenzione"],
    );
}

echo json_encode($data);
