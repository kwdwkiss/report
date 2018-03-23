<template>
    <div>
        <div class="panel panel-default">
            <div class="panel-heading">新用户注册统计</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
                        今日：{{statement.userRegister.today}}
                    </div>
                    <div class="col-md-3">
                        昨日：{{statement.userRegister.yesterday}}
                    </div>
                    <div class="col-md-3">
                        本月：{{statement.userRegister.month}}
                    </div>
                    <div class="col-md-3">
                        上月：{{statement.userRegister.lastMonth}}
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">举报统计</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
                        今日：{{statement.report.today}}
                    </div>
                    <div class="col-md-3">
                        昨日：{{statement.report.yesterday}}
                    </div>
                    <div class="col-md-3">
                        本月：{{statement.report.month}}
                    </div>
                    <div class="col-md-3">
                        上月：{{statement.report.lastMonth}}
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">查询统计</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3">
                        今日：{{statement.search.today}}
                    </div>
                    <div class="col-md-3">
                        昨日：{{statement.search.yesterday}}
                    </div>
                    <div class="col-md-3">
                        本月：{{statement.search.month}}
                    </div>
                    <div class="col-md-3">
                        上月：{{statement.search.lastMonth}}
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
                    report: {},
                    search: {}
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