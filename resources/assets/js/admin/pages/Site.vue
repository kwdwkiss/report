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
                    <el-form-item label="SEO关键字">
                        <el-input v-model="dataList.seo_keywords"></el-input>
                    </el-form-item>
                    <el-form-item label="SEO描述">
                        <el-input v-model="dataList.seo_description"></el-input>
                    </el-form-item>
                    <el-form-item label="首页博客文章">
                        <el-input v-model="dataList.index_blog_article" placeholder="请输入文章id"></el-input>
                    </el-form-item>
                    <el-form-item label="关闭注册">
                        <el-switch v-model="dataList.close_register" :active-value="1" :inactive-value="0">
                        </el-switch>
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