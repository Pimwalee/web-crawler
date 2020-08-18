<?php
class SiteResultsProvider {

    private $con;

    public function __construct($con) {
        $this->con = $con;
    }

    public function getNumResults($term) {

        $query = $this->con->prepare("SELECT COUNT(*) as total
                                        FROM sites WHERE title LIKE :term
                                        OR url LIKE :term
                                        OR keywords LIKE :term
                                        OR description LIKE :term");
        $searchTerm = "%". $term . "%";
        $query->bindParam(":term", $searchTerm);
        $query->execute();


        $row = $query->fetch(PDO::FETCH_ASSOC);
        return $row["total"];

    }

    public function getResultsHtml($page, $pageSize, $term){

        $fromLimit = ($page - 1)* $pageSize;
        //page 1: (1 - 1) * 20  =  0
        //page 2: (2 - 1) * 20  =  20
        //page 3: (3 - 1) * 20  =  40
        //so all the pages will only search and show us 20 pages per page

        $query = $this->con->prepare("SELECT *
                                        FROM sites WHERE title LIKE :term
                                        OR url LIKE :term
                                        OR keywords LIKE :term
                                        OR description LIKE :term
                                        ORDER BY clicks DESC
                                        LIMIT :fromLimit, :pageSize");//search and show from page0 stop searching/showing at page20

        $searchTerm = "%". $term . "%";
        $query->bindParam(":term", $searchTerm);
        $query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);//this is an integer 
        $query->bindParam(":pageSize", $pageSize, PDO::PARAM_INT);

        $query->execute();


        $resultsHtml = "<div class='siteResults'>";

        while($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $id = $row["id"];
            $url = $row["url"];
            $title = $row["title"];
            $description = $row["description"];

            $title = $this->trimField($title,55);//limit 55 str
            $description = $this->trimField($description, 230);// limit desc 230 str

            $resultsHtml .= "<div class='resultContainer'>

                            <h3 class='title'>
                                <a class='result' href='$url' data-linkId='$id'>
                                    $title
                                </a>
                                </h3>
                                <span class='url'>$url </span>
                                <span class='description'>$description </span>
                            
                            </div>";
            
            
        }


        $resultsHtml .="</div>";

        return $resultsHtml;
    }
    
    private function trimField($string, $charactorLimit){
        $dots = strlen($string) > $charactorLimit  ? "..." :"";//if  strlength is more than limit print... else empty""
        return substr($string,0 ,$charactorLimit) . $dots;
    
    }
    

}
?>