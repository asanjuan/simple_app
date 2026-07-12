<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Correo Electrónico de Ejemplo</title>
	<style>
	table {
		border-collapse:collapse;
		width:100%;
	}
	td, th {
		padding:5px;
		border: solid 1px;
	}

	</style>
</head>
<body style="font-family: Arial, sans-serif;">

    <div style="max-width: 600px; margin: 0 auto; padding: 20px; background-color: #eeeeee; border-radius: 10px;">
        <h1 style="color: #333;"><?php echo $data['header']; ?></h1>
        <p style="color: #555;"><strong> <a href="<?php echo $data['url']; ?>">Accede a la app</a> </strong></p>       
        <?php echo $data['body']; ?>
    </div>

</body>
</html>
