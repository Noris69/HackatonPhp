<?php
include 'includes/session.php';
include 'includes/slugify.php';

$output = array('error'=>false,'list'=>'');

$sql = "SELECT * FROM positions";
$query = $conn->prepare($sql);
$query->execute();

while($row = $query->fetch(PDO::FETCH_ASSOC)){
    $position = slugify($row['description']);
    $pos_id = $row['id'];
    if(isset($_POST[$position])){
        if($row['max_vote'] > 1){
            if(count($_POST[$position]) > $row['max_vote']){
                $output['error'] = true;
                $output['message'][] = '<li>You can only choose '.$row['max_vote'].' candidates for '.$row['description'].'</li>';
            }
            else{
                foreach($_POST[$position] as $key => $values){
                    $sql = "SELECT * FROM candidates WHERE id = ?";
                    $cmquery = $conn->prepare($sql);
                    $cmquery->execute([$values]);
                    $cmrow = $cmquery->fetch(PDO::FETCH_ASSOC);
                    $output['list'] .= "
                        <div class='row votelist'>
                           <span class='col-sm-4'><span class='pull-right'><b>".$row['description']." :</b></span></span> 
                           <span class='col-sm-8'>".$cmrow['firstname']." ".$cmrow['lastname']."</span>
                        </div>
                    ";
                }
            }
        }
        else{
            $candidate = $_POST[$position];
            $sql = "SELECT * FROM candidates WHERE id = ?";
            $csquery = $conn->prepare($sql);
            $csquery->execute([$candidate]);
            $csrow = $csquery->fetch(PDO::FETCH_ASSOC);
            $output['list'] .= "
                <div class='row votelist'>
                   <span class='col-sm-4'><span class='pull-right'><b>".$row['description']." :</b></span></span> 
                   <span class='col-sm-8'>".$csrow['firstname']." ".$csrow['lastname']."</span>
                </div>
            ";
        }
    }
}

echo json_encode($output);
?>
