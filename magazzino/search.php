<?php
$connect=new PDO('mysql:host=localhost;dbname=massi369_gestionale', 'massi369', '@Croceto99');

$data = array();

$query = "SELECT * FROM giacenza";

$statement = $connect->prepare($query);

$statement->execute();

$result = $statement->fetchAll();

foreach($result as $row)
{
    $data[] = array(
        $result['nome'] => $row["nome"],

    );
}

echo json_encode($data);