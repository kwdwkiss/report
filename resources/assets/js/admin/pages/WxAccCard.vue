<template>
    <el-tabs value="first">
        <el-tab-pane label="公众号卡券" name="first">
            <el-row>
                <el-button type="primary" @click="dialogPull.multi=true;dialogPull.display=true">拉取卡券</el-button>
                <el-button type="primary" @click="dialogDisplay.multi=true;dialogDisplay.display=true">批量展示</el-button>
                <el-pagination layout="prev, pager, next" :total="cardList.meta.total"
                               :page-size="cardList.meta.per_page" @current-change="pageChange"></el-pagination>
            </el-row>
            <el-row>
                <el-table ref="cardTable" :data="cardList.data" stripe @selection-change="multiSelect">
                    <el-table-column type="selection" width="50"></el-table-column>
                    <el-table-column prop="title" label="卡券名" width="150"></el-table-column>
                    <el-table-column prop="card_type_label" label="卡券类型" width="100"></el-table-column>
                    <el-table-column prop="brand_name" label="商户" width="150"></el-table-column>
                    <el-table-column prop="_wx_acc.nick_name" label="所属公众号" width="150"></el-table-column>
                    <el-table-column label="操作">
                        <template slot-scope="scope">
                            <el-button type="primary" @click="dialogDisplay.scope=scope;dialogDisplay.display=true">
                                添加展示
                            </el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </el-row>
            <el-dialog title="拉取卡券" :visible.sync="dialogPull.display">
                <el-form>
                    <el-form-item label="公众号" labelWidth="100px">
                        <el-select v-model="dialogPull.data" multiple placeholder="请选择">
                            <el-option v-for="item in wxAccList.data" :key="item.name" :label="item.nick_name"
                                       :value="item.id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                </el-form>
                <div slot="footer" class="dialog-footer">
                    <el-button @click="dialogPull.display = false">取 消</el-button>
                    <el-button type="primary" @click="cardPull">确 定</el-button>
                </div>
            </el-dialog>
            <el-dialog title="添加卡券展示" :visible.sync="dialogDisplay.display">
                <el-form>
                    <el-form-item label="卡券分类" labelWidth="100px">
                        <el-select v-model="dialogDisplay.card_cate_id" placeholder="请选择">
                            <el-option v-for="item in cateList.data" :key="item.name" :label="item.name"
                                       :value="item.id">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="卡券排序" labelWidth="100px">
                        <el-input v-model="dialogDisplay.order" placeholder="排序为大于0的整数"></el-input>
                    </el-form-item>
                </el-form>
                <div slot="footer" class="dialog-footer">
                    <el-button @click="dialogDisplay.display = false">取 消</el-button>
                    <el-button type="primary" @click="displayCreate">确 定</el-button>
                </div>
            </el-dialog>
        </el-tab-pane>

        <el-tab-pane label="卡券展示" name="second">
            <el-row>
                <el-button type="primary">添加</el-button>
                <el-button type="warning" @click="displayUpdate">更新排序</el-button>
                <el-button type="danger" @click="dialogDisplayDelete.multi=true;dialogDisplayDelete.display=true">批量删除
                </el-button>
                <el-select v-model="dialogDisplay.card_cate_id" placeholder="卡券分类" @change="displaySearch">
                    <el-option label="卡券分类" value=""></el-option>
                    <el-option v-for="item in cateList.data" :key="item.name" :label="item.name" :value="item.id">
                    </el-option>
                </el-select>
                <el-pagination layout="prev, pager, next" :total="displayList.meta.total"
                               :page-size="displayList.meta.per_page"
                               @current-change="displayPage"></el-pagination>
            </el-row>
            <el-row>
                <el-table ref="displayTable" :data="displayList.data" stripe @selection-change="displayMultiSelect">
                    <el-table-column type="selection" width="50"></el-table-column>
                    <el-table-column prop="_card_cate.name" label="分类" width="100"></el-table-column>
                    <el-table-column label="排序" width="100">
                        <template slot-scope="scope">
                            <el-input v-model="scope.row.order"></el-input>
                        </template>
                    </el-table-column>
                    <el-table-column prop="_wx_card.title" label="卡券名" width="150"></el-table-column>
                    <el-table-column prop="_wx_card.card_type_label" label="卡券类型" width="100"></el-table-column>
                    <el-table-column prop="_wx_card.brand_name" label="商户" width="150"></el-table-column>
                    <el-table-column label="操作">
                        <template slot-scope="scope">
                            <el-button type="danger"
                                       @click="dialogDisplayDelete.scope=scope;dialogDisplayDelete.display=true">删除
                            </el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </el-row>
            <el-dialog title="删除卡券展示" :visible.sync="dialogDisplayDelete.display">
                <label>是否删除卡券展示？</label>
                <div slot="footer" class="dialog-footer">
                    <el-button @click="dialogDisplayDelete.display = false">取 消</el-button>
                    <el-button type="primary" @click="displayDelete">确 定</el-button>
                </div>
            </el-dialog>
        </el-tab-pane>

        <el-tab-pane label="卡券分类" name="third">
            <el-row>
                <el-button type="primary" @click="dialog.display=true">添加分类</el-button>
                <el-pagination layout="prev, pager, next" :total="cateList.meta.total"
                               :page-size="cateList.meta.per_page"
                               @current-change="catePage"></el-pagination>
            </el-row>
            <el-row>
                <el-table :data="cateList.data" stripe>
                    <el-table-column prop="name" label="卡券分类名" width="200"></el-table-column>
                    <el-table-column label="是否显示" width="200">
                        <template slot-scope="scope">
                            <el-switch v-model="scope.row.display+''" active-value="1"
                                       inactive-value="0"
                                       @change="cateShowOrHide(scope.row)">
                            </el-switch>
                        </template>
                    </el-table-column>
                    <el-table-column label="操作">
                        <template slot-scope="scope">
                            <el-button type="warning" @click="dialogUpdate.scope=scope;dialogUpdate.display=true">修改
                            </el-button>
                            <el-button type="danger" @click="dialogDelete.scope=scope;dialogDelete.display=true">删除
                            </el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </el-row>
            <el-dialog title="添加卡券分类" :visible.sync="dialog.display">
                <el-input v-model="dialog.data" placeholder="请输入卡券分类名称（不能重复）"></el-input>
                <div slot="footer" class="dialog-footer">
                    <el-button @click="dialog.display = false">取 消</el-button>
                    <el-button type="primary" @click="cateCreate">确 定</el-button>
                </div>
            </el-dialog>

            <el-dialog title="修改卡券分类" :visible.sync="dialogUpdate.display">
                <el-input v-model="dialogUpdate.data" placeholder="请输入卡券分类名称（不能重复）"></el-input>
                <div slot="footer" class="dialog-footer">
                    <el-button @click="dialogUpdate.display = false">取 消</el-button>
                    <el-button type="primary" @click="cateUpdate">确 定</el-button>
                </div>
            </el-dialog>

            <el-dialog title="删除卡券分类" :visible.sync="dialogDelete.display">
                <label>是否删除卡券分类？</label>
                <div slot="footer" class="dialog-footer">
                    <el-button @click="dialogDelete.display = false">取 消</el-button>
                    <el-button type="primary" @click="cateDelete">确 定</el-button>
                </div>
            </el-dialog>
        </el-tab-pane>
    </el-tabs>
