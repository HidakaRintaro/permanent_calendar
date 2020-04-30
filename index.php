<?php

  // $yearと$monthは計算で用いるためintval()を用いて数値として取得。
  // 出力時は1~9は先頭に0を付けるため文字列として出力。

  // 変数の初期化
  $year = 0;
  $month = 0;
  $Y4m2 = '';
  $day_1st_w = null;
  $day_end = 0;
  $date_ary = [];
  $hol_ary = [];
  $sch_ary = [];
  $day = 1;
  $hol_path = './csv/holiday.csv';
  $sch_path = './csv/schedule.csv';
  $entry_pash = '';
  $hol_flg = '';
  $week_ary = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];

  // URLから値(年月)の受け取り
  if ( !empty(!empty($_GET['year']) && !empty($_GET['month'])) ) {
    $year = intval($_GET['year']);
    $month = intval($_GET['month']);
  }
  // 値の初期値(現在の年月)
  else {
    $year = intval(date('Y'));
    $month = intval(date('m'));
  }

  // monthが0以下の時、前年の12月に年度を変更
  if ($month < 1) {
    $year--;
    $month = 12;
  }
  // monthが13以上の時、翌年の1月に年度を変更
  if ($month > 12) {
    $year++;
    $month = 1;
  }

  // 表示月の「YYYY-mm」の作成
  $Y4m2 = sprintf('%04d', $year).'-'.sprintf('%02d', $month);

  // 月の1日の曜日の受け取り
  $day_1st_w = intval( date('w', strtotime($Y4m2.'-01')) );
  
  // 月の最終日の受け取り
  $day_end = intval( date('t', strtotime($Y4m2.'-01')) );

  // 月の2次元配列の作成
  for ($i = 0; $day < $day_end; $i++) { 
    for ($j = 0; $j < 7; $j++) { 
      // それぞれの配列に値がない時のnullと空文字を代入
      if ( ($i == 0 && $j < $day_1st_w) || ($day > $day_end) ) {
        $date_ary[$i][] = null;
        $hol_ary[$i][$j] = '';
        $sch_ary[$i][$j] = '';
      }
      // 月の配列に日付を代入し日付を進める
      elseif ( $day < ($day_end + 1) ) {
        $date_ary[$i][] = $day;

        // 祝日の配列を作成
        $hol_ary[$i][$j] = '';
        $fp = fopen($hol_path, 'r');
        while ($row = fgets($fp)) {
          $row = explode(',', $row);
          // 祝日の配列に祝日時、holを代入
          if ($row[0] == $Y4m2.'-'.sprintf('%02d', $day)) $hol_ary[$i][$j] = $row[1];
        }
        fclose($fp);

        // スケジュールの配列を作成
        $sch_ary[$i][$j] = '';
        $fp = fopen($sch_path, 'r');
        while ($row = fgets($fp)) {
          $row = explode(',', $row);
          // スケジュールの配列にデータがあるとき●を代入
          if ($row[1] == $Y4m2.'-'.sprintf('%02d', $day)) $sch_ary[$i][$j] = '●';
        }
        fclose($fp);
        
        $day++;
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
  <div class="link_list">
    <p><a href="./?year=<?php echo $year; ?>&month=<?php echo $month-1; ?>">前の月へ</a></p>
    <p><a href="./?year=<?php echo $year; ?>&month=<?php echo $month+1; ?>">次の月へ</a></p>
  </div>
  <table>
    <tr class="thead">
      <th colspan="7"><?php echo $year; ?>/<?php echo $month; ?></th>
    </tr>
    <tr>
      <th class="sun">日</th><th>月</th><th>火</th><th>水</th><th>木</th><th>金</th><th class="sat">土</th>
    </tr>
<?php foreach ($date_ary as $row => $weekly) : ?>
    <tr class="day_row">
<?php   foreach ($weekly as $col => $day) : ?>
<?php $entry_pash = empty($day) ? './' : './entry.php?year='.$year.'&month='.$month.'&day='.$day ; ?>
<?php $hol_flg = ($hol_ary[$row][$col] == '') ? '' : ' hol'; ?>
      <td class="<?php echo $week_ary[$col].$hol_flg; ?>">
        <a href="<?php echo $entry_pash; ?>">
          <div class="td_inner">
            <div class="td_1st_line">
              <p class="day"><?php echo $day; ?></p>
              <p class="holiday"><?php echo $hol_ary[$row][$col]; ?></p>
            </div>
            <div class="td_2nd_line">
              <p class="sch_flg"><?php echo $sch_ary[$row][$col]; ?></p>
            </div>
          </div>
        </a>
      </td>
<?php   endforeach; ?>
    </tr>
<?php endforeach; ?>
  </table>

</body>
</html>