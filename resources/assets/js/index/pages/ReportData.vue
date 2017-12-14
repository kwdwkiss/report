<template>
    <div class="row report-data">
        <div>
            <table class="table table-striped table-hover">
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
                <tr v-for="item in $store.state.page.last_4_report_data">
                    <td>{{item.account_type_label}}</td>
                    <td>{{item.account_name}}</td>
                    <td>{{item.type_label}}</td>
                    <td>{{item.ip}}</td>
                    <td>{{item.created_at}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        name: "report-data"
    }
</script>

<style scoped>

</style>