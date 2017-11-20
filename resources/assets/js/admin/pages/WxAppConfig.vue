<template>
    <div>
        <div class="panel">
            <div class="panel-heading">小程序-{{wxApp.nick_name}}-配置服务器信息</div>
            <div class="panel-body">
                <el-row>
                    <span>
                        服务器域名需经过ICP备案，新备案域名需24小时后才可配置。域名格式只支持英文大小写字母、数字及“- ”，不支持IP地址及端口号。
                    </span>
                </el-row>
                <el-row>
                    <el-form ref="form" :model="wxAppDomain" label-width="200px">
                        <el-form-item :label="index==0?'request合法域名':''"
                                      v-for="(item, index) in wxAppDomain.requestdomain"
                                      v-bind:key="index">
                            <el-input v-model="wxAppDomain.requestdomain[index]"></el-input>
                            <el-button v-if="index==0" @click="domainAdd('requestdomain')">添加</el-button>
                            <el-button v-if="index!=0" @click="domainDelete('requestdomain',index)">删除</el-button>
                        </el-form-item>
                        <el-form-item :label="index==0?'socket合法域名':''"
                                      v-for="(item, index) in wxAppDomain.wsrequestdomain"
                                      v-bind:key="index">
                            <el-input v-model="wxAppDomain.wsrequestdomain[index]"></el-input>
                            <el-button v-if="index==0" @click="domainAdd('wsrequestdomain')">添加</el-button>
                            <el-button v-if="index!=0" @click="domainDelete('wsrequestdomain',index)">删除</el-button>
                        </el-form-item>
                        <el-form-item :label="index==0?'uploadFile合法域名':''"
                                      v-for="(item, index) in wxAppDomain.uploaddomain"
                                      v-bind:key="index">
                            <el-input v-model="wxAppDomain.uploaddomain[index]"></el-input>
                            <el-button v-if="index==0" @click="domainAdd('uploaddomain')">添加</el-button>
                            <el-button v-if="index!=0" @click="domainDelete('uploaddomain',index)">删除</el-button>
                        </el-form-item>
                        <el-form-item :label="index==0?'downloadFile合法域名':''"
                                      v-for="(item, index) in wxAppDomain.downloaddomain"
                                      v-bind:key="index">
                            <el-input v-model="wxAppDomain.downloaddomain[index]"></el-input>
                            <el-button v-if="index==0" @click="domainAdd('downloaddomain')">添加</el-button>
                            <el-button v-if="index!=0" @click="domainDelete('downloaddomain',index)">删除</el-button>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="primary" @click="update">更新</el-button>
                        </el-form-item>
                    </el-form>
                </el-row>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data: function () {
            return {
                appid: '',
                wxApp: {},
                wxAppDomain: {
                    requestdomain: [],
                    wsrequestdomain: [],
                    uploaddomain: [],
                    downloaddomain: []
                }
            }
        },
        created: function () {
            this.appid = this.$route.params.appid;
            this.loadWxApp();
            this.loadWxAppDomain();
            setInterval(this.check, 100);
        },
        methods: {
            loadWxApp: function () {
                let self = this;
                axios.get(api.wxAppInfo, {params: {appid: self.appid}}).then(function (res) {
                    self.wxApp = res.data.data;
                });
            },
            loadWxAppDomain: function () {
                let self = this;
                axios.get(api.wxAppDomain, {params: {appid: self.appid}}).then(function (res) {
                    self.wxAppDomain = res.data.data;
                    window.wxAppDomain = self.wxAppDomain;
                });
            },
            check: function () {
                let self = this;
                for (let i in self.wxAppDomain) {
                    if (self.wxAppDomain[i].length === 0) {
                        self.wxAppDomain[i].push('');
                    }
                }
                for (let i in self.wxAppDomain.requestdomain) {
                    let value = self.wxAppDomain.requestdomain[i];
                    if (value.indexOf('https://') !== 0) {
                        self.wxAppDomain.requestdomain.splice(i, 1, 'https://');
                    }
                }
                for (let i in self.wxAppDomain.wsrequestdomain) {
                    let value = self.wxAppDomain.wsrequestdomain[i];
                    if (value.indexOf('wss://') !== 0) {
                        self.wxAppDomain.wsrequestdomain.splice(i, 1, 'wss://');
                    }
                }
                for (let i in self.wxAppDomain.uploaddomain) {
                    let value = self.wxAppDomain.uploaddomain[i];
                    if (value.indexOf('https://') !== 0) {
                        self.wxAppDomain.uploaddomain.splice(i, 1, 'https://');
                    }
                }
                for (let i in self.wxAppDomain.downloaddomain) {
                    let value = self.wxAppDomain.downloaddomain[i];
                    if (value.indexOf('https://') !== 0) {
                        self.wxAppDomain.downloaddomain.splice(i, 1, 'https://');
                    }
                }
            },
            domainAdd: function (target) {
                this.wxAppDomain[target].push('');
            },
            domainDelete: function (target, index) {
                this.wxAppDomain[target].splice(index, 1);
            },
            update: function () {
                let self = this;
                axios.post(api.wxAppDomain, _.assign({
                    appid: self.appid
                }, self.wxAppDomain)).then(function () {
                    self.$message.success('成功');
                    self.loadWxAppDomain();
                })
            }
        }
    }
</script>
<style scoped>
    .el-form .el-input {
        width: 400px
    }
</style>