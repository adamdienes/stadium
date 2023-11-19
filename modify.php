<?php
    session_start();
    $matchid = $_GET['id'] ?? "";
    $from = $_GET['from'] ?? "";
    $link = "modify.php?id=" .$matchid ."&from=" .$from;
    $link1 = "details.php?id=" .$from;

    if (isset($_SESSION['userid'])){
        $id = $_SESSION['userid'];
        $reg = json_decode(file_get_contents('data/users.json'), true);
        $user = $reg[$id] ?? null;
        if ($user != null && $user["isAdmin"]){
            $matches = json_decode(file_get_contents('data/matches.json'), true);
            $teams = json_decode(file_get_contents('data/teams.json'), true);
        }
        else { header("Location: index.php"); exit(); }
    } else { header("Location: index.php"); exit(); }

    $azon =  $matches[$matchid]['id'];
    $home1 = $matches[$matchid]['home']['id'];
    $home2 = $_POST['home2'] ?? $matches[$matchid]['home']['score'];
    $away1 = $matches[$matchid]['away']['id']; 
    $away2 = $_POST['away2'] ?? $matches[$matchid]['away']['score'];
    $date = $_POST['date'] ?? $matches[$matchid]['date'];

    $errors = [];

    if(count($_POST) > 0){
        if (trim($home2) == '')
            $errors['home2'] = 'A gólok számának megadása kötelező!'; 
        else if (!is_numeric($home2) || filter_var($home2, FILTER_VALIDATE_INT) === false)
            $errors['home2'] = 'A gólok számának számnak kell lennie.'; 
        else if (intval($home2) < 0)
            $errors['home2'] = 'A gólok számának nem negatívnak kell lennie.'; 

        if (trim($away2) == '')
            $errors['away2'] = 'A gólok számának megadása kötelező!'; 
        else if (!is_numeric($away2) || filter_var($away2, FILTER_VALIDATE_INT) === false)
            $errors['away2'] = 'A gólok számának számnak kell lennie.'; 
        else if (intval($away2) < 0)
            $errors['away2'] = 'A gólok számának nem negatívnak kell lennie.'; 

        if (trim($date) == '')
            $errors['date'] = 'A dátum megadása kötelező!';
        else if (trim($date) != ''){
            if (!DateTime::createFromFormat('Y-m-d', $date))
                $errors['date'] = 'Helytelen dátumformátum! (YYYY-MM-DD)';
        }
        $errors = array_map(fn($e) => "<span style='color:red'>$e</span>", $errors);

        if (!count($errors)){            
            $matches[$azon] = [
                'id' => $azon,
                'home' => [
                    'id' => $home1,
                    'score' => $home2
                ],
                'away' => [
                    'id' => $away1,
                    'score' => $away2
                ],
                'date' => $date
            ];
            file_put_contents('data/matches.json', json_encode($matches, JSON_PRETTY_PRINT));
            header('location:'. $link1);
            exit();
        }
    } 
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módosítás - Eötvös Loránd Stadion</title>
    <meta name="author" content="Dienes Ádám (i93ajy)">
    <link rel="shortcut icon" href="img/icon.png" />
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <div id="container">
        <h1>Eötvös Loránd Stadion</h1>
        <h2 id="subtitle">Eredménymódosítás - admin panel</h2>
        <a href="<?=$link1?>"><button id="reg">↺ Vissza a csapathoz</button></a><br>
        <h2>Adatok módosítása</h2>
        <b><?=$teams[$home1]['name']?> (hazai) vs. <?=$teams[$away1]['name']?> (vendég)</b>
        <p>❗ <b>Figyelem: </b>A meccsazonosító, valamint a csapatok nevei nem megváltoztató adatok.</p>
        <form action="<?=$link?>" method="post" novalidate>
            <?=$teams[$home1]['name']?> gólok: <input type="number" name="home2" value="<?= $home2 ?>"> <?= $errors['home2'] ?? '' ?><br>
            <?=$teams[$away1]['name']?> gólok: <input type="number" name="away2" value="<?= $away2 ?>"> <?= $errors['away2'] ?? '' ?><br>
            Dátum: <input type="date" name="date" value="<?= $date ?>"> <?= $errors['date'] ?? '' ?><br>
            <button type="submit" id="edit">Módosítás</button>
        </form>  
        <a href="delete.php?resetid=<?= $azon ?>&teamid=<?=$from?>"><button class="delete_match">Gólok törlése</button></a><br>
        <a href="delete.php?matchid=<?= $azon ?>&teamid=<?=$from?>"><button class="delete_match">Meccs törlése</button></a><br>
      </div>
</body>
</html>