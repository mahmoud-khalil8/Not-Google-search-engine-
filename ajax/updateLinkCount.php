<?php
include("../config.php") ;
if(isset($_POST["linkId"])){
    
    $query=$connection->prepare("
    UPDATE sites SET clicks= clicks+1 where id= :id");
    $query->bindParam(":id",$_POST["linkId"]) ;
    $query->execute() ;
}else{
    echo "no link " ;
}
?>
