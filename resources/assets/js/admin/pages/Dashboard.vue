<template>
    <div v-if="user.id===1">
        <div class="panel panel-default">
            <div class="panel-heading">新用户注册统计</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-2">
                        <p>今日：{{statement.userRegister.today}}</p>
                        <p>邀请：{{statement.userRegister.todayInviter}}</p>
                    </div>
                    <div class="col-md-2">
                        <p>昨日：{{statement.userRegister.yesterday}}</p>
                        <p>邀请：{{statement.userRegister.yesterdayInviter}}</p>
                    </div>
                    <div class="col-md-2">
                        本月：{{statement.userRegister.month}}
                    </div>
                    <div class="col-md-2">
                        上月：{{statement.userRegister.lastMonth}}
                    </div>
                    <div class="col-md-2">
                        总计：{{statement.userRegister.total}}
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">举报统计</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
                        今日：{{statement.accountReport.today}}
                    </div>
                    <div class="col-md-3">
                        昨日：{{statement.accountReport.yesterday}}
                    </div>
                    <div class="col-md-3">
                        本月：{{statement.accountReport.month}}
                    </div>
                    <div class="col-md-3">
                        上月：{{statement.accountReport.lastMonth}}
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">查询统计</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
                        今日：{{statement.accountSearch.today}}
                    </div>
                    <div class="col-md-3">
                        昨日：{{statement.accountSearch.yesterday}}
                    </div>
                    <div class="col-md-3">
                        本月：{{statement.accountSearch.month}}
                    </div>
                    <div class="col-md-3">
                        上月：{{statement.accountSearch.lastMonth}}
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">充值统计</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
                        <p>今日：{{statement.rechargeBill.today}}</p>
                        <p>首充：{{statement.rechargeBill.todayOnce}}</p>
                    </div>
                    <div class="col-md-3">
                        <p>昨日：{{statement.rechargeBill.yesterday}}</p>
                        <p>首充：{{statement.rechargeBill.yesterdayOnce}}</p>
                    </div>
                    <div class="col-md-3">
                        本月：{{statement.rechargeBill.month}}
                    </div>
                    <div class="col-md-3">
                        上月：{{statement.rechargeBill.lastMonth}}
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    export default {
        data: function () {
            return {
                statement: {
                    userRegister: {},
                    accountReport: {},
                    accountSearch: {},
                    rechargeBill: {}
                }
            }
        },
        computed: {
            user: function () {
                return this.$store.state.user;
            }
        },
        created: function () {
            this.getStatement();
        },
        methods: {
            getStatement: function () {
                let self = this;
                axios.get(api.adminDashboard).then(function (res) {
                    self.statement = _.assign(self.statement, res.data.data);
                });
            }
        }
    }
</script>

<style scoped>

</style>