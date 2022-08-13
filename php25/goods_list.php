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

$host = 'localhost';
$db_user_name = 'codecamp37224';
$db_passwd = 'codecamp37224';
$dbname = 'codecamp37224';
$select_data = [];
$img_dir = './img/';
$errors = [];
$message = [];

$link = mysqli_connect($host, $db_user_name, $db_passwd, $dbname);
mysqli_set_charset($link, 'utf8');

if ($link) {
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $item_id = '';
        
        if (isset($_POST['item_id']) === TRUE) {
            $item_id = $_POST['item_id'];
        }
        
        if ($item_id === '') {
            $errors[] = 'item_idが存在しません。管理者に連絡してください';
        }
    
        if (count($errors) === 0) {
//     // item_idとuser_idでカートテーブルを検索して該当の商品を検索する
            $sql_s = 'SELECT id FROM cart_table WHERE item_id = ' . $item_id . ' AND user_id = ' . $user_id;
            
                var_dump($sql_s);
                if ($result = mysqli_query($link, $sql_s)) {
                    var_dump($result);
                    $cart = null;
                    $cart = mysqli_fetch_assoc($result);
                }
                
                    var_dump($result);
                    mysqli_free_result($result);
        
        //     if (見つかった？) {
//         // 見つかった（UPDATE文でamount + 1する）
//     } else {
//         // 見つからなかった（INSERT INTO）
//     }
       
               if ($cart !== null) {
                   $sql_u = 'UPDATE cart_table SET amount = amount+' . 1 . 
                            ' WHERE item_id = ' . $item_id . ' AND user_id = ' . $user_id;
                            var_dump($sql_u);
                       if (mysqli_query($link, $sql_u) === TRUE) {
                           $message[] = 'カート情報を更新しました。';
                       } else {
                           $errors[] = 'カート情報が更新できませんでした。';
                       }
               } else {
                   
                           
                  $data = [
                      
                      'user_id' => $user_id,
                      'item_id' => $item_id,
                      'amount'  => 1,
                      'created_date' => date('Y-m-d H:i:s')
                  ];
                           
                      $sql_i = 'INSERT INTO cart_table (user_id, item_id, amount, created_date)
                               VALUES (\'' . implode('\',\'',$data) . '\')';
                                    
                            if (mysqli_query($link, $sql_i) !== TRUE) {
                                $errors[] = 'cart_table: insertエラー:' . $sql_i;
                            } else {
                                $message[] = 'カートに商品を追加しました';
                            }
                }
        }
    }
    $sql_s = 'SELECT 
              it.id, 
              it.name, 
              it.price, 
              it.img, 
              it.status,
              est.stock
              FROM item_table AS it
              JOIN
              ec_stock_table AS est
              ON
              it.id = est.item_id
              WHERE it.status = 1'; 
             
    if ($result = mysqli_query($link, $sql_s)) {
        $i = 0;
        while($row = mysqli_fetch_assoc($result)) {
            $select_data[$i]['id'] = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
            $select_data[$i]['name'] = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
            $select_data[$i]['price'] = htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8');
            $select_data[$i]['img'] = htmlspecialchars($row['img'], ENT_QUOTES, 'UTF-8');
            $select_data[$i]['status'] = htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8');
            $select_data[$i]['stock'] = htmlspecialchars($row['stock'], ENT_QUOTES, 'UTF-8');
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
        <title>商品一覧ページ</title>
        <style type="text/css">
           
            .price {
                margin-left: 0px;
                flex: 1;
            }
            .name {
                 flex: 1;
            }
            .soldout {
                color:red;
                position: relative;
                left: 50px;
            }
            .goods {
                display: flex;
            }
            header {
                display: flex;
            }
            ul {
                display: flex;
                list-style: none;
            }
            li {
                flex: 1;
                margin-left: 10px;
            }
            span {
                display: flex;
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
<?php foreach ($message as $mes) { ?>
    <ul>
        <li>
            <?php print $mes; ?>
        </li>
    </ul>
<?php } ?>

<?php foreach ($errors as $err) { ?>
    <ul>
        <li><?php print $err; ?></li>
    </ul>
<?php } ?>

<?php foreach($select_data as $data) { ?>
    
    <form method="post">
        <input type="hidden" name="item_id" value="<?php print $data['id']; ?>">
           
        <img src="<?php print $img_dir . $data['img']; ?>">
    
        <div class="goods">
            <span class="name"><?php print $data['name']; ?></span>
            <span class="price">￥<?php print $data['price']; ?></span>
        </div>
        <input type="hidden" name="stock" value="<?php print $data['stock']; ?>">
<?php if ($data['stock'] === '0') { ?>
        <p class="soldout">売り切れ</p>
<?php } else { ?>        
        <input type="submit" value="カートに入れる">
<?php } ?>
        
    </form>
<?php } ?>
    </body>
</html>