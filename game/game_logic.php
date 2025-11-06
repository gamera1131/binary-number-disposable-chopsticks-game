<?php

#10進数を2進数に変換する関数
function decToBin5bit($dec) {
    return str_pad(decbin($dec), 5, '0', STR_PAD_LEFT);
}
#表示するときにleft rightを日本語に変換する関数
function EnglishToJapanese($hands){
    if($hands==="left"){
        return "左";
    }
    else{
        return "右";
    }
}

function initializeGame() {
    $_SESSION['player_hands'] = ['left' => 1, 'right' => 1];
    $_SESSION['cpu_hands'] = ['left' => 1, 'right' => 1];
    $_SESSION['message'] = "あなたのターンです。攻撃を選択してください。";
}

function handlePostRequest() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }
    // 「ゲーム開始」
    if (isset($_POST['start_game'])) {
        initializeGame();
        $_SESSION['game_state'] = 'playing';
    }
    // 「もう一度遊ぶ」
    elseif (isset($_POST['play_again'])) {
        $_SESSION['game_state'] = 'start';
    }
    // 「リセット」
    elseif (isset($_POST['reset'])) {
        initializeGame();
    }
    // 「攻撃」
    elseif (isset($_POST['attack']) && isset($_SESSION['game_state']) && $_SESSION['game_state'] === 'playing') {
        processAttack();
    }

    // PRGパターン: 処理後にリダイレクト
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

//  攻撃処理の詳細 
function processAttack() {
    $player_from = $_POST['player_from'];
    $cpu_to = $_POST['cpu_to'];
    $is_game_over = false;//ゲームフラグ

    //  プレイヤーの攻撃 
    $attack_value = $_SESSION['player_hands'][$player_from];
    $_SESSION['cpu_hands'][$cpu_to] += $attack_value;//死亡判定
    if ($_SESSION['cpu_hands'][$cpu_to] >= 32) {
        $_SESSION['cpu_hands'][$cpu_to] = 0;
    }
    $cpu_to  = EnglishToJapanese($cpu_to);//手を英語から日本語に
    $_SESSION['message'] = "あなたはCPUの{$cpu_to}手に" . decToBin5bit($attack_value) . "の攻撃！<br>";

    // 勝利チェック
    if ($_SESSION['cpu_hands']['left'] == 0 && $_SESSION['cpu_hands']['right'] == 0) {
        $_SESSION['game_state'] = 'result';
        $_SESSION['result_message'] = "あなたの勝利です！";
        $is_game_over = true;
    }

    //  コンピュータの攻撃 (ゲームがまだ続いている場合) 
    if (!$is_game_over) {
        $cpu_active_hands = array_keys(array_filter($_SESSION['cpu_hands'], fn($v) => $v > 0));
        $player_active_hands = array_keys(array_filter($_SESSION['player_hands'], fn($v) => $v > 0));

        if (!empty($cpu_active_hands) && !empty($player_active_hands)) {
            $cpu_from = $cpu_active_hands[array_rand($cpu_active_hands)];
            $player_to = $player_active_hands[array_rand($player_active_hands)];

            $cpu_attack_value = $_SESSION['cpu_hands'][$cpu_from];
            $_SESSION['player_hands'][$player_to] += $cpu_attack_value;
            if ($_SESSION['player_hands'][$player_to] >= 32) {
                $_SESSION['player_hands'][$player_to] = 0;
            }
            $player_to = EnglishToJapanese($player_to);
            $_SESSION['message'] .= "CPUはあなたの{$player_to}手に" . decToBin5bit($cpu_attack_value) . "の攻撃！";

            // 敗北チェック
            if ($_SESSION['player_hands']['left'] == 0 && $_SESSION['player_hands']['right'] == 0) {
                $_SESSION['game_state'] = 'result';
                $_SESSION['result_message'] = "あなたの負けです…";
            }
        }
    }
}

?>