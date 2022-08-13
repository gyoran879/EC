<?php
$user_id = '';
$user_name = '';

session_start();

if (isset($_SESSION['user_name']) === true) {
     // ログイン済み
     $user_id = $_SESSION['user_id'];
     $user_name = $_SESSION['user_name'];
    
    if ($user_name !== 'admin') {
        header('Location: goods_list.php');
        exit;
    }
    
 } else {
     // 未ログイン
     header('Location: login.php');
     exit;
 }

// データベース接続必要情報
$host = 'localhost';
$db_user_name = 'codecamp37224';
$db_passwd = 'codecamp37224';
$dbname = 'codecamp37224';
$select_data = [];
$errors = [];

$link = mysqli_connect($host, $db_user_name, $db_passwd, $dbname);
mysqli_set_charset($link, 'utf8');

if ($link) {
    $sql_s = 'SELECT user_name, created_date FROM ec_user_table';
    
    if ($result = mysqli_query($link, $sql_s)) {
        $i = 0;
        while($row = mysqli_fetch_assoc($result)) {
            $select_data[$i]['user_name'] = htmlspecialchars($row['user_name'], ENT_QUOTES, 'UTF-8');
            var_dump($row);
            $select_data[$i]['created_date'] = htmlspecialchars($row['created_date'], ENT_QUOTES, 'UTF-8');
            $i++;
        }
        
        mysqli_free_result($result);
    } else {
        $errors[] = 'SQL失敗: ' . $sql_s;
    }
}

mysqli_close($link);

?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>ユーザー管理ページ</title>
        <style type="text/css">
            
            table, tr, th, td {
                border: solid 1px;
                padding: 10px;
                text-align: center;
                width: 1000px;
            }
            
            table {
                border-collapse: collapse;
            }
        </style>
    </head>
    <body>
        <h1>CodeSHOP 管理ページ</h1>
        <a href="./logout.php">ログアウト</a>
        <a href="./goods_management.php">商品管理ページ</a>
        
<?php foreach($errors as $err) { ?>
        <ul>
            <li><?php print $err; ?></li>
        </ul>
<?php } ?>
<?php var_dump($select_data); ?>
<?php if (count($select_data) > 0) { ?>
        <table>
            <tr>
                <th>ユーザーID</th>
                <th>登録日</th>
            </tr>
<?php foreach($select_data as $data) { ?>
            <tr>
                <td><?php print $data['user_name']; ?></td>
                <td><?php print $data['created_date']; ?></td>
            </tr>
<?php } ?>
        </table>
<?php } ?>
    </body>
</html>