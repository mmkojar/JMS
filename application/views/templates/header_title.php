<?php 

$link = $_SERVER['PHP_SELF'];
$link_array = explode('/',$link);
$page = end($link_array);
?>

<div class="card-header">
	<h4 class="float-left mb-0 mt-2"><?php echo $title; ?></h4>
</div>