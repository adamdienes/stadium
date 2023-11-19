<?php
    session_start();
    $errors = [];
    $un = $_SESSION["un"] ?? "";
    if (isset($_SESSION["loginerror"])){
        if ($_SESSION["loginerror"] == 1) $errors['un'] = "Nem létező felhasználó!";
        if ($_SESSION["loginerror"] == 2) $errors['pw'] = "Helytelen jelszó!";
        unset($_SESSION["loginerror"]);
    }
    $errors = array_map(fn($e) => "<span style='color:red'>$e</span>", $errors);
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés - Eötvös Loránd Stadion</title>
    <meta name="author" content="Dienes Ádám (i93ajy)">
    <link rel="shortcut icon" href="img/icon.png" />
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <div id="container">
        <?php if (!isset($_SESSION["userid"])): ?>
            <div id="form">
                <h1 id="form_title">Bejelentkezés</h1>
                <form action="validate.php" method="post" novalidate>
                    Felhasználónév<br><input type="text" name="un" value="<?= $un ?>" autofocus> <?= $errors['un'] ?? '' ?><br>
                    Jelszó<br><input type="password" name="pw"> <?= $errors['pw'] ?? '' ?><br>
                    <div id="pad"><button type="submit" id="login">Bejelentkezés</button></div>
                </form>
                <a href="index.php"><button id="back">Vissza ↺</button></a>
            </div>
        <?php else: ?>
            <?php header("Location: index.php") ?>
        <?php endif; ?>
    </div>
</body>
</html>