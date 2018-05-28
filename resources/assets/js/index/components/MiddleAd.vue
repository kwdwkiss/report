<template>
    <div class="row hidden-xs hidden-sm ad">
        <div class="col-xs-6" v-for="(item,index) in page.ad_third" :key="index">
            <a target="_blank" :href="item.url" @click="behaviorLog(item)">
                <img :src="item.img_src">
            </a>
        </div>
    </div>
</template>

<script>
    export default {
        name: "MiddleAd",
        computed: {
            page: function () {
                return this.$store.state.page;
            }
        },
        methods: {
            behaviorLog: function (item) {
                axios.post(api.behaviorLog, {type: 1, content: item.url}).then(function (res) {

                });
            }
        }
    }
</script>

<style scoped>
    .ad img {
        width: 100%;
        height: 80px;
    }
</style>