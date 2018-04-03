<template>
    <el-menu :default-active="defaultActive" :router="true">
        <template v-for="(item, index) in menu">
            <el-submenu :index="item.path" v-if="item.children">
                <template slot="title">
                    <i :class="item.icon" v-if="item.icon"></i>{{item.title}}
                </template>
                <template v-for="(subItem, subIndex) in item.children">
                    <el-menu-item :index="subItem.path" :key="subIndex">
                        <i :class="subItem.icon" v-if="subItem.icon"></i>{{subItem.title}}
                    </el-menu-item>
                </template>
            </el-submenu>
            <el-menu-item :index="item.path" v-if="!item.children">
                <i :class="item.icon" v-if="item.icon"></i>{{item.title}}
            </el-menu-item>
        </template>
    </el-menu>
</template>

<script>
    export default {
        data: function () {
            return {
                defaultActive: '/',
                menu: [
                    {
                        title: '控制面板',
                        path: '/',
                    },
                    {
                        title: '分类',
                        path: '/taxonomy',
                    },
                    {
                        title: '标签',
                        path: '/tag',
                    },
                    {
                        title: '文章管理',
                        path: '/article',
                    },
                    {
                        title: '用户资料',
                        path: '/user',
                    },
                    {
                        title: '账号信息',
                        path: '/account',
                    },
                    {
                        title: '举报信息',
                        path: '/account/report',
                    },
                    {
                        title: '首页设置',
                        path: '/site/index-page',
                    },
                    {
                        title: '弹窗设置',
                        path: '/site/pop-window',
                    },
                    {
                        title: '系统设置',
                        path: '/system',
                        children: [
                            {
                                title: '站点管理',
                                path: '/system/site'
                            },
                            {
                                title: '管理员',
                                path: '/system/admin'
                            },
                        ]
                    }
                ]
            }
        },
        mounted: function () {
            this.defaultActive = this.$route.path;
        },
        methods: {}
    }
</script>

<style scoped>
    .el-menu {
        height: 100%;
    }

    .el-menu-item {
        min-width: 160px;
    }
</style>