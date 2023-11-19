<?php
    /* Készítette: Dienes Ádám - i93ajy */
    /* Webprogramozás: PHP beadandó */
    
    session_start();
    $teams = json_decode(file_get_contents('data/teams.json'), true);   
    $matches = json_decode(file_get_contents('data/matches.json'), true);
    $users = json_decode(file_get_contents('data/users.json'), true);

    uasort($teams, function($a, $b){
        return (($a['name'] <=> $b['name']));
    });

    foreach($matches as $key => $match){
        if ($match['home']['score'] == "" || $match['away']['score'] == "") unset($matches[$key]);
    }    
    uasort($matches, function($a, $b){
        return (($b['date'] <=> $a['date']));
    });
    $top5_matches = array_slice($matches, 0, 5);

    if (isset($_SESSION["userid"])){
        $id = $_SESSION['userid'];
        $users = json_decode(file_get_contents('data/users.json'), true);
        $user = $users[$id] ?? null;

        //favorite team
        foreach($top5_matches as $key => $match){
            if (!in_array($match['home']['id'], $user['fav']) 
            && !in_array($match['away']['id'], $user['fav'])) unset($top5_matches[$key]);
        }    
    }
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Főoldal - Eötvös Loránd Stadion</title>
    <meta name="author" content="Dienes Ádám (i93ajy)">
    <link rel="shortcut icon" href="img/icon.png" />
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <div id="container">
        <div id="buttons">
        <?php if (!isset($_SESSION["userid"])): ?>
            <a href="login.php"><button id="login">Bejelentkezés</button></a>
        <?php else: ?>            
            <a href="logout.php"><button id="logout">Kijelentkezés</button></a>
        <?php endif; ?>
            <a href="reg.php"><button id="reg">Regisztráció</button></a>
        </div>
        <h1>Eötvös Loránd Stadion</h1>
        <h2 id="subtitle">Listaoldal</h2>
        <?php if (isset($_SESSION["userid"])): ?>
            <h2>Hello <?= $user['username'] ?>!</h2>
        <?php endif; ?>
        <p id="details">Alább találhatóak az <b>Eötvös Loránd Stadionban</b> az idei szezonban játszott meccsek eredményei. A megfelelő csapatra kattintva, annak csapatrészletei érhetőek el. Bejelentkezés után lehetőségünk van <b>hozzászólást</b> írni az adott csapathoz, illetve szabadon tudunk regisztrálni felhasználónév, e-mail és jelszó megadásával. Minden felhasználó több csapatot is meg tud jelölni <b>kedvencként</b> a <i>csapatrészletek</i> oldalon, így kiemelten nyomon tudja követni azok eredményeit. A <b>Lekérdezés</b> gomb segítségével lehetőség van további 5 lejátszott meccs megjelenítésére, figyelembe véve a kedvencnek jelölést.</p>
        <p class="title">Regisztrált csapatok</p>
        <ul id="teams">
            <?php foreach($teams as $r): ?>
            <li><a href="details.php?id=<?= $r['id'] ?>"><?= $r['name'] ?> (<?= $r['city'] ?>)
            <?php if (isset($_SESSION["userid"]) && in_array($r['id'], $user['fav'])): ?>
                <span class="star">*</span>
            <?php endif; ?> 
            </a></li>
            <?php endforeach ?>
        </ul>
        <p class="title">Legutóbbi 5 lejátszott meccs</p>
        <?php if (isset($_SESSION["userid"])): ?>
        <span>Bejelentkezett felhasználók számára <b>csak a kedvelt csapatok (*) jelennek meg.</b><br>Ha más eredményre is kíváncsi vagy: vedd fel azt a csapatot is kedvencként a csapatrészletek oldalon vagy jelentkezz ki.</span><br>
        <div id="fav"><b>Kedvencnek jelölt csapataid:</b> 
            <?php foreach($user['fav'] as $r): ?>
                <?= $teams[$r]['name'] ?> - 
            <?php endforeach ?>
        </div>
        <?php endif; ?>
        <ul>
            <?php foreach($top5_matches as $r): ?>
            <li><?= $r['date'] ?><br><?= $teams[$r['home']['id']]['name'] ?> vs. <?= $teams[$r['away']['id']]['name'] ?> (<?= $r['home']['score'] ?> - <?= $r['away']['score'] ?>)</li>
            <?php endforeach ?>
        </ul>
        <p class="title">További 5 lejátszott meccs eredménye</p>
        <button id="loadButton">Lekérdezés</button>
        <ul id="loadList"></ul>
        <footer>Készítette: Dienes Ádám / I93AJY</footer>
    </div>
    <script src="js/ajax.js"></script>
</body>
</html>