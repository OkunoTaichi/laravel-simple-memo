
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel × FullCalendar</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        
        <!-- Script -->
        <script src="{{ asset('js/app.js') }}"></script>
     
       
    </head>
    <body>
        
        <div id="test">

            <div id="app">
                <div class="m-auto w-50 m-5 p-5  test">
                    <div id='calendar'></div>
                </div>
            </div>
        </div>
        

        <link href='{{ asset('fullcalendar-5.11.3/lib/main.css') }}' rel='stylesheet' />
        <script src='{{ asset('fullcalendar-5.11.3/lib/main.js') }}'></script>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        
        <script>


            document.addEventListener('DOMContentLoaded', function() {
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    
                    initialView: 'dayGridMonth',
                    // ヘッダーに表示させる部分
                    headerToolbar: {
                        // 左に表示（前後と今の表示）
                        left: 'prev,next today',
                        // 年月
                        center: 'title',
                        // 右に表示
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                    },
                    initialDate: '2020-12-01',
                    navLinks: true,
                    businessHours: true,
                    editable: true,
                    locale: 'ja',


                    // 日付をクリック、または範囲を選択したイベント
                    selectable: true,
                    select: function (info) {
                        // alert("selected " + info.startStr + " to " + info.endStr);

                        // 入力ダイアログ
                        const eventName = prompt("イベントを入力してください");
                        
                        if (eventName) {
                            // Laravelの登録処理の呼び出し
                                axios
                                .post("/schedule-add", {
                                    start_date: info.start.valueOf(),
                                    end_date: info.end.valueOf(),
                                    event_name: eventName,
                                })
                                .then(() => {
                                    // イベントの追加
                                    calendar.addEvent({
                                        title: eventName,
                                        start: info.start,
                                        end: info.end,
                                        allDay: true,
                                    });
                                })
                                .catch(() => {
                                    // バリデーションエラーなど
                                    alert("登録に失敗しました");
                                });
                                
                        }
                    },

                });

                
                calendar.render();
            });

            

        </script>
    </body>
</html>