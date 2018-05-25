<template>
    <div>
        <div class="panel">
            <div class="panel-heading">更新</div>
            <div class="panel-body">
                <el-form>
                    <el-form-item prop="account_type" label="账号类型" labelWidth="100px">
                        <span>{{form.account_type}}</span>
                    </el-form-item>
                    <el-form-item prop="account_name" label="账号" labelWidth="100px">
                        <span>{{form.account_name}}</span>
                    </el-form-item>
                    <el-form-item prop="display" label="举报类型" labelWidth="100px">
                        <el-select v-model="form.type">
                            <el-option v-for="item in reportTypeList" :key="item.id" :value="item.id"
                                       :label="item.name"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item prop="ip" label="IP" labelWidth="100px">
                        <span>{{form.ip}}</span>
                    </el-form-item>
                    <el-form-item prop="created_at" label="举报时间" labelWidth="100px">
                        <span>{{form.created_at}}</span>
                    </el-form-item>
                    <el-form-item v-if="form.attachment" prop="attachment" label="图片" labelWidth="100px">
                    <span>
                        <a target="_blank" :href="form.attachment.url">
                            <img :src="form.attachment.url" alt="" style="max-height: 200px">
                        </a>
                    </span>
                    </el-form-item>
                    <el-form-item prop="description" label="描述" labelWidth="100px">
                        <span>{{form.description}}</span>
                    </el-form-item>
                    <el-form-item prop="remark" label="备注" labelWidth="100px">
                        <el-input v-model="form.remark"></el-input>
                    </el-form-item>
                    <el-form-item labelWidth="100px">
                        <el-button type="primary" @click="doReturn">返回</el-button>
                        <el-button type="success" @click="doSubmit">提交</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Update",
        data: function () {
            return {
                apiShow: api.accountReportShow,
                apiUpdate: api.accountReportUpdate,
                routeNameList: 'account_report_list',
                form: {},
            }
        },
        computed: {
            reportTypeList: function () {
                return this.$store.state.taxonomy.report_type;
            }
        },
        created: function () {
            let self = this;
            let id = self.$route.params.id;
            axios.get(self.apiShow, {params: {id: id}}).then(function (res) {
                self.form = res.data.data;
            });
        },
        methods: {
            doReturn: function () {
                this.$router.push({name: this.routeNameList});
            },
            doSubmit: function () {
                let self = this;
                axios.post(self.apiUpdate, self.form).then(function () {
                    self.$message.success('成功');
                    self.doReturn();
                });
            },
            //self
        }
    }
</script>

<style scoped>

</style>