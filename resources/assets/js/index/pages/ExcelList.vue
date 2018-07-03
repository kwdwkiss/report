<template>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">表格列表</div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-responsive table-striped">
                        <thead>
                        <tr>
                            <th style="width: 150px">名称</th>
                            <th style="width: 180px">创建时间</th>
                            <th style="width: 100px">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, index) in excel.data" :key="index">
                            <td>{{item.title}}</td>
                            <td>{{item.created_at}}</td>
                            <td>
                                <a class="btn btn-primary" @click="detail(item)">查看</a>
                                <a class="btn btn-danger" @click="deleteDialog(item)">删除</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <el-pagination class="pull-right" layout="prev, pager, next"
                               :total="excel.meta.total"
                               :page-size="excel.meta.per_page"
                               @current-change="paginate"></el-pagination>
            </div>
        </div>

        <div class="modal fade excel-detail-dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            &times;
                        </button>
                        <h4 class="modal-title">表格详情</h4>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
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
                        <button class="btn btn-primary" @click="download">下载</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="excel-delete-dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title">确认删除</h4>
                    </div>
                    <div class="modal-body">
                        <p class="text-danger bold" style="font-size: 16px">删除后不可恢复</p>
                        <div class="row">
                            <div class="col-xs-12">
                                <button class="btn btn-danger center-block" @click="doDelete">删除</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "RechargeList",
        data: function () {
            return {
                item: {},
                table: [],
            }
        },
        computed: {
            excel: function () {
                return this.$store.state.excel;
            }
        },
        created: function () {
            this.list();
        },
        methods: {
            list: function () {
                this.$store.commit("excel");
            },
            paginate: function (page) {
                this.$store.commit("excel", {page: page});
            },
            detail: function (item) {
                this.item = item;
                this.table = JSON.parse(item.body);
                $('.excel-detail-dialog').modal('show');
            },
            deleteDialog: function (item) {
                this.item = item;
                $('#excel-delete-dialog').modal('show');
            },
            doDelete: function () {
                let self = this;
                axios.post(api.userExcelDelete, {id: this.item.id}).then(function (res) {
                    $('#excel-delete-dialog').modal('hide');
                    self.$message.success('删除成功');
                    self.$store.commit("excel");
                });
            },
            download: function () {
                axios.post(api.oneKeyExcel, {data: this.table}).then(function (res) {
                    location.href = res.data.data;
                });
            }
        }
    }
</script>

<style scoped>
    @media (max-width: 768px) {
        .panel-body {
            padding: 15px 0px;
        }
    }
</style>