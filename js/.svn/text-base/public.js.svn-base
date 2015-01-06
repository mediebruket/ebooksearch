// Tabbed content

jQuery(document).ready(function(){
    jQuery("ul#finneboktabs li").click(function(e){
        if (!jQuery(this).hasClass("active")) {
            var tabNum = jQuery(this).index();
            var nthChild = tabNum+1;
            jQuery("ul#finneboktabs li.active").removeClass("active");
            jQuery(this).addClass("active");
            jQuery("ul#finneboktab li.active").removeClass("active");
            jQuery("ul#finneboktab li:nth-child("+nthChild+")").addClass("active");
        }
    });
});

// Facebook sharer window

function fbShare(url, winWidth, winHeight) {
	var winTop = (screen.height / 2) - (winHeight / 2);
	var winLeft = (screen.width / 2) - (winWidth / 2);
	window.open('http://www.facebook.com/sharer.php?u=' + url, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight);
    }

// TINY ACCORDION

var TINY={};function T$(i){return document.getElementById(i)}function T$$(e,p){return p.getElementsByTagName(e)}TINY.accordion=function(){function slider(n){this.n=n;this.a=[]}slider.prototype.init=function(t,e,m,o,k){var a=T$(t),i=s=0,n=a.childNodes,l=n.length;this.s=k||0;this.m=m||0;for(i;i<l;i++){var v=n[i];if(v.nodeType!=3){this.a[s]={};this.a[s].h=h=T$$(e,v)[0];this.a[s].c=c=T$$('div',v)[0];h.onclick=new Function(this.n+'.pr(0,'+s+')');if(o==s){h.className=this.s;c.style.height='auto';c.d=1}else{c.style.height=0;c.d=-1}s++}}this.l=s};slider.prototype.pr=function(f,d){for(var i=0;i<this.l;i++){var h=this.a[i].h,c=this.a[i].c,k=c.style.height;k=k=='auto'?1:parseInt(k);clearInterval(c.t);if((k!=1&&c.d==-1)&&(f==1||i==d)){c.style.height='';c.m=c.offsetHeight;c.style.height=k+'px';c.d=1;h.className=this.s;su(c,1)}else if(k>0&&(f==-1||this.m||i==d)){c.d=-1;h.className='';su(c,-1)}}};function su(c){c.t=setInterval(function(){sl(c)},20)};function sl(c){var h=c.offsetHeight,d=c.d==1?c.m-h:h;c.style.height=h+(Math.ceil(d/5)*c.d)+'px';c.style.opacity=h/c.m;c.style.filter='alpha(opacity='+h*100/c.m+')';if((c.d==1&&h>=c.m)||(c.d!=1&&h==1)){if(c.d==1){c.style.height='auto'}clearInterval(c.t)}};return{slider:slider}}();

// hide missing images 
jQuery("img").error(function(){
        $(this).hide();
});

// Start Ready
jQuery(document).ready(function() {  


	// Live Search
	// On Search Submit and Get Results
    function search() {
	    var query_value = jQuery('input#search').val();
		var makstreff = jQuery('input#finnebok_makstreff').val();
		var formater = jQuery('input#pdf:checked').val() + jQuery('input#epub:checked').val();
	    jQuery('b#finnebok_search-string').html(query_value);
		if(query_value !== '') {
			jQuery.ajax({
				type: "POST",
				url: pluginsUrl,
				data: { query: query_value, makstreff: makstreff, format: formater },
				cache: true,
				success: function(html){
					jQuery("#finnebok_results").html(html);
				}
			});
		}return false;    
	}

	//jQuery("input#search").live("keyup", function(e) {
	jQuery( document ).on("keyup click", "input#search, input#pdf, input#epub", function(e) {
		// Set Timeout
	    clearTimeout(jQuery.data(this, 'timer'));

	    // Set Search String
	    //var search_string = jQuery(this).val();
		var search_string = jQuery('input#search').val();
		// Do Search
	    //if (search_string == '') {
		if (search_string.length < 3) {
		   	jQuery("#finnebok_results").fadeOut();
	    	jQuery('h4#results-text').fadeOut();
	    }else{
	    	jQuery("#finnebok_results").fadeIn();
	    	jQuery('h4#results-text').fadeIn();
	    	jQuery(this).data('timer', setTimeout(search, 100));
	    };
	    
	});

});
