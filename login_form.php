<?php
//************************************** */
// Make login_form
//************************************** */

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

function validation()
{
    $errors = [];
    $email = $_POST['mail'];
    //メールのバリデーション
    $pattern = "/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/";
    if (preg_match($pattern, $email)) {
        // echo "正しい形式のメールアドレスです。";
    } else {
        $errors[] = "「メールアドレス」はアルファベットで入力してください。";
    }
    return $errors;
}

if (isset($_POST['btn_confirm'])) {
    $errors = validation();
    //接続するデータベースの情報
    $dsn = 'mysql:dbname=form; host=localhost';
    $user = 'root';
    $password = 'root';

    try {
        //データベースへの接続開始
        $dbh = new PDO($dsn, $user, $password);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //SQL作成
        $sql = 'SELECT * FROM form1 WHERE MAIL = :mail';

        //SQL実行準備
        $sth = $dbh->prepare($sql);

        // 値を渡して実行
        $sth->execute(array(
            ':mail' => $_POST['mail']
        ));

        // 結果を取得
        $result = $sth->fetchAll();

        // データベースに既に登録済の場合
    } catch (PDOException $e) {
        // print('既に登録されているメールアドレスです。');
        // die();
    }
    if (empty($errors)) {
        if (password_verify($_POST['password'], $result[0]['PASSWORD'])) {
            header("Location: ./form2.php");
        }
    }
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログインフォーム</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <h1 class="text-center">ログインフォーム</h1>
        <form action="" method="post">
            <div class="element_wrap">
                <label for="pop">メールアドレス</label>
                <input type="text" name="mail" class="form-control">
            </div>
            <div class="element_wrap">
                <label for="pop1">パスワード</label>
                <input type="text" name="password" class="form-control"><br>
            </div>
            <div class="d-grid gap-2 col-6 mx-auto">
                <input type="submit" class="btn btn-primary" name="btn_confirm" value="ログイン">
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
</body>

</html>