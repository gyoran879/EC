<?php
$user_name = '';
$password = '';
$errors = [];
$success = [];
$select_data = [];

$host = 'localhost';
$username = 'codecamp37224';
$passwd = 'codecamp37224';
$dbname = 'codecamp37224';

$link = mysqli_connect($host, $username, $passwd, $dbname);
mysqli_set_charset($link, 'utf8');
if ($link) { 
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
        if (isset($_POST['user_name']) === TRUE) {
            $user_name = $_POST['user_name'];
        }
        
        if (preg_match('/^[a-zA-Z0-9]{6,}+$/',$user_name) !== 1) {
            $errors[] = 'ユーザー名は6文字以上の半角英数字を入力してください';
        }
        
        if (isset($_POST['password']) === TRUE) {
            $password = $_POST['password'];
        }
        //var_dump($password);
        
        if (preg_match('/^[a-zA-Z0-9]{6,}+$/',$password) !== 1) {
            $errors[] = 'パスワードは6文字以上の半角英数字を入力してください';
        }
        //var_dump($errors);
        
        if (count($errors) === 0) {
            
            $sql_s = "SELECT user_name FROM ec_user_table";
            // $sql_s = "SELECT user_name FROM ec_user_table where user_name = '{$user_name}'";
           if ($result = mysqli_query($link, $sql_s)) {
            //   $user = mysqli_fetch_assoc($result);
            //   if ($user !== null) {
            //       // 見つかった
            //       $errors[] = '既に同じユーザー名が存在します。やり直してください';
            //   }
    
               //$i = 0;
               while($row = mysqli_fetch_assoc($result)) {
                   $select_data[] = $row['user_name'];
                   
                   print_r($row);
                //   $select_data[$i]['user_name'] = htmlspecialchars($row['user_name'], ENT_QUOTES, 'UTF-8');
                   //$i++;
               } 
               
               mysqli_free_result($result);
               
            } else {
                   $errors[] = 'SQL失敗:' . $sql_s;
               }
        }
           
        if (count($select_data) >= 1) {
            var_dump($user_name);
            var_dump($select_data);
            print_r($select_data);
            var_dump(array_search($user_name, $select_data, true));
               
           if (array_search($user_name, $select_data, true) !== false) {
               
               $errors[] = '既に同じユーザー名が存在します。やり直してください';
               
           } else {
            
                $data = [
                    'user_name' => $user_name,
                    'password' => $password,
                    'created_date' => date('Y-m-d H:i:s')
                ];
                $sql_i = 'INSERT INTO ec_user_table (user_name, password, created_date) 
                          VALUES (\'' . implode('\',\'', $data) . '\')';
                          
                if (mysqli_query($link, $sql_i) === TRUE) {
                    $success[] = 'アカウント作成を完了しました';
                } 
           }
        }
        mysqli_close($link);
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>ユーザー登録ページ</title>
    </head>
    <body>
       
<?php foreach ($success as $suc) { ?>
    <p><?php print $suc; ?></p>
<?php } ?>
<?php foreach ($errors as $err) { ?>
    <p><?php print $err; ?></p>
<?php } ?>
<?php //var_dump($errors); ?>
        <form method="post">
            <label for="user_name">ユーザー名: <input id="user_name" type="text" name="user_name" placeholder="ユーザー名"></label><br>
            <label for="password">パスワード: <input id="password" type="password" name="password" placeholder="パスワード"></label><br>
            <input type="submit" value="ユーザーを新規作成する">
        </form>
        <a href="login.php">ログインページに移動する</a>
    </body>
</html>