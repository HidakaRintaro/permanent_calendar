# 万年カレンダー

スケジュール登録のできる万年カレンダー。デザインはニューモーフィズムを意識。

レスポンシブは未対応。2020年～2022年までの祝日に対応。

## 技術仕様

HTML, CSS, PHP

## 更新履歴

### 4/28 

①カレンダーの表示。リンクから先月、来月へ1か月ずつ遷移。各日付ごとにスケジュールを登録、更新、削除が可能。2020年～2022年までの祝日に対応。祝日データと入力スケジュールはCSVファイルで管理。

②入力時のエラー処理の実装。(詳細：値が入力されているか、入力値が空白や改行のみではないか。エラーメッセージの表示。)登録、更新、削除の完了メッセージの表示。entry.phpにて曜日の表示。ボタンやリンク、カレンダーのホバーアクション、テキストの配置などのCSSの実装。フォルダ構成の変更。entry.cssの実装。

③entry.phpで祝日時、祝日名を表示。カレンダーでスケジュールが登録されている日付には「★」を出力。前記に伴う一部のCSSの修正。

④index.phpのデザインの大幅変更(ニューモーフィズムを意識した)。index.phpとstyle.cssのコードをデザインの変更に伴い大幅変更。カレンダーの日付を左上、祝日名を右上、スケジュールがあるときは右下に●を出力。

⑤entry.phpのデザインの大幅変更(ニューモーフィズムを意識した)。entry.phpとentry.cssのコードをデザインの変更に伴い大幅変更。エラー項目に更新時に内容が変更されていない時も追加。inex.phpのaタグのリンク先の修正。

⑥スケジュールの更新時に入力エラーがあった場合もとの値を表示に戻るように修正。