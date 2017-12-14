<template>
    <div class="article">
        <h2 class="title">{{articleData.title}}</h2>
        <div class="article-bar">{{articleData.updated_at}}</div>
        <hr>
        <div v-html="articleData.content"></div>
    </div>
</template>

<script>
    export default {
        name: "article",
        data: function () {
            return {
                articleData: {}
            };
        },
        mounted: function () {
            let self = this;
            axios.get(api.indexArticleShow, {params: {id: this.$route.params.id}}).then(function (res) {
                self.articleData = res.data.data;
            });
        }
    }
</script>

<style scoped>
    .article {
        margin: 20px auto;
        padding: 10px 20px;
        border: 1px solid #d4d4d4;
        border-radius: 4px;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, .05);
    }

    hr {
        margin: 10px auto;
    }

    .title, .article-bar {
        text-align: center;
    }
</style>