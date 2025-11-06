<?php

// セッションを開始
session_start();

// 必要なファイルを読み込む
require_once 'game_logic.php';
require_once 'views.php';

// POSTリクエストを処理（PRGパターン。処理後はリダイレクトされる）
handlePostRequest();

// 画面状態が未設定なら 'start' にする
if (!isset($_SESSION['game_state'])) {
    $_SESSION['game_state'] = 'start';
}

// 描画するHTMLを格納する変数
$content = '';

// 状態に応じて描画する関数を呼び出す
switch ($_SESSION['game_state']) {
    case 'start':
        $content = renderStartScreen();
        break;
    case 'playing':
        $content = renderPlayingScreen();
        break;
    case 'result':
        $content = renderResultScreen();
        break;
    default:
        // 不正な状態の場合はスタート画面に戻す
        $_SESSION['game_state'] = 'start';
        $content = renderStartScreen();
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>二進数割りばしゲーム</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php
    // 決定された画面のHTMLを出力する
    echo $content;
    ?>

</body>
</html>