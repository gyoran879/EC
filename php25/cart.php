<?php

session_start();

if (isset($_SESSION['user_name']) === true) {
    // ログイン済み
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'];
} else {
    // 未ログイン
    header('Location: login.php');
    exit;
}

$img_dir = './img/';
$item_id = '';
$goods_name = '';
$price = '';
$stock = '';
$errors = [];
$message = [];
$carving = '';
$update_amount = '';

$host = 'localhost';
$db_user_name = 'codecamp37224';
$db_passwd = 'codecamp37224';
$dbname = 'codecamp37224';

$link = mysqli_connect($host, $db_user_name, $db_passwd, $dbname);
mysqli_set_charset($link, 'utf8');

if ($link) {
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // アクションの識別情報を受け取る
    if (isset($_POST['carving']) === TRUE) {
        $carving = $_POST['carving'];
    }
    
    if ($carving === 'update_amount') {
    //     //--- カート商品の数量を変更する ---
        
    //     // フォームからのデータを受け取る（item_id, amount）
    //     // 受け取ったデータを入力チェックする
    //     // 未入力チェック、数値チェック、amountは0はNG    
        if (isset($_POST['item_id']) === TRUE) {
            $item_id = $_POST['item_id'];
        }
        
        if ($item_id === '') {
            $errors[] = 'item_idが存在しません。管理者に連絡してください。';
        } else if (preg_match('/^[0-9]+$/',$item_id) !== 1) {
            $errors[] = 'item_idが数字で入力されていません。管理者に連絡してください。';
        }
        
        if (isset($_POST['update_amount']) === TRUE) {
            $update_amount = $_POST['update_amount'];
        }
        
        if ($update_amount === '') {
            $errors[] ='個数が未入力です。個数を入力してください。';
        } else if (preg_match('/^[1-9][0-9]*$/',$update_amount) !== 1) {
            $errors[] = '個数は1以上の数値で入力してください。';
        }
        
    //     // エラーがないか確認する
        if (count($errors) === 0) {
    //         // 数量変更を実行する（UPDATE文）
            $sql_u = 'UPDATE cart_table SET amount = ' .$update_amount . ' WHERE item_id = ' . $item_id;
            
            if (mysqli_query($link, $sql_u) === TRUE) {
                $message[] = '数量を変更しました。';
            } else {
                $errors[] = '数量を更新できませんでした。' . $sql_u;
            }
        }

     } else if ($carving === 'delete_record') {
    //     //--- カート商品の削除をする ---

    //     // フォームからのデータを受け取る（item_id）
         if (isset($_POST['item_id']) === TRUE) {
             $item_id = $_POST['item_id'];
         }
        
    //     // 受け取ったデータを入力チェックする（未入力チェック、数値チェック）
        
          if ($item_id === '') {
              $errors[] = 'item_idが存在しません。管理者に連絡してください。';
          } else if (preg_match('/^[0-9]+$/',$item_id) !== 1) {
              $errors[] = 'item_idの値が数字ではありません。管理者に連絡してください。';
          }
    //     // エラーがないか確認する
         if (count($errors) === 0) {
    //         // 削除処理を実行する（DELETE FROM文）
            $sql_d = 'DELETE FROM cart_table WHERE item_id = ' . $item_id;
            
                if (mysqli_query($link, $sql_d) === TRUE) {
                    $message[] = '指定行を削除しました。';
                } else {
                    $errors[] = 'delete文実行エラー: ' . $sql_d;
                }
         }

     }
}

$total_price = 0;


    $sql_s = 'SELECT
              it.id,
              it.img,
              it.name,
              it.price,
              ct.amount
              FROM item_table AS it
              JOIN
              cart_table AS ct
              ON
              it.id = ct.item_id
              WHERE 
              ct.user_id = ' . $user_id;
$select_data = [];              
    if ($result = mysqli_query($link, $sql_s)) {
        $i = 0;
        
        while($row = mysqli_fetch_assoc($result)) {
            $select_data[$i]['item_id'] = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
            $select_data[$i]['img']     = htmlspecialchars($row['img'], ENT_QUOTES, 'UTF-8');
            $select_data[$i]['goods_name']    = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
            $select_data[$i]['price']   = htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8');
            $select_data[$i]['amount']  = htmlspecialchars($row['amount'], ENT_QUOTES, 'UTF-8');
            
            $total_price += $select_data[$i]['price'] * $select_data[$i]['amount'];
            
            $i++;
        }
        var_dump($select_data);
        mysqli_free_result($result);

        // for($i = 0; $i <count($select_data[$i]['amount']); $i++) {
        //     $total_price += (int)$select_data[$i]['price'];
        // }
        // var_dump(count($select_data[$i]['amount']));

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
        <title>ショッピングカートページ</title>
        <style type="text/css">
        
           h1 {
               margin-top: 0px;
           }
           header {
                display:flex;
           }
           
           ul {
               display:flex;
               list-style: none;
           }
           
           li {
               flex: 1;
               margin-left: 10px;
           }
           .total_price {
               padding-left: 140px;
           }
        </style>
    </head>
    <body>
        <header>
            <h1><a href="./goods_list.php"><img src="./logo_lastTask.png">CodeSHOP</a></h1>
            
            <ul>
                <li>ユーザー名:<?php print $user_name; ?></li>
                <li><a href="./cart.php">カートを見る</a></li>
                <li><a href="./logout.php">ログアウト</a></li>
            </ul>
        </header>
        <h2>ショッピングカート</h2>
<?php foreach ($errors as $err) { ?>
    <ul>
        <li><?php print $err; ?></li>
    </ul>
<?php } ?>
<?php if (count($select_data) > 0) { ?>        
        <table border="1">
            <tr>
                <th>画像</th>
                <th>商品名</th>
                <th>価格</th>
                <th>数量</th>
                <td>削除</td>
            </tr>
<?php foreach($select_data as $data) { ?>
            <tr>
                <td><img src="<?php print $img_dir . $data['img']; ?>"></td>
                <td><?php print $data['goods_name']; ?></td>
                <td><?php print $data['price']; ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="carving" value="update_amount">
                        <input type="hidden" name="item_id" value="<?php print $data['item_id']; ?>">
                        <input type="text" name="update_amount" value="<?php print $data['amount']; ?>">
                        <input type="submit" value="変更">
                    </form>
                </td>
                <td>
                    <form method="post">
                        <input type="hidden" name="carving" value="delete_record">
                        <input type="hidden" name="item_id" value="<?php print $data['item_id']; ?>">
                        <input type="submit" value="削除">
                    </form>
                </td>
            </tr>
<?php } ?>
            <tr>
                <td>合計</td>
                <td class="total_price" colspan="4"><?php print $total_price; ?>円</td>
            </tr>
        </table>
<?php } ?>
        <form method="post" action="./complete.php">
            <input type="submit" value="購入する">
        </form>
    </body>
</html>