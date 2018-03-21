<template>
    <div>
        <el-menu class="el-menu-demo" mode="horizontal" background-color="#324157" text-color="#bfcbd9"
                 active-text-color="#bfcbd9">
            <div class="brand">
                <label>{{title}}</label>
            </div>
            <el-submenu index="2">
                <template slot="title">{{user.name}}</template>
                <el-menu-item index="2-1" @click="modifyPasswordVisible = true">修改密码</el-menu-item>
                <el-menu-item index="2-2" @click="logout">注销</el-menu-item>
            </el-submenu>
        </el-menu>
        <el-dialog title="修改密码" :visible.sync="modifyPasswordVisible">
            <el-form :model="form" :rules="rules" ref="form" labelWidth="120px">
                <el-form-item prop="password" label="原密码">
                    <el-input type="password" v-model="form.password"></el-input>
                </el-form-item>
                <el-form-item prop="newPassword" label="新密码">
                    <el-input type="password" v-model="form.newPassword"></el-input>
                </el-form-item>
                <el-form-item prop="newPasswordConfirm" label="确认密码">
                    <el-input type="password" v-model="form.newPasswordConfirm"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogFormVisible = false">取 消</el-button>
                <el-button type="primary" @click="modifyPassword">确 定</el-button>
            </div>
        </el-dialog>
    </div>
</template>
<script>
    export default {
        data: function () {
            let self = this;
            return {
                title: '系统后台',
                modifyPasswordVisible: false,
                form: {
                    password: '',
                    newPassword: '',
                    newPasswordConfirm: '',
                },
                rules: {
                    password: [
                        {required: true, message: '密码不能为空', trigger: 'blur'},
                    ],
                    newPassword: [
                        {required: true, message: '新密码不能为空', trigger: 'blur'},
                        {min: 8, message: '长度不小于8', trigger: 'blur'},
                    ],
                    newPasswordConfirm: [
                        {required: true, message: '确认密码不能为空', trigger: 'blur'},
                        {min: 8, message: '长度不小于8', trigger: 'blur'},
                        {
                            validator: function (rule, value, callback) {
                                if (value !== self.form.newPassword) {
                                    callback(new Error('两次输入密码不一致!'));
                                } else {
                                    callback();
                                }
                            }, trigger: 'blur'
                        }
                    ],
                }
            }
        },
        computed: {
            user: function () {
                return this.$store.state.user
            }
        },
        created: function () {
            this.$store.commit('user');
        },
        methods: {
            modifyPassword: function () {
                let self = this;
                this.$refs.form.validate((valid) => {
                    if (valid) {
                        axios.post(api.modifyPassword, self.form).then(function (res) {
                            self.modifyPasswordVisible = false;
                            app.$message.success('修改密码成功');
                        });
                    } else {
                        return false;
                    }
                });
            },
            logout: function () {
                axios.get(api.logout).then(function () {
                    app.$router.push('/login')
                });
            }
        }
    }
</script>
<style scoped>
    .brand {
        float: left;
        width: 180px;
        text-align: center;
    }

    .brand label {
        margin-bottom: 0;
        line-height: 60px;
        font-size: 20px;
        color: #bfcbd9;
    }

    .el-submenu {
        float: right;
    }
</style>