<template>
    <div>
        <div class="row title"><h1>{{articleData.title}}</h1></div>
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
    .title {
        margin: 20px auto;
        text-align: center;
    }
</style>