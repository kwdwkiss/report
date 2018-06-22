<template>
    <div>
        <div class="row demo">
            <p class="col-xs-6">
                示例1：<br>
                日期 姓名 单号 电话 价格<br>
                9.15 小明 01 1340 100<br>
                9.15 小里 02 1341 100<br>
                9.15 小亮 03 135 100
            </p>
            <p class="col-xs-6">
                示例2:<br>
                日期+姓名+单号+电话+价格/<br>
                9.15+小明+01+1340+100/<br>
                9.15+小里+02+1341+100/<br>
                9.15+小亮+03+135+100<br>
            </p>
        </div>
        <div class="table-responsive">
            <table id="my-table" class="table table-striped table-bordered table-hover">
                <thead>
                <tr v-for="(row,index) in table" v-if="index===0">
                    <th v-for="col in row">{{col}}</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(row,index) in table" v-if="index!==0">
                    <td v-for="col in row">{{col}}</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="row">
            <textarea class="form-control" cols="20" rows="8" v-model="content"></textarea>
        </div>
        <div class="row">
            <button class="btn btn-primary" @click="preview">预览</button>
            <a id="download-btn" class="btn btn-primary" @click="download">下载</a>
        </div>
        <hr>
        <div class="row hidden-md hidden-lg hidden-sm">
            <a :href="page.mobile_ad.one_key_excel.url">
                <img :src="page.mobile_ad.one_key_excel.img_src" style="width: 100%;max-height: 100px">
            </a>
        </div>
    </div>
</template>

<script>
    export default {
        name: "OneKeyExcel",
        data: function () {
            return {
                content: '',
                table: []
            }
        },
        computed: {
            page: function () {
                return this.$store.state.page;
            },
        },
        methods: {
            parse: function (str) {
                this.table = [];
                str = str.replace(/\//g, "\n");
                str = str.replace(/\+/g, ' ');
                let rows = str.split(/\n+/);
                for (let i in rows) {
                    let rowData = rows[i].split(/\s+/);
                    this.table.push(rowData);
                }
            },
            preview: function () {
                this.parse(this.content);
            },
            download: function () {
                this.parse(this.content);
                // let uri = 'data:application/vnd.ms-excel;base64,';
                // let template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><?xml version="1.0" encoding="UTF-8" standalone="yes"?><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table style="vnd.ms-excel.numberformat:@">{table}</table></body></html>';
                // let base64 = function (s) {
                //     return window.btoa(unescape(encodeURIComponent(s)));
                // };
                // let format = function (s, c) {
                //     return s.replace(/{(\w+)}/g, function (m, p) {
                //         return c[p];
                //     });
                // };
                // let ctx = {
                //     worksheet: '',
                //     table: $('#my-table').html()
                // };
                // $('#download-btn')[0].href = uri + base64(format(template, ctx));
                // $('#download-btn')[0].download = 'temp.xls';
                axios.post(api.oneKeyExcel, {data: this.table}).then(function (res) {
                    location.href = res.data.data;
                });
            }
        }
    }
</script>

<style scoped>
    @media (max-width: 768px) {
        .demo > p {
            padding: 0;
            font-size: 12px;
        }

        textarea {
            font-size: 14px;
        }
    }
</style>