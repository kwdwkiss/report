<template>
    <div class="row notice hidden-xs">
        <div>
            <span>公告：{{page.notice.title}}</span>
            <span><a :href="page.notice.moreUrl">更多</a></span>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Notice",
        computed: {
            page: function () {
                return this.$store.state.page;
            }
        }
    }
</script>

<style scoped>
    .notice {
        font-size: 16px;
        font-weight: 600;
        color: red;
    }
</style>