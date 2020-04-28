<?php

if ( empty($_GET['year']) || empty($_GET['month']) || empty($_GET['day']) ) {
  header('location: ./index.php');
  exit;
}

// 変数の初期化
$year = $_GET['year'];
$month = $_GET['month'];
$day = $_GET['day'];
$hol = '';
$week_ja = ['日', '月', '火', '水', '木', '金', '土'];
$content = '';
$schedule_list = [];
$p_key = null;
$max_key = 0;
$sch_br = ["\r\n", "\n", "\r"];
$sch_sp = ["\r\n", "\n", "\r", " ", "　"];
$update_flg = 0;
$err_msg = '';
$cmp_msg = '';

// 祝日の名前の取得
$fp = fopen('./csv/holiday.csv', 'r');
while ($row = fgets($fp)) {
  $row = explode(',', $row);
  if ( ($year.'-'.$month.'-'.$day) == $row[0] ) {
    $hol = $row[1];
    break;
  }
}
fclose($fp);

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
fclose($fp);

// 登録や更新、削除ボタンを押されたとき
if (isset($_POST['submit'])) {
  // 入力エラーの確認
  if (empty($_POST['content'])) {
    $err_msg = 'スケジュールが入力されていません。';
  }
  elseif ( empty(str_replace($sch_sp, '', $_POST['content'])) ) {
    $err_msg = '空白や改行だけでは登録・更新できません。';
  }

  // 登録や更新、削除の処理
  if (empty($err_msg)) {
    // 改行コードの置換
    $content = str_replace($sch_br, '%改行%', $_POST['content']);
  
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
        fclose($fp);
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
  
      // 完了メッセージの代入
      $cmp_msg = 'スケジュールの登録が完了しました。';
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
  
      // 完了メッセージの代入
      $cmp_msg = 'スケジュールの更新が完了しました。';
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

      // 更新可能のフラグを取り消す
      $update_flg = 0;
  
      // 完了メッセージの代入
      $cmp_msg = 'スケジュールの削除が完了しました。';
    }
  }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>詳細登録</title>
  <link rel="stylesheet" href="./css/entry.css">
</head>
<body>
  <div id="header">
    <h1>スケジュールの<?php echo $update_flg ? '更新・削除' : '登録'; ?></h1>
    <p class="top_link"><a href="./?year=<?php echo $year; ?>&month=<?php echo $month; ?>">TOPへ戻る</a></p>
  </div>
  <p class="red msg"><?php echo $err_msg; ?></p>
  <p class="green msg"><?php echo $cmp_msg; ?></p>
  <div class="day_data">
    <h2><?php echo $year.'年'.$month.'月'.$day.'日('.$week_ja[date('w', strtotime($year.'-'.$month.'-'.$day))].')'; ?></h2>
    <p><?php echo $hol; ?></p>
  </div>
  <form method="post">
    <textarea name="content" id="content"><?php echo $content; ?></textarea>
<?php if ($update_flg) : ?>
    <div>
      <button id="btn_green" type="submit" name="submit" value="update">更新</button>
      <button id="btn_red" type="submit" name="submit" value="delete">削除</button>
    </div>
<?php else: ?>
    <div><button id="btn_green" type="submit" name="submit" value="entry">登録</button></div>
<?php endif; ?>
  </form>
</body>
</html>