<template>
    <div class="row logo hidden-xs hidden-sm">
        <div class="col-sm-12 col-md-6 hidden-xs">
            <a href="/#/">
                <img src="/images/logo.jpg">
            </a>
        </div>
        <div class="service-qq col-xs-12 col-md-3">
            <ul>
                <li class="col-xs-12">QQ客服：</li>
                <li class="col-xs-6" v-for="(item, index) in page.service_qq" :key="index">
                    {{item.name}}
                </li>
            </ul>
        </div>
        <div class="service-wx col-xs-12 col-md-3">
            <ul>
                <li>微信<br/>客服：</li>
                <li v-for="(item, index) in page.service_wx" :key="index">
                    <img :src="item.name" alt="">
                </li>
            </ul>
        </div>
    </div>    
</template>

<script>
export default {
  name: "Logo",
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
.logo img {
  width: 100%;
  height: 80px;
}

.service-qq {
  color: green;
  font-size: 16px;
  font-weight: 600;
}

.service-wx {
  color: green;
  font-size: 16px;
  font-weight: 600;
}

.service-wx li {
  margin: 0 2px;
  float: left;
}

.service-wx img {
  height: 80px;
  width: 80px;
}
</style>