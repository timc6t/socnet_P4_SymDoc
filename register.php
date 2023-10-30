<?php
$activation_key = generate_activation_key();

$activation_link = "https://yourwebsite.com/activate.php?key=$activation_key";

function generate_activation_key() {
    return bin2hex(random_bytes(16));
}
?>
