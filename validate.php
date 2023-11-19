<?php
    session_start();
    if (!isset($_SESSION["userid"]) && count($_POST) > 0){
        $un = $_POST["un"];
        $pw = $_POST["pw"];
        $reg = json_decode(file_get_contents('data/users.json'), true);
        $match = array_keys(array_filter($reg, fn($u) => $u['username'] == $un));
        $id = $match[0] ?? null;
        if ($id !== null){
            if (password_verify($pw, $reg[$id]["password"]))
                $_SESSION["userid"] = $id;
            else {
                $_SESSION["loginerror"] = 2;
                $_SESSION["un"] = $un;
            }
        } else{
            $_SESSION["loginerror"] = 1;
            unset($_SESSION["un"]);
        }
    }
    header("Location: login.php");
    exit();
?>