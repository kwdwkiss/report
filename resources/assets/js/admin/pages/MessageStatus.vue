<template>
    <iframe src="/horizon" frameborder="0" :height="height" :width="width" style="display:block;border:0"></iframe>
</template>

<script>
    export default {
        name: "MessageStatus",
        data: function () {
            return {
                height: '100%',
                width: '100%',
            }
        },
        mounted: function () {
            this.height = window.innerHeight - 51;
        }
    }
</script>

<style scoped>

</style>