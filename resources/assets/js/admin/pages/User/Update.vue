<template>
    <div>
        <div class="modal fade user-auth-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title">会员认证</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" role="form">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">认证类型</label>
                                <div class="col-sm-9">
                                    <select v-model="auth_type" class="form-control">
                                        <option value="0">无</option>
                                        <option v-for="item in userTypeList" :value="item.id">{{item.name}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">认证时长</label>
                                <div class="col-sm-9">
                                    <select v-model="auth_duration" class="form-control">
                                        <option value="0">无</option>
                                        <option value="1">1个月</option>
                                        <option value="2">2个月</option>
                                        <option value="3">3个月</option>
                                        <option value="4">4个月</option>
                                        <option value="5">5个月</option>
                                        <option value="6">6个月</option>
                                        <option value="7">7个月</option>
                                        <option value="8">8个月</option>
                                        <option value="9">9个月</option>
                                        <option value="10">10个月</option>
                                        <option value="11">11个月</option>
                                        <option value="12">一年</option>
                                        <option value="24">两年</option>
                                        <option value="36">三年</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <button type="button" class="btn btn-primary" @click="doUserAuth">提交</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">关闭</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-heading">更新</div>
            <div class="panel-body">
                <div class="row text-success" v-if="form.is_auth">
                    <span class="col-md-3">认证类型：{{form.auth_type_label}}</span>
                    <span class="col-md-3">认证时长：{{form.auth_duration_label}}</span>
                    <span class="col-md-3">认证开始时间：{{form.auth_start_at}}</span>
                    <span class="col-md-3">认证结束时间：{{form.auth_end_at}}</span>
                </div>

                <el-tabs v-model="updateActiveName">
                    <el-tab-pane label="账号资料" name="1">
                        <el-form>
                            <el-form-item prop="type" label="会员类型" labelWidth="100px">
                                <el-select v-model="form.type">
                                    <el-option v-for="item in userTypeList" :key="item.id" :value="item.id"
                                               :label="item.name"></el-option>
                                </el-select>
                                <el-button type="primary" @click="userAuth">会员认证</el-button>
                            </el-form-item>
                            <el-form-item label="手机号" labelWidth="100px">
                                <el-input v-model="form.mobile"></el-input>
                            </el-form-item>
                            <el-form-item label="密码" labelWidth="100px">
                                <el-input type="password" v-model="form.password"></el-input>
                            </el-form-item>
                            <el-form-item label="QQ" labelWidth="100px">
                                <el-input v-model="form.qq"></el-input>
                            </el-form-item>
                            <el-form-item label="微信" labelWidth="100px">
                                <el-input v-model="form.wx"></el-input>
                            </el-form-item>
                            <el-form-item label="旺旺" labelWidth="100px">
                                <el-input v-model="form.ww"></el-input>
                            </el-form-item>
                            <el-form-item label="京东" labelWidth="100px">
                                <el-input v-model="form.jd"></el-input>
                            </el-form-item>
                            <el-form-item label="IS" labelWidth="100px">
                                <el-input v-model="form.is"></el-input>
                            </el-form-item>
                            <el-form-item label="api_key" labelWidth="100px">
                                <el-input :disabled="true" v-model="form.api_key"></el-input>
                                <el-button type="primary btn-copy" :data-clipboard-text="form.api_key">复制</el-button>
                                <el-button type="primary" @click="apiKeyUpdate">更新</el-button>
                            </el-form-item>
                            <el-form-item label="api_secret" labelWidth="100px">
                                <el-input :disabled="true" v-model="form.api_secret"></el-input>
                                <el-button type="primary btn-copy" :data-clipboard-text="form.api_secret">复制</el-button>
                                <el-button type="primary" @click="apiSecretUpdate">更新</el-button>
                            </el-form-item>
                            <el-form-item labelWidth="100px">
                                <el-button type="primary" @click="doReturn">返回</el-button>
                                <el-button type="success" @click="doSubmit">提交</el-button>
                            </el-form-item>
                        </el-form>
                    </el-tab-pane>
                    <el-tab-pane label="个人资料" name="2">
                        <el-form>
                            <el-form-item label="积分" labelWidth="100px">
                                <label>{{form._profile.amount}}</label>
                            </el-form-item>
                            <el-form-item label="保证金" labelWidth="100px">
                                <label class="col-md-12">{{form._profile.deposit}}</label>
                            </el-form-item>
                            <el-form-item label="修改保证金" labelWidth="100px">
                                <el-input v-model="deposit" style="width: 200px" placeholder="输入保证金金额，最低1元">
                                </el-input>
                                <span>元</span>
                                <el-button type="primary" @click="addDeposit">缴纳</el-button>
                                <el-button type="danger" @click="subDeposit">退回</el-button>
                            </el-form-item>
                            <el-form-item label="支付宝" labelWidth="100px">
                                <el-input v-model="form._profile.alipay"></el-input>
                            </el-form-item>
                            <el-form-item label="支付宝截图" labelWidth="100px">
                                <button @click="uploadImage(form,'_profile.alipay_img',$event)"
                                        type="button" class="btn btn-primary">上传图片
                                </button>
                                <input class="input-file" style="display: none" type="file"
                                       @change="uploadChange($event)">
                                <a target="_blank" :href="form._profile.alipay_img">
                                    <img :src="form._profile.alipay_img" alt="" style="max-height: 150px">
                                </a>
                            </el-form-item>
                            <el-form-item label="身份证号" labelWidth="100px">
                                <el-input v-model="form._profile.identity_code"></el-input>
                            </el-form-item>
                            <el-form-item label="身份证正面照" labelWidth="100px">
                                <button @click="uploadImage(form,'_profile.identity_front_img',$event)"
                                        type="button" class="btn btn-primary">上传图片
                                </button>
                                <input class="input-file" style="display: none" type="file"
                                       data-params="identity"
                                       @change="uploadChange($event)">
                                <a target="_blank" :href="form._profile.identity_front_img">
                                    <img :src="form._profile.identity_front_img" alt="" style="max-height: 150px">
                                </a>
                            </el-form-item>
                            <el-form-item label="身份证背面照" labelWidth="100px">
                                <button @click="uploadImage(form,'_profile.identity_back_img',$event)"
                                        type="button" class="btn btn-primary">上传图片
                                </button>
                                <input class="input-file" style="display: none" type="file"
                                       data-params="identity"
                                       @change="uploadChange($event)">
                                <a target="_blank" :href="form._profile.identity_back_img">
                                    <img :src="form._profile.identity_back_img" alt="" style="max-height: 150px">
                                </a>
                            </el-form-item>
                            <el-form-item label="姓名" labelWidth="100px">
                                <el-input v-model="form._profile.name"></el-input>
                            </el-form-item>
                            <el-form-item label="年龄" labelWidth="100px">
                                <el-input v-model="form._profile.age"></el-input>
                            </el-form-item>
                            <el-form-item label="性别" labelWidth="100px">
                                <el-select v-model="form._profile.gender">
                                    <el-option :value="0" label="未知"></el-option>
                                    <el-option :value="1" label="男"></el-option>
                                    <el-option :value="2" label="女"></el-option>
                                </el-select>
                            </el-form-item>
                            <el-form-item label="职业" labelWidth="100px">
                                <el-input v-model="form._profile.occupation"></el-input>
                            </el-form-item>

                            <el-form-item label="省" labelWidth="100px">
                                <el-select v-model="form._profile.province" @change="provinceSelect">
                                    <el-option v-for="item in provinces" :key="item" :value="item"
                                               :label="item"></el-option>
                                </el-select>
                            </el-form-item>
                            <el-form-item label="市" labelWidth="100px">
                                <el-select v-model="form._profile.city">
                                    <el-option v-for="item in cities" :key="item" :value="item"
                                               :label="item"></el-option>
                                </el-select>
                            </el-form-item>
                            <el-form-item prop="remark" label="备注" labelWidth="100px">
                                <el-input v-model="form._profile.remark"></el-input>
                            </el-form-item>
                            <el-form-item label="个人资料锁定" labelWidth="100px">
                                <el-switch v-model="form._profile.user_lock" :active-value="1"
                                           :inactive-value="0">
                                </el-switch>
                            </el-form-item>
                            <el-form-item labelWidth="100px">
                                <el-button type="primary" @click="doReturn">返回</el-button>
                                <el-button type="success" @click="doSubmit">提交</el-button>
                            </el-form-item>
                        </el-form>
                    </el-tab-pane>
                    <el-tab-pane label="店铺资料" name="3">
                        <el-form>
                            <el-form-item label="店铺类型" labelWidth="100px">
                                <el-select v-model="form._merchant.type">
                                    <el-option :value="1" label="天猫店"></el-option>
                                    <el-option :value="2" label="企业淘宝店"></el-option>
                                    <el-option :value="3" label="个人淘宝店"></el-option>
                                </el-select>
                            </el-form-item>
                            <el-form-item label="店铺名称" labelWidth="100px">
                                <el-input v-model="form._merchant.name"></el-input>
                            </el-form-item>
                            <el-form-item label="商品类型" labelWidth="100px">
                                <el-input v-model="form._merchant.goods_type"></el-input>
                            </el-form-item>
                            <el-form-item label="店铺网址" labelWidth="100px">
                                <el-input v-model="form._merchant.url"></el-input>
                            </el-form-item>
                            <el-form-item label="店铺信誉" labelWidth="100px">
                                <el-input v-model="form._merchant.credit"></el-input>
                            </el-form-item>
                            <el-form-item label="店铺掌柜" labelWidth="100px">
                                <el-input v-model="form._merchant.manager"></el-input>
                            </el-form-item>
                            <el-form-item label="店铺资料锁定" labelWidth="100px">
                                <el-switch v-model="form._merchant.user_lock" :active-value="1"
                                           :inactive-value="0">
                                </el-switch>
                            </el-form-item>
                            <el-form-item labelWidth="100px">
                                <el-button type="primary" @click="doReturn">返回</el-button>
                                <el-button type="success" @click="doSubmit">提交</el-button>
                            </el-form-item>
                        </el-form>
                    </el-tab-pane>
                    <el-tab-pane label="管理员备注" name="4">
                        <el-input v-model="create_remark" placeholder="备注内容"></el-input>
                        <el-button type="primary" @click="createUserRemark">添加备注</el-button>
                        <el-table :data="user_remark" stripe>
                            <el-table-column prop="content" label="备注内容" min-width="600"></el-table-column>
                            <el-table-column prop="created_at" label="创建时间" min-width="250"></el-table-column>
                            <el-table-column label="操作" min-width="300">
                                <template slot-scope="scope">
                                </template>
                            </el-table-column>
                        </el-table>
                    </el-tab-pane>
                </el-tabs>
            </div>
        </div>
    </div>
</template>

<script>
    import cityData from '../../../city.json';
    import Clipboard from 'clipboard';

    export default {
        name: "Update",
        data: function () {
            return {
                apiShow: api.userShow,
                apiUpdate: api.userUpdate,
                form: {_profile: {}, _merchant: {}},
                updateActiveName: '1',
                provinces: Object.keys(cityData),
                cities: [],
                auth_type: 0,
                auth_duration: 0,
                deposit: 0,
                user_remark: [],
                create_remark: '',
            }
        },
        computed: {
            userTypeList: function () {
                return this.$store.state.taxonomy.user_type;
            }
        },
        created: function () {
            this.loadData();
            this.loadUserRemark();
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
                let id = self.$route.params.id;
                axios.get(self.apiShow, {params: {id: id}}).then(function (res) {
                    self.form = res.data.data;
                    self.form._merchant = self.form._merchant ? self.form._merchant : {};
                });
            },
            doReturn: function () {
                this.$router.push({name: 'userList'});
            },
            doSubmit: function () {
                let self = this;
                axios.post(self.apiUpdate, self.form).then(function () {
                    self.$message.success('成功');
                    self.doReturn();
                });
            },
            //self
            provinceSelect: function (value) {
                this.cities = Object.keys(cityData[value]);
                this.form._profile.city = this.cities[0];
            },
            uploadImage: function (object, key, event) {
                let inputFile = $(event.target).siblings('.input-file');
                inputFile.data('object', object).data('key', key).click();
            },
            uploadChange: function (event) {
                let self = this;
                let formData = new FormData();
                let inputFile = event.target;
                formData.append("file", inputFile.files[0]);
                let params = $(inputFile).data('params');
                if (params === 'identity') {
                    formData.append('watermark', 'identity');
                }
                axios.post(api.adminUploadOssImage, formData, {
                    headers: {"Content-Type": "multipart/form-data"}
                }).then(function (res) {
                    self.$message.success("成功");
                    let object = $(inputFile).data('object');
                    let key = $(inputFile).data('key');
                    _.set(object, key, res.data.data.url);
                    $(inputFile).val('');
                }).catch(function () {
                    $(inputFile).val('');
                });
            },
            apiKeyUpdate: function () {
                let self = this;
                axios.post(api.adminUserUpdateApiKey, {id: self.form.id}).then(function (res) {
                    self.loadData();
                });
            },
            apiSecretUpdate: function () {
                let self = this;
                axios.post(api.adminUserUpdateApiSecret, {id: self.form.id}).then(function (res) {
                    self.loadData();
                });
            },
            userAuth: function () {
                $('.user-auth-dialog').modal('show');
            },
            doUserAuth: function () {
                let self = this;
                axios.post(api.adminUserUpdateAuth, {
                    id: self.form.id,
                    auth_type: self.auth_type,
                    auth_duration: self.auth_duration
                }).then(function (res) {
                    $('.user-auth-dialog').modal('hide');
                    self.$message.success('成功');
                    self.loadData();
                }).catch(function () {
                    $('.user-auth-dialog').modal('hide');
                });
            },
            addDeposit: function () {
                let self = this;
                axios.post(api.adminUserAddDeposit, {
                    id: self.form.id,
                    deposit: self.deposit,
                }).then(function (res) {
                    self.$message.success('成功');
                    self.loadData();
                })
            },
            subDeposit: function () {
                let self = this;
                axios.post(api.adminUserSubDeposit, {
                    id: self.form.id,
                    deposit: self.deposit,
                }).then(function (res) {
                    self.deposit = 0;
                    self.$message.success('成功');
                    self.loadData();
                })
            },
            loadUserRemark: function () {
                let self = this;
                let id = self.$route.params.id;
                axios.get(api.adminUserRemarkIndex, {params: {id: id}}).then(function (res) {
                    self.user_remark = res.data.data;
                });
            },
            createUserRemark: function () {
                let self = this;
                axios.post(api.adminUserRemarkCreate, {
                    id: self.form.id,
                    content: self.create_remark
                }).then(function (res) {
                    self.create_remark = '';
                    self.$message.success('成功');
                    self.loadUserRemark();
                })
            }
        }
    }
</script>

<style scoped>

</style>