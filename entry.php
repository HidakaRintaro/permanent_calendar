<?php

if ( !isset($_GET['year']) || !isset($_GET['month']) || !isset($_GET['day']) ) {
  header('location: ./index.php');
  exit;
}

// 変数の初期化
$year = $_GET['year'];
$month = $_GET['month'];
$day = $_GET['day'];
$content = '';
$schedule_list = [];
$p_key = null;
$max_key = 0;
$search = ["\r\n", "\n", "\r"];
$update_flg = 0;

// スケジュールファイルから保存済みのデータの読込
$fp = fopen('./csv/schedule.csv', 'r');
while ($row = fgets($fp)) {
  $row = explode(',', $row);
  $schedule_list[] = $row;
  // すでに入力済みのスケジュールの取得
  if ($row[1] == ($year.'-'.$month.'-'.$day)) {
    $content = str_replace('%改行%', "\n", $row[2]);
    $p_key = $row[0];
    $update_flg = 1;
  }
}

// 登録や更新、削除ボタンを押されたとき
if (isset($_POST['submit'])) {
  // 改行コードの置換
  $content = str_replace($search, '%改行%', $_POST['content']);

  // 登録するとき
  if ($_POST['submit'] == 'entry') {
    // 主キーの取得
    if (!isset($p_key)) {
      $fp = fopen('./csv/schedule.csv', 'r');
      while ($row = fgets($fp)) {
        $row = explode(',', $row);
        if ($max_key < $row[0]) {
          $max_key = $row[0];
        }
      }
      $p_key = $max_key + 1;
    }

    // ファイルに追記
    $fp = fopen('./csv/schedule.csv', 'a');
    fputs($fp, $p_key.','.$year.'-'.$month.'-'.$day.','.$content.','.date('Y-n-j').','.date('Y-n-j')."\n");
    fclose($fp);

    // 表示用に代入
    $content = str_replace('%改行%', "\n", $content);

    // 更新可能のフラグを立てる
    $update_flg = 1;
  }

  // 更新するとき
  elseif ($_POST['submit'] == 'update') {
    // ファイルに書き込み
    $fp = fopen('./csv/schedule.csv', 'w');
    foreach ($schedule_list as $row) {
      if ($p_key == $row[0]) {
        fputs($fp, $p_key.','.$row[1].','.$content.','.$row[3].','.date('Y-n-j')."\n");
      } else {
        fputs($fp, implode(',', $row));
      }
    }
    fclose($fp);

    // 表示用に代入
    $content = str_replace('%改行%', "\n", $content);

    // 更新可能のフラグを立てる
    $update_flg = 1;
  }

  // 削除するとき
  elseif ($_POST['submit'] == 'delete') {
    // ファイルに書き込み
    $fp = fopen('./csv/schedule.csv', 'w');
    foreach ($schedule_list as $row) {
      if ($p_key != $row[0]) {
        fputs($fp, implode(',', $row));
      }
    }
    fclose($fp);

    // 表示用に代入
    $content = '';
  }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>詳細登録</title>
</head>
<body>
  <h1>スケジュールの<?php echo $update_flg ? '更新・削除' : '登録'; ?></h1>
  <p><?php echo $year.'年'.$month.'月'.$day.'日'; ?></p>
  <form method="post">
    <textarea name="content" id="content" cols="70" rows="20"><?php echo $content; ?></textarea>
<?php if ($update_flg) : ?>
    <button type="submit" name="submit" value="update">更新</button>
    <button type="submit" name="submit" value="delete">削除</button>
<?php else: ?>
    <button type="submit" name="submit" value="entry">登録</button>
<?php endif; ?>
  </form>
  <p><a href="./?year=<?php echo $year; ?>&month=<?php echo $month; ?>">カレンダーに戻る</a></p>
</body>
</html>