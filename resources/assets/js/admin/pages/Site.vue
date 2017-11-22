<template>
    <div>
        <div class="panel">
            <div class="panel-heading">基本设置</div>
            <div class="panel-body">
                <el-form ref="form" :model="dataList" label-width="150px">
                    <el-form-item label="域名">
                        <template>
                            <el-input v-model="dataList.domain"></el-input>
                        </template>
                    </el-form-item>
                    <el-form-item label="名称">
                        <el-input v-model="dataList.name"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="submit">提交</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                dataList: {}
            }
        },
        created: function () {
            this.loadData();
        },
        methods: {
            loadData: function () {
                let self = this;
                axios.get(api.siteBasic).then(function (res) {
                    self.dataList = res.data.data;
                });
            },
            submit: function () {
                let self = this;
                axios.post(api.siteBasic, self.dataList).then(function () {
                    self.$message.success('成功');
                    self.loadData();
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