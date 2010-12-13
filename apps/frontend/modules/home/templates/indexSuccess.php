        <p>Welcome to the Office of Emergency Management's NYC Coastal Storm Plan's web application. This Agasti installation will allow you to manage staff, resources, clients and facilities through an easy to use web interface.</p>
        <?php
          if (!$sf_user->isAuthenticated())
          {
            echo "<p>Please login on the top right.</p>";
          }
        ?>
        <p>Built with PHP, MySQL, Doctrine ORM and Symfony.</p>