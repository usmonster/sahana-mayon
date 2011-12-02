
<h2><?php echo sfConfig::get('sf_application_name'); ?></h2>

  <p>
    The <?php echo sfConfig::get('sf_application_name'); ?> is an application with tools to manage staff
    and facility resources, response plans, and deploy emergency event response via an easy to use
    web interface.
  </p>

<?php if (!$sf_user->isAuthenticated()): ?>
    <h3>"To begin, please login in the upper right.</h3><br/>;
<?php endif; ?>

<?php if ($sf_user->isAuthenticated()): ?>
    <h3>To begin, select an option from the menus above or the icons below.</h3>


    <table cellspacing='20'>
      <tr>
        <td><?php echo link_to('Prepare', 'home/prepare', array('class' => 'generalButton width140', 'title' => 'Prepare a Response Scenario')); ?></td>
        <td><?php echo link_to('Respond', 'home/respond', array('class' => 'generalButton width140', 'title' => 'Deploy and Manage Events')); ?></td>
      </tr>
      <tr>
        <td><?php echo link_to('Wiki Home', public_path('wiki/doku.php'), array('class' => 'generalButton width140', 'title' => 'Help', 'target' => '_blank')); ?></td>
        <td><?php echo link_to('Administration', 'admin/index', array('class' => 'generalButton width140', 'title' => 'Administration')); ?></td>
      </tr>
    </table>
<?php endif; ?>
    
  <h3>If you are working at a shelter with and entering client data:</h3>
  <table cellspacing='20'>
    <tr>
      <td><a href="<?php echo $vesuvius_address; ?>" class="generalButton width140" title="Jump to the Sahana Registry Program" target="_blank"> Jump to the Sahana Registry Program</a>

</td>
  </tr>
</table>