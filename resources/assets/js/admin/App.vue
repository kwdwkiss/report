<template>
    <div>
        <div class="header-wrapper">
            <app-header></app-header>
        </div>
        <div class="body-wrapper">
            <aside class="main-sidebar">
                <app-sidebar></app-sidebar>
            </aside>
            <div class="content-wrapper">
                <router-view></router-view>
            </div>
        </div>
    </div>
</template>
<script>
    Vue.component('app-header', require('./components/Header.vue'));
    Vue.component('app-sidebar', require('./components/Sidebar.vue'));
</script>
<style>
    .header-wrapper {
        width: 100%;
    }

    .body-wrapper {
        position: absolute;
        width: 100%;
        top: 60px;
        bottom: 0;
    }

    .main-sidebar {
        height: 100%;
        width: 180px;
    }

    .content-wrapper {
        position: absolute;
        top: 0;
        left: 180px;
        bottom: 0;
        right: 0;
        padding: 10px;
        overflow: hidden;
    }

    .el-pagination {
        float: right;
    }

    .el-row {
        margin: 10px 0;
    }
</style>