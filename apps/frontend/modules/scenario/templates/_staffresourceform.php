<?php
  $this->form = new sfForm();
  foreach ($staffResourceForms as $key => $srForm) {
    $this->form->embedForm($key, $srForm);
  }
  echo $this->form;
?>