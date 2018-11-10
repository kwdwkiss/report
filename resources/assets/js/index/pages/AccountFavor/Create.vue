<template>
    <div>
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">账号点赞</div>
                <div class="panel-body">
                    <div class="col-md-offset-2 col-md-8">
                        <form class="form-horizontal" role="form">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">账号类型</label>
                                <div class="col-sm-9">
                                    <select v-model="form.account_type" name="account_type" class="form-control">
                                        <option v-for="(item,index) in accountTypes" :value="item.id" :key="index">
                                            {{item.name}}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">点赞账号</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" placeholder="请输入账号名称"
                                           v-model="form.account_name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">点赞描述</label>
                                <div class="col-sm-9">
                                    <div class="row">
                                        <span v-for="item in descriptions">
                                            <a href="javascript:" @click="addDescription(item)" style="color:blue">
                                                {{item}}&nbsp;
                                            </a>
                                        </span>
                                    </div>
                                    <textarea cols="30" rows="4" class="form-control" placeholder="请输入点赞描述"
                                              v-model="form.description">
                                </textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <a href="javascript:" class="btn btn-default" @click="go('index')">返回</a>
                                    <a href="javascript:" class="btn btn-primary" @click="doFavor">提交</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row hidden-md hidden-lg hidden-sm">
            <a :href="page.mobile_ad.favor_create.url">
                <img :src="page.mobile_ad.favor_create.img_src" style="width: 100%;max-height: 100px">
            </a>
        </div>
    </div>
</template>

<script>
    export default {
        name: "UserMerchant",
        data: function () {
            return {
                descriptions: ['认真负责', '好评及时', '诚信靠谱', '认真截图', '收菜及时', '垫付安全'],
                form: {account_type: 203, description: ''}
            }
        },
        computed: {
            page: function () {
                return this.$store.state.page;
            },
            user: function () {
                return this.$store.state.user;
            },
            accountTypes: function () {
                return this.$store.state.taxonomy.account_type;
            }
        },
        methods: {
            doFavor: function () {
                let self = this;
                axios.post(api.userAccountFavorCreate, self.form).then(function (res) {
                    self.$message.success(res.data.message);
                    self.$store.commit("user");
                    self.go('index');
                });
            },
            go: function (name) {
                this.$router.push({name: name});
            },
            addDescription: function (item) {
                this.form.description += item + ' ';
            }
        }
    }
</script>

<style scoped>

</style>