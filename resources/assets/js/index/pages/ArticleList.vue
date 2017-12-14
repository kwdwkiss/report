<template>
    <div class="article">
        <div class="row title"><h3>{{taxonomy}}</h3></div>

        <div class="row">
            <ul>
                <li v-for="item in dataList.data">
                    <a :href="item.url">{{item.title}}</a>
                    <span class="pull-right">{{item.updated_at}}</span>
                </li>
            </ul>
        </div>

        <hr>

        <div class="row">
            <el-pagination layout="prev, pager, next"
                           :total="dataList.meta.total"
                           :page-size="dataList.meta.per_page"
                           @current-change="paginate"></el-pagination>
        </div>
    </div>
</template>

<script>
    export default {
        name: "article-list",
        data: function () {
            return {
                taxonomy: '',
                articleCateList: [],
                dataList: {
                    data: [],
                    meta: {}
                }
            }
        },
        mounted: function () {
            this.loadData();
        },
        watch: {
            '$route'(to, from) {
                this.loadData();
            }
        },
        methods: {
            loadTaxonomy: function () {
                let id = this.$route.params.id;
                for (let i in store.state.taxonomy.article_type) {
                    if (store.state.taxonomy.article_type[i].id == id) {
                        this.taxonomy = store.state.taxonomy.article_type[i].name;
                        break;
                    }
                }
            },
            loadData: function () {
                let self = this;
                self.loadTaxonomy();
                axios.get(api.indexArticleList, {params: {id: this.$route.params.id}}).then(function (res) {
                    self.dataList = res.data;
                });
            },
            paginate: function (page) {
                this.search.page = page;
                this.loadData();
            },
        }
    }
</script>

<style scoped>
    hr {
        margin: 10px auto;
    }

    .article {
        padding: 10px 20px;
        font-size: 16px;
    }

    .title {
        text-align: center;
    }

    .el-pagination {
        float: right;
    }
</style>