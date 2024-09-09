<?php 
include("DomDocumentParser.php");
include("config.php");
$alreadyCrawled = array();
$crawling = array();
$alreadyFoundImages = array();

function getValidLink($href, $url) {
    $scheme = parse_url($url)["scheme"]; // http
    $host = parse_url($url)["host"]; // www.reecekenney.com

    if (substr($href, 0, 2) == "//") {
        $href = $scheme . ":" . $href;
    } else if (substr($href, 0, 1) == "/") {
        $href = $scheme . "://" . $host . $href;
    } else if (substr($href, 0, 2) == "./") {
        $href = $scheme . "://" . $host . dirname(parse_url($url)["path"]) . substr($href, 1);
    } else if (substr($href, 0, 3) == "../") {
        $href = $scheme . "://" . $host . "/" . $href;
    } else if (substr($href, 0, 5) != "https" && substr($href, 0, 4) != "http") {
        $href = $scheme . "://" . $host . "/" . $href;
    }

    return $href;
}

function linkExists($url) {
    global $connection;

    // Debugging: Check if $connection is null
    if ($connection === null) {
        echo "Error: Database connection is null.<br>";
        return false;
    }

    try {
        $query = $connection->prepare("SELECT * FROM sites where url= :url");
        $query->bindParam(":url", $url);
        $query->execute();
        return $query->rowCount()!=0 ;
    } catch (PDOException $e) {
        echo "Error executing query: " . $e->getMessage() . "<br>";
        return false;
    }
}
function insertToDB($url, $title, $description, $keywords) {
    global $connection;

    // Debugging: Check if $connection is null
    if ($connection === null) {
        echo "Error: Database connection is null.<br>";
        return false;
    }

    try {
        $query = $connection->prepare("INSERT INTO sites(url, title, description, keywords) 
                            VALUES(:url, :title, :description, :keywords)");
        $query->bindParam(":url", $url);
        $query->bindParam(":title", $title);
        $query->bindParam(":description", $description);
        $query->bindParam(":keywords", $keywords);
        return $query->execute();
    } catch (PDOException $e) {
        echo "Error executing query: " . $e->getMessage() . "<br>";
        return false;
    }
}
function insertImgsToDB($url, $src, $alt, $title) {
    global $connection;

    // Debugging: Check if $connection is null
    if ($connection === null) {
        echo "Error: Database connection is null.<br>";
        return false;
    }

    try {
        $query = $connection->prepare("INSERT INTO images(siteUrl, imgUrl, alt, title) 
                            VALUES(:siteUrl, :imgUrl, :alt, :title)");
        $query->bindParam(":siteUrl", $url);
        $query->bindParam(":imgUrl", $src);
        $query->bindParam(":alt", $alt);
        $query->bindParam(":title", $title);
        return $query->execute();
    } catch (PDOException $e) {
        echo "Error executing query: " . $e->getMessage() . "<br>";
        return false;
    }
}

function getDetails($url) {
    global $alreadyFoundImages ;
    $parser = new DomDocumentParser($url);
    $titleArray = $parser->getTitleTags();

    if (sizeof($titleArray) == 0 || $titleArray->item(0) == null) {
        return;
    }

    $title = $titleArray->item(0)->nodeValue;

    if ($title == '') {
        return;
    }

    $desc = "";
    $keywords = "";
    $metasArray = $parser->getMetaTags();

    foreach ($metasArray as $meta) {
        if ($meta->getAttribute("name") == "description") {
            $desc = $meta->getAttribute("content");
        }

        if ($meta->getAttribute("name") == "keywords") {
            $keywords = $meta->getAttribute("content");
        }
    }

    $description = str_replace("\n", "", $desc);
    $keywords = str_replace("\n", "", $keywords);
    if(linkExists($url)){
        echo "already exists<br>" ;
    }
    else if(insertToDB($url, $title, $description, $keywords)){
        echo "success<br>";
    }else{
        echo "failed<br>";
    }
    $imageArray=$parser->getImgs();
    foreach($imageArray as $image ){
        $src=$image->getAttribute('src') ;
        $alt=$image->getAttribute('alt') ;
        $title=$image->getAttribute('title') ;
        if (!$title & !$alt){
            continue ;
        }
        $src=getValidLink($src,$url) ;
        if(! in_array($src,$alreadyFoundImages)){
            $alreadyFoundImages[]=$src ;
            insertImgsToDB($url,$src,$alt,$title);
        }
    }
    

    echo "URL: $url, Title: $title, Description: $description, Keywords: $keywords<br>";
}

function followLinks($url) {
    global $alreadyCrawled;
    global $crawling;
    $parser = new DomDocumentParser($url);
    $linkList = $parser->getLinks();

    foreach ($linkList as $link) {
        $href = $link->getAttribute("href");
        if (strpos($href, '#') !== false) {
            continue;
        } else if (substr($href, 0, 11) == "javascript:") {
            continue;
        }
        $href = getValidLink($href, $url);
        if (!in_array($href, $alreadyCrawled)) {
            $alreadyCrawled[] = $href;
            $crawling[] = $href;
            getDetails($href);
        } else {
            return;
        }
    }
    array_shift($crawling);
    foreach ($crawling as $site) {
        followLinks($site);

    }
}

$startUrl = "https://www.skysports.com/football";


followLinks($startUrl);

?>