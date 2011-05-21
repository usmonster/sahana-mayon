<div id="infobar" style="border:2px solid blue;">
  Completed in <?php echo $timer; ?> seconds.
  <script type="text/javascript">
    //TODO: use jQuery, put script in agMain.js

    var xmlHttp = new XMLHttpRequest();
    var async = false;
    xmlHttp.open("GET", "<?php echo url_for('global/status.json'/*'facility/poll'*/); ?>", async);
    if(async)
    {
      xmlHttp.onreadystatechange = function()
      {
        if(xmlHttp.readyState == 4)
        {
          if (xmlHttp.status==200) alert("It works!")
          else if (xmlHttp.status==0) alert("Arggggg!")
          else alert("Status is "+xmlHttp.status)
        }
      }
    }
    xmlHttp.send();

    var data = xmlHttp.responseText;
    //alert(data);
  </script>

</div>
