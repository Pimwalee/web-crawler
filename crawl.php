<?php
include("config.php");
include("classes/DomDocumentParser.php");

$alreadyCrawled = array();
$crawling = array(); //contain all the link that we still need to go over
$alreadyFoundImages = array();

function linkExists($url) {
    global $con;

    $query = $con->prepare("SELECT * FROM sites WHERE url = :url");

    $query->bindParam(":url", $url);
    $query->execute();

    return $query->rowCount() != 0;
} 

function insertLink($url, $title, $description, $keywords) {
    global $con;

    $query = $con->prepare("INSERT INTO sites(url, title, description, keywords)
                           VALUES(:url, :title, :description, :keywords)");

    $query->bindParam(":url", $url); // binding placeholder to variable
    $query->bindParam(":title", $title);
    $query->bindParam(":description", $description);
    $query->bindParam(":keywords", $keywords);

    return $query->execute();
} 

function insertImage($url, $src, $alt, $title) {
    /*  * $url is url of the website
        * $src is url of the image
    */
    global $con;

    $query = $con->prepare("INSERT INTO image(siteUrl, imageUrl, alt, title)
                           VALUES(:siteUrl, :imageUrl, :alt, :title)");

    $query->bindParam(":siteUrl", $url);//from the function param name
    $query->bindParam(":imageUrl", $src);
    $query->bindParam(":alt", $alt);
    $query->bindParam(":title", $title);

   return $query->execute();
} 

function createLink($src, $url) {
    /* $src(href) is the links on $url ex. www.bbc.com is $url /education/ is $src */
    
    $scheme = parse_url($url)["scheme"];
     // http
    $host = parse_url($url)["host"];
    // www.pimwalee.com

    if(substr($src, 0, 2) == "//") {
        $src = $scheme . ":" . $src;
    }
    else if(substr($src, 0, 1) == "/") {
        $src = $scheme . "://" . $host . $src;
    }
    else if(substr($src, 0, 2) == "./") {
        $src = $scheme . "://" . $host . dirname(parse_url($url)["path"]) . substr($src, 1);
    }
    /*  * path คือ ที่อยู่ของไฟล์ 
        * dirname(parse_url($url)["path"]) : give the directory of ["path"] name of this $url
        * substr($src, 1) only take character start from index 1 (we don't want ".")
    */
    else if(substr($src, 0, 3) == "../") {
        $src = $scheme . "://" . $host . "/" . $src;
    }
    else if(substr($src, 0, 5) != "https" && substr($src, 0, 4) != "http"){
        $src = $scheme . "://" . $host . "/" . $src;
    }

    return $src;
}

function getDetails($url){

global $alreadyFoundImages;

    $parser = new DomDocumentParser($url);

    $titleArray = $parser->getTitleTags();// $titleArray contian all of the litle on the site

    if(sizeof($titleArray) == 0 || $titleArray->item(0) == NULL ){
        return;
    }

    $title = $titleArray->item(0)->nodeValue; 
    $title = str_replace("\n","",$title);
    /*  * title is like the blue letter when we search google
        * nodeValue is to return the value of item in the array
        * replace newline with empty ""
    */

    if($title== ""){
        return;
    }// ignore the link wihtout title

    $description = "";
    $keywords = "";

    $metaArray = $parser->getMetatags();

    foreach($metaArray as $meta) {

        if($meta->getAttribute("name") == "description") {
            $description = $meta->getAttribute("content");
        }
        if($meta->getAttribute("name") == "keywords") {
            $keywords = $meta->getAttribute("content");
        }
    }


    $description = str_replace("\n","",$description);
    $keywords = str_replace("\n","", $keywords);


    if(linkExists($url)) {
        echo "$url already exists<br>";
    }
    else if(insertLink($url, $title, $description, $keywords)) {
        echo "SUCCESS: $url<br>";
    }
    else {
        echo "ERROR: Failed to insert $url<br>";
    }
    
    $imageArray = $parser->getImages(); //$imageArray contain all img array on the site
    foreach($imageArray as $image) {
        $src = $image->getAttribute("src");
        $alt = $image->getAttribute("alt");
        $title = $image->getAttribute("title");

        if(!$title && !$alt) {//if there is not title & alt thats fine
            continue;
        }

        $src = createLink($src, $url);// take relative links convert to absolute links
    
    if(!in_array($src, $alreadyFoundImages)) {
        $alreadyFoundImages[] = $src; // put $src in the next availiable spot in the array[]
        
       echo "INSERT: " . insertImage($url,$src, $alt, $title);
        }
        
    }
  //  echo "URL: $url, Description : $description, Keywords: $keywords <br>"; // just to check
}



function followLinks($url) {
    
    global $alreadyCrawled;
    global $crawling;

    $parser = new DomDocumentParser($url);
    
    $linkList = $parser->getLinks();
    /* we retrieved all the links
     * it should contain all of the links on $url 
     */

    foreach($linkList as $link) {
        $href = $link->getAttribute("href");
    /* foreach loop
     * loop over array $linkList
     * everytime when you go over it. $link is going to contain the link you're cuttently on
    */

        if(strpos($href,"#") !== false) {
            continue; //if it contains "#" don't do anything,just continue, ignore #
        }
        else if(substr($href,0,11) == "javascript:") {
            continue; 
        }


        $href = createLink($href, $url);

        if(!in_array($href, $alreadyCrawled)) {
            $alreadyCrawled[] = $href; //if it's not in an array put $href in $alreadyCrawled[]
            $crawling[] = $href; // and put $href in $crawling[] as well

            getDetails($href);
        }

    }

    array_shift($crawling); // once we have been over one of them we need to knock it off the array

    foreach($crawling as $site) {
        followLinks($site);
    }/* *go over every single element in crawling array 
        *every href and every time the loop goes round
        *site is going to be an ultimate array
        * first time goes around site will contain the first element
        * second time goes around site will contain the second element
     */

}

$startUrl = "https://www.bangkokpost.com/"; // the website we want to start crawling
followLinks($startUrl);
?>