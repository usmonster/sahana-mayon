/* 
Simple JQuery menu.
HTML structure to use:

Notes: 

1: each menu MUST have an ID set. It doesn't matter what this ID is as long as it's there.
2: each menu MUST have a class 'menu' set. If the menu doesn't have this, the JS won't make it dynamic

Optional extra classnames:

noaccordion : no accordion functionality
collapsible : menu works like an accordion but can be fully collapsed
expandfirst : first menu item expanded at page load

<ul id="menu1" class="menu [optional class] [optional class]">
<li><a href="#">Sub menu heading</a>
<ul>
<li><a href="http://site.com/">Link</a></li>
<li><a href="http://site.com/">Link</a></li>
<li><a href="http://site.com/">Link</a></li>
...
...
</ul>
<li><a href="#">Sub menu heading</a>
<ul>
<li><a href="http://site.com/">Link</a></li>
<li><a href="http://site.com/">Link</a></li>
<li><a href="http://site.com/">Link</a></li>
...
...
</ul>
...
...
</ul>

Copyright 2008 by Marco van Hylckama Vlieg

web: http://www.i-marco.nl/weblog/
email: marco@i-marco.nl

Free for non-commercial use
*/

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
			
		 
			      });
	$('div a.collapseall').click(
		function() {
			$('ul.menu_summary ul').hide('slow');
			$('ul.menu_summary li a').removeClass('selectedListItem');
			$('ul.menu_summary li a span.link-arrow').html("&#9660;"); //down arrow
			     });
	
	//end toggle functionality

	$.each($('ul.menu_summary'), function(){
		var cookie = $.cookie(this.id);
		if(cookie === null || String(cookie).length < 1) {
			$('#' + this.id + '.expandfirst ul:first').show();
			$('#' + this.id + '.expandfirst a:first').addClass('selectedListItem');
			$('#' + this.id + '.expandfirst a:first span.link-arrow').html("&#9650;"); //up arrow
		 
		}
		else {			
			$('#' + this.id + ' .' + cookie).next().show();
			$('#' + this.id + ' .' + cookie).addClass('selectedListItem');
			$('#' + this.id + ' .' + cookie + ' span.link-arrow').html("&#9650;"); //up arrow
			
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
				}				
			}
		} 
		
	);
 	
}
 
$(document).ready(function() {initMenus();});
