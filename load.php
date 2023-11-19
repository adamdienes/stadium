<?php
    session_start();
    $teams = json_decode(file_get_contents('data/teams.json'), true);   
    $matches = json_decode(file_get_contents('data/matches.json'), true);
    $out = [];

    uasort($matches, function($a, $b){
        return (($b['date'] <=> $a['date']));
    });

    //future match w/ no score
    foreach($matches as $key => $match){
        if ($match['home']['score'] == "" || $match['away']['score'] == ""){
            unset($matches[$key]);
        }
    }    

    //drop first 5
    $top5_matches = array_slice($matches, 5);
    $top5_matches = array_slice($top5_matches, 0, 5);

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

    foreach($top5_matches as $key => $match){
        $out[$match['id']] = [
            'home' => $teams[$match['home']['id']]['name'],
            'home-score'=> $match['home']['score'],
            'away' => $teams[$match['away']['id']]['name'],
            'away-score'=> $match['away']['score'],
            'date' => $match['date'],
        ];
    } 
    
    $resp = [
        "data" => $out,
        "success" => (count($out) == 0) ? false : true
    ];
    echo json_encode($resp);
?>