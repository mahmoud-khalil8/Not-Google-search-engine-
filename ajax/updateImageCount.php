<?php
include("../config.php") ;
if(isset($_POST["imgUrl"])){
    
    $query=$connection->prepare("
    UPDATE images SET clicks= clicks+1 where id= :id");
    $query->bindParam(":id",$_POST["imgUrl"]) ;
    $query->execute() ;
}else{
    echo "no link " ;
}
?>
