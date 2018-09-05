<template>
    <div>
        <div class="row demo">
            <p class="col-xs-6">
                示例:<br>
                日期+姓名+单号+电话+价格/<br>
                9.15+小明+01+1340+100/<br>
                9.15+小里+02+1341+100/<br>
                9.15+小亮+03+135+100<br>
            </p>
            <p class="col-xs-6 text-success" style="font-size: 18px">
                微信客服：<span>SYX0518ZA</span><br>
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
            <button class="btn btn-warning" @click="preview">预览</button>
            <a id="download-btn" class="btn btn-primary" @click="download">下载</a>
            <button class="btn btn-success" @click="save">保存</button>
        </div>
        <hr>
        <div class="row hidden-md hidden-lg hidden-sm">
            <a :href="page.mobile_ad.one_key_excel.url">
                <img :src="page.mobile_ad.one_key_excel.img_src" style="width: 100%;max-height: 100px">
            </a>
        </div>

        <div class="modal fade" id="excel-create-dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title">保存表格</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" role="form">
                            <p class="text-success bold" style="font-size: 16px">保存后可到 个人中心->我的表格 查看</p>
                            <p class="text-danger bold" style="font-size: 16px">每个用户最多保存100个表格</p>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">保存名称</label>
                                <div class="col-sm-9">
                                    <input v-model="title" name="name" type="text" class="form-control"
                                           placeholder="保存EXCEL的名称">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <button type="button" class="btn btn-primary" @click="doSave">保存</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!--<div class="modal fade" id="msg-dialog">-->
            <!--<div class="modal-dialog">-->
                <!--<div class="modal-content">-->
                    <!--<div class="modal-header">-->
                        <!--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">-->
                            <!--&times;-->
                        <!--</button>-->
                        <!--<h4 class="modal-title">通知</h4>-->
                    <!--</div>-->
                    <!--<div class="modal-body">-->
                        <!--<p style="font-size: 16px;color: red">9月1日起，EXCEL表格功能需要登录才能使用！</p>-->
                    <!--</div>-->
                <!--</div>-->
            <!--</div>-->
        <!--</div>-->
    </div>
</template>

<script>
    export default {
        name: "OneKeyExcel",
        data: function () {
            return {
                content: '',
                table: [],
                title: '',
            }
        },
        mounted: function () {
            // $('#msg-dialog').modal('show');
        },
        computed: {
            page: function () {
                return this.$store.state.page;
            },
            user: function () {
                return this.$store.state.user;
            }
        },
        methods: {
            parse: function (str) {
                this.table = [];
                str = str.replace(/\//g, "\n");
                str = str.replace(/＋/g, '+');
                //str = str.replace(/\+/g, ' ');
                let rows = str.split(/\n+/);
                for (let i in rows) {
                    if (rows[i] === '') {
                        continue;
                    }
                    let rowData = rows[i].split(/\+/);
                    this.table.push(rowData);
                }
            },
            preview: function () {
                this.parse(this.content);
            },
            download: function () {
                this.parse(this.content);
                if (this.content.length <= 1) {
                    this.$message.error('数据为空');
                    return;
                }
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
            },
            save: function () {
                if (!this.user) {
                    this.$message.error('请登录后再保存');
                    return;
                }
                this.parse(this.content);
                if (this.content.length <= 1) {
                    this.$message.error('数据为空');
                    return;
                }
                $('#excel-create-dialog').modal('show');
            },
            doSave: function () {
                if (this.title === '') {
                    this.$message.error('表格名称不能为空');
                    return;
                }
                let self = this;
                axios.post(api.userExcelCreate, {
                    title: this.title,
                    data: this.table
                }).then(function (res) {
                    $('#excel-create-dialog').modal('hide');
                    self.$message.success('保存成功');
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