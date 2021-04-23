<?php
//************************************** */
// Make form
//************************************** */

//タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

$page_flg = 0;
$error = array();

if (!empty($_POST['btn_confirm'])) {
    $error = validation($_POST);
    if (empty($error)) {
        $page_flg = 1;
    }
} elseif (!empty($_POST['btn_submit'])) {

    //パスワードの暗号化
    $hash_pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $page_flg = 2;

    // 接続するデータベースの情報
    $dsn = 'mysql:dbname=form; host=localhost';
    $user = 'root';
    $password = 'root';

    try {
        //データベースへの接続開始
        $dbh = new PDO($dsn, $user, $password);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //bindParamを利用したSQL文の実行
        $sql = 'INSERT INTO form1 (NAME,TEL,MAIL,AGE,GENDER,SEND_MAIL,PASSWORD) VALUE(:name,:tel,:mail,:age,:gender,:send_mail,:password);';
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':name', $_POST['name']);
        $sth->bindParam(':tel', $_POST['tel']);
        $sth->bindParam(':mail', $_POST['mail']);
        $sth->bindParam(':age', $_POST['age']);
        $sth->bindParam(':gender', $_POST['gender']);
        $sth->bindParam(':send_mail', $_POST['send_mail']);
        $sth->bindParam(':password', $hash_pass);
        $sth->execute();
    } catch (PDOException $e) {
        //print('既に登録されているメールアドレスです。);
        //die();
    }
}

function validation()
{

    $error = [];

    // 名前のバリデーション
    if (empty($_POST['name'])) {
        $error[] = "「名前」は必ず入力してください。";
    } elseif (20 < mb_strlen($_POST['name'])) {
        $error[] = "「名前」は20文字以内で入力してください。";
    }
    // フリガナのバリデーション
    if (20 < mb_strlen($_POST['kana'])) {
        $error[] = "「ふりがな」は20文字以内で入力してください。";
    }
    // 電話のバリデーション
    if (preg_match("/^\d{10}$/", $_POST['tel'])) {
        $error[] = "「電話」は数字で入力してください。";
    }
    $mail = $_POST['mail'];
    //メールのバリデーション
    $pattern = "/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/";
    if (preg_match($pattern, $mail)) {
    } else {
        $error[] = "「メールアドレス」はアルファベットで入力してください。";
    }
    return $error;
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員登録フォーム</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <h1 class="text-center">会員登録フォーム</h1>

        <!-- 確認画面 -->
        <?php if ($page_flg === 1) : ?>

            <form action="" method="post">
                <div class="element_wrap">
                    <label for="pop">名前</label>
                    <p><?php echo $_POST['name']; ?></p>
                </div>
                <div class="element_wrap">
                    <label for="pop1">かな</label>
                    <p><?php echo $_POST['kana']; ?></p>
                </div>
                <div class="element_wrap">
                    <label for="pop2">電話</label>
                    <p><?php echo $_POST['tel']; ?></p>
                </div>
                <div class="element_wrap">
                    <label for="pop3">メール</label>
                    <p><?php echo $_POST['mail']; ?></p>
                </div>
                <div class="element_wrap">
                    <label for="pop4">生まれ年</label>
                    <p><?php echo $_POST['age']; ?></p>
                </div>
                <div class="element_wrap">
                    <label for="pop5">性別</label>
                    <p><?php echo $_POST['gender'] == 0 ? '男性' : '女性'; ?></p>
                </div>
                <div class="element_wrap">
                    <label for="pop6">送付</label>
                    <p><?php echo $_POST['send_mail'] == 0 ? '送付しない' : '送付する'; ?></p>
                </div>
                <div class="element_wrap">
                    <label for="pop7">パスワード</label>
                    <p><?php echo $_POST['password']; ?></p>
                </div>
                <input type="submit" name="btn_back" value="戻る">
                <input type="submit" name="btn_submit" value="送信">
                <input type="hidden" name="name" value="<?php echo $_POST['name']; ?>">
                <input type="hidden" name="kana" value="<?php echo $_POST['kana']; ?>">
                <input type="hidden" name="tel" value="<?php echo $_POST['tel']; ?>">
                <input type="hidden" name="mail" value="<?php echo $_POST['mail']; ?>">
                <input type="hidden" name="age" value="<?php echo $_POST['age']; ?>">
                <input type="hidden" name="gender" value="<?php echo $_POST['gender']; ?>">
                <input type="hidden" name="send_mail" value="<?php echo $_POST['send_mail']; ?>">
                <input type="hidden" name="password" value="<?php echo $_POST['password']; ?>">
            </form>

            <!-- 完了画面 -->
        <?php elseif ($page_flg === 2) : ?>

            <h1><a href="../form/form1.php">会員登録ありがとうございました。</a></h1>
        <?php else : ?>

            <?php if (!empty($error)) : ?>
                <ul class="error_list">
                    <?php foreach ($error as $value) : ?>
                        <li><?php echo $value; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <!-- 入力画面 -->
            <form action="" method="post" name="form1">
                <div class="element_wrap">
                    <label for="pop">名前</label>
                    <input type="text" name="name" class="form-control" value="<?php if (!empty($_POST['name'])) {
                                                                                    echo $_POST['name'];
                                                                                } ?>" id="pop">
                </div>
                <div class="element_wrap">
                    <label for="pop1">かな</label>
                    <input type="text" name="kana" class="form-control" value="<?php if (!empty($_POST['kana'])) {
                                                                                    echo $_POST['kana'];
                                                                                } ?>" id="pop1">
                </div>
                <div class="element_wrap">
                    <label for="pop2">電話</label>
                    <input type="text" name="tel" class="form-control" value="<?php if (!empty($_POST['tel'])) {
                                                                                    echo $_POST['tel'];
                                                                                } ?>" id="pop2">
                </div>
                <div class="element_wrap">
                    <label for="pop3">メール</label>
                    <input type="text" name="mail" class="form-control" value="<?php if (!empty($_POST['mail'])) {
                                                                                    echo $_POST['mail'];
                                                                                } ?>" id="pop3">
                </div>
                <div class="element_wrap">
                    <label for="pop4">生まれ年</label>
                    <select name="age" id="pop5" class="form-select col-md-6">
                        <?php
                        for ($age = 1900; $age <= 2021; $age++) {
                            echo '<option value="', $age, '">', $age, '</option>';
                        }; ?>
                    </select>
                </div>
                <div class="element_wrap text-center">
                    <label for="pop5">性別</label>
                    <input type="radio" name="gender" class="form-check-input" value="0" id="pop5">男性
                    <input type="radio" name="gender" class="form-check-input" value="1" id="pop5">女性
                </div>
                <div class="element_wrap text-center">
                    <label for="pop6">メールマガジン送付</label>
                    <input type="hidden" name="send_mail" value="0">
                    <input type="checkbox" name="send_mail" value="1" id="pop6" class="form-check-input">
                </div>
                <div class="element_wrap">
                    <label for="pop7">パスワード</label>
                    <input type="text" class="form-control" name="password" value=""><br>
                </div>
                <div class="d-grid gap-2 col-6 mx-auto">
                    <input type="submit" class="btn btn-primary" name="btn_confirm" value="登録">
                </div>
            <?php endif; ?>
            </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
</body>

</html>