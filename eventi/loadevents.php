<?
$connect=new PDO('mysql:host=localhost;dbname=massi369_gestionale', 'massi369', '@Croceto99');

$data = array();

$query = "SELECT * FROM events ORDER BY id";

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
        'user_id' => $row["user_id"],
        'stato' => $row["stato"],
        'luogo' => $row["luogo"],
        'note' => $row["note"],
        'msa' => $row["msa"],
        'msb' => $row["msb"],
        'pma' => $row["pma"],
        'squadre' => $row["squadre"],
    );
}

echo json_encode($data);

