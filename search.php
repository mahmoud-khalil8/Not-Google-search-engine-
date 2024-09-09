<?php
include("config.php");
include("siteResults.php");

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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #EEEDEB;
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin-top: 2rem;
        }
        .header {
            text-align: right;
            width: 100%;
            padding: 10px 20px;
            box-sizing: border-box;
        }
        .header a {
            color: #000;
            text-decoration: none;
            margin-left: 15px;
            font-size: 13px;
        }
        .header a:hover {
            text-decoration: underline;
        }
        .logo {
            font-size: 50px;
            font-weight: bold;
            color: #2F3645;
            margin-right: 2rem;
        }
        .logo2 {
            font-size: 13px;
            font-weight: bold;
            color: #2F3645;
        }
        a {
            text-decoration: none;
        }
        .search-bar {
            width: 330px;
            height: 30px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #dfe1e5;
            border-radius: 24px;
            outline: none;
        }
        .search-bar:focus {
            box-shadow: 0 1px 6px rgba(32, 33, 36, 0.28);
            border-color: rgba(223, 225, 229, 0);
        }
        .buttons {
            margin-top: 20px;
        }
        .search-button {
            padding: 10px 20px;
            font-size: 14px;
            color: #5F6368;
            background-color: #f2f2f2;
            border: 1px solid #f2f2f2;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }
        .search-button:hover {
            border: 1px solid #c6c6c6;
            background-color: #f8f8f8;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            color: #222;
        }
        .tabs {
            margin-top: 20px;
            padding-left: 14rem;
        }
        .tabs ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        .tabs li {
            display: inline;
            font-size: 18px;
            margin-right: 10px;
        }
        .tabs a {
            text-decoration: none;
            color: #000;
        }
        .tabs a:hover {
            text-decoration: underline;
        }
        .search_container, .tabs {
            margin-left: 2rem;
        }
        .active {
            font-weight: bold;
            border-bottom: 3px solid blue;
        }
        .active .tab {
            color: blue;
        }
        .search_container {
            display: flex;
            align-items: center;
        }
        .num-results {
            margin-top: 20px;
            font-size: 14px;
            color: #5F6368;
            margin-left: 2rem;
        }
        .resultsContainer {
            margin-top: 20px;
            margin-left: 2rem;
            padding: 10px;
            border-bottom: 1px solid #dfe1e5;
        }
        .resultsContainer:last-child {
            border-bottom: none;
        }
        .title {
            font-size: 20px;
            color: #1a0dab;
            margin-bottom: 5px;
        }
        .title a {
            color: #1a0dab;
        }
        .title a:hover {
            text-decoration: underline;
        }
        .url {
            font-size: 14px;
            color: #006621;
            margin-bottom: 5px;
        }
        .description {
            font-size: 14px;
            color: #545454;
        }
        .title{
            margin-bottom:0.5rem;
        }
        .paginationContainer {
            margin-top: 20px;
            text-align: center;
        }

        .pageButtons {
            display: inline-block;
            padding: 10px;
            border: 1px solid #dfe1e5;
            border-radius: 8px;
            background-color: #f8f8f8;
        }

        .pageNumberContainer {
            display: inline-block;
            margin: 0 5px;
        }

        .pageNumber {
            display: block;
            padding: 8px 12px;
            border: 1px solid #dfe1e5;
            border-radius: 4px;
            background-color: #fff;
            color: dodgerblue;
            font-size: 14px;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }

        .pageNumber:hover {
            background-color: #1a0dab;
            color: #fff;
        }
        a .pageNumber{
            color:black;
        }

        
    </style>
</head>
<body>
    <div class="main">
        <div class="search_container">
            <a href="index.php">
                <div class="logo"> مش جوجل </div>
                <div class="logo2">a search engine</div>
            </a>
            <form action="search.php" method='GET'>
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
            $pageSize=20 ;
            $results = new siteResults($connection);
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
</body>
</html>