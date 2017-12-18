<template>
    <div class="row search-data">
        <div>
            <p class="col-xs-12" v-if="searchResult.type===1" style="color: blue">
                无{{$store.state.searchResult.name}}账号信息，如果确认是恶意号码，请到下方添加！</p>
            <table class="table table-striped table-hover" v-if="searchResult.type===2">
                <thead>
                <tr>
                    <th>账号类型</th>
                    <th>账号</th>
                    <th>举报类型</th>
                    <th>举报者IP</th>
                    <th>举报时间</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in searchResult.account_reports" style="color: red">
                    <td>{{item.account_type_label}}</td>
                    <td>{{item.account_name}}</td>
                    <td>{{item.type_label}}</td>
                    <td>{{item.ip}}</td>
                    <td>{{item.created_at}}</td>
                </tr>
                </tbody>
            </table>
            <div v-if="searchResult.type===3" style="color: green">
                <p class="col-xs-6">账号:{{searchResult.account.name}}</p>
                <p class="col-xs-6" style="color:blue">认证:{{searchResult.account.status_label}}</p>
                <p class="col-xs-6">认证时间:{{searchResult.account.created_at}}</p>
                <p class="col-xs-6">建议合作金额:{{searchResult.account.auth_cash}}</p>
                <p class="col-xs-6">常用地址:{{searchResult.account.address}}</p>
                <p class="col-xs-6">备注:{{searchResult.account.remark}}</p>
                <p class="col-xs-6">如发现此账号有恶意行为，请用户立即联系网站客服处理</p>
            </div>
            <div v-if="searchResult.type===4" style="color: red">
                <p class="col-xs-12">{{searchResult.name}}已被多数用户举报为恶意号码，请用户谨慎合作</p>
                <p class="col-xs-6">备注:{{searchResult.account.remark}}</p>
                <h1 class="col-xs-6">危险！</h1>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "search",
        computed: {
            searchResult: function () {
                return this.$store.state.searchResult
            }
        }
    }
</script>

<style scoped>
    .search-data {
        font-size: 16px;
        font-weight: 600;
    }

    .search-data p {
        margin: 5px 0;
    }
</style>