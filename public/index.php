<?php
/**
 * Daft Search
 *
 * @author Peter Caulfield <peterjcaulfield@gmail.com>
 */
 ?>

<!DOCTYPE html>
<html>
<head>
<link href="css/style.css" rel="stylesheet" type="text/css" >
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="js/main.js"></script>
</head>
<body>
   <h1>Search for a property</h1>
   <form method="GET" action="search.php">
    <input type="text" size="100" name="search" id="search">
    <input type="submit" value="search" id="submit">
   </form>
   <div id="responseText" class="response"></div>
   <div id="results" class="response"></div>
</body>
</html>
