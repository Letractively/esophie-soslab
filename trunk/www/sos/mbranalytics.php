<script type="text/javascript">
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-42095429-3', 'order.sophiemobile.com');
  <?php if (isset($ctrl) && strlen($ctrl->gaecommerce) > 0) echo $ctrl->gaecommerce; ?>
  ga('send', 'pageview', {
    'dimension3': 'MEMBER ORDER'<?php
    // PAGE TRACKING URI
    if (isset($ctrl) && strlen($ctrl->gapage) > 0)
    {
        echo ", 'page': '" .    $ctrl->gapage   . "'";
    }
    // PAGE TRACKING TITLE
    if (isset($ctrl) && strlen($ctrl->gatitle) > 0)
    {
        echo ", 'title': '" .   $ctrl->gatitle  . "'";
    }
    if (isset($ctrl) && $ctrl->login())
    {
        echo ", 'dimension1': 'MEMBER'";
        echo ", 'dimension2': '" . $ctrl->userid() . "'";
    }
?>});
</script> 
