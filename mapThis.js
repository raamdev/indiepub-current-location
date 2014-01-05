

jQuery(document).ready(function($) {
	$(document).ready(function () {
	   var cursorX;
	   var cursorY;
	   if (window.Event) {
	      document.captureEvents(Event.MOUSEMOVE);
	   }
	   document.onmousemove = getCursorXY;
	   $(".mapThis").each(function() {
	      var dPlace = $(this).attr("place");
	      var dZoom = $(this).attr("zoom");
	      var dText = $(this).html();
	      $(this).html('<a onmouseover="mapThis.show(this);" style="text-decoration:none; border-bottom:1px dotted #999" href="http://maps.google.com/maps?q=' + dPlace + '&z=' + dZoom + '">' + dText + '</a>');
	   });
	});
});	
	
	var mapThis=function(){
	 var tt;
	 var errorBox;
	 return{
	  show:function(v){
	    if (tt == null) {
	       var pNode = v.parentNode;
	       pPlace = $(pNode).attr("place");
	       pZoom = parseInt($(pNode).attr("zoom"));
	       pText = $(v).html();
	       tt = document.createElement('div');
	       $(tt).html('<a href="http://raamdev.com/travels/#map"><img border=0 src="http://maps.google.com/maps/api/staticmap?center=' + pPlace + '&zoom=' + pZoom + '&size=300x300&sensor=false&format=png&markers=color:green|' + pPlace + '"></a>');
	       tt.addEventListener('mouseover', function() { mapHover = 1; }, true);
	       tt.addEventListener('mouseout', function() { mapHover = 0; }, true);
	       tt.addEventListener('mouseout', mapThis.hide, true);
	       document.body.appendChild(tt);    
	    }
	    fromleft = cursorX;
	    fromtop = cursorY;
	    fromleft = fromleft - 25;
	    fromtop = fromtop - 25;
	    tt.style.cssText = "position:absolute; left:" + fromleft + "px; top:" + fromtop + "px; z-index:999; display:block; padding:1px; margin-left:5px; background-color:#333; width:300px; height:300px; -moz-box-shadow:0 1px 10px rgba(0, 0, 0, 0.5);";   
	    tt.style.display = 'block';
	  },
	  hide:function(){
	   tt.style.display = 'none';
	   tt = null;
	  }
	 };
	}();
	function getCursorXY(e) {
	   cursorX = (window.Event) ? e.pageX : event.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
	   cursorY = (window.Event) ? e.pageY : event.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
	}