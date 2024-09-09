<?php
class imgResults{

    public $connection ;
    public function __construct($connection)
    {
        
        $this->connection=$connection ;
    }
    public function getNumResults($term){
        $query=$this->connection->prepare("SELECT COUNT(*) as
                     total from images where (title LIKE :term
                     OR alt LIKE :term)
                     AND broken=0                     
                     ") ;
        $searchTerm='%'.$term.'%' ;
        $query->bindParam(":term",$searchTerm);
        $query->execute() ;
        $row=$query->fetch(PDO::FETCH_ASSOC) ;
        return $row["total"];
        
    }
    public function getResultsHtml($page,$pageSize,$term){
        $fromLimit=($page-1)*$pageSize ;
        $query = $this->connection->prepare("SELECT *  from images where (title LIKE :term
                                            OR alt LIKE :term)
                                            AND broken=0
                                             ORDER BY clicks DESC 
                                             LIMIT :fromLimit, :pageSize");
        $searchTerm = '%' . $term . '%';
        $query->bindParam(":term", $searchTerm);
        $query->bindValue(":fromLimit", $fromLimit, PDO::PARAM_INT);
        $query->bindValue(":pageSize", $pageSize, PDO::PARAM_INT);
        $query->execute();
        
    $resultsHtml = "<div class='imgsResults'>";
    $count = 0;
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $count++;
        $title = $row["title"];
        $id = $row["id"];
        $imgUrl = $row["imgUrl"];
        $siteUrl = $row["siteUrl"];
        $alt = $row["alt"];

        if ($title) {
            $displayText = $title;
        } else if ($alt) {
            $displayText = $alt;
        } else {
            $displayText = $imgUrl;
        }

        $resultsHtml .= "<div class='gridItem image$count'>
            <a href='$imgUrl data-fancybox data-caption=$displayText'>
            <script>
									$(document).ready(function() {
										loadImage(\"$imgUrl\", \"image$count\");
									});
									</script>
                <span class='details'>$displayText</span>
            </a>
        </div>";
    }

    $resultsHtml .= "</div>";
    return $resultsHtml;

    }

}

?>