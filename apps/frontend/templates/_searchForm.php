<?php use_helper('I18N') ?>
<form action="<?php echo url_for('staff_search') ?>" method="get" style="display: inline;">
  <input type="text" name="query" value="<?php echo $sf_request->getParameter('query') ?>
         "id="search_keywords" class="searchForm"/>
  <input type="submit" value="Search" class="buttonWhite" />
</form>
