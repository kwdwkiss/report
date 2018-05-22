<template>
    <div class="article">
        <h2 class="text-center">{{articleData.title}}</h2>
        <div class="text-center">{{articleData.updated_at.split(' ')[0]}}</div>
        <hr>
        <div class="article-content" v-html="articleData.content"></div>
    </div>
</template>

<script>
    export default {
        name: "article-detail",
        data: function () {
            return {
                articleData: {
                    updated_at: ''
                }
            };
        },
        props: ['id'],
        created: function () {
            this.loadData();
        },
        watch: {
            '$route'(to, from) {
                this.loadData();
            }
        },
        methods: {
            loadData: function () {
                let self = this;
                axios.get(api.indexArticleShow, {params: {id: this.id}}).then(function (res) {
                    self.articleData = res.data.data;
                });
            }
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
</style>
<style>
    .article-content img {
        max-width: 100%;
    }
</style>