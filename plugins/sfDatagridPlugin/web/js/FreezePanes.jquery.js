/**
freeze panes / jquery Datagrid
**/
$(document).ready(function(){
    freezePanes();	
});


function freezePanes(dgid){
    if(dgid){
    	grid=$('#'+dgid).find('.grid');
    }else{
    	grid=$('.grid');
    }
    $(grid).attr('cellspacing',0);
	cols= $(grid).find('tr.lt:first').find('td').length;
    c_width=new Array();
    $(grid).find('tr td.filter').each(function(i){
    	c_width[i]=$(this).width();
    	$(this).attr('style','width:'+(c_width[i])+'px');
    });
    $(grid).find('tr th').each(function(i){
    		$(this).attr('style','width:'+(c_width[i])+'px');
    		c_width[i]=$(this).width();
    });
    
	$(grid).find('tbody').append('<tr><td style="padding:0; border:0 none;" colspan="'+cols+'"><div class="grid-fixed-height"><table cellspacing="0" border="0" style="width:100%" cellspadding="0"></table></div></td></tr>');

	$(grid).find('tr').each(function(){
			if($(this).hasClass('lt') || $(this).hasClass('dr')){
				//Resize cols
				$(this).find('td').each(function(i){
					if(i!=(cols-1))
						$(this).attr('style','width:'+(c_width[i])+'px');
					else
						$(this).attr('style','width:'+(c_width[i]-17)+'px');//17 px scrollbar
				});
				//Replace row
				$(this).parent().parent().find('.grid-fixed-height table').append($(this));
			}
		})
	;
	//Si pas scrollbar
	if(!$('.grid-fixed-height').hasScrollbar()){
			$('.grid-fixed-height tr td:last').attr('style','width:'+(c_width[(cols-1)])+'px');
	}
}

/**
see : http://stackoverflow.com/questions/2578046/scrollbar-appear-disappear-event-in-jquery
**/
jQuery.fn.hasScrollbar = function() {
    var scrollHeight = this.get(0).scrollHeight;

    //safari's scrollHeight includes padding
    if ($.browser.safari)
        scrollHeight -= parseInt(this.css('padding-top')) + parseInt(this.css('padding-bottom'));

    if (this.height() < scrollHeight)
        return true;
    else
        return false;
}
