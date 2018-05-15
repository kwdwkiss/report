<template>
    <div class="row hidden-xs hidden-sm ad">
        <div class="col-xs-6" v-for="(item,index) in page.ad_third" :key="index">
            <a target="_blank" :href="item.url">
                <img :src="item.img_src">
            </a>
        </div>
    </div>
</template>

<script>
    export default {
        name: "MiddleAd",
        computed:{
            page:function(){
                return this.$store.state.page;
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