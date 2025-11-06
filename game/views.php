<?php

// --- 手のビジュアル生成 ---
function renderHand($decimal_value) {
    $binary_string = decToBin5bit($decimal_value); 
    $output = '<div class="finger-hand">';
    for ($i = 0; $i < 5; $i++) {
        $output .= '<div class="finger" data-active="' . $binary_string[$i] . '"></div>';
    }
    $output .= '<p class="binary-value">' . $binary_string . '</p></div>';
    return $output;
}

// --- スタート画面 ---
function renderStartScreen() {
    return '
    <div class="screen">
        <h1>二進数割りばしゲーム</h1>
        <p>5bitの二進数で戦う、新しい割りばしゲーム！</p>
        <div class="rules"><strong>ルール</strong><ul><li>自分のアクティブな手で、相手のアクティブな手を攻撃します。</li><li>攻撃すると、自分の手の値が相手の手に加算されます。</li><li>値が32以上になると、その手は 0 になります。</li><li>相手の両手を 0 にしたら勝利です！</li></ul></div>
        <form method="post" action="index.php"><button type="submit" name="start_game">ゲーム開始</button></form>
    </div>
    ';
}

// --- ゲーム画面 ---
function renderPlayingScreen() {
    $player_left_bin = decToBin5bit($_SESSION['player_hands']['left']);
    $player_right_bin = decToBin5bit($_SESSION['player_hands']['right']);
    $cpu_left_bin = decToBin5bit($_SESSION['cpu_hands']['left']);
    $cpu_right_bin = decToBin5bit($_SESSION['cpu_hands']['right']);

    $player_options = '';
    if ($_SESSION['player_hands']['left'] > 0) $player_options .= "<option value=\"left\">左手 ($player_left_bin)</option>";
    if ($_SESSION['player_hands']['right'] > 0) $player_options .= "<option value=\"right\">右手 ($player_right_bin)</option>";

    $cpu_options = '';
    if ($_SESSION['cpu_hands']['left'] > 0) $cpu_options .= "<option value=\"left\">左手 ($cpu_left_bin)</option>";
    if ($_SESSION['cpu_hands']['right'] > 0) $cpu_options .= "<option value=\"right\">右手 ($cpu_right_bin)</option>";

    return '
    <div class="container">
        <h1>ゲーム中</h1>
        <div class="cpu-area"><h2> コンピュータ</h2><div class="hands-area"><div class="hand-container"><h3>左手</h3>' . renderHand($_SESSION['cpu_hands']['left']) . '</div><div class="hand-container"><h3>右手</h3>' . renderHand($_SESSION['cpu_hands']['right']) . '</div></div></div>
        <div class="message"><p>' . $_SESSION['message'] . '</p></div>
        <div class="player-area"><h2> あなた</h2><div class="hands-area"><div class="hand-container"><h3>左手</h3>' . renderHand($_SESSION['player_hands']['left']) . '</div><div class="hand-container"><h3>右手</h3>' . renderHand($_SESSION['player_hands']['right']) . '</div></div></div>
        
        <form method="post" action="index.php">
            <div>
                <label>自分の手:</label><select name="player_from">' . $player_options . '</select>
                <label>相手の手:</label><select name="cpu_to">' . $cpu_options . '</select>
            </div>
            <div style="margin-top: 15px;">
                <button type="submit" name="attack">攻撃！</button>
                <button type="submit" name="reset" class="reset-btn">リセット</button>
            </div>
        </form>
    </div>
    ';
}

// --- 結果画面 ---
function renderResultScreen() {
    return '
    <div class="screen">
        <h1>ゲーム終了</h1>
        <h2>' . $_SESSION['result_message'] . '</h2>
        <p>もう一度挑戦しますか？</p>
        <form method="post" action="index.php"><button type="submit" name="play_again" class="play-again-btn">もう一度遊ぶ</button></form>
    </div>
    ';
}
?>