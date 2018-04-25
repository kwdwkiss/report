<template>
    <div>
        <div @keyup.enter="doSearch">
            <mt-search v-model="searchParams.name" style="height: 100%"></mt-search>
        </div>
        <mt-cell title="网站会员" :value="member"></mt-cell>
        <mt-cell title="举报总数" :value="reportTotal"></mt-cell>
        <mt-cell title="最近24小时举报" :value="lastReportNum"></mt-cell>
        <mt-cell title="最新举报"></mt-cell>
        <template v-for="(item,index) in lastReport">
            <mt-cell :title="item.account_name" :value="item.created_at" is-link :key="index"></mt-cell>
        </template>
    </div>
</template>

<script>
    export default {
        name: "Index",
        data: function () {
            return {
                searchParams: {
                    name: ''
                },
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
                this.$store.commit('searchResult', this.searchParams);
                this.$router.push('searchResult');
            }
        }
    }
</script>

<style scoped>

</style>