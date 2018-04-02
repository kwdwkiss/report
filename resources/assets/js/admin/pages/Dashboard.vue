<template>
    <div>
        <div class="panel panel-default">
            <div class="panel-heading">新用户注册统计</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-2">
                        今日：{{statement.userRegister.today}}
                    </div>
                    <div class="col-md-2">
                        昨日：{{statement.userRegister.yesterday}}
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
    </div>

</template>

<script>
    export default {
        data: function () {
            return {
                statement: {
                    userRegister: {},
                    accountReport: {},
                    accountSearch: {}
                }
            }
        },
        created: function () {
            this.getStatement();
        },
        methods: {
            getStatement: function () {
                let self = this;
                axios.get(api.adminStatement).then(function (res) {
                    self.statement = _.assign(self.statement, res.data.data);
                });
            }
        }
    }
</script>

<style scoped>

</style>