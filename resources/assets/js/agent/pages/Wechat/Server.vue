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
                <el-form-item label="Token">
                    <el-input v-model="dataList.token"></el-input>
                    <a class="btn btn-primary btn-copy" :data-clipboard-text="dataList.token">复制</a>
                    <a class="btn btn-primary" @click="refreshToken">刷新</a>
                </el-form-item>
                <el-form-item label="AESKey">
                    <el-input v-model="dataList.aes_key"></el-input>
                </el-form-item>
                <el-form-item label="服务器URL">
                    <el-input :disabled="true" v-model="dataList.url"></el-input>
                    <a class="btn btn-primary btn-copy" :data-clipboard-text="dataList.url">复制</a>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="submit">提交</el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>

<script>
    import Clipboard from 'clipboard';

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
        mounted: function () {
            let self = this;
            let clipboard = new Clipboard('.btn-copy');
            clipboard.on('success', function (e) {
                e.clearSelection();
                self.$message.success('复制成功');
            });
        },
        methods: {
            loadData: function () {
                let self = this;
                axios.get(api.agentWechatGetServer).then(function (res) {
                    self.dataList = res.data.data;
                });
            },
            submit: function () {
                let self = this;
                axios.post(api.agentWechatSetServer, self.dataList).then(function () {
                    self.$message.success('成功');
                    self.loadData();
                });
            },
            refreshToken: function () {
                let self = this;
                axios.post(api.agentWechatRefreshToken, self.dataList).then(function () {
                    self.$message.success('成功');
                    self.loadData();
                });
            }
        }
    }
</script>

<style scoped>

</style>