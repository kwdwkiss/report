<template>
    <div>
        <div class="panel">
            <div class="panel-heading">授权</div>
            <div class="panel-body">
                <el-button type="primary" @click="authorize">开始授权</el-button>
            </div>
        </div>
        <div class="panel">
            <div class="panel-heading">已授权公众号</div>
            <div class="panel-body">
                <el-table ref="wxAccTable" :data="wxAccList.data" stripe>
                    <el-table-column label="头像" width="100">
                        <template slot-scope="scope">
                            <img :src="scope.row._wx_acc.head_img" alt="" width="70">
                        </template>
                    </el-table-column>
                    <el-table-column prop="_wx_acc.nick_name" label="名称" width="150"></el-table-column>
                    <el-table-column prop="_wx_acc.service_type_label" label="账号类型" width="100"></el-table-column>
                    <el-table-column prop="_wx_acc.verify_type_label" label="认证" width="100"></el-table-column>
                    <el-table-column prop="_wx_acc.principal_name" label="主体名称"></el-table-column>
                </el-table>
            </div>
        </div>
        <div class="panel">
            <div class="panel-heading">已授权小程序</div>
            <div class="panel-body">
                <el-table ref="wxAppTable" :data="wxAppList.data" stripe>
                    <el-table-column label="头像" width="100">
                        <template slot-scope="scope">
                            <img :src="scope.row._wx_app.head_img" alt="" width="70">
                        </template>
                    </el-table-column>
                    <el-table-column prop="_wx_app.nick_name" label="名称" width="150"></el-table-column>
                    <el-table-column prop="_wx_app.service_type_label" label="账号类型" width="100"></el-table-column>
                    <el-table-column prop="_wx_app.verify_type_label" label="认证" width="100"></el-table-column>
                    <el-table-column prop="_wx_app.principal_name" label="主体名称"></el-table-column>
                    <el-table-column label="操作">
                        <template slot-scope="scope">
                            <el-button type="primary" @click="wxAppConfig(scope.row)">配置</el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                wxAccList: {
                    data: [],
                },
                wxAppList: {
                    data: []
                },
                multipleSelection: []
            }
        },
        created: function () {
            let self = this;
            axios.get(api.wxOpenAuthList, {params: {type: 1}}).then(function (res) {
                if (res.data.code === 0) {
                    self.wxAccList = res.data;
                }
            });
            axios.get(api.wxOpenAuthList, {params: {type: 2}}).then(function (res) {
                if (res.data.code === 0) {
                    self.wxAppList = res.data;
                }
            })
        },
        methods: {
            authorize: function () {
                axios.get(api.wxOpenAuthorize, {params: {redirectUrl: location.href}}).then(function (res) {
                    if (res.data.code === 0) {
                        location.href = res.data.data;
                    }
                })
            },
            wxAppConfig: function (wxApp) {
                app.$router.push({path: `/wx-app/${wxApp.appid}/config`});
            }
        }
    }
</script>
<style scoped>

</style>