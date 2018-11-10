<template>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">投诉举报</div>
            <div class="panel-body">
                <div class="col-md-offset-2 col-md-8">
                    <form class="form-horizontal" role="form">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">账号类型</label>
                            <div class="col-sm-9">
                                <select v-model="form.account_type" name="account_type"
                                        class="form-control">
                                    <option v-for="(item,index) in $store.state.taxonomy.account_type"
                                            :value="item.id" :key="index">
                                        {{item.name}}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">投诉账号</label>
                            <div class="col-sm-9">
                                <input v-model="form.name" name="name" type="text" class="form-control"
                                       placeholder="投诉账号">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">投诉类型</label>
                            <div class="col-sm-9">
                                <select v-model="form.report_type" name="report_type" class="form-control">
                                    <option v-for="(item,index) in $store.state.taxonomy.report_type"
                                            :value="item.id" :key="index">
                                        {{item.name}}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">上传图片1</label>
                            <div class="col-sm-9">
                                <button @click="uploadImage($event)" type="button"
                                        class="btn btn-primary">上传图片
                                </button>
                                <input class="input-file" style="display: none" type="file" @change="uploadChange"
                                       data-key="image">
                            </div>
                            <div class="col-sm-offset-3 col-sm-9">
                                <img :src="form.image.url" alt="" style="max-height: 200px">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">上传图片2</label>
                            <div class="col-sm-9">
                                <button @click="uploadImage($event)" type="button"
                                        class="btn btn-primary">上传图片
                                </button>
                                <input class="input-file" style="display: none" type="file" @change="uploadChange"
                                       data-key="image1">
                            </div>
                            <div class="col-sm-offset-3 col-sm-9">
                                <img :src="form.image1.url" alt="" style="max-height: 200px">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">上传图片3</label>
                            <div class="col-sm-9">
                                <button @click="uploadImage($event)" type="button"
                                        class="btn btn-primary">上传图片
                                </button>
                                <input class="input-file" style="display: none" type="file" @change="uploadChange"
                                       data-key="image2">
                            </div>
                            <div class="col-sm-offset-3 col-sm-9">
                                <img :src="form.image2.url" alt="" style="max-height: 200px">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">情况描述</label>
                            <div class="col-sm-9">
                                    <textarea cols="30" rows="4" class="form-control" placeholder="如实描述举报内容，恶意举报将封停账号"
                                              v-model="form.description"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">验证码</label>
                            <div class="col-sm-6">
                                <input v-model="form.captcha" name="captcha" type="text"
                                       class="form-control" placeholder="请输入验证码">
                            </div>
                            <div class="col-sm-3">
                                <img :src="captcha_src" alt="" @click="doCaptcha">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <button type="button" class="btn btn-default" @click="go('index')">返回</button>
                                <button type="button" class="btn btn-danger" @click="doReport">提交</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import lrz from 'lrz';

    export default {
        name: "AccountReport",
        data: function () {
            return {
                form: {
                    account_type: this.$store.state.taxonomy.account_type[0].id,
                    report_type: this.$store.state.taxonomy.report_type[0].id,
                    name: "",
                    image: {
                        attachment: {}
                    },
                    image1: {
                        attachment: {}
                    },
                    image2: {
                        attachment: {}
                    },
                    description: "",
                    captcha: ""
                },
                captcha_src: api.captcha + "?" + Date.parse(new Date()),
            }
        },
        computed: {
            user: function () {
                return this.$store.state.user;
            },
            accountTypes: function () {
                return this.$store.state.taxonomy.account_type;
            }
        },
        mounted: function () {
            this.initForm();
        },
        methods: {
            initForm: function () {
                this.form = {
                    account_type: this.$store.state.taxonomy.account_type[0].id,
                    report_type: this.$store.state.taxonomy.report_type[0].id,
                    name: "",
                    image: '',
                    image1: '',
                    image2: '',
                    description: "",
                    captcha: ""
                };
                this.doCaptcha();
            },
            doCaptcha: function () {
                this.captcha_src = api.captcha + "?" + Date.parse(new Date());
            },
            uploadImage: function (event) {
                $(event.target).siblings('.input-file').click();
            },
            uploadChange: function (event) {
                let self = this;
                let inputFile = event.target;
                lrz(inputFile.files[0], {height: 600, width: 600}).then(function (rst) {

                    axios.post(api.uploadOss, rst.formData, {
                        headers: {"Content-Type": "multipart/form-data"}
                    }).then(function (res) {
                        self.$message.success("成功");
                        let key = $(inputFile).data('key');
                        _.set(self.form, key, res.data.data);
                        $(inputFile).val('');
                    }).catch(function () {
                        $(inputFile).val('');
                    });
                }).catch(function (err) {
                    console.log(err);
                });

            },
            doReport: function () {
                let self = this;

                if (self.reporting) {
                    self.$message('举报中，请不要重复提交');
                    return;
                }
                self.reporting = true;

                axios.post(api.indexReport, self.form).then(function () {
                    self.initForm();
                    self.$message.success('投诉举报成功');
                    self.reporting = false;
                    self.go('index');
                }).catch(function () {
                    self.doCaptcha();
                    self.reporting = false;
                });
            },
            go: function (name) {
                this.$router.push({name: name});
            }
        }
    }
</script>

<style scoped>

</style>