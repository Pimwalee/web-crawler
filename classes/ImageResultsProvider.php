<?php
class ImageResultsProvider { 
    // taking the image result out of the database

    private $con;

    public function __construct($con) {
        $this->con = $con;
        // in js this.con = con
    }
    /*This function returns a count of all of rows in the image table for the searched $term */
    public function getNumResults($term) {

        $query = $this->con->prepare("SELECT COUNT(*) as total
                                        FROM image
                                        WHERE (title LIKE :term
                                        OR alt LIKE :term)
                                        AND broken=0");

        $searchTerm = "%". $term . "%";
        $query->bindParam(":term", $searchTerm);
        $query->execute();

        $row = $query->fetch(PDO::FETCH_ASSOC);
        return $row["total"];

    }

    public function getResultsHtml($page, $pageSize, $term){

        $fromLimit = ($page - 1)* $pageSize;
        //-1 because it's an array so we start counting it at 0
        //page 1: (1 - 1) * 30  =  0
        //page 2: (2 - 1) * 30  =  30
        //page 3: (3 - 1) * 30  =  60
        //so all the pages will only search and show us 30 pages per page

        $query = $this->con->prepare("SELECT *
                                        FROM image
                                        WHERE (title LIKE :term
                                        OR alt LIKE :term)
                                        AND broken=0
                                        ORDER BY clicks DESC
                                        LIMIT :fromLimit, :pageSize");//search and show from page0 stop searching/showing at page20

        $searchTerm = "%". $term . "%";
        $query->bindParam(":term", $searchTerm);
        $query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);//this is an integer 
        $query->bindParam(":pageSize", $pageSize, PDO::PARAM_INT);

        $query->execute();


        $resultsHtml = "<div class='imageResults'>";

        $count = 0;
        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $count++;
            $id = $row["id"];
            $imageUrl = $row["imageUrl"];
            $siteUrl = $row["siteUrl"];
            $title = $row["title"];
            $alt = $row["alt"];

            if($title) {
                $displayText = $title;
            }
            else if($alt) {
                $displayText = $alt;
            }
            else {
                $displayText = $imageUrl;
            }
            
            $resultsHtml .= "<div class='gridItem image$count'>

                                <a href='$imageUrl' data-fancybox data-caption='$displayText'
                                data-siteurl='$siteUrl'>

                                <script>
                                $(document).ready(function() {
                                    loadImage(\"$imageUrl\",\"image$count\");
                                });
                                </script>

                                    <span class='details'> $displayText </span>
                                 </a>

                             
                            </div>";
            
            
        }


        $resultsHtml .="</div>";

        return $resultsHtml;
    }
    
    


}
?>