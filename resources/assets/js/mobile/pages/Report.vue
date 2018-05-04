<template>
    <div>
        <div class="row">
            <div class="col-xs-12">账号类型</div>
            <div class="col-xs-12">
                <input class="form-control" type="text" placeholder="请输入手机号" v-model="form.mobile">
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">投诉账号</div>
            <div class="col-xs-12">
                <input class="form-control" type="password" placeholder="请输入密码" v-model="form.password">
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">投诉类型</div>
            <div class="col-xs-12">
                <input class="form-control" type="text" placeholder="请输入手机号" v-model="form.mobile">
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">上传图片</div>
            <div class="col-xs-12">
                <input class="form-control" type="text" placeholder="请输入手机号" v-model="form.mobile">
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">情况描述</div>
            <div class="col-xs-12">
                <input class="form-control" type="text" placeholder="请输入手机号" v-model="form.mobile">
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <button class="btn btn-danger col-xs-12" @click="report">举报</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Report",
        data: function () {
            return {
                form: {}
            }
        },
        methods: {
            report: function () {

            }
        }
    }
</script>

<style scoped>

</style>