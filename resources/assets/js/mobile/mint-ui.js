import Vue from 'vue'
import MintUI from 'mint-ui'
import 'mint-ui/lib/style.css'

Vue.use(MintUI)

import {Toast} from 'mint-ui';

Vue.prototype.$message = {};
Vue.prototype.$message.error = Toast;
Vue.prototype.$message.success = Toast;