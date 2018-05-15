<template>
    <div>
        <top-ad></top-ad>

        <my-logo></my-logo>

        <my-notice></my-notice>

        <div class="row hidden-xs dashboard">
            <div class="col-sm-4">
                <p>网站会员：<span class="text-success">{{page.auth_member_num}}</span></p>
            </div>
            <div class="col-sm-4">
                <p>恶意账号：<span class="text-warning">{{page.report_num}}</span></p>
            </div>
            <div class="col-sm-4">
                <p>最新举报：<span class="text-danger">{{page.last_24_report_num}}</span></p>
            </div>
        </div>

        <div class="row search">
            <div class="hidden-xs hidden-sm col-md-2">
                <label><a class="text-primary" href="/#/">账号查询</a></label>
            </div>
            <div class="col-xs-12 col-md-6">
                <input class="form-control" v-model="searchParams.name" name="name" type="text" placeholder="请输入账号">
            </div>
            <div class="col-xs-6 col-md-2">
                <button @click="doSearch" class="form-control btn btn-success">查询</button>
            </div>
            <div class="col-xs-6 col-md-2">
                <button @click="report" class="form-control btn btn-danger">投诉举报</button>
            </div>
        </div>

        <div class="modal fade" id="report-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title">投诉举报</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" role="form">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">账号类型</label>
                                <div class="col-sm-9">
                                    <select v-model="reportParams.account_type" name="account_type"
                                            class="form-control">
                                        <option v-for="(item,index) in $store.state.taxonomy.account_type" :value="item.id" :key="index">
                                            {{item.name}}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">投诉账号</label>
                                <div class="col-sm-9">
                                    <input v-model="reportParams.name" name="name" type="text" class="form-control"
                                           placeholder="投诉账号">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">投诉类型</label>
                                <div class="col-sm-9">
                                    <select v-model="reportParams.report_type" name="report_type" class="form-control">
                                        <option v-for="(item,index) in $store.state.taxonomy.report_type" :value="item.id" :key="index">
                                            {{item.name}}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">上传图片</label>
                                <div class="col-sm-9">
                                    <button @click="uploadImage(reportParams.image)" type="button"
                                            class="btn btn-primary">上传图片
                                    </button>
                                    <input class="input-file" style="display: none" type="file" @change="uploadChange">
                                </div>
                                <div class="col-sm-offset-3 col-sm-9">
                                    <img :src="reportParams.image.attachment.url" alt="" style="max-height: 200px">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">情况描述</label>
                                <div class="col-sm-9">
                                    <textarea cols="30" rows="4" class="form-control" placeholder="如实描述举报内容，恶意举报将封停账号"
                                              v-model="reportParams.description"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">验证码</label>
                                <div class="col-sm-6">
                                    <input v-model="reportParams.captcha" name="captcha" type="text"
                                           class="form-control" placeholder="请输入验证码">
                                </div>
                                <div class="col-sm-3">
                                    <img :src="captcha_src" alt="" @click="doCaptcha">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <button type="button" class="btn btn-danger" @click="doReport">提交</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <router-view></router-view>

        <middle-ad></middle-ad>

        <article-data></article-data>

        <bottom-ad></bottom-ad>
    </div>
</template>

<script>
export default {
  name: "index",
  computed: {
    page: function() {
      return this.$store.state.page;
    },
    user: function() {
      return this.$store.state.user;
    }
  },
  data: function() {
    return {
      searchParams: {},
      reportParams: {},
      captcha_src: api.captcha + "?" + Date.parse(new Date())
    };
  },
  created: function() {
    this.initSearchParams();
    this.initReportParams();
  },
  methods: {
    initSearchParams: function() {
      this.searchParams = { name: "" };
    },
    initReportParams: function() {
      this.reportParams = {
        account_type: this.$store.state.taxonomy.account_type[0].id,
        report_type: this.$store.state.taxonomy.report_type[0].id,
        name: "",
        image: {
          attachment: {}
        },
        description: "",
        captcha: ""
      };
      this.doCaptcha();
    },
    doCaptcha: function() {
      this.captcha_src = api.captcha + "?" + Date.parse(new Date());
    },
    doSearch: function() {
      let self = this;
      let account_type = this.searchParams.account_type;
      let name = this.searchParams.name;
      this.$store.commit("searchResult", {
        account_type: account_type,
        name: name,
        callback: function() {
          self.$store.commit("user");
          self.$router.push("/search");
        }
      });
    },
    report: function() {
      $("#report-dialog").modal("show");
    },
    doReport: function() {
      let self = this;
      axios
        .post(api.indexReport, self.reportParams)
        .then(function() {
          self.initReportParams();
          self.$message.success("成功");
          $("#report-dialog").modal("hide");
        })
        .catch(function() {
          self.doCaptcha();
        });
    },
    uploadImage: function(item) {
      let inputFile = $(".input-file");
      inputFile.data("target", item).click();
    },
    uploadChange: function() {
      let self = this;
      let formData = new FormData();
      let inputFile = $(".input-file");
      formData.append("file", inputFile.get(0).files[0]);
      axios
        .post(api.uploadOss, formData, {
          headers: { "Content-Type": "multipart/form-data" }
        })
        .then(function(res) {
          self.$message.success("成功");
          inputFile.data("target")["attachment"] = res.data.data;
          inputFile.val("");
        })
        .catch(function() {
          inputFile.val("");
        });
    }
  }
};
</script>

<style scoped>
.dashboard {
  padding: 10px 0;
  font-size: 20px;
  font-weight: bold;
  text-align: center;
}

.search > div {
  margin: 10px 0;
}

.search {
  text-align: center;
}

.search label {
  margin: 0;
  line-height: 36px;
  font-size: 20px;
  font-weight: bold;
  text-align: center;
}
</style>