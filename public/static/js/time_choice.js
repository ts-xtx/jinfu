//$.getScript('/public/static/laydate/laydate.js',function () {
    var start = {
        elem: '#start-time',
        format: 'YYYY-MM-DD hh:mm:ss',
        min: '2017-07-01 00:00:00',
        max: '2099-06-16 23:59:59',
        istime: true,
        istoday: false,
        choose: function(datas){
            end.min = datas;
            end.start = datas
        }
    };
    var end = {
        elem: '#end-time',
        format: 'YYYY-MM-DD hh:mm:ss',
        min: '2017-07-01 00:00:00',
        max: '2099-06-16 23:59:59',
        istime: true,
        istoday: false,
        choose: function(datas){
            start.max = datas;
        }
    };
    laydate(start);
    laydate(end);
//});