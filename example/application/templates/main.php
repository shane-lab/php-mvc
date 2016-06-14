<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo PRODUCT_NAME; ?></title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/content/site.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo BASE_URL; ?>assets/content/materialize/materialize.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="<?php echo BASE_URL; ?>assets/scripts/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo BASE_URL; ?>assets/scripts/jquery.materialize.min.js"></script>
</head>
<body>
<nav id="navbar" class="indigo sticky">
  <div class="nav-wrapper">
    <div class="hamburger-wrapper "><span class="hamburger-inner"></span></div>
    <div class="logo-wrapper">
      <a class="white-text waves-effect waves-light" style="margin-top: 4px;" href="<?php echo BASE_URL; ?>">
      HOME
        <!--<div class="shanelab"></div>-->
      </a>
    </div>
  </div>
</nav>
<div id="body-content">
  <?php require($this->_view_file); ?>
</div>
</body>
</html>