<?php

/* ログインページ*/
// セッション開始
// session_start();
// // セッション変数からログイン済みか確認
// if (isset($_SESSION['id']) === TRUE) {
//     // ログイン済みの場合、商品一覧ページへリダイレクト
//     header('Location: ./goods_list.php');
//     exit;
// }
// // もしくは管理側の商品管理ページへリダイレクト

// // セッション変数からログインエラーフラグを確認
// if (isset($_SESSION['login_err_flag']) === TRUE) {
//     // ログインエラーフラグ取得
//     $login_err_flag = $_SESSION['login_err_flag'];
//     // エラー表示は1度だけのため、フラグをFALSEへ変更
//     $_SESSION['login_err_flag'] = FALSE;
// } else {
//     // セッション変数が存在しなければエラーフラグはFALSE
//     $login_err_flag = FALSE;
// }
// Cookie情報からユーザー名を取得
 if (isset($_COOKIE['user_name']) === TRUE) {
     $user_name = $_COOKIE['user_name'];
 } else {
     $user_name = '';
 }
// // Cookie情報からパスワードを取得
// if (isset($_COOKIE['password']) === TRUE) {
//     $password = $_COOKIE['password'];
// } else {
//     $password = '';
// }
// 特殊文字をHTMLエンティティに変換
// $user_name = htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8');
// $password = htmlspecialchars($password, ENT_QUOTES, 'UTF-8');

$errors = [];
$host = 'localhost';
$username = 'codecamp37224';
$passwd = 'codecamp37224';
$dbname = 'codecamp37224';

// リクエストメソッド確認
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // login
    session_start();

    if (isset($_POST['user_name']) === TRUE) {
        $user_name = $_POST['user_name'];
    }
    
    if ($user_name === '') {
        $errors[] = 'ユーザー名を入力してください';
    }
    
    $password = '';
    if (isset($_POST['passwd']) === TRUE) {
        $password = $_POST['passwd'];
    }
    
    if ($password === '') {
        $errors[] = 'パスワードを入力してください';
    }

    if (count($errors) === 0) {
        // データベース接続
        $link = mysqli_connect($host, $username, $passwd, $dbname);
        mysqli_set_charset($link, 'utf8');

if ($link) {   
        // ユーザー名とパスワードからidを取得するSQL
        $sql = 'SELECT id FROM ec_user_table
                WHERE user_name = \'' . $user_name . '\' AND password = \'' . $password . '\'';
        // SQL実行し登録データを配列で取得
        if ($result = mysqli_query($link, $sql)) {
            
            if (mysqli_num_rows($result) > 0) {
                // 1件ずつ取り出す
                while($row = mysqli_fetch_assoc($result)) {
                    $data[] = $row;
                }
            }
            
            mysqli_free_result($result);
        }
        
        mysqli_close($link);
    
        // 登録データを取得できたか確認
        if (isset($data[0]['id'])) {
            // ユーザー名をCookieへ保存
            setcookie('user_name', $user_name, time() + 60 * 60 * 24 * 365);

            // セッション変数にidを保存
            $_SESSION['user_id'] = $data[0]['id'];
            $_SESSION['user_name'] = $user_name;
            
            // ログイン済みのユーザー商品一覧ページへリダイレクト
            if ($user_name === 'admin') {
                header('Location: goods_management.php');
                exit;
            } else {
                header('Location: goods_list.php');
                exit;
            }
            // ログイン済みの管理側商品管理ページへリダイレクト
            
            // ユーザー登録ページへ遷移
        } else {
            $errors[] = 'ユーザー名またはパスワードが違います';
        }

    }
    
    if (count($errors) > 0) {
        $user_name = '';
    }
}

}

// 特殊文字をHTMLエンティティに変換
$user_name = htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8');

?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>ログインページ</title>
    </head>
    <body>
        <a href="./goods_list.php">
            <img src="./logo_lastTask.png">
        </a>
<?php foreach ($errors as $error) { ?>
    <p><?php print $error; ?></p>
<?php } ?>

        <form method="post" action="login.php">
            <input type="text" name="user_name" value="<?php print $user_name; ?>" placeholder="ユーザー名"><br>
            <input type="password" name="passwd" value="" placeholder="パスワード"><br>
            <input type="submit" value="ログイン">
        </form>
        
        <a href="user_registration.php">
            ユーザーの新規作成
        </a>
    </body>
</html>