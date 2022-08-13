<?php

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


$id = '';
$host = 'localhost';
$user_name = 'codecamp37224';
$passwd = 'codecamp37224';
$dbname = 'codecamp37224';
$data = [];
// // セッション開始
// session_start();
// // セッション変数からユーザーid取得
// if (isset($_SESSION['id']) === TRUE) {
//     $id = $_SESSION['id'];
// } else {
//     // 非ログインの場合、ログインページへリダイレクト
//     header('Location: login.php');
//     exit;
// }
// データベース接続
$link = mysqli_connect($host, $user_name, $passwd, $dbname);
mysqli_set_charset($link, 'utf8');

// // ユーザーidからユーザー名パスワードを取得するSQL
// $sql = 'SELECT username, password FROM ec_user_table WHERE id = ' . $id;
// // SQL実行し登録データを配列で取得
// if ($result = mysqli_query($link, $sql)) {
    
//     if (mysqli_num_rows($result) > 0) {
        
//         while ($row = mysqli_fetch_assoc($result)) {
//             $data[] = $row;
//         }
//     }
//     mysqli_free_result($result);
// }
// // ユーザー名を取得できたか確認
// if (isset($data[0]['user_name'])) {
//     $login_user_name = $data[0]['user_name'];
// } else {
//     // ユーザー名が取得できない場合、ログインページに遷移するためにログアウト処理へ
//     header('Location: logout.php');
//     exit;
// }

// // パスワードを取得できたかを確認
// if (isset($data[0]['password'])) {
//     $login_password = $data[0]['password'];
// } else {
//     // パスワードが取得できない場合、ログインページへ遷移するためにログアウト処理へ
//     header('Location: logout.php');
//     exit;
// }
$carving = '';
$goods_name = '';
$price = '';
$stock = '';
$public_nopublic = '';
$errors = [];
$img_dir = './img/';
$message = [];
$select_data = [];
$item_id = '';
$update_stock = '';
$update_public_nopublic = '';

