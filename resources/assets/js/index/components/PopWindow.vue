<template>
</template>

<script>
    import VueCookie from 'vue-cookie';

    export default {
        name: "pop-window",
        data: function () {
            return {
                data: {
                    title: '',
                    content: ''
                }
            };
        },
        created: function () {
            this.loadData();
        },
        watch: {
            data: function (data) {
                let cookie = VueCookie.get('popWindow');
                if (!cookie && data.title !== '' && data.content !== '') {
                    this.$alert(data.content, data.title, {
                        confirmButtonText: '确定',
                        dangerouslyUseHTMLString: true,
                        callback: action => {
                        }
                    });
                    VueCookie.set('popWindow', true, 1);
                }
            }
        },
        methods: {
            loadData: function () {
                let self = this;
                axios.get(api.indexPopWindow).then(function (res) {
                    self.data = res.data.data;
                })
            }
        }
    }
</script>

<style scoped>

</style>