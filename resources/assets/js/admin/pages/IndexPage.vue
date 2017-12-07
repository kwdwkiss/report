<template>
    <el-tabs v-model="activeName">
        <el-tab-pane label="菜单设置" name="menu">
            <div class="panel">
                <div class="panel-heading">菜单设置</div>
                <div class="panel-body">
                    <el-form label-width="100px">
                        <template v-for="item in data.menu">
                            <el-form-item label="名称">
                                <el-input v-model="item.name"></el-input>
                            </el-form-item>
                            <el-form-item label="链接">
                                <el-input v-model="item.url"></el-input>
                            </el-form-item>
                        </template>
                        <el-form-item>
                            <el-button type="success" @click="doSave">保存</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
        </el-tab-pane>
        <el-tab-pane label="文章设置" name="article">
            <template v-for="item in data.article_data">
                <div class="panel">
                    <div class="panel-heading">分组</div>
                    <div class="panel-body">
                        <el-form label-width="100px">
                            <el-form-item label="分类名称">
                                <el-input v-model="item.type"></el-input>
                            </el-form-item>
                            <el-form-item label="分类链接">
                                <el-input v-model="item.url"></el-input>
                            </el-form-item>
                            <template v-for="subItem in item.data">
                                <el-form-item label="标题">
                                    <el-input v-model="subItem.title"></el-input>
                                </el-form-item>
                                <el-form-item label="链接">
                                    <el-input v-model="subItem.url"></el-input>
                                </el-form-item>
                            </template>
                            <el-form-item>
                                <el-button type="success" @click="doSave">保存</el-button>
                            </el-form-item>
                        </el-form>
                    </div>
                </div>
            </template>
        </el-tab-pane>
        <el-tab-pane label="广告设置" name="ad">
            <div class="panel">
                <div class="panel-heading">顶部广告</div>
                <div class="panel-body">
                    <el-form label-width="100px">
                        <template v-for="item in data.ad_top">
                            <el-form-item label="图片路径">
                                <el-input v-model="item.img_src"></el-input>
                            </el-form-item>
                            <el-form-item label="链接">
                                <el-input v-model="item.url"></el-input>
                            </el-form-item>
                        </template>
                        <el-form-item>
                            <el-button type="success" @click="doSave">保存</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
            <div class="panel">
                <div class="panel-heading">第二排广告</div>
                <div class="panel-body">
                    <el-form label-width="100px">
                        <template v-for="item in data.ad_second">
                            <el-form-item label="图片路径">
                                <el-input v-model="item.img_src"></el-input>
                            </el-form-item>
                            <el-form-item label="链接">
                                <el-input v-model="item.url"></el-input>
                            </el-form-item>
                        </template>
                        <el-form-item>
                            <el-button type="success" @click="doSave">保存</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
            <div class="panel">
                <div class="panel-heading">第三排广告</div>
                <div class="panel-body">
                    <el-form label-width="100px">
                        <template v-for="item in data.ad_third">
                            <el-form-item label="图片路径">
                                <el-input v-model="item.img_src"></el-input>
                            </el-form-item>
                            <el-form-item label="链接">
                                <el-input v-model="item.url"></el-input>
                            </el-form-item>
                        </template>
                        <el-form-item>
                            <el-button type="success" @click="doSave">保存</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
            <div class="panel">
                <div class="panel-heading">底部广告</div>
                <div class="panel-body">
                    <el-form label-width="100px">
                        <template v-for="item in data.ad_foot">
                            <el-form-item label="图片路径">
                                <el-input v-model="item.img_src"></el-input>
                            </el-form-item>
                            <el-form-item label="链接">
                                <el-input v-model="item.url"></el-input>
                            </el-form-item>
                        </template>
                        <el-form-item>
                            <el-button type="success" @click="doSave">保存</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
        </el-tab-pane>
        <el-tab-pane label="客服设置" name="service">
            <div class="panel">
                <div class="panel-heading">微信客服设置</div>
                <div class="panel-body">
                    <el-form label-width="100px">
                        <template v-for="item in data.service_wx">
                            <el-form-item label="微信">
                                <el-input v-model="item.name"></el-input>
                            </el-form-item>
                        </template>
                        <el-form-item>
                            <el-button type="success" @click="doSave">保存</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
            <div class="panel">
                <div class="panel-heading">QQ客服设置</div>
                <div class="panel-body">
                    <el-form label-width="100px">
                        <template v-for="item in data.service_qq">
                            <el-form-item label="QQ">
                                <el-input v-model="item.name"></el-input>
                            </el-form-item>
                        </template>
                        <el-form-item>
                            <el-button type="success" @click="doSave">保存</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
        </el-tab-pane>
    </el-tabs>
</template>

<script>
    export default {
        name: "index-set",
        data: function () {
            return {
                activeName: 'article',
                data: {
                    article_data: []
                }
            }
        },
        created: function () {
            let self = this;
            axios.get(api.siteIndex).then(function (res) {
                self.data = res.data.data;
            });
        },
        methods: {
            doSave: function () {
                let self = this;
                axios.post(api.siteIndex, self.data).then(function () {
                    self.$message.success('成功');
                });
            }
        }
    }
</script>

<style scoped>

</style>