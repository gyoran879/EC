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
$total_price = 0;
$errors = [];
$success = '';

$host = 'localhost';
$db_user_name = 'codecamp37224';
$db_passwd = 'codecamp37224';
$dbname = 'codecamp37224';

$link = mysqli_connect($host, $db_user_name, $db_passwd, $dbname);
mysqli_set_charset($link, 'utf8');

if ($link) {
    
    $sql_s = 'SELECT
              it.id,
              it.img,
              it.name,
              it.price,
              ct.amount
              FROM cart_table AS ct
              JOIN
              item_table AS it
              ON
              ct.item_id = it.id
              WHERE
              ct.user_id = ' . $user_id;
$select_data = [];
    if ($result = mysqli_query($link, $sql_s)) {
        $i = 0;
        while($row = mysqli_fetch_assoc($result)) {
            $select_data[$i]['item_id'] = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
            $select_data[$i]['img']     = htmlspecialchars($row['img'], ENT_QUOTES, 'UTF-8');
            $select_data[$i]['goods_name'] = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
            $select_data[$i]['price']      = htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8');
            $select_data[$i]['amount']    = htmlspecialchars($row['amount'], ENT_QUOTES, 'UTF-8');
            
            $total_price += $select_data[$i]['price'] * $select_data[$i]['amount'];
            
            $i++;
        }
        mysqli_free_result($result);
        if(count($select_data)>0){
            //在庫チェック
            foreach($select_data as $item){
                $item_id = $item['item_id'];
                $amount = $item['amount'];
                
                $sql = "SELECT item_id, stock
                        FROM ec_stock_table
                        WHERE item_id = '${item_id}'";
                        
                $result = mysqli_query($link, $sql);
                $row = mysqli_fetch_assoc($result);
                
                if($row['stock'] - $amount<0){
                    $errors[]="item_idの${item_id}が在庫が足りません。購入できませんでした。";
                }
                 mysqli_free_result($result);   
  
            }
            
            
            //エラーがなければ更新
            if(count($errors)===0){
                foreach($select_data as $item){
                    $item_id = $item['item_id'];
                    $amount = $item['amount'];
                    
                    $sql = "UPDATE ec_stock_table
                    SET stock = stock - ${amount} 
                    WHERE item_id = ${item_id}";
                    $result = mysqli_query($link, $sql);

                }
                
                $sql_d = 'DELETE FROM cart_table WHERE user_id = ' . $user_id;
    
                if (mysqli_query($link, $sql_d) !== TRUE) {
                    $errors[] = 'SQL失敗: ' . $sql_d;
                }
                $success = 'ご購入ありがとうございました。';
            }
            
        }
        
        
        // for($i = 0; $i <count($select_data[$i]['amount']); $i++) {
        //     $total_price += (int)$select_data[$i]['price'];
        // }
        // var_dump(count($select_data[$i]['amount']));
    } else {
        $errors[] = 'SQL失敗: ' . $sql_s;
    }
    
    // $sql_u = 'UPDATE ec_stock_table SET stock = stock-' . $
    // mysqli_close($link);
    
    
}

?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>購入完了ページ</title>
        <style type="text/css">
            h1 {
                margin-top: 0px;
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
            
            .error_mes {
                font-size: 40px;
                font-color: red;
            }
            
            .success {
                background-color: #00FFFF;
                width: 600px;
                text-align: center;
                margin: 0 auto;
            }
            
            table{ 
                 /*border-collapse: separate;*/
                 /*border-spacing: 50px;*/
                 border-collapse: collapse ;
            }
            
            th {
                padding-left: 60px;
            }
            
            td {
                border-top: solid 1px;
                border-color: red;
                padding-top: 10px;
                padding-left: 60px;
            }
            
            .total-price {
                padding-left: 1000px;
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
<?php foreach($errors as $err) { ?>  
        <p class="error_mes"><?php print $err; ?></p>
<?php } ?>
            <h1 class="success"><?php print $success; ?></h1>
            
<?php if (count($select_data) > 0) { ?>
        <table>
            <tr>
                <th></th>
                <th></th>
                <th>価格</th>
                <th>数量</th>
            </tr>
<?php foreach($select_data as $data) { ?>
            <tr>
                <td><img src="<?php print $img_dir . $data['img']; ?>"></td>
                <td><?php print $data['goods_name']; ?></td>
                <td><?php print $data['price']; ?></td>
                <td><?php print $data['amount']; ?></td>
            </tr>
<?php } ?>
            <tr>
                <td>合計</td>
                <td></td>
                <td colspan="2"><?php print $total_price; ?>円</td>
            </tr>
        </table>
<?php } ?>
    </body>
</html>