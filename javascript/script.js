
$(document).ready(function(){
    $(".result").on("click",function(){

        let id=$(this).attr("data-linkId");
        let url=$(this).attr("href");
        console.log(id)
        increaseLinkClicks(id,url)

        return false
    })
    let grid=$(".imgsResults");
    grid.on("layoutComplete",function(){
        $(".gridItem img").css("visibility","visible");
    }
    )

    grid.masonry({
        itemSelector:".gridItem",
        columnWidth:200,
        gutter:5,
        isInitLayout:false
    })
    $("[data-fancybox]").fancybox({

		caption : function( instance, item ) {
	        var caption = $(this).data('caption') || '';
	        var siteUrl = $(this).data('siteurl') || '';


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
    
    // grid.on("layoutComplete",function(){
    //     $(".gridItem img").css("visibility","visible");
    // })
    // $('[data-fancybox]').fancybox();
  


})
});

function loadImage(src, className) {
            let img = $("<img>");
            img.on('load', function() {
                $("." + className + " a").append(img);
                $(".imgsResults").masonry();
                
            });
            img.on('error', function() {
                // $("." + className).remove();
                // $.post("ajax/setBroken.php",{src:src});
            });
            img.attr("src", src);
            
        }

function increaseLinkClicks(linkId,url){
    $.post("ajax/updateLinkCount.php",{linkId:linkId}).done(
        function(result){
            if(result!=''){
                alert('result')
                return ;
            }
            window.location.href=url;
        }
    )
}
function increaseImageClicks(imageUrl) {

	$.post("ajax/updateImageCount.php", {imageUrl: imageUrl})
	.done(function(result) {
		if(result != "") {
			alert(result);
			return;
		}
	});

}