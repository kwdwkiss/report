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
                        <div class="col-xs-4">结束时间：</div>
                        <div class="col-xs-8">{{user.auth_end_at}}</div>
                    </div>
                </div>
                <div class="row">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#apply-dialog">申请认证</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-responsive table-striped">
                        <thead>
                        <tr>
                            <th style="width: 60px">ID</th>
                            <th style="width: 110px">内容</th>
                            <th style="width: 80px">时长</th>
                            <th style="width: 80px">积分</th>
                            <th style="width: 80px">状态</th>
                            <th style="width: 160px">审核时间</th>
                            <th style="width: 160px">创建时间</th>
                            <th style="width: 100px">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, index) in dataList.data" :key="index">
                            <td>{{item.id}}</td>
                            <td>{{item._product.title}}</td>
                            <td>{{item._product_bill.quantity}}{{item._product.type_label}}</td>
                            <td>{{item._product_bill.amount}}</td>
                            <td>{{item.status_label}}</td>
                            <td>{{item.check_at}}</td>
                            <td>{{item.created_at}}</td>
                            <td>
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

        <div class="modal fade" id="apply-dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title">申请认证</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" role="form">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">认证类型</label>
                                <div class="col-sm-10">
                                    <select class="form-control" v-model="product_id">
                                        <option :value="item.id" v-for="item in products">
                                            {{item.title}}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">产品名称</label>
                                <div class="col-sm-10">
                                    <label class="form-control-static">{{product.title}}</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">单价</label>
                                <div class="col-sm-10">
                                    <label class="form-control-static">{{product.amount}}</label>积分/{{product.type_label}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">时长</label>
                                <div class="col-sm-10">
                                    <select v-model="form.duration" class="form-control">
                                        <option :value="item" v-for="item in product.duration">
                                            {{item}}{{product.type_label}}
                                        </option>
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
                                    <button class="btn btn-primary" data-dismiss="modal" @click="doApply(product_id)">
                                        确认申请
                                    </button>
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
                product_id: 2,
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
            },
            product: function () {
                return this.$store.state.product;
            },
            products: function () {
                return this.$store.state.products;
            }
        },
        watch: {
            'product_id': function (to, from) {
                this.$store.commit('product', {id: to});
            }
        },
        created: function () {
            this.list();
            this.$store.commit('products', {group: 'user_auth'});
            this.$store.commit('product', {id: this.product_id});
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
            doApply: function (id) {
                let self = this;
                self.form.id = id;
                axios.post(api.userAuthBillApply, self.form).then(function (res) {
                    self.$message.success(res.data.message);
                    self.list();
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