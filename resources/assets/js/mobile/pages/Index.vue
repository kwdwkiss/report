<template>
    <div>
        <div @keyup.enter="doSearch">
            <mt-search v-model="name" style="height: 100%"></mt-search>
        </div>
        <div class="row">
            <div class="col-xs-6">网站会员</div>
            <div class="col-xs-6">{{member}}</div>
        </div>
        <div class="row">
            <div class="col-xs-6">举报总数</div>
            <div class="col-xs-6">{{reportTotal}}</div>
        </div>
        <div class="row">
            <div class="col-xs-6">最近24小时举报</div>
            <div class="col-xs-6">{{lastReportNum}}</div>
        </div>
        <hr>
        <div class="row" v-for="(item,index) in lastReport" :key="index" @click="detail(item)">
            <div class="row">
                <span>{{item.account_type_label}}</span>
                <span class="pull-right">{{item.account_name}}</span>
            </div>
            <div class="row">
                <span>{{item.type_label}}</span>
                <span class="pull-right">{{item.created_at}}</span>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Index",
        data: function () {
            return {
                name: ''
            }
        },
        computed: {
            member: function () {
                return this.$store.state.page.auth_member_num;
            },
            reportTotal: function () {
                return this.$store.state.page.report_num;
            },
            lastReportNum: function () {
                return this.$store.state.page.last_24_report_num;
            },
            lastReport: function () {
                return this.$store.state.page.last_4_report_data;
            }
        },
        methods: {
            doSearch: function () {
                this.$router.push({name: 'search', params: {name: this.name}});
            },
            detail: function () {

            }
        }
    }
</script>

<style scoped>

</style>