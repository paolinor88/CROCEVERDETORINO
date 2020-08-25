<?
session_start();
$connect=new PDO('mysql:host=localhost;dbname=massi369_gestionale', 'urhqx7h4kxv84', 'Gestional€');
if(isset($_POST["user_id"]))
{
    $query = "INSERT INTO agenda (title, start_event, end_event, user_id) VALUES (:title, :start_event, :end_event, :user_id)";

    $statement = $connect->prepare($query);
    $statement->execute(
        array(
            ':title'  => $_POST['title'],
            ':start_event' => $_POST['start'],
            ':end_event' => $_POST['end'],
            ':user_id' =>$_POST['user_id']
        )
    );
};
if(isset($_POST["stato"]))
{
    $query = "UPDATE agenda SET stato=:stato WHERE id=:id";

    $statement = $connect->prepare($query);
    $statement->execute(
        array(
            ':id'  => $_POST['id'],
            ':stato'  => $_POST['stato']
        )
    );
};

?>
