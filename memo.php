<?php
  //データベースへ接続
  $dsn = "mysql:dbname=php_tools;host=localhost;charset=utf8mb4";
  $username = "root";
  $password = "";
  $options = [];
  $pdo = new PDO($dsn, $username, $password, $options);

  //OKボタンが押された際の処理を記述
  if(null !== @$_POST["create"]){ //OKボタンが押されたか確認
    
    if(@$_POST["title"] != "" OR @$_POST["contents"] != ""){
      //メモの内容を追加するSQL文を作成、executeで実行
      $stmt = $pdo->prepare("INSERT INTO memo(title,contents) VALUES(:title,:contents)");
      $stmt -> bindValue(":title",@$_POST["title"]); //:titleをpost送信されたtitleの内容に置換
      $stmt -> bindValue(":contents",@$_POST["contents"]); //:contentsをpost送信されたcontentsの内容に置換
      $stmt -> execute(); //SQL文を実行
    }

  }

  //変更ボタン処理
  if(null !== @$_POST["update"]){ //変更ボタンが押されたか確認
    $stmt = $pdo -> prepare("UPDATE memo SET title=:title, contents=:contents WHERE ID=:id");
    $stmt -> bindValue(":title",@$_POST["title"]);
    $stmt -> bindValue(":contents",@$_POST["contents"]);
    $stmt -> bindValue(":id",@$_POST["id"]);
    $stmt -> execute();
  }

  //削除ボタン処理
  if(null !== @$_POST["delete"]){
    $stmt = $pdo -> prepare("DELETE FROM memo WHERE ID=:id");
    $stmt -> bindValue(":id",@$_POST["id"]);
    $stmt -> execute();
  }

?>

<!-- HTML -->

<!DOCTYPE html>
<html lang = ja>
<head>
  <meta charset = "utf-8">
  <title>memo</title>
</head>
<body>
  <form action="memo.php" method="post">
    title<br>
    <input type="text" name="title" size="20"><br>
    contents<br>
    <textarea name="contents" style="width:300px; height:100px;"></textarea><br>
    <input type="submit" name="create" value="OK">
  </form>

  <!-- memo memory -->

  <?php
  //memoテーブルからデータを取得
  $stmt = $pdo -> query("SELECT * FROM memo");
  //foreachをつかってデータを1個ずつ順番に処理
  foreach($stmt as $row):
  ?>

    <form action="memo.php" method="post">
      <input type = "hidden" name="id" value="<?php echo $row[0]?>"></input>
    title<br>
      <input type = "text" name="title" size="20" value="<?php echo $row[1]?>"></input><br>
    contents<br>
      <textarea name="contents" style="width:300px; height:200px;"><?php echo $row[2]?></textarea><br>
      <input type="submit" name="update" value="変更">
      <input type="submit" name="delete" value="削除">
    </form>
  <?php endforeach; ?>
</body>
</html>