if ($link) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        if (isset($_POST['carving']) === TRUE) {
            $carving = $_POST['carving'];
        }
        
        if ($carving === 'insert') {
            
            if (isset($_POST['goods_name']) === TRUE) {
                $goods_name = trim($_POST['goods_name']);
            }
            
            if ($goods_name === '') {
                $errors[] = '商品名が未入力です。商品名を入力してください。';
            }
            
            if (isset($_POST['price']) === TRUE) {
                $price = trim($_POST['price']);
            }
            
            if ($price === '') {
                $errors[] = '値段が未入力です。値段を入力してください。';
            } else if (preg_match('/^[0-9]+$/',$price) !== 1) {
                $errors[] = '値段は0以上の数値で入力してください。';
            }
            
            if (isset($_POST['stock']) === TRUE) {
                $stock = trim($_POST['stock']);
            }
            
            if ($stock === '') {
                $errors[] = '個数が未入力です。個数を入力してください。';
            } else if (preg_match('/^[0-9]+$/',$stock) !== 1) {
                $errors[] = '個数は0以上の数値で入力してください。';
            }
            
            // エラーチェックと保存ファイル名を作成する
            if (isset($_FILES['new_img']) !== TRUE) {
                $errors[] = 'ファイルを選択してください';
            } else if (is_uploaded_file($_FILES['new_img']['tmp_name']) !== TRUE) {
                $errors[] = 'ファイルを選択してください';
            } else {
                // PC側の元画像ファイル名から拡張子を取得
                
                $extension = pathinfo($_FILES['new_img']['name'], PATHINFO_EXTENSION);
                $extension = strtolower($extension);
                
                // 保存する新しいファイル名の生成 (ユニークな値を設定する)
                $img_filename = sha1(uniqid(mt_rand(), true)) . '.' . $extension;
                // fas098f7s0a9f7s9ad80f.jpeg
                
                if ($extension !== 'jpg' && $extension !== 'jpeg' && $extension !== 'png') {
                    // 指定の拡張子であるかどうかチェック
                    $errors[] = 'ファイル形式が異なります。画像ファイルはJPEGまたはPNGのみ利用可能です。';
                } else if (is_file($img_dir . $img_filename) === TRUE) {
                    // 同名ファイルが存在するかどうかチェック
                    $errors[] = 'ファイルアップロードに失敗しました。再度お試しください。';
                }
                
            }
            
             if (isset($_POST['public_nopublic']) === TRUE) {
                $public_nopublic = trim($_POST['public_nopublic']);
            }
            
            if ($public_nopublic === '') {
                $errors[] = '公開・非公開を入力してください。';
            } else if ($public_nopublic !== '0' && $public_nopublic !== '1') {
                $errors[] ='ステータスは公開・非公開のみ選択可能です。やり直してください。';
            }
            
           
            if (count($errors) === 0) {
                
                if (move_uploaded_file($_FILES['new_img']['tmp_name'], $img_dir . $img_filename) !== TRUE) {
                    $errors[] = 'ファイルアップロードに失敗しました';
                }
                mysqli_autocommit($link, false);
                $data = [
                    'name' => $goods_name,
                    'price' => $price,
                    'img' => $img_filename,
                    'status' => $public_nopublic,
                    'created_date' => date('Y-m-d H:i:s')
                ];
                $sql_i = 'INSERT INTO item_table (name, price, img, status, created_date) 
                          VALUES(\'' . implode('\',\'', $data) .'\')';
                     var_dump($sql_i);     
                if (mysqli_query($link, $sql_i) === TRUE) {
                     
                    $item_id = mysqli_insert_id($link);
                   
                    $data = [
                        'item_id' => $item_id,
                        'stock' => $stock,
                        'created_date' => date('Y-m-d H:i:s')
                    ];
                    
                    $sql_i = 'INSERT INTO ec_stock_table (item_id, stock, created_date)
                              VALUES (\'' . implode('\',\'',$data) . '\')';
                             
                    if (mysqli_query($link, $sql_i) !== TRUE) {
                        $errors[] = 'ec_stock_table: insertエラー:' . $sql_i;
                    }
                } else {
                    $errors[] = 'item_table: insertエラー:' . $sql_i;
                }
                
                if (count($errors) === 0) {
                    mysqli_commit($link);
                    $message[] = '追加成功';
                } else {
                    $errors[] = '追加失敗';
                    mysqli_rollback($link);
                }
            }
        } else if ($carving === 'update_stock') {
            if (isset($_POST['item_id']) === TRUE) {
                $item_id = $_POST['item_id'];
            }
            
            if (isset($_POST['update_stock']) === TRUE) {
                $update_stock = trim($_POST['update_stock']);
            }
            
            if ($update_stock === '') {
                $errors[] = '更新在庫数を入力してください。';
            } else if (preg_match('/^[0-9]+$/',$update_stock) !== 1) {
                $errors[] = '更新在庫数は数字で入力してください。';
            }
            
            if (count($errors) === 0) {
                
                $sql_u = 'UPDATE ec_stock_table SET stock = ' . $update_stock . ' WHERE item_id = ' . $item_id;
                
                if (mysqli_query($link, $sql_u) === TRUE) {
                    $message[] = '在庫数を更新しました。';
                } else {
                    $errors[] = '在庫数が更新できませんでした。' . $sql_u;
                }
            }
        } else if ($carving === 'update_status') {
            
            if (isset($_POST['item_id']) === TRUE) {
                $item_id = $_POST['item_id'];
            }
            
            if (isset($_POST['update_public_nopublic']) === TRUE) {
                $update_public_nopublic = trim($_POST['update_public_nopublic']);
            }
            
            if ($update_public_nopublic === '') {
                $errors[] = '公開 非公開を選択してください。';
            } else if ($update_public_nopublic !== '0' && $update_public_nopublic !== '1') {
                $errors[] = '0か1を入力してください。';
            }
            
            if (count($errors) === 0) {
                $sql_u = 'UPDATE item_table SET status = ' . $update_public_nopublic . ' WHERE id = ' . $item_id;
               
                if (mysqli_query($link, $sql_u) === TRUE) {
                    $message[] = '公開ステータスを更新しました。';
                } else {
                    $errors[] = '公開ステータスが更新できませんでした。' . $sql_u;
                }
            }
        } else if ($carving === 'delete_record') {
            if (isset($_POST['item_id']) === TRUE) {
                $item_id = $_POST['item_id'];
            }
            
            mysqli_autocommit($link, false);
            
             $sql_d = 'DELETE FROM cart_table WHERE item_id = ' . $item_id;
             
                if (mysqli_query($link, $sql_d) === TRUE) {
                    
                    // $item_id = mysqli_insert_id($link);
                 
                    $sql_d = 'DELETE FROM ec_stock_table WHERE item_id = ' . $item_id;
                  
                        if (mysqli_query($link, $sql_d) !== TRUE) {
                        
                            $errors[] = 'ec_stock_table: deleteエラー' . $sql_d;
                        
                        } else if (mysqli_query($link, $sql_d) === TRUE) {
                            
                             $sql_d = 'DELETE FROM item_table WHERE id = ' . $item_id;
                             var_dump($sql_d);
                            
                                if (mysqli_query($link, $sql_d) !== TRUE) {
                                    
                                    $errors[] = 'item_table: deleteエラー' . $sql_d;
                                } 
                        }
                        
                } else {
                    $errors[] = 'cart_table: deleteエラー' . $sql_d;
                }
                
                if (count($errors) === 0) {
                    $message[] = '削除成功';
                        mysqli_commit($link);
                } else {
                    $errors[] = '削除失敗' . $sql_d;
                        mysqli_rollback($link);
                }
        }
}



    $sql_s = 'SELECT 
              it.id,
              it.img, 
              it.name, 
              it.price, 
              est.stock, 
              it.status
              FROM item_table AS it 
              JOIN 
              ec_stock_table AS est
              ON 
              it.id = est.item_id';
              
   if ($result = mysqli_query($link, $sql_s)) {
       $i = 0;
       while($row = mysqli_fetch_assoc($result)) {
           $select_data[$i]['id'] = htmlspecialchars($row['id'] , ENT_QUOTES, 'UTF-8');
           $select_data[$i]['img'] = htmlspecialchars($row['img'], ENT_QUOTES, 'UTF-8');
           $select_data[$i]['name'] = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
           $select_data[$i]['price'] = htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8');
           $select_data[$i]['stock'] = htmlspecialchars($row['stock'], ENT_QUOTES, 'UTF-8');
           $select_data[$i]['status'] = htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8');
           $i++;
       }

        mysqli_free_result($result);
   } else {
       $errors[] = 'SQL失敗:' . $sql_s;
   }
}


mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>codeSHOP管理ページ</title>
        
        <style type="text/css">
        
            table, tr, th, td {
                border: solid 1px;
                padding: 10px;
                text-align: center;
            }
            
            table {
                /*width: 660px;*/
                border-collapse: collapse;
            }
            
            table tr:nth-child(2n) {
                /* 表の偶数行のみを対象に装飾するCSS */
                background-color: yellow;
            }
            
            .goods_insert {
                margin-top: 30px;
            }
            
            .links {
                border-bottom: solid black 1px;
            }
            
            .forms {
                padding-bottom: 20px;
                border-bottom: solid black 1px;
            }
        </style>
        
    </head>
    <body>
        <h1>codeSHOP 管理ページ</h1>
        
        <div class="links">
            <a href="./logout.php">ログアウト</a>
            <a href="./user_management.php">ユーザ管理ページ</a>
        </div>
        
<?php foreach ($message as $mes) { ?>
        <ul>
            <li><?php print $mes; ?></li>
        </ul>
<?php var_dump($message); ?>
<?php } ?>

<?php foreach ($errors as $err) { ?>
        <ul>
            <li><?php print $err; ?></li>
        </ul>
<?php } ?>
        <h2 class="goods_insert">商品の登録</h2>
        
        <div class="forms">
        <form method="post" enctype="multipart/form-data">
            
            <input type="hidden" name="carving" value="insert">
            <label>商品名: <input type="text" name="goods_name"></label><br>
            <label>値 段: <input type="text" name="price"></label><br>
            <label>個 数: <input type="text" name="stock"></label><br>
            <label>商品画像<input type="file" name="new_img"></label><br>
            <label id="status">ステータス:
                <select name="public_nopublic" id="status">
                    <option value="2"></option>
                    <option value="0">非公開</option>
                    <option value="1">公開</option>
                </select><br>
            </label>
            <input type="submit" value="商品を登録する">
        </form>
        </div>
        
        <h2>商品情報の一覧・変更</h2>
<?php if (count($select_data) >= 1) { ?>
        <table>
            <tr>
                <th>商品画像</th>
                <th>商品名</th>
                <th>価格</th>
                <th>在庫数</th>
                <th>ステータス</th>
                <th>操作</th>
            </tr>
<?php foreach ($select_data as $data) { ?>
            <tr>
                <td><img src="<?php print $img_dir . $data['img']; ?>"></td>
                <td><?php print $data['name']; ?></td>
                <td><?php print $data['price']; ?>円</td>
                <td>
                    <form method="post">
                        <input type="hidden" name="carving" value="update_stock">
                        <input type="hidden" name="item_id" value="<?php print $data['id']; ?>">
                        <input type="text" name="update_stock" value="<?php print $data['stock']; ?>">
                        <input type="submit" value="変更する">
                    </form>
                </td>
                <td>
                    <form method="post">
                        <input type="hidden" name="carving" value="update_status">
                        <input type="hidden" name="item_id" value="<?php print $data['id']; ?>">
<?php             if ($data['status'] === '0') { ?>
                        <input type="hidden" name="update_public_nopublic" value="1">
                        <input type="submit" value="非公開→公開にする">
<?php             } else { ?>
                        <input type="hidden" name="update_public_nopublic" value="0">
                        <input type="submit" value="公開→非公開にする">
<?php             } ?>
                    </form>
                </td>
                <td>
                    <form method="post">
                        <input type="hidden" name="carving" value="delete_record">
                        <input type="hidden" name="item_id" value="<?php print $data['id']; ?>">
                        <input type="submit" value="削除する">
                    </form>
                </td>
            </tr>
<?php } ?>
        </table>
<?php } ?>
    </body>
</html>