<template>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">充值记录</div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-responsive table-striped">
                        <thead>
                        <tr>
                            <th style="width: 130px">系统订单号</th>
                            <th style="width: 90px">支付类型</th>
                            <th style="width: 260px">外部订单号</th>
                            <th style="width: 100px">金额（元）</th>
                            <th style="width: 70px">状态</th>
                            <th style="width: 180px">创建时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, index) in recharge.data" :key="index">
                            <td>{{item.bill_no}}</td>
                            <td>{{item.pay_type_label}}</td>
                            <td>{{item.pay_no}}</td>
                            <td>{{item.money}}</td>
                            <td>{{item.status_label}}</td>
                            <td>{{item.created_at}}</td>
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
    </div>
</template>

<script>
    export default {
        name: "RechargeList",
        data: function () {
            return {
                rechargeForm: {mount: ""},
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