<?php
    session_start();
    if ((isset($_GET['add']) || isset($_GET['remove'])) && isset($_SESSION["userid"])){
        $users = json_decode(file_get_contents('data/users.json'), true);

        $userid = $_SESSION['userid'];
        $users = json_decode(file_get_contents('data/users.json'), true);
        $user = $users[$userid] ?? null;
        if ($user == null){ header('location: index.php'); exit(); }

        if (isset($_GET['add'])){
            $loc = $_GET['add'];
            if (!in_array($_GET['add'], $users[$userid]['fav']))
                array_push($users[$userid]['fav'], $_GET['add']);
        }
        if (isset($_GET['remove'])){
            $loc = $_GET['remove'];
            if (($key = array_search($_GET['remove'], $users[$userid]['fav'])) !== false)
                unset($users[$userid]['fav'][$key]);
        }
        file_put_contents('data/users.json', json_encode($users, JSON_PRETTY_PRINT));
        header('location: details.php?id=' .$loc);
        exit();
    } else { header('location: index.php'); exit(); }
?>