<template>
    <div v-if="user">
        <div class="row hidden-xs hidden-sm ad">
            <div class="col-md-6" v-for="(item, index) in page.ad_top" :key="index">
                <a target="_blank" :href="item.url">
                    <img :src="item.img_src">
                </a>
            </div>
        </div>

        <div class="row hidden-xs hidden-sm ad">
            <div class="col-md-3" v-for="(item, index) in page.ad_second" :key="index">
                <a target="_blank" :href="item.url">
                    <img :src="item.img_src">
                </a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
  name: "TopAd",
  computed: {
    user: function() {
      return this.$store.state.user;
    },
    page: function() {
      return this.$store.state.page;
    }
  }
};
</script>

<style scoped>
.ad img {
  width: 100%;
  height: 80px;
}
</style>