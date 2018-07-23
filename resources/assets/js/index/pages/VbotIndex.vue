<template>
    <div>
        <button class="btn btn-primary" @click="getQrcode">获取二维码</button>
        <div class="hidden qrcode"></div>
        <img :src="qrcode" alt="">
    </div>
</template>

<script>
    export default {
        name: "VbotIndex",
        data: function () {
            return {
                qrcode: ''
            }
        },
        methods: {
            getQrcode: function () {
                let self = this;
                axios.get(api.userVbotQrcode).then(function (res) {
                    let url = res.data.data;
                    let canvas = $('.qrcode').qrcode({width: 128, height: 128, text: url}).find('canvas');
                    self.qrcode = canvas.get(0).toDataURL('image/jpg');
                })
            }
        }
    }
</script>

<style scoped>

</style>