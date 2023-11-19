<?php
    session_start();
    if (isset($_SESSION['userid'])){
        $id = $_SESSION['userid'];
        $reg = json_decode(file_get_contents('data/users.json'), true);
        $user = $reg[$id] ?? null;
        if ($user != null && $user["isAdmin"]){
            if (isset($_GET['matchid']) && isset($_GET['teamid'])){
                $matches = json_decode(file_get_contents('data/matches.json'), true);
                if (isset($matches[$_GET['matchid']])){
                    unset($matches[$_GET['matchid']]);
                    file_put_contents('data/matches.json', json_encode($matches, JSON_PRETTY_PRINT));
                }
                $link = "details.php?id=" .$_GET['teamid'];
                header('location:' .$link);
                exit();
            } 
            if (isset($_GET['resetid']) && isset($_GET['teamid'])){
                print_r("if");
                $matches = json_decode(file_get_contents('data/matches.json'), true);
                if (isset($matches[$_GET['resetid']])){
                    $matches[$_GET['resetid']]['home']['score'] = "";
                    $matches[$_GET['resetid']]['away']['score'] = "";
                    file_put_contents('data/matches.json', json_encode($matches, JSON_PRETTY_PRINT));
                }
                $link = "details.php?id=" .$_GET['teamid'];
                header('location:' .$link);
                exit();
            } 
            if (isset($_GET['commentid']) && isset($_GET['teamid'])){
                $comments = json_decode(file_get_contents('data/comments.json'), true);
                if (isset($comments[$_GET['commentid']])){
                    unset($comments[$_GET['commentid']]);
                    file_put_contents('data/comments.json', json_encode($comments, JSON_PRETTY_PRINT));
                }
                $link = "details.php?id=" .$_GET['teamid'];
                header('location:' .$link);
                exit();
            }
        } else { header("Location: index.php"); exit(); }
    } else { header("Location: index.php"); exit(); }
?>
