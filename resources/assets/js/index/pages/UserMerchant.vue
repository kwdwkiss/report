<template>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">店铺资料</div>
            <div class="panel-body">
                <div class="col-md-offset-2 col-md-8">
                    <form class="form-horizontal" role="form">
                        <div v-if="lock" class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <label class="text-warning">店铺资料锁定，修改请联系客服</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">店铺类型</label>
                            <div class="col-sm-9">
                                <select :disabled="lock" class="form-control" v-model="userMerchantForm.type">
                                    <option value="1">天猫店</option>
                                    <option value="2">企业淘宝店</option>
                                    <option value="3">个人淘宝店</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">店铺名称</label>
                            <div class="col-sm-9">
                                <input :disabled="lock" type="text" class="form-control" placeholder="请输入店铺名称"
                                       v-model="userMerchantForm.name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">商品类型</label>
                            <div class="col-sm-9">
                                <input :disabled="lock" type="text" class="form-control" placeholder="请输入商品类型"
                                       v-model="userMerchantForm.goods_type">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">店铺网址</label>
                            <div class="col-sm-9">
                                <input :disabled="lock" type="text" class="form-control" placeholder="请输入店铺网址"
                                       v-model="userMerchantForm.url">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">店铺信誉</label>
                            <div class="col-sm-9">
                                <input :disabled="lock" type="text" class="form-control" placeholder="个人淘宝店才需要填写"
                                       v-model="userMerchantForm.credit">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">掌柜旺旺</label>
                            <div class="col-sm-9">
                                <input :disabled="lock" type="text" class="form-control" placeholder="请输入掌柜旺旺号"
                                       v-model="userMerchantForm.manager">
                            </div>
                        </div>
                        <div v-if="!lock" class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <a v-if="!userMerchantForm.user_lock" class="btn btn-success"
                                   @click="doModify">提交</a>
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
                userMerchantForm: {}
            }
        },
        computed: {
            user: function () {
                return this.$store.state.user;
            },
            lock: function () {
                return _.get(this.user, '_merchant.user_lock') === 1;
            }
        },
        created: function () {
            this.userMerchantForm = _.assign({}, _.cloneDeep(this.$store.state.user._merchant));
        },
        methods: {
            doModify: function () {
                let self = this;
                axios.post(api.userMerchantModify, self.userMerchantForm).then(function (res) {
                    self.$message.success("成功");
                    self.$store.commit("user");
                });
            },
        }
    }
</script>

<style scoped>

</style>