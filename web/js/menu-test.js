function initMenus() {
		
	$('ul.menu_summary ul').hide();
	
	$('<span class="link-arrow"></span>').appendTo('ul.menu_summary > li > a')
	$('span.link-arrow').html('&#9660;');
	
	//toggle functionality
	$('div a.expandall').click(
		function() {
			$('ul.menu_summary ul').show('slow');
			$('ul.menu_summary li a').addClass('selectedListItem');
			$('ul.menu_summary li a span.link-arrow').html("&#9650;"); //up arrow
			$('ul.menu_summary li div.grp_sumry').hide();
			      });
	$('div a.collapseall').click(
		function() {
			$('ul.menu_summary ul').hide('slow');
			$('ul.menu_summary li a').removeClass('selectedListItem');
			$('ul.menu_summary li a span.link-arrow').html("&#9660;"); //down arrow
      $('ul.menu_summary li div.grp_sumry').show();
			     });
	
	//end toggle functionality

	$.each($('ul.menu_summary'), function(){
		var cookie = $.cookie(this.id);
		if(cookie === null || String(cookie).length < 1) {
			$('#' + this.id + '.expandfirst ul:first').show();
			$('#' + this.id + '.expandfirst a:first').addClass('selectedListItem');
			$('#' + this.id + '.expandfirst a:first span.link-arrow').html("&#9650;"); //up arrow
      $('#' + this.id + '.expandfirst ul:first').parent().children('div.grp_sumry').hide();
		 
		}
		else {			
			$('#' + this.id + ' .' + cookie).next().show();
			$('#' + this.id + ' .' + cookie).addClass('selectedListItem');
			$('#' + this.id + ' .' + cookie + ' span.link-arrow').html("&#9650;"); //up arrow
			$('#' + this.id + ' .' + cookie).parent().children('div.grp_sumry').hide();
		}
		
		//alert(cookie);
		
	});
	
	
	
	$('ul.menu_summary li a').click(
		function(e) {
                 e.preventDefault();
			var checkElement = $(this).next();
			var parent = this.parentNode.parentNode.id;

			if($('#' + parent).hasClass('noaccordion')) {
				if((String(parent).length > 0) && (String(this.className).length > 0)) {
					if($(this).next().is(':visible')) {
						$.cookie(parent, null);
					}
					else {
						$.cookie(parent, this.className);
					}
					$(this).next().slideToggle('normal');
					 $(this).toggleClass('selectedListItem');
					 $(this).children('span.link-arrow').html($(this).hasClass('selectedListItem') ? '&#9650;' : '&#9660;');
           $(this).parent().children('div.grp_sumry').toggle();
				}				
			}
		} 
		
	);
 	
}
 
$(document).ready(function() {initMenus();});
