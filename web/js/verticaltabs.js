/*
 *
 */

(function($){  
 $.fn.verticaltabs = function(options) {
 	 // Default Values
	 var defaults = {  
		speed: 500,
		activeIndex: 0
	 };  
	 var options2 = $.extend(defaults, options);
	   
	 // Main Plugin Code	 
	 return this.each(function() {  
		var verticaltabs = $(this);
		var tabs = $(verticaltabs).children(".verticalslider_tabs").children(); // all of the tabs
		var contents = $(verticaltabs).children(".verticalslider_contents").children(); // all of the contents
		var arrowBlock = "<div class=\"arrow\">&nbsp;</div>";
		var activeIndex = defaults.activeIndex;
		
		// Initializing Code
		$(contents[defaults.activeIndex]).addClass("activeContent");
		$(tabs[activeIndex]).addClass("activeTab").append(arrowBlock); // Set first tab and first content to active
		
		// Event Bindings
		$(".verticalslider_tabs a", verticaltabs).click(function (){	
			if (!$(this).parent().hasClass("activeTab")){ // do nothing if the clicked tab is already the active tab
				activeIndex	= $(this).parent().prevAll().length; // a clicked -> li -> previous siblings
				switchContents();
			}
			return false;
		});
		
		// Plugin Methods
		function switchContents() {	
			$(".activeTab", verticaltabs).removeClass("activeTab");
			$('.arrow', verticaltabs).remove();
			$(tabs[activeIndex], verticaltabs).addClass("activeTab").append(arrowBlock);	// Update tabs	
			$(".activeContent", verticaltabs).fadeOut(options2.speed).removeClass(".activeContent");
			$(contents[activeIndex], verticaltabs).fadeIn(options2.speed).addClass("activeContent"); // Update content
		};
		
	 }); 
 };  
})(jQuery);  



