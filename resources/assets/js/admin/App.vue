<template>
    <div>
        <my-nav></my-nav>

        <div class="container-fluid" style="padding-top:51px">

            <router-view></router-view>

        </div>
    </div>
</template>
<script>
    Vue.component('my-nav', require('./components/Nav.vue'));
</script>
<style>
    .el-pagination {
        float: right;
    }

    .el-row {
        margin: 10px 0;
    }
</style>