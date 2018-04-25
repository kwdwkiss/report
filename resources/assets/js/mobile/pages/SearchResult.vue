<template>
    <div>
        <mt-header title="查询结果">
            <router-link to="/" slot="left">
                <mt-button icon="back" @click="goBack">返回</mt-button>
                <mt-button @click="handleClose">关闭</mt-button>
            </router-link>
        </mt-header>
    </div>
</template>

<script>
    export default {
        name: "SearchResult",
        methods: {
            goBack: function () {
                this.router.go(-1);
            }
        }
    }
</script>

<style scoped>

</style>