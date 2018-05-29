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
        created: function () {
            let self = this;
            let cookie = VueCookie.get('popWindow');
            if (!cookie && this.$route.name !== 'check_tb') {
                axios.get(api.indexPopWindow).then(function (res) {
                    self.notification = res.data.data;
                    if (self.notification.title !== '' && self.notification.content !== '') {
                        $('.pop-window-dialog').modal('show');
                        VueCookie.set('popWindow', true, 1);
                    }
                });
            }
        },
    }
</script>

<style scoped>

</style>