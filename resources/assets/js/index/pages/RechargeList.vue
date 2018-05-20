<template>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">充值记录</div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-responsive table-striped">
                        <thead>
                        <tr>
                            <th class="hidden-xs" style="width: 130px">系统订单号</th>
                            <th class="hidden-xs" style="width: 90px">支付类型</th>
                            <th class="hidden-xs" style="width: 260px">外部订单号</th>
                            <th style="width: 70px">金额</th>
                            <th style="width: 70px">状态</th>
                            <th style="width: 180px">创建时间</th>
                            <th style="width: 70px"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, index) in recharge.data" :key="index">
                            <td class="hidden-xs">{{item.bill_no}}</td>
                            <td class="hidden-xs">{{item.pay_type_label}}</td>
                            <td class="hidden-xs">{{item.pay_no}}</td>
                            <td>{{item.money}}</td>
                            <td>{{item.status_label}}</td>
                            <td>{{item.created_at}}</td>
                            <td><a href="javascript:" @click="detail(item)">详情</a></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <el-pagination class="pull-right" layout="prev, pager, next"
                               :total="recharge.meta.total"
                               :page-size="recharge.meta.per_page"
                               @current-change="rechargePaginate"></el-pagination>
            </div>
        </div>

        <div class="modal fade recharge-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title">充值详情</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" role="form">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">系统订单号</label>
                                <div class="col-sm-9 form-control-static">
                                    {{item.bill_no}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">支付类型</label>
                                <div class="col-sm-9 form-control-static">
                                    {{item.pay_type_label}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">外部订单号</label>
                                <div class="col-sm-9 form-control-static">
                                    {{item.pay_no}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">金额（元）</label>
                                <div class="col-sm-9 form-control-static">
                                    {{item.money}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">状态</label>
                                <div class="col-sm-9 form-control-static">
                                    {{item.status_label}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">创建时间</label>
                                <div class="col-sm-9 form-control-static">
                                    {{item.created_at}}
                                </div>
                            </div>
                        </form>
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
                rechargeForm: {mount: ""},
                item: {}
            }
        },
        computed: {
            recharge: function () {
                return this.$store.state.recharge;
            }
        },
        created: function () {
            this.list();
        },
        methods: {
            list: function () {
                this.$store.commit("recharge");
            },
            paginate: function (page) {
                this.$store.commit("recharge", {page: page});
            },
            detail: function (item) {
                this.item = item;
                $('.recharge-dialog').modal('show');
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