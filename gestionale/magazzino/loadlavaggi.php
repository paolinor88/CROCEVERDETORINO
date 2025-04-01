<?
include "../config/pdo.php";

$data = array();

$query = "SELECT * FROM lavaggio_mezzi  ORDER BY id";

$statement = $connect->prepare($query);

$statement->execute();

$result = $statement->fetchAll();

foreach($result as $row)
{
    $data[] = array(
        'id' => $row["id"],
        'title' => $row["title"],
        'user_id' => $row["user_id"],
        'start' => $row["start_event"],
        'stato' => $row["stato"],
    );
}

echo json_encode($data);
