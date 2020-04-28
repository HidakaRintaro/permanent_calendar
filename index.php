<?php

  // 変数の初期化
  $date = [];
  $bg_list = [];
  $week_list = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'hol'];
  $week_days = '';
  $start_flg = 0;
  $sch_data = [];

  // 受け取った年月を代入
  if (!empty($_GET['year']) && !empty($_GET['month'])) {
    $year = $_GET['year'];
    $month = $_GET['month'];
  }
  // 受け取る値がない時の変数の初期設定
  else {
    $year = date('Y');
    $month = date('n');
  }
  
  // 月が0の時、前年の12月にする
  if ( $month < 1) {
    $year--;
    $month = 12;
  }
  // 月が13の時、翌年の1月にする
  elseif ( $month > 12 ) {
    $year++;
    $month = 1;
  }

  // 月の初日の曜日と最終日を取得
  $first_week = date('w', strtotime($year.'-'.$month.'-1'));
  $end_day = date('t', strtotime($year.'-'.$month.'-1'));

  // 月の2次元配列の作成
  $day = 1;
  for ($i=0; $i < 6; $i++) {
    for ($j=0; $j < 7; $j++) {

      // 月の初日の曜日になったら開始フラグを立てる
      if ($j == $first_week) $start_flg = 1;

      // 日付の代入
      if ($start_flg && $day <= $end_day) {
        $date[$i][] = $day;
        
        // スケジュールが存在するか確認
        $sch_data[$i][$j] = '';
        $fp = fopen('./csv/schedule.csv', 'r');
        while ($row = fgets($fp)) {
          $row = explode(',', $row);
          if ($row[1] == ($year.'-'.$month.'-'.$day)) {
            $sch_data[$i][$j] = '★';
          }
        }
        fclose($fp);

        // 祝日の時配列に祝日用のクラス名を代入
        $fp = fopen('./csv/holiday.csv', 'r');
        while ($row = fgets($fp)) {
          $row = explode(',', $row);
          if ($row[0] == ($year.'-'.$month.'-'.$day)) $bg_list[$i][] = $week_list[7];
        }
        fclose($fp);
        // 祝日ではないとき各曜日のクラス名を配列に代入
        if (!isset($bg_list[$i][$j])) $bg_list[$i][] = $week_list[$j];
        $day++;
      }
      // 日付の入らないマスに空文字を代入
      else {
        $date[$i][] = '';
        $bg_list[$i][] = '';
        $sch_data[$i][] = '';
      }
    }
  }

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>万年カレンダー</title>
  <link rel="stylesheet" href="./css/style.css">
</head>
<body>
  <h1><?php echo $year; ?>年<?php echo $month; ?>月</h1>
  <div class="move_link">
    <p><a href="./?year=<?php echo $year; ?>&month=<?php echo $month-1; ?>">前の月へ</a></p>
    <p><a href="./?year=<?php echo $year; ?>&month=<?php echo $month+1; ?>">次の月へ</a></p>
  </div>
  <table>
    <tr>
      <th class="red">日</th><th>月</th><th>火</th><th>水</th><th>木</th><th>金</th><th class="blue">土</th>
    </tr>
<?php foreach ($date as $key1 => $row) : ?>
    <tr>
<?php   foreach ($row as $key2 => $val) : ?>
<?php $url = $val == '' ? './' : './entry.php?year='.$year.'&month='.$month.'&day='.$val ; ?>
      <td class="<?php echo $bg_list[$key1][$key2]; ?>"><a href="<?php echo $url; ?>"><?php echo $val.$sch_data[$key1][$key2]; ?></a></td>
<?php   endforeach; ?>
    </tr>
<?php endforeach; ?>
  </table>
</body>
</html>