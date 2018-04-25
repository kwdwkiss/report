<template>
    <div>
        <router-view>
        </router-view>
        <mt-tabbar v-model="selected" @input="tabbarInput">
            <mt-tab-item id="/">
                首页
            </mt-tab-item>
            <mt-tab-item id="/article">
                资讯
            </mt-tab-item>
            <mt-tab-item id="/user">
                我的
            </mt-tab-item>
        </mt-tabbar>
    </div>
</template>

<script>
    export default {
        name: "App",
        data: function () {
            return {
                search: '',
                selected: '首页',
            }
        },
        methods: {
            tabbarInput: function (id) {
                this.$router.push(id);
            }
        }
    }
</script>

<style scoped>

</style>