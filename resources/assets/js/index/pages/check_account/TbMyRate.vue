<template>
    <div>
        <div class="layer"></div>
        <div class="geo">{{geo}}</div>
        <div id="timer" class="timer"></div>
        <iframe id="my-frame" :src="iframe_src" frameborder="0" style="width: 100%;min-height: 800px"></iframe>
    </div>
</template>

<script>
    export default {
        name: "TbMyRate",
        data: function () {
            return {
                geo: '',
                iframe_src: 'https://rate.taobao.com/user-myrate-UvCHyMmg0MFxT--buyerOrSeller%7C3--receivedOrPosted%7C1.htm',
            }
        },
        mounted: function () {
            let self = this;
            this.getGeo();
            $('meta[name=viewport]').attr('content','width=990');
            $('iframe').css('width', '100%').css('height', window.innerHeight);
            $('#timer').html(self.dateFmt("yyyy-MM-dd hh:mm:ss", new Date));
            setInterval(function () {
                $('#timer').html(self.dateFmt("yyyy-MM-dd hh:mm:ss", new Date));
            }, 1000);
        },
        methods: {
            getGeo: function () {
                let self = this;
                axios.get(api.checkGeo).then(function (res) {
                    self.geo = res.data.data;
                });
            },
            dateFmt: function (fmt, date) { //author: meizz
                var o = {
                    "M+": date.getMonth() + 1,                 //月份
                    "d+": date.getDate(),                    //日
                    "h+": date.getHours(),                   //小时
                    "m+": date.getMinutes(),                 //分
                    "s+": date.getSeconds(),                 //秒
                    "q+": Math.floor((date.getMonth() + 3) / 3), //季度
                    "S": date.getMilliseconds()             //毫秒
                };
                if (/(y+)/.test(fmt))
                    fmt = fmt.replace(RegExp.$1, (date.getFullYear() + "").substr(4 - RegExp.$1.length));
                for (var k in o)
                    if (new RegExp("(" + k + ")").test(fmt))
                        fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
                return fmt;
            }
        }
    }
</script>

<style scoped>
    .layer {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background-image: url('/images/check_bg.png');
        background-color: #000;
        opacity: 0.15;
    }

    .timer {
        position: fixed;
        top: 50px;
        right: 20px;
        color: #000;
        font-size: 30px;
    }

    .geo {
        position: fixed;
        top: 100px;
        right: 20px;
        color: #000;
        font-size: 30px;
    }
</style>