<template>
    <div class="panel">
        <div class="panel-heading">弹窗设置</div>
        <div class="panel-body">
            <el-form label-width="100px">
                <el-form-item label="标题">
                    <el-input v-model="data.title"></el-input>
                </el-form-item>
                <el-form-item label="内容">
                    <VueUEditor ueditorPath="/ueditor/" @ready="editorReady" style="line-height: 20px"></VueUEditor>
                </el-form-item>
                <el-form-item>
                    <el-button type="success" @click="doSave">保存</el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>

<script>
    import VueUEditor from 'vue-ueditor';

    export default {
        components: {VueUEditor},
        name: "pop-window",
        data: function () {
            return {
                editor: null,
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
                    if (self.editor) {
                        self.editor.setContent(self.data.content);
                    }
                });
            },
            doSave: function () {
                let self = this;
                self.data.content = self.editor.getContent();
                axios.post(api.sitePopWindow, self.data).then(function () {
                    self.$message.success('成功');
                })
            },
            editorReady: function (ue) {
                this.editor = ue;
                if (this.data.content) {
                    this.editor.setContent(this.data.content);
                }
            },
        }
    }
</script>

<style scoped>
</style>