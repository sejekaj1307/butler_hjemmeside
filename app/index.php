<?php
$conn = mysqli_connect('localhost', 'victor', 'test1234', 'butler_hjemmeside');
if (!$conn) {
    echo 'Connection error: ' . mysqli_connect_error();
}
?>

<html>

<head>
    <title>PHP Test</title>
</head>

<body>
    <?php echo '<p>Hello World</p>'; ?>
</body>

</html>