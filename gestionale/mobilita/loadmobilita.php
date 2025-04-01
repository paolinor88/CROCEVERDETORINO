<?
include "../config/pdo.php";

$data = array();

$query = "SELECT * FROM mobilita ORDER BY id";

$statement = $connect->prepare($query);

$statement->execute();

$result = $statement->fetchAll();

foreach($result as $row)
{
    $data[] = array(
        'id' => $row["id"],
        'title' => $row["title"],
        'start' => $row["start_event"],
        'end' => $row["end_event"],
        'id_mezzo' => $row["id_mezzo"],
        'partenza' => $row["partenza"],
        'destinazione' => $row["destinazione"],
        'equipaggio' => $row["equipaggio"],
        'AR' => $row["AR"],
        'note' => $row["note"],
        'stato' => $row["stato"],
    );
}

echo json_encode($data);
