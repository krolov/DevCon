<?php 
require('sql.php');
?>
<html>
  <head>
    <script src="assets/vis.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="assets/vis.css">
  </head>
  <body>
    <div id="visualization"></div>

<script type="text/javascript">

  var container = document.getElementById('visualization');
  var items = <?=sql::getEmotion('happiness') ?>;
console.log(items);
  var dataset = new vis.DataSet(items);
  var options = {
    start: '2016-09-15',
    end: '2016-12-12'
  };
  var graph2d = new vis.Graph2d(container, dataset, options);
</script>
  </body>
</html>