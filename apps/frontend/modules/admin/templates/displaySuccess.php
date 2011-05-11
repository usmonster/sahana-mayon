<h1>Edit Display Options</h1>
<caption>this is a very simple module that allows you to turn off a bunch of items from appearing in drop down lists, etc</caption>
<br />


<form action="<?php echo url_for('admin/display') ?>" method="post" enctype="multipart/form-data" name="display">
<!--<input type="hidden" name="sf_method" value="put" />-->

<?php
//code here and some default buttons to set app_display of items to 0

echo $form;
//$agReligions = Doctrine::getTable('agReligion')
//      ->createQuery('getReligions')
//      ->select('pn.*')
//      ->from('agPersonName pn')
//      ->where('pn.person_name = ?', $newName)
//      ->fetchOne()

?>
<br />
the items that you select above will have their app_display(visibility) set to 1, i.e. they will show up
<br />

<input type="submit" value="Make Changes" class="continueButton" />
</form>