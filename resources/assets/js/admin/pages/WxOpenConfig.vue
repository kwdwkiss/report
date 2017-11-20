<template>
    <div>
        <div class="panel">
            <div class="panel-heading">开放平台设置</div>
            <div class="panel-body">
                <el-form ref="form" :model="wxOpen" label-width="200px">
                    <el-form-item label="AppID">
                        <template>
                            <el-input v-model="wxOpen.appid" :readonly="true"></el-input>
                            <el-button @click="dialogOpen('AppID')">修改</el-button>
                        </template>
                    </el-form-item>
                    <el-form-item label="AppSecret">
                        <el-input v-model="wxOpen.secret" :readonly="true"></el-input>
                        <el-button @click="dialogOpen('AppSecret')">修改</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>
        <div class="panel">
            <div class="panel-heading">授权流程相关</div>
            <div class="panel-body">
                <el-form ref="form" :model="configInfo" label-width="200px">
                    <el-form-item label="授权发起页域名">
                        <el-input id="authDomainInput" v-model="configInfo.authStartPageDomain" :readonly="true"></el-input>
                        <el-button class="btn-copy" data-clipboard-target="#authDomainInput">复制</el-button>
                    </el-form-item>
                    <el-form-item label="授权事件接收URL">
                        <el-input id="authUrlInput" v-model="configInfo.authEventAcceptUrl" :readonly="true"></el-input>
                        <el-button class="btn-copy" data-clipboard-target="#authUrlInput">复制</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>
        <div class="panel">
            <div class="panel-heading">授权后代替公众号实现业务</div>
            <div class="panel-body">
                <el-form ref="form" :model="configInfo" label-width="200px">
                    <el-form-item label="公众号消息校验Token">
                        <el-input id="tokenInput" v-model="wxOpen.token" :readonly="true"></el-input>
                        <el-button class="btn-copy" data-clipboard-target="#tokenInput">复制</el-button>
                        <el-button @click="dialogOpen('Token')">修改</el-button>
                        <el-button @click="generateToken">生成新的</el-button>
                    </el-form-item>
                    <el-form-item label="公众号消息加解密Key">
                        <el-input id="aeskeyInput" v-model="wxOpen.aeskey" :readonly="true"></el-input>
                        <el-button class="btn-copy" data-clipboard-target="#aeskeyInput">复制</el-button>
                        <el-button @click="dialogOpen('AESKey')">修改</el-button>
                        <el-button @click="generateAesKey">生成新的</el-button>
                    </el-form-item>
                    <el-form-item label="公众号消息与事件接收URL">
                        <el-input id="wxAccUrlInput" v-model="configInfo.wxAccMessageEventAcceptUrl" :readonly="true"></el-input>
                        <el-button class="btn-copy" data-clipboard-target="#wxAccUrlInput">复制</el-button>
                    </el-form-item>
                    <el-form-item label="公众号开发域名">
                        <el-input id="wxAccDomainInput" v-model="configInfo.wxAccDevelopDomain" :readonly="true"></el-input>
                        <el-button class="btn-copy" data-clipboard-target="#wxAccDomainInput">复制</el-button>
                    </el-form-item>
                    <el-form-item label="小程序服务器域名">
                        <el-input id="wxAppDomainInput" v-model="configInfo.wxAppServerDomain" :readonly="true"></el-input>
                        <el-button class="btn-copy" data-clipboard-target="#wxAppDomainInput">复制</el-button>
                    </el-form-item>
                    <el-form-item label="白名单IP地址">
                        <el-input id="whiteListIpInput" v-model="configInfo.whiteListIp" :readonly="true"></el-input>
                        <el-button class="btn-copy" data-clipboard-target="#whiteListIpInput">复制</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>
        <el-dialog :title="cateCreateDialog.title" :visible.sync="cateCreateDialog.display">
            <el-input v-model="cateCreateDialog.data" :placeholder="cateCreateDialog.placeholder"></el-input>
            <div slot="footer" class="dialog-footer">
                <el-button @click="cateCreateDialog.display = false">取 消</el-button>
                <el-button type="primary" @click="dialogConfirm">确 定</el-button>
            </div>
        </el-dialog>
    </div>
</template>
<script>
    import Clipboard from 'clipboard';

    export default {
        data() {
            return {
                cateCreateDialog: {
                    target: '',
                    display: false,
                    title: '',
                    placeholder: '',
                    data: '',
                },
                wxOpen: {
                    appid: '',
                    secret: '',
                    token: '',
                    asekey: ''
                },
                configInfo: {
                    authStartPageDomain: '',
                    authEventAcceptUrl: '',
                    wxAccMessageEventAcceptUrl: '',
                    wxAccDevelopDomain: '',
                    wxAppServerDomain: '',
                    whiteListIp: ''
                }
            };
        },
        created: function () {
            this.loadData();
        },
        mounted: function () {
            let clipboard = new Clipboard('.btn-copy');
            clipboard.on('success', function (e) {
                console.info('Action:', e.action);
                console.info('Text:', e.text);
                console.info('Trigger:', e.trigger);
                e.clearSelection();
            });
        },
        methods: {
            loadData: function () {
                let self = this;
                axios.get(api.wxOpenConfig).then(function (res) {
                    if (res.data.code === 0) {
                        self.wxOpen = res.data.data.wxOpen;
                        self.configInfo = res.data.data.configInfo;
                    }
                });
            },
            generateToken: function () {
                let self = this;
                if (confirm('确定要生成新的吗？')) {
                    axios.post(api.wxOpenGenerateToken).then(function (res) {
                        if (res.data.code === 0) {
                            self.$message.success('生成成功');
                            self.loadData();
                        }
                    })
                }
            },
            generateAesKey: function () {
                let self = this;
                if (confirm('确定要生成新的吗？')) {
                    axios.post(api.wxOpenGenerateAeskey).then(function (res) {
                        if (res.data.code === 0) {
                            self.$message.success('生成成功');
                            self.loadData();
                        }
                    })
                }
            },
            dialogOpen: function (target) {
                this.cateCreateDialog.target = target;
                this.cateCreateDialog.title = '修改' + target;
                this.cateCreateDialog.placeholder = '请填写新的' + target;
                this.cateCreateDialog.data = '';
                this.cateCreateDialog.display = true;
            },
            dialogConfirm: function () {
                switch (this.cateCreateDialog.target) {
                    case 'AppID':
                        this.wxOpen.appid = this.cateCreateDialog.data;
                        break;
                    case 'AppSecret':
                        this.wxOpen.secret = this.cateCreateDialog.data;
                        break;
                    case 'Token':
                        this.wxOpen.token = this.cateCreateDialog.data;
                        break;
                    case 'AESKey':
                        this.wxOpen.aeskey = this.cateCreateDialog.data;
                        break;
                }
                let self = this;
                axios.post(api.wxOpenConfig, self.wxOpen).then(function (res) {
                    if (res.data.code === 0) {
                        self.$message.success('修改' + self.cateCreateDialog.target + '成功');
                    }
                    self.cateCreateDialog.display = false;
                });
            }
        }
    }
</script>
<style scoped>
    .el-form .el-input {
        width: 500px
    }
</style>