(function($) {
	$.fn.positioning = function(leftOffset, topOffset, scroll, center, horisontalAlign, verticalAlign){
		var ScrollTop=0;
		var ScrollLeft=0;
		var InnerWidth;
		var InnerHeight;
		/*Width,Height*/
		if (window.innerWidth){
			InnerWidth=window.innerWidth;
			InnerHeight=window.innerHeight;
		} else if (document.documentElement.offsetWidth){
			InnerWidth=document.documentElement.offsetWidth;
			InnerHeight=document.documentElement.offsetHeight;
		} else {
			InnerWidth=document.body.offsetWidth;
			InnerHeight=document.body.offsetHeight;
		}
	    /*Scroll left,top*/
		if (window.pageXOffset || window.pageYOffset){
			ScrollLeft=window.pageXOffset;
			ScrollTop=window.pageYOffset;
		} else if (document.documentElement.scrollLeft || document.documentElement.scrollTop){
			ScrollLeft=document.documentElement.scrollLeft;
			ScrollTop=document.documentElement.scrollTop;
		} else if (document.body.scrollLeft || document.body.scrollTop){
			ScrollLeft=document.body.scrollLeft;
			ScrollTop=document.body.scrollTop;
		}
		
		var str = new String(leftOffset);
		leftOffset = (str.indexOf('%') > 0) ? (leftOffset = parseInt(leftOffset) / 100 * InnerWidth) : (parseInt(leftOffset));

		str = new String(topOffset);
		topOffset = (str.indexOf('%') > 0) ? (topOffset = parseInt(topOffset) / 100 * InnerHeight) : (parseInt(topOffset));

		if (scroll){
			leftOffset+=ScrollLeft;
			topOffset+=ScrollTop;
		}
		this.css("position", "absolute");
		if (center){
			if (horisontalAlign && verticalAlign){
				this.css({"left" : ((InnerWidth + leftOffset - this[0].offsetWidth) / 2) + 'px', "top": InnerHeight + topOffset - ((this[0].offsetHeight + InnerHeight) / 2) + 'px'});									
			} else {
				if (horisontalAlign){
					this.css({"left" : ((InnerWidth + leftOffset - this[0].offsetWidth) / 2) + 'px', "top": topOffset + 'px'});	
				}
				if (verticalAlign){
					this.css({"top" : InnerHeight + topOffset - ((this[0].offsetHeight + InnerHeight) / 2) + 'px', "left": leftOffset + 'px'});				
				}
			}
		} else {
			this.css({"left" : leftOffset + 'px', "top": topOffset + 'px'})
		}
		return this;
	}
	
	$.fn.centering = function(horisontalAlign, verticalAlign){
		this.positioning(0,0,1,1, horisontalAlign, verticalAlign);
	}
	
	
})(jQuery)