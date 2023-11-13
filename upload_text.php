<?php
require_once "config.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($link, $_POST["title"]);
    $content = mysqli_real_escape_string($link, $_POST["content"]);
    $is_public = isset($_POST["is_public"]) ? 1 : 0;
    $tags = $_POST["tags"];

    $user_id = $_SESSION["user_id"];

    $sql  = "INSERT INTO texts (text_id, user_id, title, content, is_public, created_at) VALUES (NULL, ?, ?, ?, ?, NOW())";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt,"isss", $user_id, $title, $content, $is_public);

        if (mysqli_stmt_execute($stmt)) {
            $upload_success = true;
        } else {
            $upload_error = "Error executing upload query: " . mysqli_error($link);
        }
        mysqli_stmt_close($stmt);
    } else {
        $upload_error = "Error preparing upload query: " . mysqli_error($link);
    }

    if ($upload_success) {
        header("Location: dashboard.php");
    } else {
        $upload_error = "Text upload failed";
    }
}
?>
