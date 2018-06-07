<template>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">用户资料</div>
            <div class="panel-body">
                <div class="col-md-offset-2 col-md-8">
                    <form class="form-horizontal" role="form">
                        <div v-if="lock" class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <label class="text-warning">个人资料锁定，修改请联系客服</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">会员类型</label>
                            <div class="col-sm-9 form-control-static">
                                {{userModifyForm.type_label}}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">QQ</label>
                            <div class="col-sm-9">
                                <input :disabled="lock" type="text" class="form-control" placeholder="请输入QQ号"
                                       v-model="userModifyForm.qq">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">微信</label>
                            <div class="col-sm-9">
                                <input :disabled="lock" type="text" class="form-control" placeholder="请输入微信号"
                                       v-model="userModifyForm.wx">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">旺旺</label>
                            <div class="col-sm-9">
                                <input :disabled="lock" type="text" class="form-control" placeholder="请输入旺旺号"
                                       v-model="userModifyForm.ww">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">京东</label>
                            <div class="col-sm-9">
                                <input :disabled="lock" type="text" class="form-control" placeholder="请输入京东号"
                                       v-model="userModifyForm.jd">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">IS</label>
                            <div class="col-sm-9">
                                <input :disabled="lock" type="text" class="form-control" placeholder="请输入IS号"
                                       v-model="userModifyForm.is">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">支付宝</label>
                            <div class="col-sm-9">
                                <input :disabled="lock" type="text" class="form-control" placeholder="请输入支付宝"
                                       v-model="userModifyForm._profile.alipay">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">支付宝截图</label>
                            <div class="col-sm-9 form-control-static">
                                <button v-if="!lock"
                                        @click="uploadImage(userModifyForm,'_profile.alipay_img',$event)"
                                        type="button"
                                        class="btn btn-primary">上传图片
                                </button>
                                <input class="input-file" style="display: none" type="file"
                                       @change="uploadChange($event)">
                                <img :src="userModifyForm._profile.alipay_img" alt=""
                                     style="margin-top:10px;max-height: 200px">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">身份证号</label>
                            <div class="col-sm-9">
                                <input :disabled="lock" type="text" class="form-control" placeholder="请输入身份证号"
                                       v-model="userModifyForm._profile.identity_code">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">身份证正面照</label>
                            <div class="col-sm-9 form-control-static">
                                <button v-if="!lock"
                                        @click="uploadImage(userModifyForm,'_profile.identity_front_img',$event)"
                                        type="button"
                                        class="btn btn-primary">上传图片
                                </button>
                                <input class="input-file" style="display: none" type="file"
                                       @change="uploadChange($event)">
                                <img :src="userModifyForm._profile.identity_front_img" alt=""
                                     style="margin-top:10px;max-height: 200px">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">身份证背面照</label>
                            <div class="col-sm-9 form-control-static">
                                <button v-if="!lock"
                                        @click="uploadImage(userModifyForm,'_profile.identity_back_img',$event)"
                                        type="button"
                                        class="btn btn-primary">上传图片
                                </button>
                                <input class="input-file" style="display: none" type="file"
                                       @change="uploadChange($event)">
                                <img :src="userModifyForm._profile.identity_back_img" alt=""
                                     style="margin-top:10px;max-height: 200px">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">姓名</label>
                            <div class="col-sm-9">
                                <input :disabled="lock" type="text" class="form-control" placeholder="请输入姓名"
                                       v-model="userModifyForm._profile.name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">年龄</label>
                            <div class="col-sm-9">
                                <input :disabled="lock" type="text" class="form-control" placeholder="请输入年龄"
                                       v-model="userModifyForm._profile.age">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">性别</label>
                            <div class="col-sm-9">
                                <select :disabled="lock" class="form-control" v-model="userModifyForm._profile.gender">
                                    <option value="0">未知</option>
                                    <option value="1">男</option>
                                    <option value="2">女</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">职业</label>
                            <div class="col-sm-9">
                                <input :disabled="lock" type="text" class="form-control" placeholder="请输入职业"
                                       name="mobile"
                                       v-model="userModifyForm._profile.occupation">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">省</label>
                            <div class="col-sm-9">
                                <select :disabled="lock" class="form-control" v-model="userModifyForm._profile.province"
                                        @change="provinceSelect">
                                    <option v-for="item in provinces" :key="item" :value="item">{{item}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">市</label>
                            <div class="col-sm-9">
                                <select :disabled="lock" class="form-control" v-model="userModifyForm._profile.city">
                                    <option v-for="item in cities" :key="item" :value="item">{{item}}</option>
                                </select>
                            </div>
                        </div>
                        <div v-if="!lock" class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <a v-if="!userModifyForm._profile.user_lock" class="btn btn-success"
                                   @click="doModify">提交</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import cityData from "../../city.json";

    export default {
        name: "UserProfile",
        data: function () {
            return {
                userModifyForm: {
                    _profile: {
                        alipay_img: '',
                        identity_front_img: '',
                        identity_back_img: '',
                    }
                },
                provinces: Object.keys(cityData),
                cities: [],
            }
        },
        computed: {
            user: function () {
                return this.$store.state.user;
            },
            lock: function () {
                return this.user._profile.user_lock === 1;
            }
        },
        created: function () {
            this.userModifyForm = _.cloneDeep(this.$store.state.user);
        },
        methods: {
            initCities: function (province) {
                this.cities = Object.keys(_.get(cityData, province, []));
            },
            provinceSelect: function (event) {
                this.initCities(event.target.value);
                this.userModifyForm._profile.city = this.cities[0];
            },
            doModify: function () {
                let self = this;
                axios.post(api.userModify, self.userModifyForm).then(function (res) {
                    self.$message.success("成功");
                    self.$store.commit("user");
                });
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
                axios
                    .post(api.uploadOss, formData, {
                        headers: {"Content-Type": "multipart/form-data"}
                    })
                    .then(function (res) {
                        self.$message.success("成功");
                        let object = $(inputFile).data('object');
                        let key = $(inputFile).data('key');
                        _.set(object, key, res.data.data.url);
                        $(inputFile).val('');
                    })
                    .catch(function () {
                        $(inputFile).val('');
                    });
            }
        }
    }
</script>

<style scoped>

</style>