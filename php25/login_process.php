<?php
/* ログイン処理*/
$user_name = '';
$password = '';
$errors_login = [];
$host = 'localhost';
$username = 'codecamp37224';
$passwd = 'codecamp37224';
$dbname = 'codecamp37224';

// リクエストメソッド確認
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // POSTでなければログインページへリダイレクト
    header('Location: login.php');
    exit;
}
// セッション開始
