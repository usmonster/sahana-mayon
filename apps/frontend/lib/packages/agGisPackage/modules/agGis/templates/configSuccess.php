<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<h3>Configure GIS Settings</h3>

<h4>Map URL</h4>
<?php echo $map_url->getValue();?>
<br>

<a href="<?php echo url_for('admin/config?param='.$map_url->getId()) ?>">edit</a>



