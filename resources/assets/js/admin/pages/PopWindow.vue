<template>
    <div class="panel">
        <div class="panel-heading">弹窗设置</div>
        <div class="panel-body">
            <el-form label-width="100px">
                <el-form-item label="标题">
                    <el-input v-model="data.title"></el-input>
                </el-form-item>
                <el-form-item label="内容">
                    <el-input type="textarea" :rows="3" v-model="data.content"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="success" @click="doSave">保存</el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>

<script>
    export default {
        name: "pop-window",
        data: function () {
            return {
                data: {
                    title: '',
                    content: ''
                },
            }
        },
        created: function () {
            this.loadData();
        },
        methods: {
            loadData: function () {
                let self = this;
                axios.get(api.indexPopWindow).then(function (res) {
                    self.data = res.data.data;
                });
            },
            doSave: function () {
                let self = this;
                axios.post(api.sitePopWindow, self.data).then(function () {
                    self.$message.success('成功');
                })
            }
        }
    }
</script>

<style scoped>

</style>