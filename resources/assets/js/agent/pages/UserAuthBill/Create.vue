<template>
    <div class="panel">
        <div class="panel-heading">创建用户认证</div>
        <div class="panel-body">
            <el-form ref="form" :model="form">
                <el-form-item prop="mobile" label="用户手机号" labelWidth="100px">
                    <el-input v-model="form.mobile"></el-input>
                </el-form-item>
                <el-form-item prop="type" label="认证类型" labelWidth="100px">
                    <el-select v-model="form.type">
                        <el-option v-for="item in userTypeList" :key="item.id" :value="item.id"
                                   :label="item.name"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item prop="duration" label="认证时长" labelWidth="100px">
                    <el-select v-model="form.duration">
                        <el-option v-for="(value, key) in durationList" :key="key" :value="key"
                                   :label="value"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item labelWidth="100px">
                    <el-button type="primary" @click="doReturn">返回</el-button>
                    <el-button type="success" @click="doSubmit">提交</el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Recharge",
        data: function () {
            return {
                api: api.agentUserAuthBillCreate,
                form: {},
                durationList: {
                    1: '1个月',
                    2: '2个月',
                    3: '3个月',
                    4: '4个月',
                    5: '5个月',
                    6: '6个月',
                    7: '7个月',
                    8: '8个月',
                    9: '9个月',
                    10: '10个月',
                    11: '11个月',
                    12: '1年',
                    24: '2年',
                    36: '3年',
                }
            }
        },
        computed: {
            userTypeList: function () {
                return this.$store.state.taxonomy.user_type;
            }
        },
        methods: {
            doReturn: function () {
                this.$router.push({name: 'userAuthBillList'});
            },
            doSubmit: function () {
                let self = this;
                axios.post(self.api, self.form).then(function () {
                    self.$message.success('成功');
                    self.doReturn();
                });
            }
        }
    }
</script>

<style scoped>

</style>