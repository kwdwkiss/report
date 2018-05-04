<template>
    <div>
        <div class="row search">
            <div class="col-xs-8">
                <select v-model="searchParams.account_type">
                    <option v-for="item in $store.state.taxonomy.account_type" :value="item.id">{{item.name}}
                    </option>
                </select>
                <input v-model="searchParams.name" name="name" type="text" placeholder="请输入账号">
                <button @click="doSearch" class="btn btn-success">查询</button>
                <button @click="report" class="btn btn-danger">投诉举报</button>
            </div>
            <div class="col-xs-4 member-num">
                <p>网站实名认证会员：<span>{{page.auth_member_num}}</span>名会员</p>
            </div>
        </div>

        <div class="modal fade" id="report-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title">投诉举报</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" role="form">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">账号类型</label>
                                <div class="col-sm-9">
                                    <select v-model="reportParams.account_type" name="account_type"
                                            class="form-control">
                                        <option v-for="item in $store.state.taxonomy.account_type" :value="item.id">
                                            {{item.name}}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">投诉账号</label>
                                <div class="col-sm-9">
                                    <input v-model="reportParams.name" name="name" type="text" class="form-control"
                                           placeholder="投诉账号">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">投诉类型</label>
                                <div class="col-sm-9">
                                    <select v-model="reportParams.report_type" name="report_type" class="form-control">
                                        <option v-for="item in $store.state.taxonomy.report_type" :value="item.id">
                                            {{item.name}}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">上传图片</label>
                                <div class="col-sm-9">
                                    <button @click="uploadImage(reportParams.image)" type="button"
                                            class="btn btn-primary">上传图片
                                    </button>
                                    <input class="input-file" style="display: none" type="file" @change="uploadChange">
                                </div>
                                <div class="col-sm-offset-3 col-sm-9">
                                    <img :src="reportParams.image.attachment.url" alt="" style="max-height: 200px">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">情况描述</label>
                                <div class="col-sm-9">
                                    <textarea cols="30" rows="4" class="form-control" placeholder="如实描述举报内容，恶意举报将封停账号"
                                              v-model="reportParams.description"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">验证码</label>
                                <div class="col-sm-6">
                                    <input v-model="reportParams.captcha" name="captcha" type="text"
                                           class="form-control" placeholder="请输入验证码">
                                </div>
                                <div class="col-sm-3">
                                    <img :src="captcha_src" alt="" @click="doCaptcha">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <button type="button" class="btn btn-danger" @click="doReport">提交</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row report-num">
            <p>
                本站目前已有<span>{{page.report_num}}</span>条恶意账号数据，最近24小时危险监测更新<span>{{page.last_24_report_num}}</span>条数据
            </p>
        </div>

        <router-view></router-view>

        <div class="row hidden-xs hidden-sm ad">
            <div class="col-xs-6" v-for="item in page.ad_third">
                <a target="_blank" :href="item.url">
                    <img :src="item.img_src">
                </a>
            </div>
        </div>

        <div class="row hidden-xs hidden-sm article-data">
            <div class="col-xs-6" v-for="item in page.article_data">
                <div>
                    <p>{{item.type}}<a :href="item.url">更多</a></p>
                    <p v-for="subItem in item.data">
                        <a class="article-title" target="_blank" :href="subItem.url">{{subItem.title}}</a>
                        <span class="pull-right">{{subItem.created_at}}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "index",
        computed: {
            page: function () {
                return this.$store.state.page;
            }
        },
        data: function () {
            return {
                searchParams: {},
                reportParams: {},
                captcha_src: api.captcha + '?' + Date.parse(new Date())
            }
        },
        created: function () {
            this.initSearchParams();
            this.initReportParams();
        },
        methods: {
            initSearchParams: function () {
                this.searchParams = {
                    account_type: this.$store.state.taxonomy.account_type[0].id,
                    name: ''
                };
            },
            initReportParams: function () {
                this.reportParams = {
                    account_type: this.$store.state.taxonomy.account_type[0].id,
                    report_type: this.$store.state.taxonomy.report_type[0].id,
                    name: '',
                    image: {
                        attachment: {}
                    },
                    description: '',
                    captcha: ''
                };
                this.doCaptcha();
            },
            doCaptcha: function () {
                this.captcha_src = api.captcha + '?' + Date.parse(new Date());
            },
            doSearch: function () {
                this.$router.push({
                    name: 'search',
                    params: {account_type: this.searchParams.account_type, name: this.searchParams.name}
                });

                // axios.post(api.indexSearch, self.searchParams).then(function (res) {
                //     self.$store.commit('searchResult', res.data.data);
                //     self.$router.push('/search');
                // }).catch(function () {
                //     self.$router.push('/');
                // });
            },
            report: function () {
                $("#report-dialog").modal('show');
            },
            doReport: function () {
                let self = this;
                axios.post(api.indexReport, self.reportParams).then(function () {
                    self.initReportParams();
                    self.$message.success('成功');
                    $("#report-dialog").modal('hide');
                }).catch(function () {
                    self.doCaptcha();
                });
            },
            uploadImage: function (item) {
                let inputFile = $('.input-file');
                inputFile.data('target', item).click();
            },
            uploadChange: function () {
                let self = this;
                let formData = new FormData();
                let inputFile = $('.input-file');
                formData.append('file', inputFile.get(0).files[0]);
                axios.post(api.uploadOss, formData, {
                    headers: {'Content-Type': 'multipart/form-data'}
                }).then(function (res) {
                    self.$message.success('成功');
                    inputFile.data('target')['attachment'] = res.data.data;
                    inputFile.val('');
                }).catch(function () {
                    inputFile.val('');
                });
            },
        }
    }
</script>

<style scoped>
    .search {
        background-color: #f5f5f5;
        line-height: 60px;
        font-size: 16px;
    }

    .search > div:first-child > * {
        margin: 0 5px;
        height: 35px;
    }

    .search select {
        width: 100px;
    }

    .search input[name=name] {
        width: 250px;
    }

    .search button {
        width: 80px;
    }

    .member-num {
        font-size: 16px;
        font-weight: 600;
    }

    .member-num span {
        color: green;
    }

    .report-num {
        font-size: 16px;
        font-weight: 600;
    }

    .report-num span {
        color: red;
    }

    .report-data {
        font-size: 16px;
    }

    .article-data > div {
        margin: 5px 0;
    }

    .article-data > div > div {
        border: 1px solid #9d9d9d;
        border-radius: 3px;
        height: 160px;
    }

    .article-data p {
        padding: 5px 10px;
        height: 32px;
    }

    .article-data p:first-child {
        background-color: #f5f5f5;
    }

    .article-data p:first-child > a {
        float: right;
    }

    .article-data .article-title {
        display: inline-block;
        width: 370px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .report-form {
        background-color: #f5f5f5;
        line-height: 60px;
    }

    .report-form > div > * {
        margin: 0 5px;
        height: 35px;
    }

    .report-form select {
        width: 100px;
    }

    .report-form input[name=name] {
        width: 200px;
    }

    .report-form input[name=captcha] {
        width: 100px;
    }
</style>