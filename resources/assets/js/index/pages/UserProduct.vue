<template>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">我的产品</div>
            <div class="panel-body">

                <div class="row text-success">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#buy-dialog">购买</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-responsive table-striped">
                        <thead>
                        <tr>
                            <th style="width: 60px">ID</th>
                            <th style="width: 80px">产品</th>
                            <th style="width: 80px">状态</th>
                            <th style="width: 160px">开始时间</th>
                            <th style="width: 160px">结束时间</th>
                            <th style="width: 100px">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, index) in dataList.data" :key="index">
                            <td>{{item.id}}</td>
                            <td>{{item._product.title}}</td>
                            <td>{{item.status_label}}</td>
                            <td>{{item.start_at}}</td>
                            <td>{{item.end_at}}</td>
                            <td></td>
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

        <div class="modal fade" id="buy-dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title">购买服务</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" role="form">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">产品名称</label>
                                <div class="col-sm-10">
                                    <label class="form-control-static">{{product.title}}</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">单价</label>
                                <div class="col-sm-10">
                                    <label class="form-control-static">{{product.amount}}</label>积分/月
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">时长</label>
                                <div class="col-sm-10">
                                    <select v-model="form.duration" class="form-control">
                                        <option :value="item" v-for="item in product.duration">{{item}}个月</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">总价</label>
                                <div class="col-sm-10">
                                    <label class="form-control-static">{{amountTotal}}积分</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button class="btn btn-primary" @click="doBuy(product_id)">确认购买</button>
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
                product_id: 1,
                product: {duration: []},
                durations: [],
                dataList: {meta: {}},
                form: {duration: 1},
            }
        },
        computed: {
            user: function () {
                return this.$store.state.user;
            },
            amountTotal: function () {
                return this.form.duration * this.product.amount;
            }
        },
        created: function () {
            this.list();
            this.productDetail();
        },
        mounted: function () {
            let action = this.$route.query.action;
            if (action === 'buy') {
                $('#buy-dialog').modal('show');
            }
        },
        methods: {
            list: function (payload) {
                let self = this;
                axios.get(api.indexUserProductIndex, {params: payload}).then(function (res) {
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
            productDetail: function () {
                let self = this;
                axios.get(api.indexProductShow, {params: {id: self.product_id}}).then(function (res) {
                    self.product = res.data.data;
                })
            },
            doBuy: function (id) {
                let self = this;
                self.form.id = id;
                axios.post(api.indexUserProductCreate, self.form).then(function (res) {
                    self.$store.commit('user');
                    self.list();
                    $('#buy-dialog').modal('hide');
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