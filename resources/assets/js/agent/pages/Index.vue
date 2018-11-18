<template>
    <div>
        <div class="article" v-for="item in data">
            <h3 class="text-center">{{item.title}}</h3>
            <div class="text-center">{{item.updated_at.split(' ')[0]}}</div>
            <hr>
            <div class="article-content" v-html="item.content"></div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Index",
        data: function () {
            return {
                data: [],
                form: {updated_at: ''}
            }
        },
        created: function () {
            this.loadData();
        },
        methods: {
            loadData: function () {
                let self = this;
                axios.get(api.agentAdminArticleShowLast).then(function (res) {
                    if (res.data.data) {
                        self.data = res.data.data;
                    }
                });
            },
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