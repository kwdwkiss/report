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
            <div class="panel">
                <div class="panel-heading">公告</div>
                <div class="panel-body">
                    <el-form label-width="100px">
                        <el-form-item label="标题">
                            <el-input v-model="data.notice.title"></el-input>
                        </el-form-item>
                        <el-form-item label="链接">
                            <el-input v-model="data.notice.url"></el-input>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
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
                                <el-form-item label="日期">
                                    <el-input v-model="subItem.created_at"></el-input>
                                    <el-button type="primary" @click="refreshDate(subItem)">刷新日期</el-button>
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
                                <el-button type="primary" @click="uploadImage(item)">上传</el-button>
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
                                <el-button type="primary" @click="uploadImage(item)">上传</el-button>
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
                                <el-button type="primary" @click="uploadImage(item)">上传</el-button>
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
                                <el-button type="primary" @click="uploadImage(item)">上传</el-button>
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
        <el-tab-pane label="移动端广告设置" name="mobile_ad">
            <div class="panel">
                <div class="panel-heading">恶意查询页面广告</div>
                <div class="panel-body">
                    <el-form label-width="100px">
                        <el-form-item label="图片路径">
                            <el-input v-model="data.mobile_ad.report_search.img_src"></el-input>
                            <el-button type="primary" @click="uploadImage(data.mobile_ad.report_search)">上传</el-button>
                        </el-form-item>
                        <el-form-item label="链接">
                            <el-input v-model="data.mobile_ad.report_search.url"></el-input>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="success" @click="doSave">保存</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
            <div class="panel">
                <div class="panel-heading">淘宝验号页面广告</div>
                <div class="panel-body">
                    <el-form label-width="100px">
                        <el-form-item label="图片路径">
                            <el-input v-model="data.mobile_ad.check_tb.img_src"></el-input>
                            <el-button type="primary" @click="uploadImage(data.mobile_ad.check_tb)">上传</el-button>
                        </el-form-item>
                        <el-form-item label="链接">
                            <el-input v-model="data.mobile_ad.check_tb.url"></el-input>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="success" @click="doSave">保存</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
            <div class="panel">
                <div class="panel-heading">拼多多验号页面广告</div>
                <div class="panel-body">
                    <el-form label-width="100px">
                        <el-form-item label="图片路径">
                            <el-input v-model="data.mobile_ad.check_pdd.img_src"></el-input>
                            <el-button type="primary" @click="uploadImage(data.mobile_ad.check_pdd)">上传</el-button>
                        </el-form-item>
                        <el-form-item label="链接">
                            <el-input v-model="data.mobile_ad.check_pdd.url"></el-input>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="success" @click="doSave">保存</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
            <div class="panel">
                <div class="panel-heading">京东验号页面广告</div>
                <div class="panel-body">
                    <el-form label-width="100px">
                        <el-form-item label="图片路径">
                            <el-input v-model="data.mobile_ad.check_jd.img_src"></el-input>
                            <el-button type="primary" @click="uploadImage(data.mobile_ad.check_jd)">上传</el-button>
                        </el-form-item>
                        <el-form-item label="链接">
                            <el-input v-model="data.mobile_ad.check_jd.url"></el-input>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="success" @click="doSave">保存</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
            <div class="panel">
                <div class="panel-heading">一键生成EXCEL页面广告</div>
                <div class="panel-body">
                    <el-form label-width="100px">
                        <el-form-item label="图片路径">
                            <el-input v-model="data.mobile_ad.one_key_excel.img_src"></el-input>
                            <el-button type="primary" @click="uploadImage(data.mobile_ad.one_key_excel)">上传</el-button>
                        </el-form-item>
                        <el-form-item label="链接">
                            <el-input v-model="data.mobile_ad.one_key_excel.url"></el-input>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="success" @click="doSave">保存</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
            <div class="panel">
                <div class="panel-heading">微信清粉页面广告</div>
                <div class="panel-body">
                    <el-form label-width="100px">
                        <el-form-item label="图片路径">
                            <el-input v-model="data.mobile_ad.wx_clear_friends.img_src"></el-input>
                            <el-button type="primary" @click="uploadImage(data.mobile_ad.wx_clear_friends)">上传</el-button>
                        </el-form-item>
                        <el-form-item label="链接">
                            <el-input v-model="data.mobile_ad.wx_clear_friends.url"></el-input>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="success" @click="doSave">保存</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
            <div class="panel">
                <div class="panel-heading">登录页面广告</div>
                <div class="panel-body">
                    <el-form label-width="100px">
                        <el-form-item label="图片路径">
                            <el-input v-model="data.mobile_ad.login.img_src"></el-input>
                            <el-button type="primary" @click="uploadImage(data.mobile_ad.login)">上传</el-button>
                        </el-form-item>
                        <el-form-item label="链接">
                            <el-input v-model="data.mobile_ad.login.url"></el-input>
                        </el-form-item>
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
                                <el-button type="primary" @click="uploadImage(item,'name')">上传</el-button>
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
            <div class="panel">
                <div class="panel-heading">微信号设置</div>
                <div class="panel-body">
                    <el-form label-width="100px">
                        <template v-for="item in data.service_wx_id">
                            <el-form-item label="微信账号">
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
        <input class="input-file" style="display: none" type="file" name="file" @change="uploadChange">
    </el-tabs>
</template>

<script>
    export default {
        name: "index-set",
        data: function () {
            return {
                activeName: 'article',
                data: {
                    notice: {title: '', url: ''},
                    article_data: []
                }
            }
        },
        created: function () {
            this.loadData();
        },
        methods: {
            loadData: function () {
                let self = this;
                axios.get(api.siteIndex).then(function (res) {
                    self.data = res.data.data;
                });
            },
            doSave: function () {
                let self = this;
                axios.post(api.siteIndex, self.data).then(function () {
                    self.$message.success('成功');
                });
            },
            refreshDate: function (subItem) {
                subItem.created_at = new Date().toLocaleDateString().replace(/\//g, '-');
            },
            uploadImage: function (item, key) {
                let inputFile = $('.input-file');
                key = key ? key : 'img_src';
                inputFile.data('key', key);
                inputFile.data('target', item).click();
            },
            uploadChange: function () {
                let self = this;
                let formData = new FormData();
                let inputFile = $('.input-file');
                formData.append('file', inputFile.get(0).files[0]);
                axios.post(api.adminUploadOss, formData, {
                    headers: {'Content-Type': 'multipart/form-data'}
                }).then(function (res) {
                    self.$message.success('成功');
                    let key = inputFile.data('key');
                    //inputFile.data('target')['attachment'] = res.data.data;
                    inputFile.data('target')[key] = res.data.data.url;
                    inputFile.val('');
                }).catch(function () {
                    inputFile.val('');
                });
            },
        }
    }
</script>

<style scoped>
    .panel-body .el-input {
        max-width: 600px;
    }
</style>