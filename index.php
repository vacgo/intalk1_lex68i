<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    
    if ($username === "aaa" && $password === "aaa") {
        $_SESSION["username"] = $username;
        header("Location: form.php");
        exit;
    } else {
        $error = "Hibás felhasználónév vagy jelszó.";
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="stilus.css">
    <title>Bejelentkezés</title>
</head>
<body>
    <h2>Bejelentkezés</h2>
    <form method="post" action="">
        <label for="username">Felhasználónév:</label>
        <input type="text" name="username" required><br>
        <label for="password">Jelszó:</label>
        <input type="password" name="password" required><br>
        <button type="submit">Bejelentkezés</button>
    </form>

    <?php if (isset($error)) { ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php } ?>
</body>
</html>