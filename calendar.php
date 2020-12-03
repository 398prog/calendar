<?php
class calendar{
  private $year;
  private $month;

  public function __construct($y,$m){
    $this->year = $y;
    $this->month = $m;
  }

  public function get_info(){
    return $this->year."-".$this->month;
  }
  
  public static function init_row(){
    $arr = array();
    for($i = 0;$i <= 6;$i++){
      $arr[$i] = "・";
    }
    return $arr;
  }

  public function create_rows(){
    $last_day = date("j",mktime(0,0,0,$this->month+1,0,$this->year));
    $rows = array();
    $row = self::init_row();

    for($i = 1;$i <= $last_day;$i++){
      $date = Date("w",mktime(0,0,0,$this->month,$i,$this->year));
      $row[$date] = $i;

      if($date == 6 || $i == $last_day){
        $rows []= $row;
        $row = self::init_row();
      }
    }
    return $rows;
  }
  
}

$year = Date("Y");
$month = Date("n");
$cal = new calendar($year,$month);
?>

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

<!-- html -->

<!DOCTYPE html>
<html lang = "ja">
<head>
  <meta charset="UTF-8">
  <title>PHP calendar</title>
  <link rel="stylesheet" href="style.css">
  </head>
<body>
  <h1>
    <?php
    echo $cal->get_info();
    ?>
  </h1>

  <div>
  <table class="table">
    <tr class="day">
      <th>日</th>
      <th>月</th>
      <th>火</th>
      <th>水</th>
      <th>木</th>
      <th>金</th>
      <th>土</th>
    </tr>

    <?php
    foreach($cal->create_rows() as $row){
      echo "<tr>";
      for($i = 0;$i <= 6;$i++){
        echo "<td>".$row[$i]."</td>";
        
      }

    

    }
  ?>
  </table>

  <ul class="memo">
  <li>
  <form method="post" >
      title<br>
      <input type="text" name="title" size="20"><br>
      contents<br>
      <textarea name="contents" style="width:300px; height:80px;"></textarea><br>
      <input type="submit" name="create" value="OK">
  </form>
  </li>

  <?php
  //memoテーブルからデータを取得
  $stmt = $pdo -> query("SELECT * FROM memo");
  //foreachをつかってデータを1個ずつ順番に処理
  foreach($stmt as $row):
  ?>

  <li>
  <form method="post">
    <input type = "hidden" name="id" value="<?php echo $row[0]?>"></input>
    title<br>
    <input type = "text" name="title" size="20" value="<?php echo $row[1]?>"></input><br>
    contents<br>
    <textarea name="contents" style="width:600px; height:80px;"><?php echo $row[2]?></textarea><br>
    <input type="submit" name="update" value="変更">
    <input type="submit" name="delete" value="削除">
  </form>
  </li>
  
  <?php endforeach; ?>
  </ul>
  </div>

</body>
</html>
