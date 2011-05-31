<div id="infobar" style="border:2px solid blue;">
  Completed in <?php echo $timer; ?> seconds.
</div>
  <script type="text/javascript">
    //TODO: use jQuery, put script in agMain.js

    var xmlHttp = new XMLHttpRequest();
    var async = false;
    xmlHttp.open("GET", "<?php echo url_for('agStaff/status.json'/*'facility/status.json'*/); ?>", async);
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
    document.getElementById('infobar').innerHTML += data;
    //alert(data);
  </script>
