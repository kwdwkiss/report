<template>
    <ol class="breadcrumb" v-if="!hide">
        <template v-for="(item,index) in list">
            <li v-if="item.url" :key="index">
                <a :href="item.url">{{item.name}}</a>
            </li>
            <li v-else-if="item.articleList" class="active">
                <a :href="articleListUrl">{{articleListName}}</a>
            </li>
            <li v-else class="active">
                {{item.name}}
            </li>
        </template>
    </ol>
</template>

<script>
    export default {
        name: "Breadcrumb",
        data: function () {
            return {
                articleListUrl: '',
                articleListName: '',
            };
        },
        watch: {
            breadcrumb: function (val) {
                this.articleListUrl = val.articleType;
                this.articleListName = val.articleTypeLabel;
            }
        },
        computed: {
            breadcrumb: function () {
                return this.$store.state.breadcrumb;
            },
            map: function () {
                return {
                    'index': [
                        {name: '首页'}
                    ],
                    'login': [
                        {name: '首页', url: '/#/'},
                        {name: '登录'},
                    ],
                    'register': [
                        {name: '首页', url: '/#/'},
                        {name: '注册'},
                    ],
                    'forgetPassword': [
                        {name: '首页', url: '/#/'},
                        {name: '登录', url: '/#/login'},
                        {name: '忘记密码'},
                    ],
                    'articleList': [
                        {name: '首页', url: '/#/'},
                        {name: '文章列表'},
                    ],
                    'articleDetail': [
                        {name: '首页', url: '/#/'},
                        {name: '文章详情'},
                    ],
                    'searchResult': [
                        {name: '首页', url: '/#/'},
                        {name: '查询结果'},
                    ],
                }
            },
            list: function () {
                return this.map[this.$route.name];
            },
            hide: function () {
                return this.list.length <= 1
                    || this.$route.name === 'index';
            },
        },
    }
</script>

<style scoped>

</style>