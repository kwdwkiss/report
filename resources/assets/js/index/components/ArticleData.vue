<template>
    <div class="row hidden-xs hidden-sm article-data">
        <div class="col-xs-6" v-for="(item,index) in page.article_data" :key="index">
            <div>
                <p>{{item.type}}<a class="pull-right" :href="item.url">更多</a></p>
                <p v-for="(subItem,subIndex) in item.data" :key="index+'-'+subIndex">
                    <a class="article-title" target="_blank" :href="subItem.url">{{subItem.title}}</a>
                    <span class="pull-right">{{subItem.created_at}}</span>
                </p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
  name: "ArticleData",
  computed: {
    page: function() {
      return this.$store.state.page;
    }
  }
};
</script>

<style scoped>
.article-data > div {
  margin: 5px 0;
}

.article-data > div > div {
  border: 1px solid #9d9d9d;
  border-radius: 3px;
  height: 160px;
}

.article-data p {
  padding: 5px 10px;
  height: 32px;
}

.article-data .article-title {
  display: inline-block;
  width: 360px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>