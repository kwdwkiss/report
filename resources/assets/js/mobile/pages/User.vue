<template>
    <div>
        <div v-if="user">
            <div class="row">
                <div class="col-xs-6">用户</div>
                <div class="col-xs-6">{{user.mobile}}</div>
            </div>
            <div class="row">
                <div class="col-xs-6">积分</div>
                <div class="col-xs-6">{{user._profile.amount}}</div>
            </div>
            <div class="row">
                <button class="btn btn-danger" @click="logout">退出</button>
            </div>
        </div>
        <div v-else>
            <div class="row">
                <div class="col-xs-12">
                    <button class="btn btn-success" @click="login">登录</button>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <button class="btn btn-primary " @click="register">注册</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "User",
        data: function () {
            return {};
        },
        computed: {
            user: function () {
                return this.$store.state.user;
            }
        },
        methods: {
            logout: function () {
                let self = this;
                axios.get(api.userLogout).then(function (res) {
                    self.$store.commit('user');
                });
            },
            login: function () {
                this.$router.push('/login');
            },
            register: function () {
                this.$router.push('/register');
            }
        }
    }
</script>

<style scoped>
    button {
        width: 100%;
    }
</style>