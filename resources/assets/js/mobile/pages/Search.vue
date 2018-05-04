<template>
    <div>
        <mt-header title="查询结果">
            <router-link to="/" slot="left">
                <mt-button icon="back" @click="goBack">返回</mt-button>
            </router-link>
        </mt-header>

        <div class="row" v-for="(item,index) in searchResult" :key="index" @click="detail(item)">
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
        name: "Search",
        computed: {
            searchResult: function () {
                return this.$store.state.searchResult;
            }
        },
        created: function () {
            this.search();
        },
        watch: {
            '$route'(to, from) {
                this.search();
            }
        },
        methods: {
            search: function () {
                let self = this;
                let name = this.$route.params.name;
                this.$store.commit('searchResult', {
                    name: name, callback: function () {
                        self.$store.commit('user');
                    }
                });
            },
            goBack: function () {
                this.$router.go(-1);
            },
            detail: function (item) {

            }
        }
    }
</script>

<style scoped>

</style>