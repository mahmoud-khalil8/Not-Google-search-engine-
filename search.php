<?php
include("config.php");
include("results/siteResults.php");
include("results/imgResults.php");

if (isset($_GET['words'])) {
    $words = $_GET['words'];
} else {
    exit("You must enter something to search");
}
$type = isset($_GET['type']) ? $_GET['type'] : 'sites';
$page = isset($_GET['page']) ? $_GET['page'] : 1;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Not Google</title>
        	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.5/jquery.fancybox.min.css" />

        <link rel="stylesheet" href="styles/styles.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</head>
<body>
    <div class="main">
        <div class="search_container">
            <a href="index.php">
                <div class="logo"> مش جوجل </div>
                <div class="logo2">a search engine</div>
            </a>
            <form action="search.php" method='GET'>
                <input type="hidden" name='type' value=<?php echo $type;?>>
                <input type="text" class="search-bar" placeholder="Search Here" name="words" value="<?php echo $words;?>">
                <input type="submit" class="search-button" value="Search">
            </form>
        </div>
        <div class='tabs'>
            <ul>
                <li class="<?php echo $type == 'sites' ? 'active' : '' ?>">
                    <a class='tab' href='<?php echo "search.php?words=$words&type=sites"; ?>'>Sites</a>
                </li>
                <li class="<?php echo $type == 'images' ? 'active' : '' ?>">
                    <a class='tab' href='<?php echo "search.php?words=$words&type=images"; ?>'>Images</a>
                </li>
            </ul>
        </div>
        <div class="search_results">
            <?php
            if($type=='sites'){
                $results = new siteResults($connection);
                $pageSize=20 ;
            }else{
                $results = new imgResults($connection);
                $pageSize=30 ;
            }
             $numResults = $results->getNumResults($words);
            echo "<div class='num-results'>About $numResults results found</div>";
            
            echo $results->getResultsHtml($page,$pageSize,$words) ;
            ?>
        </div>
        <div class='paginationContainer'>
    <div class="pageButtons">
        <div class="logo"> مش جوجل </div>
        <?php
        $pagesToShow=10 ;
        $numPages=ceil($numResults/$pageSize);
        
        $pagesLeft = min($pagesToShow,$numPages);
        $currentPage=$page-floor(($pagesToShow)/2);
        if($currentPage<1){
            $currentPage=1 ;
        }
        while ($pagesLeft != 0) {
            if($currentPage==$page){

                echo "<div class='pageNumberContainer'>
                    <span class='pageNumber'>$currentPage</span>
                </div>";
            }else{
                echo "<div class='pageNumberContainer'>
                <a href='search.php?words=$words&type=$type&page=$currentPage'>
                    <span class='pageNumber'>$currentPage</span>
                </a>
                    </div>";
            }
            $currentPage++;
            $pagesLeft--;
        }
        ?>
    </div>
</div>
    </div>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.5/jquery.fancybox.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/masonry/4.2.2/masonry.pkgd.min.js" integrity="sha512-JRlcvSZAXT8+5SQQAvklXGJuxXTouyq8oIMaYERZQasB8SBDHZaUbeASsJWpk0UUrf89DP3/aefPPrlMR1h1yQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src='javascript/script.js'></script>
</body>
</html>