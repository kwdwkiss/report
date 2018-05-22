<template>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">积分记录</div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-responsive table-striped">
                        <thead>
                        <tr>
                            <th class="hidden-xs" style="min-width: 130px">积分流水</th>
                            <th style="min-width: 75px">收支类型</th>
                            <th style="min-width: 75px">积分</th>
                            <!--<th class="hidden-xs" style="min-width: 75px">用户积分</th>-->
                            <th class="hidden-xs" style="min-width: 75px">业务类型</th>
                            <th class="hidden-xs" style="min-width: 200px">内容</th>
                            <th style="min-width: 170px">创建时间</th>
                            <th style="min-width: 75px">详情</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, index) in amount.data" :key="index">
                            <td class="hidden-xs">{{item.bill_no}}</td>
                            <td>{{item.type_label}}</td>
                            <td class="hidden-xs">{{item.amount}}</td>
                            <!--<td>{{item.user_amount}}</td>-->
                            <td class="hidden-xs">{{item.biz_type_label}}</td>
                            <td>{{item.description}}</td>
                            <td>{{item.created_at}}</td>
                            <td><a href="javascript:" @click="detail(item)">查看</a></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <el-pagination class="pull-right" layout="prev, pager, next"
                               :total="amount.meta.total"
                               :page-size="amount.meta.per_page"
                               @current-change="paginate"></el-pagination>
            </div>
        </div>

        <div class="modal fade amount-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title">积分详情</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" role="form">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">积分流水</label>
                                <div class="col-sm-9 form-control-static">
                                    {{item.bill_no}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">收支类型</label>
                                <div class="col-sm-9 form-control-static">
                                    {{item.type_label}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">积分</label>
                                <div class="col-sm-9 form-control-static">
                                    {{item.amount}}
                                </div>
                            </div>
                            <!--<div class="form-group">-->
                                <!--<label class="col-sm-3 control-label">用户积分</label>-->
                                <!--<div class="col-sm-9 form-control-static">-->
                                    <!--{{item.user_amount}}-->
                                <!--</div>-->
                            <!--</div>-->
                            <div class="form-group">
                                <label class="col-sm-3 control-label">业务类型</label>
                                <div class="col-sm-9 form-control-static">
                                    {{item.biz_type_label}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">内容</label>
                                <div class="col-sm-9 form-control-static">
                                    {{item.description}}
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
        name: "AmountList",
        data: function () {
            return {
                item: {}
            }
        },
        computed: {
            amount: function () {
                return this.$store.state.amount;
            }
        },
        created: function () {
            this.list();
        },
        methods: {
            list: function () {
                this.$store.commit("amount");
            },
            paginate: function (page) {
                this.$store.commit("amount", {page: page});
            },
            detail: function (item) {
                this.item = item;
                $('.amount-dialog').modal('show');
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