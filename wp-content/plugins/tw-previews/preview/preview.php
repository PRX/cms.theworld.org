<?php
/**
 * Preview template.
 *
 * @package tw_previews
 */

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Preview Window</title>
	<?php
	wp_print_scripts();
	?>
</head>
<body>
	<div id="<?php echo esc_attr( TW_PREVIEWS_APP_CONTAINER_ID ); ?>"></div>
</body>
</html>
