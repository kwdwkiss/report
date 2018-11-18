<template>
    <div>
        <div class="panel">
            <div class="panel-heading">编辑文章</div>
            <div class="panel-body">
                <el-form ref="form" :model="form">
                    <el-form-item prop="type" label="文章类型" labelWidth="100px">
                        <el-select v-model="form.type">
                            <el-option v-for="item in articleTypeList" :key="item.id" :value="item.id"
                                       :label="item.name"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item prop="title" label="标题" labelWidth="100px">
                        <el-input v-model="form.title"></el-input>
                    </el-form-item>
                    <el-form-item prop="remark" label="备注" labelWidth="100px">
                        <el-input v-model="form.remark"></el-input>
                    </el-form-item>
                    <el-form-item prop="content" label="内容" labelWidth="100px">
                        <VueUEditor ueditorPath="/ueditor/" @ready="editorReady" style="line-height: 20px">
                        </VueUEditor>
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
    import VueUEditor from 'vue-ueditor';

    export default {
        name: "Update",
        components: {VueUEditor},
        data: function () {
            return {
                apiShow: api.agentArticleShow,
                apiUpdate: api.agentArticleUpdate,
                form: {},
                editor: {},
            }
        },
        computed: {
            articleTypeList: function () {
                return this.$store.state.taxonomy.article_type
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
                this.$router.push({name: 'articleList'});
            },
            doSubmit: function () {
                let self = this;

                self.form.content = self.editor.getContent();

                axios.post(self.apiUpdate, self.form).then(function () {
                    self.$message.success('成功');
                    self.doReturn();
                });
            },
            editorReady: function (ue) {
                this.editor = ue;
                this.editor.setContent(this.form.content);
            },
        }
    }
</script>

<style scoped>

</style>