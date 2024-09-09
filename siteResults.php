<?php
class siteResults{

    public $connection ;
    public function __construct($connection)
    {
        
        $this->connection=$connection ;
    }
    public function getNumResults($term){
        $query=$this->connection->prepare("SELECT COUNT(*) as
                     total from sites where title LIKE :term
                     OR url LIKE :term 
                     OR keywords LIKE :term
                     OR description LIKE :term
                     ") ;
        $searchTerm='%'.$term.'%' ;
        $query->bindParam(":term",$searchTerm);
        $query->execute() ;
        $row=$query->fetch(PDO::FETCH_ASSOC) ;
        return $row["total"];
        
    }
    public function getResultsHtml($page,$pageSize,$term){
        $fromLimit=($page-1)*$pageSize ;
        $query = $this->connection->prepare("SELECT * 
                                             FROM sites 
                                             WHERE title LIKE :term 
                                             OR url LIKE :term 
                                             OR keywords LIKE :term 
                                             OR description LIKE :term 
                                             ORDER BY clicks DESC 
                                             LIMIT :fromLimit, :pageSize");
        $searchTerm = '%' . $term . '%';
        $query->bindParam(":term", $searchTerm);
        $query->bindValue(":fromLimit", $fromLimit, PDO::PARAM_INT);
        $query->bindValue(":pageSize", $pageSize, PDO::PARAM_INT);
        $query->execute();
        
        $resultsHtml="<div class='siteResults'>";
        while($row=$query->fetch(PDO::FETCH_ASSOC) ){
            $title=$row["title"] ;
            $id=$row["id"] ;
            $url=$row["url"] ;
            $description=$row["description"] ;

            $resultsHtml.="<div class='resultsContainer'>
            
            <h3 class='title'>
             <a class='result' href='url'>$title</a><br>
             </h3>
             <span class='url'>$url</span><br>
             <span class='description'>$description</span>
            </div>
            ";

            $resultsHtml .= "<br>" ;

        }

        $resultsHtml .="</div>";
        return $resultsHtml;

    }

}

?>