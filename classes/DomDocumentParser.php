<?php
class DomDocumentParser {

    private $doc;
             /* $doc contains a wholeHTML of that website */

    public function __construct($url){
    
        $options = array( 
            'http'=>array('method'=>"GET", 'header'=>"User-Agent: goowaleeBot/0.1\n")
            );
            /*  * $options is when we request. A webpage will go to $url and request the whole document
                and the option we're specifying. 
                * User-Agent is how a website knows who's visited the website : name of the website/version
             */
        $context = stream_context_create($options);
            /*  Sends an http request to $url
                with additional headers shown above 
            */
        $this->doc = new DomDocument();
            /*  * creates a new object(new DomDocument()) and we load the HTML into the object by using the function file_get_contents()
                * new DomDocument() is a built-in php class to allow us to perform actions on webpages on DomDocument
            */
         $this->doc->loadHTML(file_get_contents($url, false, $context));

    }       /*  * file_get_contents to convert file to one big string file
                * making the request to $url
                * passing the contents of that website into our DomDocument object.
                * false is whether we want to use the include part (configuration url)for php
                * $doc contains a wholeHTML of that website
            */

    public function getLinks() {
        return $this->doc->getElementsByTagName("a");
    }

    public function getTitletags() {
        return $this->doc->getElementsByTagName("title");
    }

    public function getMetatags() {
        return $this->doc->getElementsByTagName("meta");
    }

    public function getImages() {
        return $this->doc->getElementsByTagName("img");
    }
}
?>