</template>
<script>
    export default {
        data: function () {
            return {
                dialog: {
                    display: false,
                    data: '',
                },
                dialogPull: {
                    display: false,
                    data: [],
                },
                dialogUpdate: {
                    display: false,
                    data: '',
                },
                dialogDelete: {
                    display: false,
                },
                dialogDisplay: {
                    display: false,
                    card_cate_id: '',
                    order: 0,
                },
                dialogDisplayDelete: {
                    display: false,
                },
                cardList: {
                    meta: {}
                },
                cateList: {
                    meta: {}
                },
                displayList: {
                    meta: {}
                },
                wxAccList: {}
            }
        },
        created: function () {
            this.loadWxAccList();
            this.loadCardList();
            this.loadCateList();
            this.loadDisplayList();
        },
        methods: {
            loadWxAccList() {
                let self = this;
                axios.get(api.wxAccAuthList).then(function (res) {
                    _.assign(self.wxAccList, res.data);
                })
            },
            //>>>>>>>>>>wxCard
            loadCardList(params) {
                let self = this;
                params = _.assign(self.cardList.search, params);
                axios.get(api.wxAccCardList, {params: params}).then(function (res) {
                    _.assign(self.cardList, res.data);
                });
            },
            pageChange(currentPage) {
                _.merge(this.cardList, {search: {page: currentPage}});
                this.loadCardList();
            },
            multiSelect: function (val) {
                this.cardList.select = val;
            },
            cardPull() {
                let self = this;
                axios.post(api.wxAccPullCard, {ids: this.dialogPull.data.join(',')}).then(function (res) {
                    self.dialogPull.display = false;
                    self.dialogPull.data = [];
                    self.$message.success('成功');
                    self.loadCardList();
                });
            },
            displayCreate() {
                let self = this;
                let ids = [];
                if (self.dialogDisplay.multi) {
                    ids = _.map(self.cardList.select, function (val) {
                        return val.id
                    });
                    self.$refs.cardTable.clearSelection();
                } else {
                    ids = [self.dialogDisplay.scope.row.id];
                }
                axios.post(api.cardDisplayCreate, {
                    wx_card_id: ids.join(','),
                    card_cate_id: self.dialogDisplay.card_cate_id,
                    order: self.dialogDisplay.order,
                }).then(function () {
                    self.dialogDisplay.card_cate_id = '';
                    self.dialogDisplay.order = 0;
                    self.dialogDisplay.display = false;
                    self.dialogDisplay.multi = false;
                    self.$message.success('添加成功');
                    self.loadDisplayList();
                });
            },
            //>>>>>>>>>cardDisplay
            loadDisplayList(params) {
                let self = this;
                params = _.assign(self.displayList.search, params);
                axios.get(api.cardDisplayList, {params: params}).then(function (res) {
                    _.assign(self.displayList, res.data);
                });
            },
            displayPage(currentPage) {
                _.merge(this.displayList, {search: {page: currentPage}});
                this.loadDisplayList();
            },
            displaySearch(select) {
                _.merge(this.displayList, {search: {card_cate_id: select}});
                this.loadDisplayList();
            },
            displayMultiSelect(val) {
                this.displayList.select = val;
            },
            displayUpdate() {
                let self = this;
                let data = [];
                for (let i in self.displayList.data) {
                    let item = {};
                    item.id = self.displayList.data[i].id;
                    item.order = self.displayList.data[i].order;
                    data.push(item);
                }
                axios.post(api.cardDisplayUpdate, data).then(function () {
                    self.$message.success('更新排序成功');
                    self.loadDisplayList();
                });
            },
            displayDelete() {
                let self = this;
                let ids = [];
                if (self.dialogDisplayDelete.multi) {
                    ids = _.map(self.displayList.select, function (val) {
                        return val.id
                    });
                    self.$refs.displayTable.clearSelection();
                } else {
                    ids = [self.dialogDisplayDelete.scope.row.id];
                }
                axios.post(api.cardDisplayDelete, {
                    id: ids.join(','),
                }).then(function () {
                    self.dialogDisplayDelete.display = false;
                    self.$message.success('删除成功');
                    self.loadDisplayList();
                });
                self.dialogDisplayDelete.multi = false;
            },
            //>>>>>>>>>>cardCate
            loadCateList(params) {
                let self = this;
                params = _.assign(self.cateList.search, params);
                axios.get(api.cardCateList, {params: params}).then(function (res) {
                    _.assign(self.cateList, res.data);
                });
            },
            catePage(currentPage) {
                this.loadCateList({page: currentPage});
            },
            cateCreate() {
                let self = this;
                axios.post(api.cardCateCreate, {name: self.dialog.data}).then(function () {
                    self.dialog.data = '';
                    self.dialog.display = false;
                    self.$message.success('添加卡券分类成功');
                    self.loadCateList();
                });
            },
            cateShowOrHide(row) {
                let self = this;
                let displayValue = row.display ? 0 : 1;
                axios.post(api.cardCateUpdate, {
                    id: row.id,
                    display: displayValue
                }).then(function () {
                    self.loadCateList();
                });
            },
            cateUpdate() {
                let self = this;
                axios.post(api.cardCateUpdate, {
                    id: self.dialogUpdate.scope.row.id,
                    name: self.dialogUpdate.data
                }).then(function () {
                    self.dialogUpdate.data = '';
                    self.dialogUpdate.display = false;
                    self.$message.success('修改卡券分类成功');
                    self.loadCateList();
                    self.loadDisplayList();
                });
            },
            cateDelete() {
                let self = this;
                axios.post(api.cardCateDelete, {
                    id: self.dialogDelete.scope.row.id,
                }).then(function () {
                    self.dialogDelete.display = false;
                    self.$message.success('删除卡券分类成功');
                    self.loadDisplayList();
                    self.loadCateList();
                });
            }
        }
    }
</script>
<style scoped>
    .el-row {
        margin: 10px 0;
    }

    .el-dialog .el-select {
        width: 100%;
    }
</style>