import Vue from 'vue'
import 'element-ui/lib/theme-chalk/index.css'
import {
    Message,
    MessageBox,
    Pagination,
} from 'element-ui'

Vue.component(Message.name, Message)
Vue.component(MessageBox.name, MessageBox)
Vue.component(Pagination.name,Pagination)

Vue.prototype.$msgbox = MessageBox
Vue.prototype.$alert = MessageBox.alert
Vue.prototype.$confirm = MessageBox.confirm
Vue.prototype.$prompt = MessageBox.prompt
Vue.prototype.$message = Message