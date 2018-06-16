<template>
    <div class="panel">
        <div class="panel-heading">微信-服务器设置</div>
        <div class="panel-body">
            <el-form ref="form" :model="dataList" label-width="150px">
                <el-form-item label="原始ID">
                    <el-input v-model="dataList.wechat_id"></el-input>
                </el-form-item>
                <el-form-item label="AppId">
                    <el-input v-model="dataList.app_id"></el-input>
                </el-form-item>
                <el-form-item label="AppSecret">
                    <el-input v-model="dataList.app_secret"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="submit">提交</el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Server",
        data: function () {
            return {
                dataList: {}
            }
        },
        created: function () {
            this.loadData();
        },
        methods: {
            loadData: function () {
                let self = this;
                axios.get(api.adminWechatGetServer).then(function (res) {
                    self.dataList = res.data.data;
                });
            },
            submit: function () {
                let self = this;
                axios.post(api.adminWechatSetServer, self.dataList).then(function () {
                    self.$message.success('成功');
                    self.loadData();
                });
            }
        }
    }
</script>

<style scoped>

</style>