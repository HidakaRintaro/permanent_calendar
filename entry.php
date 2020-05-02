<?php

  // entry.phpへの直接アクセスへの対策
  if ( empty($_GET['year']) || empty($_GET['month']) || empty($_GET['day']) ) {
    header('location: ./index.php');
    exit;
  }
  
  // 変数の初期化
  $year = $_GET['year'];
  $month = $_GET['month'];
  $day = $_GET['day'];
  $Y4m2d2 = '';
  $hol_path = './csv/holiday.csv';
  $sch_path = './csv/schedule.csv';
  $holiday = '';
  $sch_ary = [];
  $br_list = ["\r\n", "\n", "\r"];
  $sp_list = ["\r\n", "\n", "\r", " ", "　"];
  $content = '';
  $pm_key = null;
  $err_msg = '';
  $max_key = 0;
  $cmp_msg = '';
  $week_ja = ['日', '月', '火', '水', '木', '金', '土'];

  // 表示月の「YYYY-mm-dd」の作成
  $Y4m2d2 = sprintf('%04d', $year).'-'.sprintf('%02d', $month).'-'.sprintf('%02d', $day);

  // 祝日の名前の取得
  $fp = fopen($hol_path, 'r');
  while ($row = fgets($fp)) {
    $row = explode(',', $row);
    if ($Y4m2d2 == $row[0]) {
      $holiday = $row[1];
      break;
    }
  }
  fclose($fp);

  // 既存スケジュールの2次元配列の作成
  $fp = fopen($sch_path, 'r');
  while ($row = fgets($fp)) {
    $row = explode(',', $row);
    $sch_ary[] = $row;
    // 日付一致のスケジュールの取得
    if ($Y4m2d2 == $row[1]) {
      $content = str_replace('%改行%', "\n", $row[2]);
      $pm_key = $row[0];
    }
  }
  fclose($fp);

  // 登録や更新、削除ボタンを押されたとき
  if (isset($_POST['submit'])) {
    
    // 改行コードの置換
    $in_content = str_replace($br_list, '%改行%', $_POST['content']);

    // 入力エラーの確認
    if (empty($_POST['content'])) {
      $err_msg = 'スケジュールが入力されていません。';
    } elseif ( empty(str_replace($sp_list, '', $_POST['content'])) ) {
      $err_msg = '空白や改行だけでは登録・更新できません。';
      
      // 空白や改行だけの時は$contentを空文字に置き換える
      // 更新時はもと値を戻す
      if ($_POST['submit'] != 'update') {
        $content = '';
      }
    }
    // 更新ボタンを押されたが内容が変更されてないとき
    elseif ($_POST['submit'] == 'update') {
      foreach ($sch_ary as $row) {
        if ($pm_key == $row[0] && $in_content == $row[2]) {
          $err_msg = '内容が変更されていません。';
        break;
      } 
    }
  }
  
  // 登録や更新、削除の処理の実行
  if (empty($err_msg)) {
    
    // 入力値の取得
    $content = $_POST['content'];

    // 登録処理
    if ($_POST['submit'] == 'entry') {
      
      // 主キーの取得
      $fp = fopen($sch_path, 'r');
      while ($row = fgets($fp)) {
        $row = explode(',', $row);
        if ($max_key < $row[0]) {
          $max_key = $row[0];
        }
      }
        fclose($fp);
        $pm_key = $max_key + 1;

        // ファイルへ追記
        $fp = fopen($sch_path, 'a');
        fputs($fp, $pm_key.','.$Y4m2d2.','.$in_content.','.date('Y-m-d').','.date('Y-m-d')."\n");
        fclose($fp);

        // 完了メッセージの作成
        $cmp_msg = 'スケジュールの登録が完了しました。';
      }
      // 更新処理
      elseif ($_POST['submit'] == 'update') {

        // ファイルへ書き込み
        $fp = fopen($sch_path, 'w');
        foreach ($sch_ary as $row) {
          if ($pm_key == $row[0]) {
            fputs($fp, $pm_key.','.$row[1].','.$in_content.','.$row[3].','.date('Y-m-d')."\n");
          } else {
            fputs($fp, implode(',', $row));
          }
        }
        fclose($fp);

        // 完了メッセージの作成
        $cmp_msg = 'スケジュールの更新が完了しました。';
      }
      // 削除処理
      elseif ($_POST['submit'] == 'delete') {

        // ファイルへ書き込み
        $fp = fopen($sch_path, 'w');
        foreach ($sch_ary as $row) {
          if ($pm_key != $row[0]) {
            fputs($fp, implode(',', $row));
          }
        }
        fclose($fp);

        // $contentの削除
        $content = '';

        // 完了メッセージの作成
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
  <div class="header">
    <h1>スケジュールの<?php echo empty($content) ? '登録' : '更新・削除'; ?></h1>
    <p class="btn_top"><a href="./?year=<?php echo $year; ?>&month=<?php echo $month; ?>">TOPへ戻る</a></p>
  </div>
  <div class="day_data">
    <h2 class="date"><?php echo $year; ?>/<?php echo $month; ?>/<?php echo $day; ?>(<?php echo $week_ja[date('w', strtotime($Y4m2d2))]; ?>)</h2>
    <p class="holiday"><?php echo $holiday; ?></p>
  </div>
<?php if (!empty($err_msg)) : ?>
  <p class="err_msg"><?php echo $err_msg; ?></p>
<?php elseif (!empty($cmp_msg)) : ?>
  <p class="cmp_msg"><?php echo $cmp_msg; ?></p>
<?php endif; ?>
  <form method="post">
    <textarea name="content" id="content" placeholder="スケジュールなどを入力してください"><?php echo $content; ?></textarea>
<?php if (empty($content)) : ?>
    <div>
      <button class="btn_entry" type="submit" name="submit" value="entry">登録</button>
    </div>
<?php else : ?>
    <div>
      <button class="btn_update" type="submit" name="submit" value="update">更新</button>
      <button class="btn_delete" type="submit" name="submit" value="delete">削除</button>
    </div>
<?php endif; ?>
  </form>
</body>
</html>