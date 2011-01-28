<h3>New Address to GeoCode</h3>

<?php include_partial('form', array('form' => $form)) ?>
<h3>
  Address to Geocode from existing</h3><br/>
  <strong>, or from a new entity (workflow insert)</strong><br>

<small>here we need to put a button that (possibly jquery-ajax button which pulls
  information from our selected ag_geo_source</small><br>

  <small> will need database refactor: <strong>add datasource url and api</strong></small><br />

<form action="<?php echo url_for('gis/geocode') ?>" method="post">
  <label>number</label><input type="text" name="number" />
  <label>street</label><input type="text" name="street" />
  <label>zip</label><input type="text" name="zip" />
  <input type="submit">
</form>

