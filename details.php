<?php
    session_start();
    date_default_timezone_set('Europe/Budapest');
    $users = json_decode(file_get_contents('data/users.json'), true);
    $message = $_POST['message'] ?? '';    
    $errors = [];

    if (isset($_SESSION["userid"])){
        $id = $_SESSION['userid'];
        $user = $users[$id] ?? null;
    }

    if (isset($_GET['id'])){
        $comments = json_decode(file_get_contents('data/comments.json'), true);
        $teams = json_decode(file_get_contents('data/teams.json'), true);
        $matches = json_decode(file_get_contents('data/matches.json'), true);
        if (isset($teams[$_GET['id']])){
            $i = $teams[$_GET['id']];
            $club = $teams[$_GET['id']];
        } else { header('location: index.php'); exit(); }
    } else { header('location: index.php'); exit(); }

    uasort($matches, function($a, $b){
        return (($a['date'] <=> $b['date']));
    });

    $link = "details.php?id=" .$club['id'];
    if(count($_POST) > 0){
        if (trim($message) == '')
            $errors['message'] = 'Az üzenet megadása kötelező!'; 

        if (!count($errors)){  
            $index = count($comments)+1;        
            $comments[$index] = [
                'teamid' => $club['id'],
                'text' => $message,
                'author' => $_SESSION["userid"],
                'date' => date("Y-m-d H:i")
            ];
            file_put_contents('data/comments.json', json_encode($comments, JSON_PRETTY_PRINT));
            header('location:'. $link);
            exit();
        }
    } 
    $errors = array_map(fn($e) => "<span style='color:red'>$e</span>", $errors);   

    function findColor($home, $away){
        global $club;
        if ($home['score'] == "" || $away['score'] == "") return "#999999";
        $hs = intval($home['score']);
        $as = intval($away['score']);
        if ($home['id'] == $club['id']){
            if ($hs > $as) return "#8FE388";
            if ($hs < $as) return "#FF7276";
            if ($hs == $as) return "#FFE983";
        } else {
            if ($hs < $as) return "#8FE388";
            if ($hs > $as) return "#FF7276";
            if ($hs == $as) return "#FFE983";
        }
    }
    
    function countFav($v){
        $ctr = 0;
        global $users;
        foreach($users as $key => $r)
            if (in_array($v, $r['fav'])) $ctr++;
        return $ctr;
    }
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Csapatrészletek - Eötvös Loránd Stadion</title>
    <meta name="author" content="Dienes Ádám (i93ajy)">
    <link rel="shortcut icon" href="img/icon.png" />
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <div id="container">
        <h1>Eötvös Loránd Stadion</h1>
        <h2 id="subtitle">Csapatrészletek</h2>
        <a href="index.php"><button id="reg">↺ Vissza a kezdőlapra</button></a>
        <p class="title">Csapatnév: <?= $i['name'] ?></p>
        <?php if (isset($_SESSION["userid"]) && ($key = array_search($i['id'], $user['fav'])) !== false): ?>
        <span><a href="favorite.php?remove=<?= $_GET['id'] ?>"><b>[Eltávolítás a kedvencek közül]</b>⭐</a></span>
        <?php else: ?>  
            <?php if (isset($_SESSION["userid"])): ?> 
            <a href="favorite.php?add=<?= $_GET['id'] ?>"><b>[Kedvencekhez adás]</b>⭐</a><br>
            <?php endif; ?> 
        <?php endif; ?> 
        <div id="sum">Összes <?= countFav($i['id']) ?> felhasználó jelölte ezt a csapatot a kedvencként.</div>
        <p class="title">Legfrissebb eredmények (időrendben):</p>
        <span>Jelmagyarázat:</span>
        <ul>
            <li>zöld - <?= $i['name'] ?> nyert</li>
            <li>piros - <?= $i['name'] ?> vesztett</li>
            <li>sárga - döntetlen eredmény</li>
            <li>szürke - jövőbeli, lejátszatlan meccs</li>
        </ul>
        <ul class="matches">
            <?php foreach($matches as $key => $r): ?>
                <?php if ($r['home']['id'] == $club['id'] || $r['away']['id'] == $club['id']): ?>
                <li class="match" style="background-color: <?= findColor($r['home'], $r['away']) ?>;">
                    <?= $r['date'] ?><br>
                    <b><?= $teams[$r['home']['id']]['name'] ?> vs. <?= $teams[$r['away']['id']]['name'] ?></b><br>
                    <?= $r['home']['score'] ?> - <?= $r['away']['score'] ?><br>
                    <?php if (isset($_SESSION["userid"]) && $user["isAdmin"]): ?>
                        <a href="modify.php?id=<?= $key ?>&from=<?= $i['id'] ?>">[⚙️ szerkesztés ]</a>
                    <?php endif; ?>    
                </li>
                <?php endif; ?>
            <?php endforeach ?>
        </ul>
        <p class="title">Hozzászólások</p>
            <?php foreach($comments as $key => $r): ?>
            <?php if ($r['teamid'] == $club['id']): ?>
                <p>- <b><?= $r['author'] ?></b> (<?= $r['date'] ?>): <?= $r['text'] ?>
                <?php if (isset($_SESSION["userid"]) && $user["isAdmin"]): ?>
                    <a href="delete.php?commentid=<?= $key ?>&teamid=<?=$club['id']?>"> <span class="delete">--- [X Törlés]</span></a>
                <?php endif; ?>  
                </p>
            <?php endif; ?>
            <?php endforeach ?>
        </p>
        <p class="title"> Új hozzászólás írása</p>
        <?php if (isset($_SESSION["userid"])): ?>
            <form action="<?=$link?>" method="post" novalidate>
            <label for="message">Üzenet</label><br>
            <textarea id="message" name="message" placeholder="Kérlek, ide írd a hozzászólásod!" rows="5" cols="70"></textarea><?= $errors['message'] ?? '' ?><br>
            <button type="submit" id="send">Küldés</button>
        </form>
        <?php else: ?>
            <p>Csak bejelentkezett felhasználók írhatnak megjegyzést!</p>
        <?php endif; ?>
    </div>
</body>
</html>