<template>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">我的认证</div>
            <div class="panel-body">
                <div class="row text-success" v-if="user.is_auth">
                    <div class="col-md-6">
                        <div class="col-xs-4">认证类型：</div>
                        <div class="col-xs-8">{{user.auth_type_label}}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-xs-4">认证时长：</div>
                        <div class="col-xs-8">{{user.auth_duration_label}}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-xs-4">开始时间：</div>
                        <div class="col-xs-8">{{user.auth_start_at}}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-xs-4">结束时间：</div>
                        <div class="col-xs-8">{{user.auth_end_at}}</div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-responsive table-striped">
                        <thead>
                        <tr>
                            <th style="width: 60px">ID</th>
                            <th style="width: 80px">状态</th>
                            <th style="width: 80px">支付积分</th>
                            <th style="width: 80px">认证类型</th>
                            <th style="width: 80px">时长</th>
                            <th style="width: 160px">支付时间</th>
                            <th style="width: 160px">创建时间</th>
                            <th style="width: 100px">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, index) in dataList.data" :key="index">
                            <td>{{item.id}}</td>
                            <td>{{item.status_label}}</td>
                            <td>{{item.amount}}</td>
                            <td>{{item.type_label}}</td>
                            <td>{{item.duration_label}}</td>
                            <td>{{item.pay_at}}</td>
                            <td>{{item.created_at}}</td>
                            <td>
                                <button v-if="item.status===0"
                                        class="btn btn-primary" @click="payConfirm(item)">支付
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <el-pagination class="pull-right" layout="prev, pager, next"
                               :total="dataList.meta.total"
                               :page-size="dataList.meta.per_page"
                               @current-change="paginate"></el-pagination>
            </div>
        </div>

        <div class="modal fade" id="pay-dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title">确认支付</h4>
                    </div>
                    <div class="modal-body">
                        <p class="text-primary bold" style="font-size: 16px">支付积分：{{item.amount}}</p>
                        <div class="row">
                            <div class="col-xs-12">
                                <button class="btn btn-primary center-block" @click="doPay(item.id)">支付</button>
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
                dataList: {meta: {}},
            }
        },
        computed: {
            user: function () {
                return this.$store.state.user;
            },
        },
        created: function () {
            this.list();
        },
        methods: {
            list: function (payload) {
                let self = this;
                axios.get(api.userAuthBillIndex, {params: payload}).then(function (res) {
                    self.dataList = res.data;
                });
            },
            paginate: function (page) {
                this.list({page: page});
            },
            detail: function (item) {
                this.item = item;
                $('.recharge-dialog').modal('show');
            },
            payConfirm: function (item) {
                this.item = item;
                $('#pay-dialog').modal('show');
            },
            doPay: function (id) {
                let self = this;
                axios.post(api.userAuthBillPay, {id: id}).then(function (res) {
                    $('#pay-dialog').modal('hide');
                    self.$message.success('支付成功');
                    self.list();
                    self.$store.commit('user');
                })
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