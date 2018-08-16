<template>
    <div class="article">
        <div class="row text-center"><h3>{{taxonomy}}</h3></div>

        <div class="row" v-for="(item,index) in dataList.data" :key="index">
            <div class="col-md-10">
                <a :href="item.url">{{item.title}}</a>
            </div>
            <div class="col-md-2">
                <span class="pull-right">{{item.updated_at.split(' ')[0]}}</span>
            </div>
        </div>

        <div class="row">
            <div class="pull-right">
                <el-pagination layout="prev, pager, next"
                               :total="dataList.meta.total"
                               :page-size="dataList.meta.per_page"
                               @current-change="paginate"></el-pagination>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "article-list",
        data: function () {
            return {
                taxonomy: '',
                dataList: {
                    data: [],
                    meta: {}
                },
                search: {}
            }
        },
        computed: {
            articleType: function () {
                return this.$store.state.taxonomy.article_type;
            }
        },
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
                let id = this.$route.params.id;
                this.taxonomy = _.find(this.articleType, function (item) {
                    return item.id == id;
                }).name;
                this.search.id = id;
                axios.get(api.indexArticleList, {params: this.search}).then(function (res) {
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
    .article {
        padding: 10px 20px;
    }
</style>