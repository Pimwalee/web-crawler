var timer;

$(document).ready(function() { 
    

$(".result").on("click", function() { //.result is <a class='result' href='$url'> on Site.php
 
    var id = $(this).attr("data-linkId"); //says get the value of href
    var url = $(this).attr("href");
    //console.log(id);

    if(!id) {
        alert("data-linkId attribute not found");
    }

    increaseLinkClicks(id, url);

    return false;// to only count click but dont take us to the page we click
});

var grid = $(".imageResults");

grid.on("layoutComplete", function() {
    $(".gridItem img").css("visibility", "visible");
})//we set it hidden on css when loading // but here we set it as when it finishes calculating the layout it gonna set them to be visible again

grid.masonry({
    itemSelecto: ".gridItem",
    columnWidth: 200,
    gutter: 20,
    isInitLayout: false
});

    $("[data-fancybox]").fancybox({

        caption : function( instance, item ) {
            var caption = $(this).data('caption') || ''; //data-caption
            var siteUrl = $(this).data('siteurl') || ''; //data-siteurl from imageResultProvider
    
            
            if ( item.type === 'image' ) {
                caption = (caption.length ? caption + '<br />' : '')
                 + '<a href="' + item.src + '">View image</a><br>'
                 + '<a href="' + siteUrl + '">Visit page</a>';
            }
    
            return caption;

        },
       afterShow : function( instance, item ) {
            increaseImageClicks(item.src);
        }


    }); // call every fancy box element

});

function loadImage(src, className) {

    var image = $("<img>");
    image.on("load", function() { // 2. or
        $("." + className + " a").append(image);

        clearTimeout(timer);

        timer = setTimeout(function() {
            $(".imageResults").masonry();
        },500);  // when <img> load stop current time and in half of sec call the function.masonry again

    });

    image.on("error", function() { //3. depends if it load or error
        $("." + className).remove();

        $.post("ajax/setBroken.php", {src: src});

    });

    image.attr("src", src); //1. it will do this line first then



}

function increaseLinkClicks(linkId, url ) {

$.post("ajax/updateLinkCount.php",{linkId: linkId})
.done(function(result) { //when you done with ajax call then do the following function
    if(result != "") {
        alert(result);
        return;
    }
    window.location.href = url;
});  

}


function increaseImageClicks(imageUrl) {

    $.post("ajax/updateImageCount.php",{imageUrl: imageUrl})
    .done(function(result) { //when you done with ajax call then do the following function
        if(result != "") {
            alert(result);
            return;
        }
        
    });  
    }
