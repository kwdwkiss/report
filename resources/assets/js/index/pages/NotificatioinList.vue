<template>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">通知列表</div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-responsive table-striped">
                        <thead>
                        <tr>
                            <th style="width: 60%">通知</th>
                            <th style="width: 10%">查阅</th>
                            <th style="width: 30%">时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, index) in notification.data" :key="index">
                            <td><a href="javascript:" @click="detail(item)">{{item.data.name}}</a></td>
                            <td>
                                <template v-if="item.read_at">
                                    已读
                                </template>
                                <template v-else>
                                    未读
                                </template>
                            </td>
                            <td>{{item.created_at}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <el-pagination layout="prev, pager, next"
                               :total="notification.meta.total"
                               :page-size="notification.meta.per_page"
                               @current-change="paginate"></el-pagination>
            </div>
        </div>

        <div class="modal fade notification-detail-dialog" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title">通知详情</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <h4 class="text-center">{{notificationItem.data.name}}</h4>
                        </div>
                        <div class="row">
                            {{notificationItem.data.content}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "NotificatioinList",
        data: function () {
            return {
                rechargeForm: {mount: ""},
                notificationItem: {data: {}}
            }
        },
        computed: {
            notification: function () {
                return this.$store.state.notification;
            },
        },
        created: function () {
            this.list();
        },
        methods: {
            list: function () {
                this.$store.commit("notification");
            },
            paginate: function (page) {
                this.$store.commit("notification", {page: page});
            },
            detail: function (item) {
                this.notificationItem = item;

                let self = this;
                axios.post(api.userReadNotification, item).then(function (res) {
                    self.$store.commit("notification");
                    self.$store.commit("unreadNotification");
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