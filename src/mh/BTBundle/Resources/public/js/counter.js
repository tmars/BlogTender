jQuery(document).ready(function() {
    $('#countdown_dashboard').countDown({
        targetDate: {
            'day': 		12,
            'month': 	04,
            'year': 	2013,
            'hour': 	11,
            'min': 		20,
            'sec': 		00					},
            omitWeeks: true // Отключаем вывод количества недель
    });

});