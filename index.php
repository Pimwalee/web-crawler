<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Goowalee</title>

    <meta name="description" content="Search the web for sites and images.">
    <meta name="keywords" content="Search engine, Goowalee, websites">
    <meta name="author" content="Pimwalee H">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body>



    <div class ="wrapper indexPage"> <!--2 classes bc we want the code to only applied on index page-->

        <div class="mainSection">

            <div class="logoContainer">
                <img src="assets/images/goowaleelogo.png" title="Logo of our site" alt="Site logo">
            </div>

            <div class="searchContainer">

                <form action="search.php" method="GET"> <!--sending data to another page-->

                    <input class="searchBox" type="text" name="term" placeholder="Enter search">
                     <!--name="term" is what we will see on the link like this. if we search the word dogs it will be term=dogs => http://localhost:8080/doodle/search.php?term=dogs-->
                    <input class="searchButton" type="submit" value="Search">



                </form>

            </div>

        </div>


    </div>
</body>
</html>