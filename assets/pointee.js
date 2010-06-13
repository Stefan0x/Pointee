jQuery.fn.elementlocation = function() {
  var curleft = -1;
  var curtop = -1;
  var obj = this;
  do {
	curleft += obj.attr('offsetLeft');
	curtop += obj.attr('offsetTop');
	obj = obj.offsetParent();
  } while ( obj.attr('tagName') != 'BODY' );
  return ( {x:curleft, y:curtop} );
}; 
      		 	
var marker_offset_x = 9;
var marker_offset_y = 27;

// move the markers on page load
$(window).load(function() {
	$(".pointee-wrapper").each(function(){
		if($(this).is('.pointee-wrapper', this)){
			var marker_x = $('.x-coordinate', this).text();
			var marker_y = $('.y-coordinate', this).text();
			var marker_xy =  marker_x + '|' + marker_y;

			$('.map_marker', this).css({
			left: (marker_x - marker_offset_x) + 'px', top: (marker_y - marker_offset_y) + 'px'
			});

		}	
	});
});

$(document).ready(function(){

	$(".pointee-wrapper").each(function(){	

		if($(this).is('.pointee-wrapper', this)){
			
			$('img.clickable-image',this).click( function( eventObj ){

					var location = $(this, 'img.clickable-image').elementlocation();
					var marker_x = eventObj.pageX - location.x;
					var marker_y = eventObj.pageY - location.y;
					var marker_xy =  marker_x + '|' + marker_y;

					$(this).parent('.map-wrapper').children('p').children('.x-coordinate').text( marker_x );
					$(this).parent('.map-wrapper').children('p').children('.y-coordinate').text( marker_y );
					$(this).parent('.map-wrapper').children('.map_marker').css(
					{
						
						left: (marker_x - marker_offset_x) + 'px', top: (marker_y - marker_offset_y) + 'px'
					});

					$(this).parent('.map-wrapper').children('input.coordinates').val(marker_xy);

			});
		}
	});
	
	$('.pointee_image_select button').click(function() {
	  $(this).parents('table.pointee_image_select').next('span').text('Please save the entry to view the image');
	});
	
	
});

