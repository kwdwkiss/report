<template>
    <div>
        <div class="panel">
            <div class="panel-heading">更新</div>
            <div class="panel-body">
                <el-tabs v-model="updateActiveName">
                    <el-tab-pane label="账号资料" name="1">
                        <el-form>
                            <el-form-item prop="type" label="会员类型" labelWidth="100px">
                                <el-select v-model="form.type">
                                    <el-option v-for="item in userTypeList" :key="item.id" :value="item.id"
                                               :label="item.name"></el-option>
                                </el-select>
                            </el-form-item>
                            <el-form-item label="手机号" labelWidth="100px">
                                <el-input v-model="form.mobile"></el-input>
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
            }
        },
        computed: {
            userTypeList: function () {
                return this.$store.state.taxonomy.user_type;
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
                let id = self.$route.params.id;
                axios.get(self.apiShow, {params: {id: id}}).then(function (res) {
                    self.form = res.data.data;
                    self.form._merchant = self.form._merchant ? self.form._merchant : {};
                });
            },
            doReturn: function () {
                this.$router.push({name: 'user_list'});
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
                axios.post(api.uploadOss, formData, {
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
            }
        }
    }
</script>

<style scoped>

</style>