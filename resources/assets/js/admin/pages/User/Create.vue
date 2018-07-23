<template>
    <div>
        <div class="panel">
            <div class="panel-heading">创建</div>
            <div class="panel-body">
                <el-tabs v-model="updateActiveName">
                    <el-tab-pane label="个人资料" name="1">
                        <el-form ref="form" :model="form">
                            <el-form-item prop="type" label="会员类型" labelWidth="100px">
                                <el-select v-model="form.type">
                                    <el-option v-for="item in userTypeList" :key="item.id" :value="item.id"
                                               :label="item.name"></el-option>
                                </el-select>
                            </el-form-item>
                            <el-form-item prop="mobile" label="手机号" labelWidth="100px">
                                <el-input v-model="form.mobile"></el-input>
                            </el-form-item>
                            <el-form-item label="密码" labelWidth="100px">
                                <el-input type="password" v-model="form.password"></el-input>
                            </el-form-item>
                            <el-form-item prop="qq" label="QQ" labelWidth="100px">
                                <el-input v-model="form.qq"></el-input>
                            </el-form-item>
                            <el-form-item prop="wx" label="微信" labelWidth="100px">
                                <el-input v-model="form.wx"></el-input>
                            </el-form-item>
                            <el-form-item prop="ww" label="旺旺" labelWidth="100px">
                                <el-input v-model="form.ww"></el-input>
                            </el-form-item>
                            <el-form-item prop="jd" label="京东" labelWidth="100px">
                                <el-input v-model="form.jd"></el-input>
                            </el-form-item>
                            <el-form-item prop="is" label="IS" labelWidth="100px">
                                <el-input v-model="form.is"></el-input>
                            </el-form-item>
                            <el-form-item prop="remark" label="支付宝" labelWidth="100px">
                                <el-input v-model="form._profile.alipay"></el-input>
                            </el-form-item>
                            <el-form-item prop="name" label="姓名" labelWidth="100px">
                                <el-input v-model="form._profile.name"></el-input>
                            </el-form-item>
                            <el-form-item prop="age" label="年龄" labelWidth="100px">
                                <el-input v-model.number="form._profile.age"></el-input>
                            </el-form-item>
                            <el-form-item label="性别" labelWidth="100px">
                                <el-select v-model="form._profile.gender">
                                    <el-option value="1" label="男"></el-option>
                                    <el-option value="2" label="女"></el-option>
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

    export default {
        name: "Recharge",
        data: function () {
            return {
                apiCreate: api.userCreate,
                form: {_profile: {}, _merchant: {}},
                editor: {},
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
        methods: {
            doReturn: function () {
                this.$router.push({name: 'userList'});
            },
            doSubmit: function () {
                let self = this;
                axios.post(self.apiCreate, self.form).then(function () {
                    self.$message.success('成功');
                    self.doReturn();
                });
            },
            //self
            provinceSelect: function (value) {
                this.cities = Object.keys(cityData[value]);
                this.form._profile.city = this.cities[0];
            },
        }
    }
</script>

<style scoped>

</style>