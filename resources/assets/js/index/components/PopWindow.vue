<template>
    <div class="modal fade pop-window-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title">{{notification.title}}</h4>
                </div>
                <div class="modal-body">
                    <p v-html="notification.content"></p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import VueCookie from 'vue-cookie';

    export default {
        name: "pop-window",
        data: function () {
            return {
                notification: {
                    title: '',
                    content: ''
                }
            };
        },
        mounted: function () {
            this.popWindow(this.$route);
        },
        watch: {
            '$route'(to, from) {
                this.popWindow(to);
            }
        },
        methods: {
            popWindow: function (route) {
                let self = this;
                let cookie = VueCookie.get('popWindow');
                if (!cookie && ['checkTb', 'checkPdd', 'checkJd'].indexOf(route.name) === -1) {
                    axios.get(api.indexPopWindow).then(function (res) {
                        self.notification = res.data.data;
                        if (self.notification.title !== '' && self.notification.content !== '') {
                            $('.pop-window-dialog').modal('show');
                        }
                        VueCookie.set('popWindow', true, 1);
                    });
                }
            }
        }
    }
</script>

<style scoped>

</style>