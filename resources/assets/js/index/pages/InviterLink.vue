<template>
    <div class="row" v-if="user">
        <div class="panel panel-default">
            <div class="panel-heading">推广赚积分</div>
            <div class="panel-body">
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">推广福利</label>
                        <div class="col-sm-6 form-control-static">
                            <p>1、邀请朋友注册，推广者获得100积分。</p>
                            <p>2、被邀请人充值积分，推广者获得充值积分的10%。（即将上线）</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">推广链接</label>
                        <div class="col-sm-6">
                            <input class="form-control" disabled="disabled" type="text" :value="url">
                        </div>
                        <div class="col-sm-3">
                            <button class="btn btn-primary btn-copy" :data-clipboard-text="url">复制链接</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">推广二维码</label>
                        <div class="col-sm-9">
                            <img class="qrcode-img" :src="qrcode" alt="">
                            <div class="hidden link-qrcode"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import Clipboard from 'clipboard';

    export default {
        name: "InviterLink",
        data: function () {
            return {
                url: '',
                qrcode: '',
            }
        },
        computed: {
            user: function () {
                return this.$store.state.user;
            },
        },
        mounted: function () {
            let self = this;
            let clipboard = new Clipboard('.btn-copy');
            clipboard.on('success', function (e) {
                e.clearSelection();
                self.$message.success('复制成功');
            });

            this.url = location.origin + '/#/register?inviter=' + this.user.mobile;
            let canvas = $('.link-qrcode').qrcode({width: 128, height: 128, text: this.url}).find('canvas');
            this.qrcode = canvas.get(0).toDataURL('image/jpg');
        }
    }
</script>

<style scoped>

</style>