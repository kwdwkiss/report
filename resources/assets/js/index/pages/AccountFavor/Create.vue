<template>
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
</template>

<script>
    export default {
        name: "UserMerchant",
        data: function () {
            return {
                form: {account_type: 203}
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
            }
        }
    }
</script>

<style scoped>

</style>