<?php use_helper('I18N') ?>
<form action="<?php echo url_for($sf_request->getParameter('module') . '/search') ?>" method="get" class="displayInline">
  <!--<input type="text" name="query" value="<?php echo $sf_request->getParameter('query'); ?>"
         id="search_keywords" class="searchForm"/>
  <input type="submit" value="Search" class="buttonWhite" />-->
  <input type="text" name="query" value="<?php echo $sf_request->getParameter('query'); ?>"
         id="search_keywords" class="searchTextBox"/><input class="searchButton" type="submit" name="submit" value="" style="margin-top: 3px; width: 28px; height:23px; display:inline; border: none;">
</form>