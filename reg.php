<?php
$reg = json_decode(file_get_contents('data/users.json'), true);
$un = $_POST['un'] ?? '';
$email = $_POST['email'] ?? '';
$pass1 = $_POST['pass1'] ?? '';
$pass2 = $_POST['pass2'] ?? '';

$recaptcha = $_POST['g-recaptcha-response'] ?? '';

if ($recaptcha != '') $res = reCaptcha($recaptcha);
$errors = [];

function reCaptcha($recaptcha)
{
    $secret = "SECRET_KEY";
    $ip = $_SERVER['REMOTE_ADDR'];

    $postvars = array("secret" => $secret, "response" => $recaptcha, "remoteip" => $ip);
    $url = "https://www.google.com/recaptcha/api/siteverify";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
    $data = curl_exec($ch);
    curl_close($ch);

    return json_decode($data, true);
}

if (count($_POST) > 0) {
    if (!$res['success'])
        $errors['captcha'] = 'A reCaptcha megadása kötelező!';
    if (trim($un) == '')
        $errors['un'] = 'A felhasználónév megadása kötelező!';
    else if (isset($reg[$un]))
        $errors['un'] = 'Foglalt felhasználónév';

    if (trim($email) == '')
        $errors['email'] = 'Az e-mail cím megadása kötelező!';
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors['email'] = 'Nem valid e-mail cím!';

    if (trim($pass1) == '')
        $errors['pass1'] = 'A jelszó megadása kötelező!';

    if (trim($pass2) == '')
        $errors['pass2'] = 'Mindkét jelszó megadása kötelező!';
    else if ($pass1 != $pass2)
        $errors['pass2'] = 'A jelszavaknak egyezniük kell';

    $errors = array_map(fn ($e) => "<span style='color:red'>$e</span>", $errors);

    if (!count($errors)) {
        $hash = password_hash($pass1, PASSWORD_DEFAULT);
        $reg[$un] = [
            'username' => $un,
            'email' => $email,
            'password' => $hash,
            'isAdmin' => false,
            'fav' => []
        ];
        file_put_contents('data/users.json', json_encode($reg, JSON_PRETTY_PRINT));
        header('location: login.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regisztráció - Eötvös Loránd Stadion</title>
    <meta name="author" content="Dienes Ádám (i93ajy)">
    <link rel="shortcut icon" href="img/icon.png" />
    <link rel="stylesheet" href="style/style.css">
</head>
<div id="container">
    <div id="form">
        <h1 id="form_title">Regisztráció</h1>
        <form action="reg.php" method="post" novalidate>
            Felhasználónév<br><input type="text" name="un" value="<?= $un ?>" autofocus> <?= $errors['un'] ?? '' ?> <br>
            E-mail<br><input type="text" name="email" value="<?= $email ?>"> <?= $errors['email'] ?? '' ?> <br>
            Jelszó<br><input type="password" name="pass1" value="<?= $pass1 ?>"> <?= $errors['pass1'] ?? '' ?> <br>
            Jelszó újra<br><input type="password" name="pass2" value="<?= $pass2 ?>"> <?= $errors['pass2'] ?? '' ?> <br>
            <div class="g-recaptcha brochure__form__captcha" style="transform:scale(0.9);transform-origin:0 0;margin-top:15px" data-sitekey="6LfPwAMeAAAAAMqkgRtqa-pkD9B4b1l7rLUnYDv0"></div><?= $errors['captcha'] ?? '' ?> <br>
            <button type="submit" id="login">Regisztráció</button>
        </form>
        <a href="index.php"><button id="back">Vissza ↺</button></a>
    </div>
</div>
<script src="https://www.google.com/recaptcha/api.js"></script>
</body>

</html>