<?php 
include("../config.php") ;
if(isset($_POST["src"])){
        
        $query=$connection->prepare("
        UPDATE images SET broken=1 where imageUrl= :src");
        $query->bindParam(":src",$_POST["src"]) ;
        $query->execute() ;
}else{
    echo "no src " ;
}
?>