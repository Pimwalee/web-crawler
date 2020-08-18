<?php
include("config.php");
include("classes/SiteResultsProvider.php");
include("classes/ImageResultsProvider.php");

    /* * $term is assigned here and we pass $term into class::function
       * ImageResultsProvider::getNumResults()
       * ImageResultsProvider::getResultsHtml()
     */

    if(isset($_GET["term"])) {
        $term = $_GET["term"];
    }
    else {
        exit("You must enter a search term");
    }

    $type = isset($_GET["type"]) ? $_GET["type"] : "sites";
    $page = isset($_GET["page"]) ? $_GET["page"] : 1;




?>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Goowalee</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" 
            integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
            crossorigin="anonymous">
    </script>

</head>
<body>

    <div class ="wrapper">

        <div class="header">

            <div class="headerContent">
                <div class="logoContainer">
                        <a href="index.php">
                            <img src="assets/images/goowaleelogo.png">
                        </a>
                </div>

                <div class="searchContainer">

                        <form action="search.php" medthod="GET">

                            <div class="searchBarContainer">
                                <input type="hidden" name="type" value="<?php echo $type; ?>">
                                <input class="searchBox" type="text" name="term" value="<?php echo $term; ?>">
                                <button class="searchButton"> 
                                 <img src="assets/images/icons/iconssearch.png">
                                </button>
                            </div>
                        </form>
                </div> 

            </div>

            <div class="tabsContainer">
                <ul class="tabList">

                    <li class = "<?php echo $type == 'sites' ? 'active' : '' ?>" > 
                        <a href='<?php echo "search.php?term=$term&type=sites";?> '>
                            Sites
                        </a> 
                    </li>

                    <li class = "<?php echo $type == 'images' ? 'active' : '' ?>"> 
                        <a href='<?php echo "search.php?term=$term&type=images";?> '>
                            Images
                        </a> 
                    </li>

                </ul>
            </div>





        </div> <!--header div> -->








<div class="mainResultsSection">

<?php

/* we pass $pageSize into 
* SiteResultsProvider::getResultsHtml()
* ImageResultsProvider::getResultsHtml()
 */
if($type == "sites") {
    $resultsProvider = new SiteResultsProvider($con);
    $pageSize = 20;
}
else {
    $resultsProvider = new ImageResultsProvider($con);
    $pageSize = 30;//fit 30 images in 1 page
}

$numResults = $resultsProvider->getNumResults($term);

echo "<p class='resultsCount'>$numResults results found</p>";


echo $resultsProvider->getResultsHtml($page, $pageSize, $term);
?>


    </div>



    <div class="paginationContainer">

        <div class="pageButtons">


            <div class="pageNumberContainer">
                <img src="assets/images/pageStart.png">
            </div>

            <?php


            $pagesToShow = 10;
            $numPages = ceil($numResults / $pageSize);
            $pagesLeft = min($pagesToShow, $numPages);

            $currentPage = $page - floor($pagesToShow / 2);
            //(currentPage is 10 = page(is a page we're on) - pageToShow($ that we set to only show 10 pages on each page) / 2 = 5)
            //example we are on page 10 we want to see 5 6 7 8 9 "10" 11 12 13 14 
            //pageToShow /2 = 5 so show 5pages before and 5 pages afterward
            //ex, pageToShow is 10/2 = 5 and if we're on page 6 so 6-5 =1 so puts us on page 1 so starts from page1 and forward
            

            if($currentPage < 1 ) {
                $currentPage =1;
            }   
            
            if($currentPage + $pagesLeft > $numPages +1) {
                $currentPage = $numPages + 1 - $pagesLeft;
            }

            while($pagesLeft != 0 && $currentPage <= $numPages) {

                if($currentPage == $page) {
                    echo"<div class='pageNumberContainer'>
                            <img src='assets/images/pageSelected.png'>
                            <span class='pageNumber'> $currentPage</span>
                    </div>";
                }
                else{
                    echo"<div class='pageNumberContainer'>
                        <a href='search.php?term=$term&type=$type&page=$currentPage'>
                            <img src='assets/images/page.png'>
                            <span class='pageNumber'> $currentPage</span>
                        </a>
                    </div>";
                }

                
                $currentPage++;
                $pagesLeft--;
            }








            ?>
            <div class="pageNumberContainer">
                <img src="assets/images/pageEnd.png">
            </div>


        </div>


    </div>






</div>
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
<script type="text/javascript" src="assets/js/script.js"> </script>
</body>
</html>