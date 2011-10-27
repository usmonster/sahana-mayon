<?php use_helper('I18N') ?>


<form action="<?php echo url_for($sf_request->getParameter('module') . '/list') ?>" method="post" class="displayInline">
  <input type="text" name="query" value="<?php echo $sf_request->getParameter('query'); ?>"
         id="search_keywords" class="searchTextBox"/><input id="submit_search" class="searchButton" type="submit"
         style="margin-top: 3px; width: 28px; height:23px; display:inline; border: none; font-size: 0px" />
</form> 


<!--Code for JQUERY Search Box
<a href="#" class="signin" style="float:right; margin-top: 12px; margin-right:12px; text-decoration:none; color:#999"><span>Search</span></a>
          <span class="MarginPointOneEm">
           <fieldset id="signin_menu">
          <form action="<?php echo url_for($sf_request->getParameter('module') . '/search') ?>" method="get" class="displayInline">

  <input type="text" name="query" value="<?php echo $sf_request->getParameter('query'); ?>"
         id="search_keywords" class="searchTextBox"/><input id="submit_search" class="searchButton" type="submit"
         style="border: none; font-size: 0px" />
</form>
 </fieldset>
        </span>

<script type="text/javascript">
        $(document).ready(function() {

            $(".signin").click(function(e) {
				e.preventDefault();
                $("fieldset#signin_menu").toggle();
				$(".signin").toggleClass("menu-open");
				$("#search_keywords").focus();
                                $("#search_keywords").select();
            });

			$("fieldset#signin_menu").mouseup(function() {
				return false
			});
			
                        $('fieldset#signin_menu').click(function(event) {
				event.stopPropagation();
			});

                        $(document).click(function(e) {
				if($(e.target).parent("a.signin").length==0) {
					$(".signin").removeClass("menu-open");
					$("fieldset#signin_menu").hide();
                                        //$("fieldset#signin_menu").fadeOut(1000);
					//$("#search_keywords").val("");
				}
			});

        });
    </script>
 End Code for JQUERY Search Box-->