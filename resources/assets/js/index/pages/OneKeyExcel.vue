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
            <p class="col-xs-6" style="color: red">
                QQ交流群：<span>{{page.service_excel[0].name}}</span><br>
                1、下载1张表格，消耗10个积分<br>
                2、推荐好友注册，好友下载1张表格，本人可以获得4点积分<br>
                3、推荐方法：菜单->积分充值->推广赚积分
            </p>
        </div>
        <div class="row">
            <p class="col-xs-12 col-sm-6 text-primary" style="font-size: 16px">扣费类型：{{excelCostType}}</p>
            <p class="col-xs-12 col-sm-6 text-primary" style="font-size: 16px">包月更实惠（5元/月），
                <a href="javascript:" @click="goBuy">立即前往</a>
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

        <div class="modal fade" id="msg-dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title">通知</h4>
                    </div>
                    <div class="modal-body">
                        <p style="font-size: 16px;color: red">
                            由于智能表格系统用户使用量大增，导致服务器负载过高，网站需要升级服务器和网络带宽来保障用户体验，所以决定10月1日起，每下载一张表格，会扣除10点宏海积分。</p>
                    </div>
                </div>
            </div>
        </div>
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
                doing: false,
                excelCostType: '',
            }
        },
        mounted: function () {
            //$('#msg-dialog').modal('show');
            this.getExcelCostType();
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
                let self = this;
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

                if (self.doing) {
                    self.$message('下载中，请不要重复提交');
                    return;
                }
                self.doing = true;
                setTimeout(function () {
                    self.doing = false;
                }, 2000);

                axios.post(api.oneKeyExcel, {data: this.table}).then(function (res) {
                    self.$store.commit('user');
                    location.href = res.data.data;
                });
            },
            getExcelCostType: function () {
                let self = this;
                axios.get(api.indexExcelCostType).then(function (res) {
                    self.excelCostType = res.data.data;
                })
            },
            goBuy: function () {
                this.$router.push({name: 'userProduct', query: {action: 'buy'}})
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