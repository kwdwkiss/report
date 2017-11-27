webpackJsonp([1],[
/* 0 */,
/* 1 */,
/* 2 */,
/* 3 */,
/* 4 */,
/* 5 */
/***/ (function(module, exports, __webpack_require__) {

/*
  MIT License http://www.opensource.org/licenses/mit-license.php
  Author Tobias Koppers @sokra
  Modified by Evan You @yyx990803
*/

var hasDocument = typeof document !== 'undefined'

if (typeof DEBUG !== 'undefined' && DEBUG) {
  if (!hasDocument) {
    throw new Error(
    'vue-style-loader cannot be used in a non-browser environment. ' +
    "Use { target: 'node' } in your Webpack config to indicate a server-rendering environment."
  ) }
}

var listToStyles = __webpack_require__(192)

/*
type StyleObject = {
  id: number;
  parts: Array<StyleObjectPart>
}

type StyleObjectPart = {
  css: string;
  media: string;
  sourceMap: ?string
}
*/

var stylesInDom = {/*
  [id: number]: {
    id: number,
    refs: number,
    parts: Array<(obj?: StyleObjectPart) => void>
  }
*/}

var head = hasDocument && (document.head || document.getElementsByTagName('head')[0])
var singletonElement = null
var singletonCounter = 0
var isProduction = false
var noop = function () {}

// Force single-tag solution on IE6-9, which has a hard limit on the # of <style>
// tags it will allow on a page
var isOldIE = typeof navigator !== 'undefined' && /msie [6-9]\b/.test(navigator.userAgent.toLowerCase())

module.exports = function (parentId, list, _isProduction) {
  isProduction = _isProduction

  var styles = listToStyles(parentId, list)
  addStylesToDom(styles)

  return function update (newList) {
    var mayRemove = []
    for (var i = 0; i < styles.length; i++) {
      var item = styles[i]
      var domStyle = stylesInDom[item.id]
      domStyle.refs--
      mayRemove.push(domStyle)
    }
    if (newList) {
      styles = listToStyles(parentId, newList)
      addStylesToDom(styles)
    } else {
      styles = []
    }
    for (var i = 0; i < mayRemove.length; i++) {
      var domStyle = mayRemove[i]
      if (domStyle.refs === 0) {
        for (var j = 0; j < domStyle.parts.length; j++) {
          domStyle.parts[j]()
        }
        delete stylesInDom[domStyle.id]
      }
    }
  }
}

function addStylesToDom (styles /* Array<StyleObject> */) {
  for (var i = 0; i < styles.length; i++) {
    var item = styles[i]
    var domStyle = stylesInDom[item.id]
    if (domStyle) {
      domStyle.refs++
      for (var j = 0; j < domStyle.parts.length; j++) {
        domStyle.parts[j](item.parts[j])
      }
      for (; j < item.parts.length; j++) {
        domStyle.parts.push(addStyle(item.parts[j]))
      }
      if (domStyle.parts.length > item.parts.length) {
        domStyle.parts.length = item.parts.length
      }
    } else {
      var parts = []
      for (var j = 0; j < item.parts.length; j++) {
        parts.push(addStyle(item.parts[j]))
      }
      stylesInDom[item.id] = { id: item.id, refs: 1, parts: parts }
    }
  }
}

function createStyleElement () {
  var styleElement = document.createElement('style')
  styleElement.type = 'text/css'
  head.appendChild(styleElement)
  return styleElement
}

function addStyle (obj /* StyleObjectPart */) {
  var update, remove
  var styleElement = document.querySelector('style[data-vue-ssr-id~="' + obj.id + '"]')

  if (styleElement) {
    if (isProduction) {
      // has SSR styles and in production mode.
      // simply do nothing.
      return noop
    } else {
      // has SSR styles but in dev mode.
      // for some reason Chrome can't handle source map in server-rendered
      // style tags - source maps in <style> only works if the style tag is
      // created and inserted dynamically. So we remove the server rendered
      // styles and inject new ones.
      styleElement.parentNode.removeChild(styleElement)
    }
  }

  if (isOldIE) {
    // use singleton mode for IE9.
    var styleIndex = singletonCounter++
    styleElement = singletonElement || (singletonElement = createStyleElement())
    update = applyToSingletonTag.bind(null, styleElement, styleIndex, false)
    remove = applyToSingletonTag.bind(null, styleElement, styleIndex, true)
  } else {
    // use multi-style-tag mode in all other cases
    styleElement = createStyleElement()
    update = applyToTag.bind(null, styleElement)
    remove = function () {
      styleElement.parentNode.removeChild(styleElement)
    }
  }

  update(obj)

  return function updateStyle (newObj /* StyleObjectPart */) {
    if (newObj) {
      if (newObj.css === obj.css &&
          newObj.media === obj.media &&
          newObj.sourceMap === obj.sourceMap) {
        return
      }
      update(obj = newObj)
    } else {
      remove()
    }
  }
}

var replaceText = (function () {
  var textStore = []

  return function (index, replacement) {
    textStore[index] = replacement
    return textStore.filter(Boolean).join('\n')
  }
})()

function applyToSingletonTag (styleElement, index, remove, obj) {
  var css = remove ? '' : obj.css

  if (styleElement.styleSheet) {
    styleElement.styleSheet.cssText = replaceText(index, css)
  } else {
    var cssNode = document.createTextNode(css)
    var childNodes = styleElement.childNodes
    if (childNodes[index]) styleElement.removeChild(childNodes[index])
    if (childNodes.length) {
      styleElement.insertBefore(cssNode, childNodes[index])
    } else {
      styleElement.appendChild(cssNode)
    }
  }
}

function applyToTag (styleElement, obj) {
  var css = obj.css
  var media = obj.media
  var sourceMap = obj.sourceMap

  if (media) {
    styleElement.setAttribute('media', media)
  }

  if (sourceMap) {
    // https://developer.chrome.com/devtools/docs/javascript-debugging
    // this makes source maps inside style tags work properly in Chrome
    css += '\n/*# sourceURL=' + sourceMap.sources[0] + ' */'
    // http://stackoverflow.com/a/26603875
    css += '\n/*# sourceMappingURL=data:application/json;base64,' + btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap)))) + ' */'
  }

  if (styleElement.styleSheet) {
    styleElement.styleSheet.cssText = css
  } else {
    while (styleElement.firstChild) {
      styleElement.removeChild(styleElement.firstChild)
    }
    styleElement.appendChild(document.createTextNode(css))
  }
}


/***/ }),
/* 6 */
/***/ (function(module, exports) {

/* globals __VUE_SSR_CONTEXT__ */

// IMPORTANT: Do NOT use ES2015 features in this file.
// This module is a runtime utility for cleaner component module output and will
// be included in the final webpack user bundle.

module.exports = function normalizeComponent (
  rawScriptExports,
  compiledTemplate,
  functionalTemplate,
  injectStyles,
  scopeId,
  moduleIdentifier /* server only */
) {
  var esModule
  var scriptExports = rawScriptExports = rawScriptExports || {}

  // ES6 modules interop
  var type = typeof rawScriptExports.default
  if (type === 'object' || type === 'function') {
    esModule = rawScriptExports
    scriptExports = rawScriptExports.default
  }

  // Vue.extend constructor export interop
  var options = typeof scriptExports === 'function'
    ? scriptExports.options
    : scriptExports

  // render functions
  if (compiledTemplate) {
    options.render = compiledTemplate.render
    options.staticRenderFns = compiledTemplate.staticRenderFns
    options._compiled = true
  }

  // functional template
  if (functionalTemplate) {
    options.functional = true
  }

  // scopedId
  if (scopeId) {
    options._scopeId = scopeId
  }

  var hook
  if (moduleIdentifier) { // server build
    hook = function (context) {
      // 2.3 injection
      context =
        context || // cached call
        (this.$vnode && this.$vnode.ssrContext) || // stateful
        (this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext) // functional
      // 2.2 with runInNewContext: true
      if (!context && typeof __VUE_SSR_CONTEXT__ !== 'undefined') {
        context = __VUE_SSR_CONTEXT__
      }
      // inject component styles
      if (injectStyles) {
        injectStyles.call(this, context)
      }
      // register component module identifier for async chunk inferrence
      if (context && context._registeredComponents) {
        context._registeredComponents.add(moduleIdentifier)
      }
    }
    // used by ssr in case component is cached and beforeCreate
    // never gets called
    options._ssrRegister = hook
  } else if (injectStyles) {
    hook = injectStyles
  }

  if (hook) {
    var functional = options.functional
    var existing = functional
      ? options.render
      : options.beforeCreate

    if (!functional) {
      // inject component registration as beforeCreate hook
      options.beforeCreate = existing
        ? [].concat(existing, hook)
        : [hook]
    } else {
      // for template-only hot-reload because in that case the render fn doesn't
      // go through the normalizer
      options._injectStyles = hook
      // register for functioal component in vue file
      options.render = function renderWithStyleInjection (h, context) {
        hook.call(context)
        return existing(h, context)
      }
    }
  }

  return {
    esModule: esModule,
    exports: scriptExports,
    options: options
  }
}


/***/ }),
/* 7 */,
/* 8 */,
/* 9 */,
/* 10 */,
/* 11 */,
/* 12 */,
/* 13 */,
/* 14 */,
/* 15 */,
/* 16 */,
/* 17 */,
/* 18 */,
/* 19 */,
/* 20 */,
/* 21 */,
/* 22 */,
/* 23 */,
/* 24 */,
/* 25 */,
/* 26 */,
/* 27 */,
/* 28 */,
/* 29 */,
/* 30 */,
/* 31 */,
/* 32 */,
/* 33 */,
/* 34 */,
/* 35 */,
/* 36 */,
/* 37 */,
/* 38 */,
/* 39 */,
/* 40 */,
/* 41 */,
/* 42 */,
/* 43 */,
/* 44 */,
/* 45 */,
/* 46 */,
/* 47 */,
/* 48 */,
/* 49 */,
/* 50 */,
/* 51 */,
/* 52 */,
/* 53 */,
/* 54 */,
/* 55 */,
/* 56 */,
/* 57 */,
/* 58 */,
/* 59 */,
/* 60 */,
/* 61 */,
/* 62 */,
/* 63 */,
/* 64 */,
/* 65 */,
/* 66 */,
/* 67 */,
/* 68 */,
/* 69 */,
/* 70 */,
/* 71 */,
/* 72 */,
/* 73 */,
/* 74 */,
/* 75 */,
/* 76 */,
/* 77 */,
/* 78 */,
/* 79 */,
/* 80 */,
/* 81 */,
/* 82 */,
/* 83 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(84);
module.exports = __webpack_require__(236);


/***/ }),
/* 84 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_vue_router__ = __webpack_require__(59);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_element_ui__ = __webpack_require__(60);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_element_ui___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_element_ui__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_element_ui_lib_theme_chalk_index_css__ = __webpack_require__(82);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_element_ui_lib_theme_chalk_index_css___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_element_ui_lib_theme_chalk_index_css__);
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

__webpack_require__(85);



Vue.use(__WEBPACK_IMPORTED_MODULE_0_vue_router__["default"]);




Vue.use(__WEBPACK_IMPORTED_MODULE_1_element_ui___default.a);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
window.api = __webpack_require__(188);

var routes = [{ path: '/login', component: __webpack_require__(189) }, {
    path: '/admin',
    component: __webpack_require__(195),
    alias: '/',
    children: [{ path: '', component: __webpack_require__(210) }, { path: 'tag', component: __webpack_require__(215) }, { path: 'user/profile', component: __webpack_require__(220) }, { path: 'system/site', component: __webpack_require__(226) }, { path: 'system/admin', component: __webpack_require__(231) }]
}];

var router = new __WEBPACK_IMPORTED_MODULE_0_vue_router__["default"]({
    routes: routes // （缩写）相当于 routes: routes
});

axios.interceptors.response.use(function (response) {
    var errorMessage = null;

    if (response.data === '') {
        errorMessage = 'data is empty string';
    } else if (response.data.code !== 0) {
        errorMessage = response.data.message;
    }

    if (errorMessage) {
        app.$message.error(errorMessage);
        throw new Error(errorMessage);
    }
    return response;
}, function (error) {
    if (error.response) {
        if (error.response.status === 401) {
            //Unauthorized
            app.$router.push('/login');
        } else if (error.response.status === 419) {
            //csrf token invalid
            location.reload();
        } else {
            var message = error.response.data.message ? error.response.data.message : error.response.statusText;
            app.$message.error(message);
        }
    }
    return Promise.reject(error);
});

var app = new Vue({
    el: '#app',
    router: router,
    template: '<router-view></router-view>'
});

window.app = app;

/***/ }),
/* 85 */
/***/ (function(module, exports, __webpack_require__) {


window._ = __webpack_require__(49);

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
  window.$ = window.jQuery = __webpack_require__(50);

  __webpack_require__(51);
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = __webpack_require__(52);

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

var token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
  window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
  console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'your-pusher-key'
// });

window.Vue = __webpack_require__(3);

/***/ }),
/* 86 */,
/* 87 */,
/* 88 */,
/* 89 */,
/* 90 */,
/* 91 */,
/* 92 */,
/* 93 */,
/* 94 */,
/* 95 */,
/* 96 */,
/* 97 */,
/* 98 */,
/* 99 */,
/* 100 */,
/* 101 */,
/* 102 */,
/* 103 */,
/* 104 */,
/* 105 */,
/* 106 */,
/* 107 */,
/* 108 */,
/* 109 */,
/* 110 */,
/* 111 */,
/* 112 */,
/* 113 */,
/* 114 */,
/* 115 */,
/* 116 */,
/* 117 */,
/* 118 */,
/* 119 */,
/* 120 */,
/* 121 */,
/* 122 */,
/* 123 */,
/* 124 */,
/* 125 */,
/* 126 */,
/* 127 */,
/* 128 */,
/* 129 */,
/* 130 */,
/* 131 */,
/* 132 */,
/* 133 */,
/* 134 */,
/* 135 */,
/* 136 */,
/* 137 */,
/* 138 */,
/* 139 */,
/* 140 */,
/* 141 */,
/* 142 */,
/* 143 */,
/* 144 */,
/* 145 */,
/* 146 */,
/* 147 */,
/* 148 */,
/* 149 */,
/* 150 */,
/* 151 */,
/* 152 */,
/* 153 */,
/* 154 */,
/* 155 */,
/* 156 */,
/* 157 */,
/* 158 */,
/* 159 */,
/* 160 */,
/* 161 */,
/* 162 */,
/* 163 */,
/* 164 */,
/* 165 */,
/* 166 */,
/* 167 */,
/* 168 */,
/* 169 */,
/* 170 */,
/* 171 */,
/* 172 */,
/* 173 */,
/* 174 */,
/* 175 */,
/* 176 */,
/* 177 */,
/* 178 */,
/* 179 */,
/* 180 */,
/* 181 */,
/* 182 */,
/* 183 */,
/* 184 */,
/* 185 */,
/* 186 */,
/* 187 */,
/* 188 */
/***/ (function(module, exports) {

module.exports = {
    login: '/admin/login',
    logout: '/admin/logout',
    userInfo: '/admin/info',
    register: '/admin/register',
    modifyPassword: '/admin/modify-password',

    adminList: '/admin/list',
    adminCreate: '/admin/create',
    adminUpdate: '/admin/update',
    adminDelete: '/admin/delete',

    siteBasic: '/admin/site/basic',

    tagList: '/admin/tag/list',
    tagCreate: '/admin/tag/create',
    tagUpdate: '/admin/tag/update',
    tagDelete: '/admin/tag/delete',

    userProfileList: '/admin/user-profile/list',
    userProfileCreate: '/admin/user-profile/create',
    userProfileUpdate: '/admin/user-profile/update',
    userProfileDelete: '/admin/user-profile/delete'
};

/***/ }),
/* 189 */
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(190)
}
var normalizeComponent = __webpack_require__(6)
/* script */
var __vue_script__ = __webpack_require__(193)
/* template */
var __vue_template__ = __webpack_require__(194)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-42ae57bc"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/assets/js/admin/pages/Login.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {  return key !== "default" && key.substr(0, 2) !== "__"})) {  console.error("named exports are not supported in *.vue files.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-42ae57bc", Component.options)
  } else {
    hotAPI.reload("data-v-42ae57bc", Component.options)
' + '  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),
/* 190 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(191);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(5)("10b32468", content, false);
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-42ae57bc\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/sass-loader/lib/loader.js!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0&bustCache!./Login.vue", function() {
     var newContent = require("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-42ae57bc\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/sass-loader/lib/loader.js!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0&bustCache!./Login.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 191 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(4)(undefined);
// imports


// module
exports.push([module.i, "\n.form-container[data-v-42ae57bc] {\n  /*box-shadow: 0 0px 8px 0 rgba(0, 0, 0, 0.06), 0 1px 0px 0 rgba(0, 0, 0, 0.02);*/\n  border-radius: 5px;\n  -moz-border-radius: 5px;\n  background-clip: padding-box;\n  margin: 180px auto;\n  width: 350px;\n  padding: 35px 35px 15px 35px;\n  background: #fff;\n  border: 1px solid #eaeaea;\n  -webkit-box-shadow: 0 0 25px #cac6c6;\n          box-shadow: 0 0 25px #cac6c6;\n}\n.form-container .title[data-v-42ae57bc] {\n    margin: 0 auto 40px;\n    text-align: center;\n    color: #505458;\n}\n.form-container .remember[data-v-42ae57bc] {\n    margin-bottom: 22px;\n}\n.form-container .el-button[data-v-42ae57bc] {\n    width: 100%;\n}\n", ""]);

// exports


/***/ }),
/* 192 */
/***/ (function(module, exports) {

/**
 * Translates the list format produced by css-loader into something
 * easier to manipulate.
 */
module.exports = function listToStyles (parentId, list) {
  var styles = []
  var newStyles = {}
  for (var i = 0; i < list.length; i++) {
    var item = list[i]
    var id = item[0]
    var css = item[1]
    var media = item[2]
    var sourceMap = item[3]
    var part = {
      id: parentId + ':' + i,
      css: css,
      media: media,
      sourceMap: sourceMap
    }
    if (!newStyles[id]) {
      styles.push(newStyles[id] = { id: id, parts: [part] })
    } else {
      newStyles[id].parts.push(part)
    }
  }
  return styles
}


/***/ }),
/* 193 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
    data: function data() {
        return {
            loading: false,
            form: {
                username: '',
                password: ''
            },
            rules: {
                username: [{ required: true, message: '请输入账号', trigger: 'blur' }],
                password: [{ required: true, message: '请输入密码', trigger: 'blur' }]
            },
            checked: true
        };
    },

    methods: {
        login: function login() {
            var self = this;
            this.$refs.form.validate(function (valid) {
                if (valid) {
                    self.loading = true;
                    axios.post(api.login, {
                        username: self.form.username,
                        password: self.form.password
                    }).then(function (res) {
                        self.loading = false;
                        if (res.data.code === 0) {
                            app.$data.user = res.data.user;
                            app.$router.push('/');
                        }
                    }).catch(function () {
                        self.loading = false;
                    });
                }
            });
        }
    }
});

/***/ }),
/* 194 */
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "el-form",
    {
      ref: "form",
      staticClass: "form-container",
      attrs: { model: _vm.form, rules: _vm.rules }
    },
    [
      _c("h3", { staticClass: "title" }, [_vm._v("系统登录")]),
      _vm._v(" "),
      _c(
        "el-form-item",
        { attrs: { prop: "username" } },
        [
          _c("el-input", {
            attrs: { type: "text", placeholder: "账号" },
            model: {
              value: _vm.form.username,
              callback: function($$v) {
                _vm.$set(_vm.form, "username", $$v)
              },
              expression: "form.username"
            }
          })
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "el-form-item",
        { attrs: { prop: "password" } },
        [
          _c("el-input", {
            attrs: { type: "password", placeholder: "密码" },
            model: {
              value: _vm.form.password,
              callback: function($$v) {
                _vm.$set(_vm.form, "password", $$v)
              },
              expression: "form.password"
            }
          })
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "el-checkbox",
        {
          staticClass: "remember",
          attrs: { checked: "" },
          model: {
            value: _vm.checked,
            callback: function($$v) {
              _vm.checked = $$v
            },
            expression: "checked"
          }
        },
        [_vm._v("记住密码")]
      ),
      _vm._v(" "),
      _c(
        "el-form-item",
        [
          _c(
            "el-button",
            {
              attrs: { type: "primary", loading: _vm.loading },
              nativeOn: {
                click: function($event) {
                  $event.preventDefault()
                  _vm.login($event)
                }
              }
            },
            [_vm._v("登录")]
          )
        ],
        1
      )
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-42ae57bc", module.exports)
  }
}

/***/ }),
/* 195 */
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(196)
}
var normalizeComponent = __webpack_require__(6)
/* script */
var __vue_script__ = __webpack_require__(198)
/* template */
var __vue_template__ = __webpack_require__(209)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/assets/js/admin/App.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {  return key !== "default" && key.substr(0, 2) !== "__"})) {  console.error("named exports are not supported in *.vue files.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-010a9602", Component.options)
  } else {
    hotAPI.reload("data-v-010a9602", Component.options)
' + '  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),
/* 196 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(197);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(5)("9db73548", content, false);
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-010a9602\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0&bustCache!./App.vue", function() {
     var newContent = require("!!../../../../node_modules/css-loader/index.js!../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-010a9602\",\"scoped\":false,\"hasInlineConfig\":true}!../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0&bustCache!./App.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 197 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(4)(undefined);
// imports


// module
exports.push([module.i, "\n.header-wrapper {\n    width: 100%;\n}\n.body-wrapper {\n    position: absolute;\n    width: 100%;\n    top: 60px;\n    bottom: 0;\n}\n.main-sidebar {\n    height: 100%;\n    width: 180px;\n}\n.content-wrapper {\n    position: absolute;\n    top: 0;\n    left: 180px;\n    bottom: 0;\n    right: 0;\n    padding: 10px;\n}\n.el-pagination {\n    float: right;\n}\n.el-row {\n    margin: 10px 0;\n}\n", ""]);

// exports


/***/ }),
/* 198 */
/***/ (function(module, exports, __webpack_require__) {

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

Vue.component('app-header', __webpack_require__(199));
Vue.component('app-sidebar', __webpack_require__(204));

/***/ }),
/* 199 */
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(200)
}
var normalizeComponent = __webpack_require__(6)
/* script */
var __vue_script__ = __webpack_require__(202)
/* template */
var __vue_template__ = __webpack_require__(203)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-a8053478"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/assets/js/admin/components/Header.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {  return key !== "default" && key.substr(0, 2) !== "__"})) {  console.error("named exports are not supported in *.vue files.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-a8053478", Component.options)
  } else {
    hotAPI.reload("data-v-a8053478", Component.options)
' + '  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),
/* 200 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(201);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(5)("41484963", content, false);
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-a8053478\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0&bustCache!./Header.vue", function() {
     var newContent = require("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-a8053478\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0&bustCache!./Header.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 201 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(4)(undefined);
// imports


// module
exports.push([module.i, "\n.brand[data-v-a8053478] {\n    float: left;\n    width: 180px;\n    text-align: center;\n}\n.brand label[data-v-a8053478] {\n    margin-bottom: 0;\n    line-height: 60px;\n    font-size: 20px;\n    color: #bfcbd9;\n}\n.el-submenu[data-v-a8053478] {\n    float: right;\n}\n", ""]);

// exports


/***/ }),
/* 202 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
    data: function data() {
        var self = this;
        return {
            title: '系统后台',
            user: {
                name: ''
            },
            modifyPasswordVisible: false,
            form: {
                password: '',
                newPassword: '',
                newPasswordConfirm: ''
            },
            rules: {
                password: [{ required: true, message: '密码不能为空', trigger: 'blur' }],
                newPassword: [{ required: true, message: '新密码不能为空', trigger: 'blur' }, { min: 8, message: '长度不小于8', trigger: 'blur' }],
                newPasswordConfirm: [{ required: true, message: '确认密码不能为空', trigger: 'blur' }, { min: 8, message: '长度不小于8', trigger: 'blur' }, {
                    validator: function validator(rule, value, callback) {
                        if (value !== self.form.newPassword) {
                            callback(new Error('两次输入密码不一致!'));
                        } else {
                            callback();
                        }
                    }, trigger: 'blur'
                }]
            }
        };
    },
    created: function created() {
        var self = this;
        axios.get(api.userInfo).then(function (res) {
            self.user = res.data.data;
        });
    },
    methods: {
        info: function info() {},
        modifyPassword: function modifyPassword() {
            var self = this;
            this.$refs.form.validate(function (valid) {
                if (valid) {
                    axios.post(api.modifyPassword, self.form).then(function (res) {
                        self.modifyPasswordVisible = false;
                        app.$message.success('修改密码成功');
                    });
                } else {
                    return false;
                }
            });
        },
        logout: function logout() {
            axios.get(api.logout).then(function () {
                app.$router.push('/login');
            });
        }
    }
});

/***/ }),
/* 203 */
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _c(
        "el-menu",
        {
          staticClass: "el-menu-demo",
          attrs: {
            mode: "horizontal",
            "background-color": "#324157",
            "text-color": "#bfcbd9",
            "active-text-color": "#bfcbd9"
          }
        },
        [
          _c("div", { staticClass: "brand" }, [
            _c("label", [_vm._v(_vm._s(_vm.title))])
          ]),
          _vm._v(" "),
          _c(
            "el-submenu",
            { attrs: { index: "2" } },
            [
              _c("template", { slot: "title" }, [
                _vm._v(_vm._s(_vm.user.name))
              ]),
              _vm._v(" "),
              _c(
                "el-menu-item",
                {
                  attrs: { index: "2-1" },
                  on: {
                    click: function($event) {
                      _vm.modifyPasswordVisible = true
                    }
                  }
                },
                [_vm._v("修改密码")]
              ),
              _vm._v(" "),
              _c(
                "el-menu-item",
                { attrs: { index: "2-2" }, on: { click: _vm.logout } },
                [_vm._v("注销")]
              )
            ],
            2
          )
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "el-dialog",
        {
          attrs: { title: "修改密码", visible: _vm.modifyPasswordVisible },
          on: {
            "update:visible": function($event) {
              _vm.modifyPasswordVisible = $event
            }
          }
        },
        [
          _c(
            "el-form",
            {
              ref: "form",
              attrs: { model: _vm.form, rules: _vm.rules, labelWidth: "120px" }
            },
            [
              _c(
                "el-form-item",
                { attrs: { prop: "password", label: "原密码" } },
                [
                  _c("el-input", {
                    attrs: { type: "password" },
                    model: {
                      value: _vm.form.password,
                      callback: function($$v) {
                        _vm.$set(_vm.form, "password", $$v)
                      },
                      expression: "form.password"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { prop: "newPassword", label: "新密码" } },
                [
                  _c("el-input", {
                    attrs: { type: "password" },
                    model: {
                      value: _vm.form.newPassword,
                      callback: function($$v) {
                        _vm.$set(_vm.form, "newPassword", $$v)
                      },
                      expression: "form.newPassword"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { prop: "newPasswordConfirm", label: "确认密码" } },
                [
                  _c("el-input", {
                    attrs: { type: "password" },
                    model: {
                      value: _vm.form.newPasswordConfirm,
                      callback: function($$v) {
                        _vm.$set(_vm.form, "newPasswordConfirm", $$v)
                      },
                      expression: "form.newPasswordConfirm"
                    }
                  })
                ],
                1
              )
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "div",
            {
              staticClass: "dialog-footer",
              attrs: { slot: "footer" },
              slot: "footer"
            },
            [
              _c(
                "el-button",
                {
                  on: {
                    click: function($event) {
                      _vm.dialogFormVisible = false
                    }
                  }
                },
                [_vm._v("取 消")]
              ),
              _vm._v(" "),
              _c(
                "el-button",
                {
                  attrs: { type: "primary" },
                  on: { click: _vm.modifyPassword }
                },
                [_vm._v("确 定")]
              )
            ],
            1
          )
        ],
        1
      )
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-a8053478", module.exports)
  }
}

/***/ }),
/* 204 */
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(205)
}
var normalizeComponent = __webpack_require__(6)
/* script */
var __vue_script__ = __webpack_require__(207)
/* template */
var __vue_template__ = __webpack_require__(208)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-773fb4b5"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/assets/js/admin/components/Sidebar.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {  return key !== "default" && key.substr(0, 2) !== "__"})) {  console.error("named exports are not supported in *.vue files.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-773fb4b5", Component.options)
  } else {
    hotAPI.reload("data-v-773fb4b5", Component.options)
' + '  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),
/* 205 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(206);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(5)("260806fc", content, false);
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-773fb4b5\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0&bustCache!./Sidebar.vue", function() {
     var newContent = require("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-773fb4b5\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0&bustCache!./Sidebar.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 206 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(4)(undefined);
// imports


// module
exports.push([module.i, "\n.el-menu[data-v-773fb4b5] {\n    height: 100%;\n}\n.el-menu-item[data-v-773fb4b5] {\n    min-width: 180px;\n}\n", ""]);

// exports


/***/ }),
/* 207 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
    data: function data() {
        return {
            defaultActive: '/',
            menu: [{
                title: '控制面板',
                path: '/'
            }, {
                title: '标签',
                path: '/tag'
            }, {
                title: '用户资料',
                path: '/user/profile'
            }, {
                title: '系统设置',
                path: '/system',
                children: [{
                    title: '站点管理',
                    path: '/system/site'
                }, {
                    title: '管理员',
                    path: '/system/admin'
                }]
            }]
        };
    },
    mounted: function mounted() {
        this.defaultActive = this.$route.path;
    },
    methods: {}
});

/***/ }),
/* 208 */
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "el-menu",
    { attrs: { "default-active": _vm.defaultActive, router: true } },
    [
      _vm._l(_vm.menu, function(item, index) {
        return [
          item.children
            ? _c(
                "el-submenu",
                { attrs: { index: item.path } },
                [
                  _c("template", { slot: "title" }, [
                    item.icon ? _c("i", { class: item.icon }) : _vm._e(),
                    _vm._v(_vm._s(item.title) + "\n            ")
                  ]),
                  _vm._v(" "),
                  _vm._l(item.children, function(subItem, subIndex) {
                    return [
                      _c(
                        "el-menu-item",
                        { key: subIndex, attrs: { index: subItem.path } },
                        [
                          subItem.icon
                            ? _c("i", { class: subItem.icon })
                            : _vm._e(),
                          _vm._v(_vm._s(subItem.title) + "\n                ")
                        ]
                      )
                    ]
                  })
                ],
                2
              )
            : _vm._e(),
          _vm._v(" "),
          !item.children
            ? _c("el-menu-item", { attrs: { index: item.path } }, [
                item.icon ? _c("i", { class: item.icon }) : _vm._e(),
                _vm._v(_vm._s(item.title) + "\n        ")
              ])
            : _vm._e()
        ]
      })
    ],
    2
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-773fb4b5", module.exports)
  }
}

/***/ }),
/* 209 */
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", [
    _c("div", { staticClass: "header-wrapper" }, [_c("app-header")], 1),
    _vm._v(" "),
    _c("div", { staticClass: "body-wrapper" }, [
      _c("aside", { staticClass: "main-sidebar" }, [_c("app-sidebar")], 1),
      _vm._v(" "),
      _c("div", { staticClass: "content-wrapper" }, [_c("router-view")], 1)
    ])
  ])
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-010a9602", module.exports)
  }
}

/***/ }),
/* 210 */
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(211)
}
var normalizeComponent = __webpack_require__(6)
/* script */
var __vue_script__ = __webpack_require__(213)
/* template */
var __vue_template__ = __webpack_require__(214)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-47a22fe7"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/assets/js/admin/pages/Dashboard.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {  return key !== "default" && key.substr(0, 2) !== "__"})) {  console.error("named exports are not supported in *.vue files.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-47a22fe7", Component.options)
  } else {
    hotAPI.reload("data-v-47a22fe7", Component.options)
' + '  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),
/* 211 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(212);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(5)("c5c12e68", content, false);
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-47a22fe7\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0&bustCache!./Dashboard.vue", function() {
     var newContent = require("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-47a22fe7\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0&bustCache!./Dashboard.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 212 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(4)(undefined);
// imports


// module
exports.push([module.i, "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n", ""]);

// exports


/***/ }),
/* 213 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
//
//
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
    data: function data() {
        return {};
    },
    methods: {}
});

/***/ }),
/* 214 */
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _vm._m(0)
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "panel panel-default" }, [
      _c("div", { staticClass: "panel-heading" }, [
        _vm._v("Dashboard Component")
      ]),
      _vm._v(" "),
      _c("div", { staticClass: "panel-body" }, [
        _vm._v("\n        I'm an dashboard component!\n    ")
      ])
    ])
  }
]
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-47a22fe7", module.exports)
  }
}

/***/ }),
/* 215 */
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(216)
}
var normalizeComponent = __webpack_require__(6)
/* script */
var __vue_script__ = __webpack_require__(218)
/* template */
var __vue_template__ = __webpack_require__(219)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-28f9fa2d"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/assets/js/admin/pages/Tag.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {  return key !== "default" && key.substr(0, 2) !== "__"})) {  console.error("named exports are not supported in *.vue files.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-28f9fa2d", Component.options)
  } else {
    hotAPI.reload("data-v-28f9fa2d", Component.options)
' + '  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),
/* 216 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(217);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(5)("249729b2", content, false);
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-28f9fa2d\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0&bustCache!./Tag.vue", function() {
     var newContent = require("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-28f9fa2d\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0&bustCache!./Tag.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 217 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(4)(undefined);
// imports


// module
exports.push([module.i, "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n", ""]);

// exports


/***/ }),
/* 218 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
    data: function data() {
        return {
            dataList: {
                meta: {},
                search: {}
            },
            dialogCreate: {
                display: false,
                data: {}
            },
            dialogUpdate: {
                display: false,
                data: {}
            },
            dialogDelete: {
                display: false
            }
        };
    },
    created: function created() {
        this.loadData();
    },
    methods: {
        loadData: function loadData(params) {
            var self = this;
            axios.get(api.tagList, {
                params: _.merge(self.dataList.search, params)
            }).then(function (res) {
                self.dataList = res.data;
            });
        },
        paginate: function paginate(page) {
            _.merge(this.dataList, { search: { page: page } });
            this.loadData();
        },
        dataCreate: function dataCreate() {
            var self = this;
            axios.post(api.tagCreate, self.dialogCreate.data).then(function () {
                self.dialogCreate.data = {};
                self.dialogCreate.display = false;
                self.$message.success('成功');
                self.loadData();
            });
        },
        dataUpdate: function dataUpdate() {
            var self = this;
            axios.post(api.tagUpdate, _.assign({ id: self.dialogUpdate.row.id }, self.dialogUpdate.data)).then(function () {
                self.dialogUpdate.data = {};
                self.dialogUpdate.display = false;
                self.$message.success('成功');
                self.loadData();
            });
        },
        dataDelete: function dataDelete() {
            var self = this;
            axios.post(api.tagDelete, { id: self.dialogDelete.row.id }).then(function () {
                self.dialogDelete.display = false;
                self.$message.success('成功');
                self.loadData();
            });
        }
    }
});

/***/ }),
/* 219 */
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _c(
        "el-row",
        [
          _c(
            "el-button",
            {
              attrs: { type: "primary" },
              on: {
                click: function($event) {
                  _vm.dialogCreate.display = true
                }
              }
            },
            [_vm._v("添加")]
          ),
          _vm._v(" "),
          _c("el-pagination", {
            attrs: {
              layout: "prev, pager, next",
              total: _vm.dataList.meta.total,
              "page-size": _vm.dataList.meta.per_page
            },
            on: { "current-change": _vm.paginate }
          })
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "el-row",
        [
          _c(
            "el-table",
            { attrs: { data: _vm.dataList.data, stripe: "" } },
            [
              _c("el-table-column", {
                attrs: { prop: "name", label: "名称", width: "200" }
              }),
              _vm._v(" "),
              _c("el-table-column", {
                attrs: { prop: "group_id", label: "分组", width: "200" }
              }),
              _vm._v(" "),
              _c("el-table-column", {
                attrs: { label: "操作" },
                scopedSlots: _vm._u([
                  {
                    key: "default",
                    fn: function(scope) {
                      return [
                        _c(
                          "el-button",
                          {
                            attrs: { type: "warning" },
                            on: {
                              click: function($event) {
                                _vm.dialogUpdate.row = scope.row
                                _vm.dialogUpdate.display = true
                              }
                            }
                          },
                          [
                            _vm._v(
                              "\n                        修改\n                    "
                            )
                          ]
                        ),
                        _vm._v(" "),
                        _c(
                          "el-button",
                          {
                            attrs: { type: "danger" },
                            on: {
                              click: function($event) {
                                _vm.dialogDelete.row = scope.row
                                _vm.dialogDelete.display = true
                              }
                            }
                          },
                          [
                            _vm._v(
                              "\n                        删除\n                    "
                            )
                          ]
                        )
                      ]
                    }
                  }
                ])
              })
            ],
            1
          )
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "el-dialog",
        {
          attrs: { title: "创建", visible: _vm.dialogCreate.display },
          on: {
            "update:visible": function($event) {
              _vm.$set(_vm.dialogCreate, "display", $event)
            }
          }
        },
        [
          _c(
            "el-form",
            [
              _c(
                "el-form-item",
                { attrs: { label: "名称", labelWidth: "100px" } },
                [
                  _c("el-input", {
                    model: {
                      value: _vm.dialogCreate.data.name,
                      callback: function($$v) {
                        _vm.$set(_vm.dialogCreate.data, "name", $$v)
                      },
                      expression: "dialogCreate.data.name"
                    }
                  })
                ],
                1
              )
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "div",
            {
              staticClass: "dialog-footer",
              attrs: { slot: "footer" },
              slot: "footer"
            },
            [
              _c(
                "el-button",
                {
                  on: {
                    click: function($event) {
                      _vm.dialogCreate.display = false
                    }
                  }
                },
                [_vm._v("取 消")]
              ),
              _vm._v(" "),
              _c(
                "el-button",
                { attrs: { type: "primary" }, on: { click: _vm.dataCreate } },
                [_vm._v("确 定")]
              )
            ],
            1
          )
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "el-dialog",
        {
          attrs: { title: "更新", visible: _vm.dialogUpdate.display },
          on: {
            "update:visible": function($event) {
              _vm.$set(_vm.dialogUpdate, "display", $event)
            }
          }
        },
        [
          _c(
            "el-form",
            [
              _c(
                "el-form-item",
                { attrs: { label: "名称", labelWidth: "100px" } },
                [
                  _c("el-input", {
                    model: {
                      value: _vm.dialogUpdate.data.name,
                      callback: function($$v) {
                        _vm.$set(_vm.dialogUpdate.data, "name", $$v)
                      },
                      expression: "dialogUpdate.data.name"
                    }
                  })
                ],
                1
              )
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "div",
            {
              staticClass: "dialog-footer",
              attrs: { slot: "footer" },
              slot: "footer"
            },
            [
              _c(
                "el-button",
                {
                  on: {
                    click: function($event) {
                      _vm.dialogUpdate.display = false
                    }
                  }
                },
                [_vm._v("取 消")]
              ),
              _vm._v(" "),
              _c(
                "el-button",
                { attrs: { type: "primary" }, on: { click: _vm.dataUpdate } },
                [_vm._v("确 定")]
              )
            ],
            1
          )
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "el-dialog",
        {
          attrs: { title: "删除", visible: _vm.dialogDelete.display },
          on: {
            "update:visible": function($event) {
              _vm.$set(_vm.dialogDelete, "display", $event)
            }
          }
        },
        [
          _c("label", [_vm._v("是否删除？")]),
          _vm._v(" "),
          _c(
            "div",
            {
              staticClass: "dialog-footer",
              attrs: { slot: "footer" },
              slot: "footer"
            },
            [
              _c(
                "el-button",
                {
                  on: {
                    click: function($event) {
                      _vm.dialogDelete.display = false
                    }
                  }
                },
                [_vm._v("取 消")]
              ),
              _vm._v(" "),
              _c(
                "el-button",
                { attrs: { type: "primary" }, on: { click: _vm.dataDelete } },
                [_vm._v("确 定")]
              )
            ],
            1
          )
        ]
      )
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-28f9fa2d", module.exports)
  }
}

/***/ }),
/* 220 */
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(221)
}
var normalizeComponent = __webpack_require__(6)
/* script */
var __vue_script__ = __webpack_require__(223)
/* template */
var __vue_template__ = __webpack_require__(225)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-15f9f8f1"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/assets/js/admin/pages/UserProfile.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {  return key !== "default" && key.substr(0, 2) !== "__"})) {  console.error("named exports are not supported in *.vue files.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-15f9f8f1", Component.options)
  } else {
    hotAPI.reload("data-v-15f9f8f1", Component.options)
' + '  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),
/* 221 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(222);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(5)("52ddf0fc", content, false);
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-15f9f8f1\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0&bustCache!./UserProfile.vue", function() {
     var newContent = require("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-15f9f8f1\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0&bustCache!./UserProfile.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 222 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(4)(undefined);
// imports


// module
exports.push([module.i, "\n.el-dialog .el-select[data-v-15f9f8f1] {\n    width: 100%;\n}\n", ""]);

// exports


/***/ }),
/* 223 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__city_json__ = __webpack_require__(224);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__city_json___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__city_json__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//



/* harmony default export */ __webpack_exports__["default"] = ({
    data: function data() {
        return {
            tagList: [],
            provinces: Object.keys(__WEBPACK_IMPORTED_MODULE_0__city_json___default.a),
            cities: [],
            genderLabel: {
                0: '未知',
                1: '男',
                2: '女'
            },
            dataList: {
                meta: {}
            },
            search: {},
            dialogCreate: {
                display: false,
                data: {
                    tags: []
                }
            },
            dialogUpdate: {
                display: false,
                data: {
                    tags: []
                }
            },
            dialogDelete: {
                display: false
            },
            rules: {
                name: [{ required: true, message: '姓名不能为空', trigger: 'blur' }],
                mobile: [{ required: true, message: '手机号不能为空', trigger: 'blur' }],
                qq: [{ required: true, message: 'QQ不能为空', trigger: 'blur' }],
                wx: [{ required: true, message: '微信不能为空', trigger: 'blur' }]
            }
        };
    },
    created: function created() {
        this.loadTagList();
        this.loadData();
    },
    methods: {
        provinceSelect: function provinceSelect(value) {
            this.cities = Object.keys(__WEBPACK_IMPORTED_MODULE_0__city_json___default.a[value]);
            this.dialogCreate.data.city = this.cities[0];
            this.dialogUpdate.data.city = this.cities[0];
        },
        loadTagList: function loadTagList() {
            var self = this;
            axios.get(api.tagList).then(function (res) {
                self.tagList = res.data.data;
            });
        },
        loadData: function loadData() {
            var self = this;
            axios.get(api.userProfileList, { params: self.search }).then(function (res) {
                self.dataList = res.data;
            });
        },
        paginate: function paginate(page) {
            this.search.page = page;
            this.loadData();
        },
        reset: function reset() {
            this.search = {};
            this.loadData();
        },
        openCreateDialog: function openCreateDialog() {
            this.dialogCreate.data = {
                tags: [],
                city: ''
            };
            this.dialogCreate.display = true;
        },
        doCreate: function doCreate() {
            var self = this;
            self.$refs.createForm.validate(function (valid) {
                if (valid) {
                    axios.post(api.userProfileCreate, self.dialogCreate.data).then(function () {
                        self.dialogCreate.display = false;
                        self.$message.success('成功');
                        self.loadData();
                    });
                } else {
                    return false;
                }
            });
        },
        openUpdateDialog: function openUpdateDialog(scope) {
            this.dialogUpdate.data = Object.assign({}, scope.row);
            this.dialogUpdate.display = true;
        },
        doUpdate: function doUpdate() {
            var self = this;
            axios.post(api.userProfileUpdate, self.dialogUpdate.data).then(function () {
                self.dialogUpdate.display = false;
                self.$message.success('成功');
                self.loadData();
            });
        },
        openDeleteDialog: function openDeleteDialog(scope) {
            this.dialogDelete.data = Object.assign({}, scope.row);
            this.dialogDelete.display = true;
        },
        doDelete: function doDelete() {
            var self = this;
            axios.post(api.userProfileDelete, { id: self.dialogDelete.data.id }).then(function () {
                self.dialogDelete.display = false;
                self.$message.success('成功');
                self.loadData();
            });
        }
    }
});

/***/ }),
/* 224 */
/***/ (function(module, exports) {

module.exports = {"北京":{"北京":["东城区","西城区","崇文区","宣武区","朝阳区","丰台区","石景山区","海淀区","门头沟区","房山区","通州区","顺义区","昌平区","大兴区","平谷区","怀柔区","密云县","延庆县","其他"]},"天津":{"天津":["和平区","河东区","河西区","南开区","河北区","红挢区","滨海新区","东丽区","西青区","津南区","北辰区","宁河区","武清区","静海县","宝坻区","蓟县","塘沽区","汉沽区","大港区","宝坻区","其他"]},"河北":{"石家庄":["长安区","桥东区","桥西区","新华区","井陉矿区","裕华区","井陉县","正定县","栾城县","行唐县","灵寿县","高邑县","深泽县","赞皇县","无极县","平山县","元氏县","赵县","辛集市","藁城市","晋州市","新乐市","鹿泉市","其他"],"唐山":["路南区","路北区","古冶区","开平区","丰南区","丰润区","滦县","滦南县","乐亭县","迁西县","玉田县","唐海县","遵化市","迁安市"," 曹妃甸区","其他"],"秦皇岛":["海港区","山海关区","北戴河区","青龙满族自治县","昌黎县","抚宁县","卢龙县","其他"],"邯郸":["邯山区","丛台区","复兴区","峰峰矿区","邯郸县","临漳县","成安县","大名县","涉县","磁县","肥乡县","永年县","邱县","鸡泽县","广平县","馆陶县","魏县","曲周县","武安市","其他"],"邢台":["桥东区","桥西区","邢台县","临城县","内丘县","柏乡县","隆尧县","任县","南和县","宁晋县","巨鹿县","新河县","广宗县","平乡县","威县","清河县","临西县","南宫市","沙河市","其他"],"保定":["新市区","北市区","南市区","满城县","清苑县","涞水县","阜平县","徐水县","定兴县","唐县","高阳县","容城县","涞源县","望都县","安新县","易县","曲阳县","蠡县","顺平县","博野县","雄县","涿州市","定州市","安国市","高碑店市","其他"],"张家口":["桥东区","桥西区","宣化区","下花园区","宣化县","张北县","康保县","沽源县","尚义县","蔚县","阳原县","怀安县","万全县","怀来县","涿鹿县","赤城县","崇礼县","其他"],"承德":["双桥区","双滦区","鹰手营子矿区","承德县","兴隆县","平泉县","滦平县","隆化县","丰宁满族自治县","宽城满族自治县","围场满族蒙古族自治县","其他"],"沧州":["新华区","运河区","沧县","青县","东光县","海兴县","盐山县","肃宁县","南皮县","吴桥县","献县","孟村回族自治县","泊头市","任丘市","黄骅市","河间市","其他"],"廊坊":["安次区","广阳区","固安县","永清县","香河县","大城县","文安县","大厂回族自治县","霸州市","三河市","其他"],"衡水":["桃城区","枣强县","武邑县","武强县","饶阳县","安平县","故城县","景县","阜城县","冀州市","深州市","其他"]},"山西":{"太原":["小店区","迎泽区","杏花岭区","尖草坪区","万柏林区","晋源区","清徐县","阳曲县","娄烦县","古交市","其他"],"大同":["城区","矿区","南郊区","新荣区","阳高县","天镇县","广灵县","灵丘县","浑源县","左云县","大同县","其他"],"阳泉":["城区","矿区","郊区","平定县","盂县","其他"],"长治":["长治","城区","郊区","长治县","襄垣县","屯留县","平顺县","黎城县","壶关县","长子县","武乡县","沁县","沁源县","潞城市","其他"],"晋城":["城区","沁水县","阳城县","陵川县","泽州县","高平市","其他"],"朔州":["朔城区","平鲁区","山阴县","应县","右玉县","怀仁县","其他"],"晋中":["榆次区","榆社县","左权县","和顺县","昔阳县","寿阳县","太谷县","祁县","平遥县","灵石县","介休市","其他"],"运城":["盐湖区","临猗县","万荣县","闻喜县","稷山县","新绛县","绛县","垣曲县","夏县","平陆县","芮城县","永济市","河津市","其他"],"忻州":["忻府区","定襄县","五台县","代县","繁峙县","宁武县","静乐县","神池县","五寨县","岢岚县","河曲县","保德县","偏关县","原平市","其他"],"临汾":["尧都区","曲沃县","翼城县","襄汾县","洪洞县","古县","安泽县","浮山县","吉县","乡宁县","大宁县","隰县","永和县","蒲县","汾西县","侯马市","霍州市","其他"],"吕梁":["离石区","文水县","交城县","兴县","临县","柳林县","石楼县","岚县","方山县","中阳县","交口县","孝义市","汾阳市","其他"]},"内蒙古":{"呼和浩特":["新城区","回民区","玉泉区","玉泉区","赛罕区","土默特左旗","托克托县","和林格尔县","清水河县","武川县","其他"],"包头":["东河区","昆都仑区","青山区","石拐区","白云矿区","九原区","土默特右旗","固阳县","达尔罕茂明安联合旗","其他"],"乌海":["海勃湾区","海南区","乌达区","其他"],"赤峰":["红山区","元宝山区","松山区","阿鲁科尔沁旗","巴林左旗","巴林右旗","林西县","克什克腾旗","翁牛特旗","喀喇沁旗","宁城县","敖汉旗","其他"],"通辽":["科尔沁区","科尔沁左翼中旗","科尔沁左翼后旗","开鲁县","库伦旗","奈曼旗","扎鲁特旗","霍林郭勒市","其他"],"鄂尔多斯":["东胜区","达拉特旗","准格尔旗","鄂托克前旗","鄂托克旗","杭锦旗","乌审旗","伊金霍洛旗","其他"],"呼伦贝尔":["海拉尔区","扎赉诺尔","阿荣旗","莫力达瓦达斡尔族自治旗","鄂伦春自治旗","鄂温克族自治旗","陈巴尔虎旗","新巴尔虎左旗","新巴尔虎右旗","满洲里市","牙克石市","扎兰屯市","额尔古纳市","根河市","其他"],"巴彦淖尔":["临河区","五原县","磴口县","乌拉特前旗","乌拉特中旗","乌拉特后旗","杭锦后旗","其他"],"乌兰察布":["集宁区","卓资县","化德县","商都县","兴和县","凉城县","察哈尔右翼前旗","察哈尔右翼中旗","察哈尔右翼后旗","四子王旗","丰镇市","其他"],"兴安":["乌兰浩特市","阿尔山市","科尔沁右翼前旗","科尔沁右翼中旗","扎赉特旗","突泉县","其他"],"锡林郭勒":["二连浩特市","锡林浩特市","阿巴嘎旗","苏尼特左旗","苏尼特右旗","东乌珠穆沁旗","西乌珠穆沁旗","太仆寺旗","镶黄旗","正镶白旗","正蓝旗","多伦县","其他"],"阿拉善":["阿拉善左旗","阿拉善右旗","额济纳旗","其他"]},"辽宁":{"沈阳":["和平区","沈河区","大东区","皇姑区","铁西区","苏家屯区","东陵区","新城子区","于洪区","辽中县","康平县","法库县","新民市","其他"],"大连":["中山区","西岗区","沙河口区","甘井子区","旅顺口区","金州区","长海县","瓦房店市","普兰店市","庄河市","其他"],"鞍山":["铁东区","铁西区","立山区","千山区","台安县","岫岩县","海城市","其他"],"抚顺":["新抚区","东洲区","望花区","顺城区","抚顺县","新宾满族自治县","清原满族自治县","其他"],"本溪":["平山区","溪湖区","明山区","南芬区","本溪满族自治县","桓仁满族自治县","其他"],"丹东":["元宝区","振兴区","振安区","宽甸满族自治县","东港市","凤城市","其他"],"锦州":["古塔区","凌河区","太和区","黑山县","义县","凌海市","北镇市","其他"],"营口":["站前区","西市区","鲅鱼圈区","老边区","盖州市","大石桥市","其他"],"阜新":["海州区","新邱区","太平区","清河门区","细河区","阜新蒙古族自治县","彰武县","其他"],"辽阳":["白塔区","文圣区","宏伟区","弓长岭区","太子河区","辽阳县","灯塔市","其他"],"盘锦":["双台子区","兴隆台区","大洼县","盘山县","其他"],"铁岭":["银州区","清河区","铁岭县","西丰县","昌图县","调兵山市","开原市","其他"],"朝阳":["双塔区","龙城区","朝阳县","建平县","喀喇沁左翼蒙古族自治县","北票市","凌源市","其他"],"葫芦岛":["连山区","龙港区","南票区","绥中县","建昌县","兴城市","其他"]},"吉林":{"长春":["南关区","宽城区","朝阳区","二道区","绿园区","双阳区","农安县","九台市","榆树市","德惠市","其他"],"吉林":["昌邑区","龙潭区","船营区","丰满区","永吉县","蛟河市","桦甸市","舒兰市","磐石市","其他"],"四平":["铁西区","铁东区","梨树县","伊通满族自治县","公主岭市","双辽市","其他"],"辽源":["龙山区","西安区","东丰县","东辽县","其他"],"通化":["东昌区","二道江区","通化县","辉南县","柳河县","梅河口市","集安市","其他"],"白山":["八道江区","江源区","抚松县","靖宇县","长白朝鲜族自治县","临江市","其他"],"松原":["宁江区","前郭尔罗斯蒙古族自治县","长岭县","乾安县","扶余县","其他"],"白城":["洮北区","镇赉县","通榆县","洮南市","大安市","其他"],"延边":["延吉市","图们市","敦化市","珲春市","龙井市","和龙市","汪清县","安图县","其他"]},"黑龙江":{"哈尔滨":["道里区","南岗区","道外区","平房区","松北区","香坊区","呼兰区","阿城区","依兰县","方正县","宾县","巴彦县","木兰县","通河县","延寿县","双城市","尚志市","五常市","其他"],"齐齐哈尔":["龙沙区","建华区","铁锋区","昂昂溪区","富拉尔基区","碾子山区","梅里斯达斡尔族区","龙江县","依安县","泰来县","甘南县","富裕县","克山县","克东县","拜泉县","讷河市","其他"],"鸡西":["鸡冠区","恒山区","滴道区","梨树区","城子河区","麻山区","鸡东县","虎林市","密山市","其他"],"鹤岗":["向阳区","工农区","南山区","兴安区","东山区","兴山区","萝北县","绥滨县","其他"],"双鸭山":["尖山区","岭东区","四方台区","宝山区","集贤县","友谊县","宝清县","饶河县","其他"],"大庆":["萨尔图区","龙凤区","让胡路区","红岗区","大同区","肇州县","肇源县","林甸县","杜尔伯特蒙古族自治县","其他"],"伊春":["伊春区","南岔区","友好区","西林区","翠峦区","新青区","美溪区","金山屯区","五营区","乌马河区","汤旺河区","带岭区","乌伊岭区","红星区","上甘岭区","嘉荫县","铁力市","其他"],"佳木斯":["向阳区","前进区","东风区","郊区","桦南县","桦川县","汤原县","抚远县","同江市","富锦市","其他"],"七台河":["新兴区","桃山区","茄子河区","勃利县","其他"],"牡丹江":["东安区","阳明区","爱民区","西安区","东宁县","林口县","绥芬河市","海林市","宁安市","穆棱市","其他"],"黑河":["爱辉区","嫩江县","逊克县","孙吴县","北安市","五大连池市","其他"],"绥化":["北林区","望奎县","兰西县","青冈县","庆安县","明水县","绥棱县","安达市","肇东市","海伦市","其他"],"大兴安岭":["加格达奇区","松岭区","新林区","呼中区","呼玛县","塔河县","漠河县","其他"]},"上海":{"上海":["黄浦区","卢湾区","徐汇区","长宁区","静安区","普陀区","闸北区","虹口区","杨浦区","闵行区","宝山区","嘉定区","浦东新区","金山区","松江区","奉贤区","青浦区","奉贤区","崇明县","其他"]},"江苏":{"南京":["玄武区","秦淮区","建邺区","鼓楼区","浦口区","栖霞区","雨花台区","江宁区","六合区","溧水县","高淳县","其他"],"无锡":["崇安区","南长区","北塘区","锡山区","惠山区","滨湖区","江阴市","宜兴市","其他"],"徐州":["鼓楼区","云龙区","九里区","贾汪区","泉山区","丰县","沛县","铜山县","睢宁县","新沂市","邳州市","其他"],"常州":["天宁区","钟楼区","戚墅堰区","新北区","武进区","溧阳市","金坛市","其他"],"苏州":["沧浪区","平江区","金阊区","虎丘区","吴中区","相城区","常熟市","张家港市","昆山市","吴江市","太仓市","其他"],"南通":["崇川区","港闸区","海安县","如东县","启东市","如皋市","通州市","海门市","其他"],"连云港":["连云区","新浦区","海州区","赣榆县","东海县","灌云县","灌南县","其他"],"淮安":["清河区","楚州区","淮阴区","清浦区","涟水县","洪泽县","盱眙县","金湖县","其他"],"盐城":["亭湖区","盐都区","响水县","滨海县","阜宁县","射阳县","建湖县","东台市","大丰市","其他"],"扬州":["广陵区","邗江区","维扬区","宝应县","仪征市","高邮市","江都市","其他"],"镇江":["京口区","润州区","丹徒区","丹阳市","扬中市","句容市","其他"],"泰州":["海陵区","高港区","兴化市","靖江市","泰兴市","姜堰市","其他"],"宿迁":["宿城区","宿豫区","沭阳县","泗阳县","泗洪县","其他"]},"浙江":{"杭州":["上城区","下城区","江干区","拱墅区","西湖区","滨江区","萧山区","余杭区","桐庐县","淳安县","建德市","富阳市","临安市","其他"],"宁波":["海曙区","江东区","江北区","北仑区","镇海区","鄞州区","象山县","宁海县","余姚市","慈溪市","奉化市","其他"],"温州":["鹿城区","龙湾区","瓯海区","洞头县","永嘉县","平阳县","苍南县","文成县","泰顺县","瑞安市","乐清市","其他"],"嘉兴":["南湖区","秀洲区","嘉善县","海盐县","海宁市","平湖市","桐乡市","其他"],"湖州":["吴兴区","南浔区","德清县","长兴县","安吉县","其他"],"绍兴":["越城区","绍兴县","新昌县","诸暨市","上虞市","嵊州市","其他"],"金华":["婺城区","金东区","武义县","浦江县","磐安县","兰溪市","义乌市","东阳市","永康市","其他"],"衢州":["柯城区","衢江区","常山县","开化县","龙游县","江山市","其他"],"舟山":["定海区","普陀区","岱山县","嵊泗县","其他"],"台州":["椒江区","黄岩区","路桥区","玉环县","三门县","天台县","仙居县","温岭市","临海市","其他"],"丽水":["莲都区","青田县","缙云县","遂昌县","松阳县","云和县","庆元县","景宁畲族自治县","龙泉市","其他"]},"安徽":{"合肥":["瑶海区","庐阳区","蜀山区","包河区","长丰县","肥东县","肥西县","其他"],"芜湖":["镜湖区","弋江区","鸠江区","三山区","芜湖县","繁昌县","南陵县","其他"],"蚌埠":["龙子湖区","蚌山区","禹会区","淮上区","怀远县","五河县","固镇县","其他"],"淮南":["大通区","田家庵区","谢家集区","八公山区","潘集区","凤台县","其他"],"马鞍山":["金家庄区","花山区","雨山区","当涂县","其他"],"淮北":["杜集区","相山区","烈山区","濉溪县","其他"],"铜陵":["铜官山区","狮子山区","郊区","铜陵县","其他"],"安庆":["迎江区","大观区","宜秀区","怀宁县","枞阳县","潜山县","太湖县","宿松县","望江县","岳西县","桐城市","其他"],"黄山":["屯溪区","黄山区","徽州区","歙县","休宁县","黟县","祁门县","其他"],"滁州":["琅琊区","南谯区","来安县","全椒县","定远县","凤阳县","天长市","明光市","其他"],"阜阳":["颍州区","颍东区","颍泉区","临泉县","太和县","阜南县","颍上县","界首市","其他"],"宿州":["埇桥区","砀山县","萧县","灵璧县","泗县","其他"],"巢湖":["居巢区","庐江县","无为县","含山县","和县","其他"],"六安":["金安区","裕安区","寿县","霍邱县","舒城县","金寨县","霍山县","其他"],"亳州":["谯城区","涡阳县","蒙城县","利辛县","其他"],"池州":["贵池区","东至县","石台县","青阳县","其他"],"宣城":["宣州区","郎溪县","广德县","泾县","绩溪县","旌德县","宁国市","其他"]},"福建":{"福州":["鼓楼区","台江区","仓山区","马尾区","晋安区","闽侯县","连江县","罗源县","闽清县","永泰县","平潭县","福清市","长乐市","其他"],"厦门":["思明区","海沧区","湖里区","集美区","同安区","翔安区","其他"],"莆田":["城厢区","涵江区","荔城区","秀屿区","仙游县","其他"],"三明":["梅列区","三元区","明溪县","清流县","宁化县","大田县","尤溪县","沙县","将乐县","泰宁县","建宁县","永安市","其他"],"泉州":["鲤城区","丰泽区","洛江区","泉港区","惠安县","安溪县","永春县","德化县","金门县","石狮市","晋江市","南安市","其他"],"漳州":["芗城区","龙文区","云霄县","漳浦县","诏安县","长泰县","东山县","南靖县","平和县","华安县","龙海市","其他"],"南平":["延平区","顺昌县","浦城县","光泽县","松溪县","政和县","邵武市","武夷山市","建瓯市","建阳市","其他"],"龙岩":["新罗区","长汀县","永定县","上杭县","武平县","连城县","漳平市","其他"],"宁德":["蕉城区","霞浦县","古田县","屏南县","寿宁县","周宁县","柘荣县","福安市","福鼎市","其他"]},"江西":{"南昌":["东湖区","西湖区","青云谱区","湾里区","青山湖区","南昌县","新建县","安义县","进贤县","其他"],"景德镇":["昌江区","珠山区","浮梁县","乐平市","其他"],"萍乡":["安源区","湘东区","莲花县","上栗县","芦溪县","其他"],"九江":["庐山区","浔阳区","九江县","武宁县","修水县","永修县","德安县","星子县","都昌县","湖口县","彭泽县","瑞昌市","其他"],"新余":["渝水区","分宜县","其他"],"鹰潭":["月湖区","余江县","贵溪市","其他"],"赣州":["章贡区","赣县","信丰县","大余县","上犹县","崇义县","安远县","龙南县","定南县","全南县","宁都县","于都县","兴国县","会昌县","寻乌县","石城县","瑞金市","南康市","其他"],"吉安":["吉州区","青原区","吉安县","吉水县","峡江县","新干县","永丰县","泰和县","遂川县","万安县","安福县","永新县","井冈山市","其他"],"宜春":["袁州区","奉新县","万载县","上高县","宜丰县","靖安县","铜鼓县","丰城市","樟树市","高安市","其他"],"抚州":["临川区","南城县","黎川县","南丰县","崇仁县","乐安县","宜黄县","金溪县","资溪县","东乡县","广昌县","其他"],"上饶":["信州区","上饶县","广丰县","玉山县","铅山县","横峰县","弋阳县","余干县","鄱阳县","万年县","婺源县","德兴市","其他"]},"山东":{"济南":["历下区","市中区","槐荫区","天桥区","历城区","长清区","平阴县","济阳县","商河县","章丘市","其他"],"青岛":["市南区","市北区","四方区","黄岛区","崂山区","李沧区","城阳区","胶州市","即墨市","平度市","胶南市","莱西市","其他"],"淄博":["淄川区","张店区","博山区","临淄区","周村区","桓台县","高青县","沂源县","其他"],"枣庄":["市中区","薛城区","峄城区","台儿庄区","山亭区","滕州市","其他"],"东营":["东营区","河口区","垦利县","利津县","广饶县","其他"],"烟台":["芝罘区","福山区","牟平区","莱山区","长岛县","龙口市","莱阳市","莱州市","蓬莱市","招远市","栖霞市","海阳市","其他"],"潍坊":["潍城区","寒亭区","坊子区","奎文区","临朐县","昌乐县","青州市","诸城市","寿光市","安丘市","高密市","昌邑市","其他"],"济宁":["市中区","任城区","微山县","鱼台县","金乡县","嘉祥县","汶上县","泗水县","梁山县","曲阜市","兖州市","邹城市","其他"],"泰安":["泰山区","岱岳区","宁阳县","东平县","新泰市","肥城市","其他"],"威海":["环翠区","文登市","荣成市","乳山市","其他"],"日照":["东港区","岚山区","五莲县","莒县","其他"],"莱芜":["莱城区","钢城区","其他"],"临沂":["兰山区","罗庄区","河东区","沂南县","郯城县","沂水县","苍山县","费县","平邑县","莒南县","蒙阴县","临沭县","其他"],"德州":["德城区","陵县","宁津县","庆云县","临邑县","齐河县","平原县","夏津县","武城县","乐陵市","禹城市","其他"],"聊城":["东昌府区","阳谷县","莘县","茌平县","东阿县","冠县","高唐县","临清市","其他"],"滨州":["滨城区","惠民县","阳信县","无棣县","沾化县","博兴县","邹平县","其他"],"菏泽":["牡丹区","曹县","单县","成武县","巨野县","郓城县","鄄城县","定陶县","东明县","其他"]},"河南":{"郑州":["中原区","二七区","管城回族区","金水区","上街区","惠济区","中牟县","巩义市","荥阳市","新密市","新郑市","登封市","其他"],"开封":["龙亭区","顺河回族区","鼓楼区","禹王台区","金明区","杞县","通许县","尉氏县","开封县","兰考县","其他"],"洛阳":["老城区","西工区","廛河回族区","涧西区","吉利区","洛龙区","孟津县","新安县","栾川县","嵩县","汝阳县","宜阳县","洛宁县","伊川县","偃师市","其他"],"平顶山":["新华区","卫东区","石龙区","湛河区","宝丰县","叶县","鲁山县","郏县","舞钢市","汝州市","其他"],"安阳":["文峰区","北关区","殷都区","龙安区","安阳县","汤阴县","滑县","内黄县","林州市","其他"],"鹤壁":["鹤山区","山城区","淇滨区","浚县","淇县","其他"],"新乡":["红旗区","卫滨区","凤泉区","牧野区","新乡县","获嘉县","原阳县","延津县","封丘县","长垣县","卫辉市","辉县市","其他"],"焦作":["解放区","中站区","马村区","山阳区","修武县","博爱县","武陟县","温县","沁阳市","孟州市","其他"],"濮阳":["华龙区","清丰县","南乐县","范县","台前县","濮阳县","其他"],"许昌":["魏都区","许昌县","鄢陵县","襄城县","禹州市","长葛市","其他"],"漯河":["源汇区","郾城区","召陵区","舞阳县","临颍县","其他"],"三门峡":["湖滨区","渑池县","陕县","卢氏县","义马市","灵宝市","其他"],"南阳":["宛城区","卧龙区","南召县","方城县","西峡县","镇平县","内乡县","淅川县","社旗县","唐河县","新野县","桐柏县","邓州市","其他"],"商丘":["梁园区","睢阳区","民权县","睢县","宁陵县","柘城县","虞城县","夏邑县","永城市","其他"],"信阳":["浉河区","平桥区","罗山县","光山县","新县","商城县","固始县","潢川县","淮滨县","息县","其他"],"周口":["川汇区","扶沟县","西华县","商水县","沈丘县","郸城县","淮阳县","太康县","鹿邑县","项城市","其他"],"驻马店":["驿城区","西平县","上蔡县","平舆县","正阳县","确山县","泌阳县","汝南县","遂平县","新蔡县","其他"],"济源":["沁园街道","济水街道","北海街道","天坛街道","玉泉街道","克井镇","五龙口镇","轵城镇","承留镇","邵原镇","坡头镇","梨林镇","大峪镇","思礼镇","王屋镇","下冶镇","其他"]},"湖北":{"武汉":["江岸区","江汉区","硚口区","汉阳区","武昌区","青山区","洪山区","东西湖区","汉南区","蔡甸区","江夏区","黄陂区","新洲区","其他"],"黄石":["黄石港区","西塞山区","下陆区","铁山区","阳新县","大冶市","其他"],"十堰":["茅箭区","张湾区","郧县","郧西县","竹山县","竹溪县","房县","丹江口市","其他"],"宜昌":["西陵区","伍家岗区","点军区","猇亭区","夷陵区","远安县","兴山县","秭归县","长阳土家族自治县","五峰土家族自治县","宜都市","当阳市","枝江市","其他"],"襄樊":["襄城区","樊城区","襄阳区","南漳县","谷城县","保康县","老河口市","枣阳市","宜城市","其他"],"鄂州":["梁子湖区","华容区","鄂城区","其他"],"荆门":["东宝区","掇刀区","京山县","沙洋县","钟祥市","其他"],"孝感":["孝南区","孝昌县","大悟县","云梦县","应城市","安陆市","汉川市","其他"],"荆州":["沙市区","荆州区","公安县","监利县","江陵县","石首市","洪湖市","松滋市","其他"],"黄冈":["黄州区","团风县","红安县","罗田县","英山县","浠水县","蕲春县","黄梅县","麻城市","武穴市","其他"],"咸宁":["咸安区","嘉鱼县","通城县","崇阳县","通山县","赤壁市","其他"],"随州":["曾都区","随县","广水市","其他"],"恩施":["恩施市","利川市","建始县","巴东县","宣恩县","咸丰县","来凤县","鹤峰县","其他"],"仙桃":["沙嘴街道","干河街道","龙华山办事处","郑场镇","毛嘴镇","豆河镇","三伏潭镇","胡场镇","长倘口镇","西流河镇","沙湖镇","杨林尾镇","彭场镇","张沟镇","郭河镇","沔城回族镇","通海口镇","陈场镇","工业园区","九合垸原种场","沙湖原种场","五湖渔场","赵西垸林场","畜禽良种场","排湖风景区","其他"],"潜江":["园林办事处","杨市办事处","周矶办事处","广华办事处","泰丰办事处","高场办事处","竹根滩镇","渔洋镇","王场镇","高石碑镇","熊口镇","老新镇","浩口镇","积玉口镇","张金镇","龙湾镇","江汉石油管理局","潜江经济开发区","周矶管理区","后湖管理区","熊口管理区","总口管理区","白鹭湖管理区","运粮湖管理区","浩口原种场","其他"],"天门":["竟陵街道","侨乡街道开发区","杨林街道","多宝镇","拖市镇","张港镇","蒋场镇","汪场镇","渔薪镇","黄潭镇","岳口镇","横林镇","彭市镇","麻洋镇","多祥镇","干驿镇","马湾镇","卢市镇","小板镇","九真镇","皂市镇","胡市镇","石河镇","佛子山镇","净潭乡","蒋湖农场","白茅湖农场","沉湖管委会","其他"],"神农架":["松柏镇","阳日镇","木鱼镇","红坪镇","新华镇","九湖镇","宋洛乡","下谷坪","土家族乡","其他"]},"湖南":{"长沙":["芙蓉区","天心区","岳麓区","开福区","雨花区","长沙县","望城县","宁乡县","浏阳市","其他"],"株洲":["荷塘区","芦淞区","石峰区","天元区","株洲县","攸县","茶陵县","炎陵县","醴陵市","其他"],"湘潭":["雨湖区","岳塘区","湘潭县","湘乡市","韶山市","其他"],"衡阳":["珠晖区","雁峰区","石鼓区","蒸湘区","南岳区","衡阳县","衡南县","衡山县","衡东县","祁东县","耒阳市","常宁市","其他"],"邵阳":["双清区","大祥区","北塔区","邵东县","新邵县","邵阳县","隆回县","洞口县","绥宁县","新宁县","城步苗族自治县","武冈市","其他"],"岳阳":["岳阳楼区","云溪区","君山区","岳阳县","华容县","湘阴县","平江县","汨罗市","临湘市","其他"],"常德":["武陵区","鼎城区","安乡县","汉寿县","澧县","临澧县","桃源县","石门县","津市市","其他"],"张家界":["永定区","武陵源区","慈利县","桑植县","其他"],"益阳":["资阳区","赫山区","南县","桃江县","安化县","沅江市","其他"],"郴州":["北湖区","苏仙区","桂阳县","宜章县","永兴县","嘉禾县","临武县","汝城县","桂东县","安仁县","资兴市","其他"],"永州":["零陵区","冷水滩区","祁阳县","东安县","双牌县","道县","江永县","宁远县","蓝山县","新田县","江华瑶族自治县","其他"],"怀化":["鹤城区","中方县","沅陵县","辰溪县","溆浦县","会同县","麻阳苗族自治县","新晃侗族自治县","芷江侗族自治县","靖州苗族侗族自治县","通道侗族自治县","洪江市","其他"],"娄底":["娄星区","双峰县","新化县","冷水江市","涟源市","其他"],"湘西":["吉首市","泸溪县","凤凰县","花垣县","保靖县","古丈县","永顺县","龙山县","其他"]},"广东":{"广州":["荔湾区","越秀区","海珠区","天河区","白云区","黄埔区","番禺区","花都区","南沙区","萝岗区","增城市","从化市","其他"],"韶关":["武江区","浈江区","曲江区","始兴县","仁化县","翁源县","乳源瑶族自治县","新丰县","乐昌市","南雄市","其他"],"深圳":["罗湖区","福田区","南山区","宝安区","龙岗区","盐田区","其他"],"珠海":["香洲区","斗门区","金湾区","其他"],"汕头":["龙湖区","金平区","濠江区","潮阳区","潮南区","澄海区","南澳县","其他"],"佛山":["禅城区","南海区","顺德区","三水区","高明区","其他"],"江门":["蓬江区","江海区","新会区","台山市","开平市","鹤山市","恩平市","其他"],"湛江":["赤坎区","霞山区","坡头区","麻章区","遂溪县","徐闻县","廉江市","雷州市","吴川市","其他"],"茂名":["茂南区","茂港区","电白县","高州市","化州市","信宜市","其他"],"肇庆":["端州区","鼎湖区","广宁县","怀集县","封开县","德庆县","高要市","四会市","其他"],"惠州":["惠城区","惠阳区","博罗县","惠东县","龙门县","其他"],"梅州":["梅江区","梅县","大埔县","丰顺县","五华县","平远县","蕉岭县","兴宁市","其他"],"汕尾":["城区","海丰县","陆河县","陆丰市","其他"],"河源":["源城区","紫金县","龙川县","连平县","和平县","东源县","其他"],"阳江":["江城区","阳西县","阳东县","阳春市","其他"],"清远":["清城区","佛冈县","阳山县","连山壮族瑶族自治县","连南瑶族自治县","清新县","英德市","连州市","其他"],"东莞":["东城街道","南城街道","万江街道","莞城街道","石碣镇","石龙镇","茶山镇","石排镇","企石镇","横沥镇","桥头镇","谢岗镇","东坑镇","常平镇","寮步镇","樟木头镇","大朗镇","黄江镇","清溪镇","塘厦镇","凤岗镇","大岭山镇","长安镇","虎门镇","厚街镇","沙田镇","道滘镇","洪梅镇","麻涌镇","望牛墩镇","中堂镇","高埗镇","松山湖管委会","虎门港管委会","东莞生态园","其他"],"中山":["石岐区街道","东区街道","火炬开发区街道","西区街道","南区街道","五桂山街道","小榄镇","黄圃镇","民众镇","东凤镇","东升镇","古镇镇","沙溪镇","坦洲镇","港口镇","三角镇","横栏镇","南头镇","阜沙镇","南朗镇","三乡镇","板芙镇","大涌镇","神湾镇","其他"],"潮州":["湘桥区","潮安县","饶平县","其他"],"揭阳":["榕城区","揭东县","揭西县","惠来县","普宁市","其他"],"云浮":["云城区","新兴县","郁南县","云安县","罗定市","其他"]},"广西":{"南宁":["兴宁区","青秀区","江南区","西乡塘区","良庆区","邕宁区","武鸣县","隆安县","马山县","上林县","宾阳县","横县","其他"],"柳州":["城中区","鱼峰区","柳南区","柳北区","柳江县","柳城县","鹿寨县","融安县","融水苗族自治县","三江侗族自治县","其他"],"桂林":["秀峰区","叠彩区","象山区","七星区","雁山区","阳朔县","临桂县","灵川县","全州县","兴安县","永福县","灌阳县","龙胜各族自治县","资源县","平乐县","荔蒲县","恭城瑶族自治县","其他"],"梧州":["万秀区","蝶山区","长洲区","苍梧县","藤县","蒙山县","岑溪市","其他"],"北海":["海城区","银海区","铁山港区","合浦县","其他"],"防城港":["港口区","防城区","上思县","东兴市","其他"],"钦州":["钦南区","钦北区","灵山县","浦北县","其他"],"贵港":["港北区","港南区","覃塘区","平南县","桂平市","其他"],"玉林":["玉州区","容县","陆川县","博白县","兴业县","北流市","其他"],"百色":["右江区","田阳县","田东县","平果县","德保县","靖西县","那坡县","凌云县","乐业县","田林县","西林县","隆林各族自治县","其他"],"贺州":["八步区","昭平县","钟山县","富川瑶族自治县","其他"],"河池":["金城江区","南丹县","天峨县","凤山县","东兰县","罗城仫佬族自治县","环江毛南族自治县","巴马瑶族自治县","都安瑶族自治县","大化瑶族自治县","宜州市","其他"],"来宾":["兴宾区","忻城县","象州县","武宣县","金秀瑶族自治县","合山市","其他"],"崇左":["江洲区","扶绥县","宁明县","龙州县","大新县","天等县","凭祥市","其他"]},"海南":{"海口":["秀英区","龙华区","琼山区","美兰区","其他"],"三亚":["海棠湾镇","吉阳镇","凤凰镇","崖城镇","天涯镇","育才镇","国营南田农场","国营南新农场","国营立才农场","国营南滨农场","河西区街道","河东区街道","其他"],"三沙":["西沙南沙中沙","其他"],"五指山":["通什镇","南圣镇","毛阳镇","番阳镇","畅好乡","毛道乡","水满乡","国营畅好农场","其他"],"琼海":["嘉积镇","万泉镇","石壁镇","中原镇","博鳌镇","阳江镇","龙江镇","潭门镇","塔洋镇","长坡镇","大路镇","会山镇","国营东太农场","国营东红农场","国营东升农场","彬村山华侨农场","其他"],"儋州":["那大镇","和庆镇","南丰镇","大成镇","雅星镇","兰洋镇","光村镇","木棠镇","海头镇","峨蔓镇","三都镇","王五镇","白马井镇","中和镇","排浦镇","东成镇","新州镇","国营西培农场","国营西联农场","国营蓝洋农场","国营八一农场","洋浦经济开发区","华南热作学院","其他"],"文昌":["文城镇","重兴镇","蓬莱镇","会文镇","东路镇","潭牛镇","东阁镇","文教镇","东郊镇","龙楼镇","昌洒镇","翁田镇","抱罗镇","冯坡镇","锦山镇","铺前镇","公坡镇","国营东路农场","国营南阳农场","国营罗豆农场","其他"],"万宁":["万城镇","龙滚镇","和乐镇","后安镇","大茂镇","东澳镇","礼纪镇","长丰镇","山根镇","北大镇","南桥镇","三更罗镇","国营东兴农场","国营东和农场","国营新中农场","兴隆华侨农场","地方国营六连林场","其他"],"东方":["八所镇","东河镇","大田镇","感城镇","板桥镇","三家镇","四更镇","新龙镇","天安乡","江边乡","国营广坝农场","东方华侨农场","其他"],"定安":["定城镇","新竹镇","龙湖镇","黄竹镇","雷鸣镇","龙门镇","龙河镇","岭口镇","翰林镇","富文镇","国营中瑞农场","国营南海农场","国营金鸡岭农场","其他"],"屯昌":["屯城镇","新兴镇","枫木镇","乌坡镇","南吕镇","南坤镇","坡心镇","西昌镇","国营中建农场","国营中坤农场","其他"],"澄迈":["金江镇","老城镇","瑞溪镇","永发镇","加乐镇","文儒镇","中兴镇","仁兴镇","福山镇","桥头镇","大丰镇","国营红光农场","国营西达农场","国营金安农场","其他"],"临高":["临城镇","波莲镇","东英镇","博厚镇","皇桐镇","多文镇","和舍镇","南宝镇","新盈镇","调楼镇","国营红华农场","国营加来农场","其他"],"白沙":["牙叉镇","七坊镇","邦溪镇","打安镇","细水乡","元门乡","南开乡","阜龙乡","青松乡","金波乡","荣邦乡","国营白沙农场","国营龙江农场","国营邦溪农场","其他"],"昌江":["石碌镇","叉河镇","十月田镇","乌烈镇","昌化镇","海尾镇","七叉镇","王下乡","国营红林农场","国营霸王岭林场","海南矿业联合有限公司","其他"],"乐东":["抱由镇","万冲镇","大安镇","志仲镇","千家镇","九所镇","利国镇","黄流镇","佛罗镇","尖峰镇","莺歌海镇","国营山荣农场","国营乐光农场","国营保国农场","国营尖峰岭林业公司","国营莺歌海盐场","其他"],"陵水":["椰林镇","光坡镇","三才镇","英州镇","隆广镇","文罗镇","本号镇","新村镇","黎安镇","提蒙乡","群英乡","国营岭门农场","国营南平农场","国营吊罗山林业公司","其他"],"保亭":["保城镇","什玲镇","加茂镇","响水镇","新政镇","三道镇","六弓乡","南林乡","毛感乡","国营新星农场","海南保亭热带作物研究所","国营金江农场","国营三道农场","其他"],"琼中":["营根镇","湾岭镇","黎母山镇","和平镇","长征镇","红毛镇","中平镇","吊罗山乡","上安乡","什运乡","国营阳江农场","国营乌石农场","国营加钗农场","国营长征农场","国营黎母山林业公司","其他"]},"重庆":{"重庆":["万州区","涪陵区","渝中区","大渡口区","江北区","沙坪坝区","九龙坡区","南岸区","北碚区","万盛区","双挢区","渝北区","巴南区","长寿区","綦江县","潼南县","铜梁县","大足县","荣昌县","壁山县","梁平县","城口县","丰都县","垫江县","武隆县","忠县","开县","云阳县","奉节县","巫山县","巫溪县","黔江区","石柱土家族自治县","秀山土家族苗族自治县","酉阳土家族苗族自治县","彭水苗族土家族自治县","江津区","合川区","永川区","南川区","其他"]},"四川":{"成都":["锦江区","青羊区","金牛区","武侯区","成华区","龙泉驿区","青白江区","新都区","温江区","金堂县","双流县","郫县","大邑县","蒲江县","新津县","都江堰市","彭州市","邛崃市","崇州市","其他"],"自贡":["自流井区","贡井区","大安区","沿滩区","荣县","富顺县","其他"],"攀枝花":["东区","西区","仁和区","米易县","盐边县","其他"],"泸州":["江阳区","纳溪区","龙马潭区","泸县","合江县","叙永县","古蔺县","其他"],"德阳":["旌阳区","中江县","罗江县","广汉市","什邡市","绵竹市","其他"],"绵阳":["涪城区","游仙区","三台县","盐亭县","安县","梓潼县","北川羌族自治县","平武县","江油市","其他"],"广元":["利州区","元坝区","朝天区","旺苍县","青川县","剑阁县","苍溪县","其他"],"遂宁":["船山区","安居区","蓬溪县","射洪县","大英县","其他"],"内江":["市中区","东兴区","威远县","资中县","隆昌县","其他"],"乐山":["市中区","沙湾区","五通桥区","金口河区","犍为县","井研县","夹江县","沐川县","峨边彝族自治县","马边彝族自治县","峨眉山市","其他"],"南充":["顺庆区","高坪区","嘉陵区","南部县","营山县","蓬安县","仪陇县","西充县","阆中市","其他"],"眉山":["东坡区","仁寿县","彭山县","洪雅县","丹棱县","青神县","其他"],"宜宾":["翠屏区","宜宾县","南溪县","江安县","长宁县","高县","珙县","筠连县","兴文县","屏山县","其他"],"广安":["广安区","岳池县","武胜县","邻水县","华蓥市","其他"],"达川":["通川区","达县","宣汉县","开江县","大竹县","渠县","万源市","其他"],"雅安":["雨城区","名山县","荥经县","汉源县","石棉县","天全县","芦山县","宝兴县","其他"],"巴中":["巴州区","通江县","南江县","平昌县","其他"],"资阳":["雁江区","安岳县","乐至县","简阳市","其他"],"阿坝":["汶川县","理县","茂县","松潘县","九寨沟县","金川县","小金县","黑水县","马尔康县","壤塘县","阿坝县","若尔盖县","红原县","其他"],"甘孜":["康定县","泸定县","丹巴县","九龙县","雅江县","道孚县","炉霍县","甘孜县","新龙县","德格县","白玉县","石渠县","色达县","理塘县","巴塘县","乡城县","稻城县","得荣县","其他"],"凉山":["西昌市","木里藏族自治县","盐源县","德昌县","会理县","会东县","宁南县","普格县","布拖县","金阳县","昭觉县","喜德县","冕宁县","越西县","甘洛县","美姑县","雷波县","其他"]},"贵州":{"贵阳":["南明区","云岩区","花溪区","乌当区","白云区","小河区","开阳县","息烽县","修文县","清镇市","其他"],"六盘水":["钟山区","六枝特区","水城县","盘县","其他"],"遵义":["红花岗区","汇川区","遵义县","桐梓县","绥阳县","正安县","道真仡佬族苗族自治县","务川仡佬族苗族自治县","凤冈县","湄潭县","余庆县","习水县","赤水市","仁怀市","其他"],"安顺":["西秀区","平坝县","普定县","镇宁布依族苗族自治县","关岭布依族苗族自治县","紫云苗族布依族自治县","其他"],"铜仁":["铜仁市","江口县","玉屏侗族自治县","石阡县","思南县","印江土家族苗族自治县","德江县","沿河土家族自治县","松桃苗族自治县","万山特区","其他"],"黔西南":["兴义市","兴仁县","普安县","晴隆县","贞丰县","望谟县","册亨县","安龙县","其他"],"毕节":["毕节市","大方县","黔西县","金沙县","织金县","纳雍县","威宁彝族回族苗族自治县","赫章县","其他"],"黔东南":["凯里市","黄平县","施秉县","三穗县","镇远县","岑巩县","天柱县","锦屏县","剑河县","台江县","黎平县","榕江县","从江县","雷山县","麻江县","丹寨县","其他"],"黔南":["都匀市","福泉市","荔波县","贵定县","瓮安县","独山县","平塘县","罗甸县","长顺县","龙里县","惠水县","三都水族自治县","其他"]},"云南":{"昆明":["五华区","盘龙区","官渡区","西山区","东川区","呈贡县","晋宁县","富民县","宜良县","石林彝族自治县","嵩明县","禄劝彝族苗族自治县","寻甸回族彝族自治县","安宁市","其他"],"曲靖":["麒麟区","马龙县","陆良县","师宗县","罗平县","富源县","会泽县","沾益县","宣威市","其他"],"玉溪":["红塔区","江川县","澄江县","通海县","华宁县","易门县","峨山彝族自治县","新平彝族傣族自治县","元江哈尼族彝族傣族自治县","其他"],"保山":["隆阳区","施甸县","腾冲县","龙陵县","昌宁县","其他"],"昭通":["昭阳区","鲁甸县","巧家县","盐津县","大关县","永善县","绥江县","镇雄县","彝良县","威信县","水富县","其他"],"丽江":["古城区","玉龙纳西族自治县","永胜县","华坪县","宁蒗彝族自治县","其他"],"普洱":["思茅区","宁洱镇","墨江哈尼族自治县","景东彝族自治县","景谷傣族彝族自治县","镇沅彝族哈尼族拉祜族自治县","江城哈尼族彝族自治县","孟连傣族拉祜族佤族自治县","澜沧拉祜族自治县","西盟佤族自治县","其他"],"临沧":["临翔区","凤庆县","云县","永德县","镇康县","双江拉祜族佤族布朗族傣族自治县","耿马傣族佤族自治县","沧源佤族自治县","其他"],"楚雄":["楚雄市","双柏县","牟定县","南华县","姚安县","大姚县","永仁县","元谋县","武定县","禄丰县","其他"],"红河":["个旧市","开远市","蒙自县","屏边苗族自治县","建水县","石屏县","弥勒县","泸西县","元阳县","红河县","金平苗族瑶族傣族自治县","绿春县","河口瑶族自治县","其他"],"文山":["文山县","砚山县","西畴县","麻栗坡县","马关县","丘北县","广南县","富宁县","其他"],"西双版纳":["景洪市","勐海县","勐腊县","其他"],"大理":["大理市","漾濞彝族自治县","祥云县","宾川县","弥渡县","南涧彝族自治县","巍山彝族回族自治县","永平县","云龙县","洱源县","剑川县","鹤庆县","其他"],"德宏":["瑞丽市","潞西市","梁河县","盈江县","陇川县","其他"],"怒江":["泸水县","福贡县","贡山独龙族怒族自治县","兰坪白族普米族自治县","其他"],"迪庆":["香格里拉县","德钦县","维西傈僳族自治县","其他"]},"西藏":{"拉萨":["城关区","林周县","当雄县","尼木县","曲水县","堆龙德庆县","达孜县","墨竹工卡县","其他"],"昌都":["昌都县","江达县","贡觉县","类乌齐县","丁青县","察雅县","八宿县","左贡县","芒康县","洛隆县","边坝县","其他"],"山南":["乃东县","扎囊县","贡嘎县","桑日县","琼结县","曲松县","措美县","洛扎县","加查县","隆子县","错那县","浪卡子县","其他"],"日喀则":["日喀则市","南木林县","江孜县","定日县","萨迦县","拉孜县","昂仁县","谢通门县","白朗县","仁布县","康马县","定结县","仲巴县","亚东县","吉隆县","聂拉木县","萨嘎县","岗巴县","其他"],"那曲":["那曲县","嘉黎县","比如县","聂荣县","安多县","申扎县","索县","班戈县","巴青县","尼玛县","其他"],"阿里":["普兰县","札达县","噶尔县","日土县","革吉县","改则县","措勤县","其他"],"林芝":["林芝县","工布江达县","米林县","墨脱县","波密县","察隅县","朗县","其他"]},"陕西":{"西安":["新城区","碑林区","莲湖区","灞桥区","未央区","雁塔区","阎良区","临潼区","长安区","蓝田县","周至县","户县","高陵县","其他"],"铜川":["王益区","印台区","耀州区","宜君县","其他"],"宝鸡":["渭滨区","金台区","陈仓区","凤翔县","岐山县","扶风县","眉县","陇县","千阳县","麟游县","凤县","太白县","其他"],"咸阳":["秦都区","杨凌区","渭城区","三原县","泾阳县","乾县","礼泉县","永寿县","彬县","长武县","旬邑县","淳化县","武功县","兴平市","其他"],"渭南":["临渭区","华县","潼关县","大荔县","合阳县","澄城县","蒲城县","白水县","富平县","韩城市","华阴市","其他"],"延安":["宝塔区","延长县","延川县","子长县","安塞县","志丹县","吴起县","甘泉县","富县","洛川县","宜川县","黄龙县","黄陵县","其他"],"汉中":["汉台区","南郑县","城固县","洋县","西乡县","勉县","宁强县","略阳县","镇巴县","留坝县","佛坪县","其他"],"榆林":["榆阳区","神木县","府谷县","横山县","靖边县","定边县","绥德县","米脂县","佳县","吴堡县","清涧县","子洲县","其他"],"安康":["汉滨区","汉阴县","石泉县","宁陕县","紫阳县","岚皋县","平利县","镇坪县","旬阳县","白河县","其他"],"商洛":["商州区","洛南县","丹凤县","商南县","山阳县","镇安县","柞水县","其他"]},"甘肃":{"兰州":["区(县)","城关区","七里河区","西固区","安宁区","红古区","永登县","皋兰县","榆中县","其他"],"嘉峪关":["嘉峪关市","其他"],"金昌":["金川区","永昌县","其他"],"白银":["白银区","平川区","靖远县","会宁县","景泰县","其他"],"天水":["秦城区","麦积区","清水县","秦安县","甘谷县","武山县","张家川回族自治县","其他"],"武威":["凉州区","民勤县","古浪县","天祝藏族自治县","其他"],"张掖":["甘州区","肃南裕固族自治县","民乐县","临泽县","高台县","山丹县","其他"],"平凉":["崆峒区","泾川县","灵台县","崇信县","华亭县","庄浪县","静宁县","其他"],"酒泉":["肃州区","金塔县","瓜州县","肃北蒙古族自治县","阿克塞哈萨克族自治县","玉门市","敦煌市","其他"],"庆阳":["西峰区","庆城县","环县","华池县","合水县","正宁县","宁县","镇原县","其他"],"定西":["安定区","通渭县","陇西县","渭源县","临洮县","漳县","岷县","其他"],"陇南":["武都区","成县","文县","宕昌县","康县","西和县","礼县","徽县","两当县","其他"],"临夏":["临夏市","临夏县","康乐县","永靖县","广河县","和政县","东乡族自治县","积石山保安族东乡族撒拉族自治县","其他"],"甘南":["合作市","临潭县","卓尼县","舟曲县","迭部县","玛曲县","碌曲县","夏河县","其他"]},"青海":{"西宁":["城东区","城中区","城西区","城北区","大通回族土族自治县","湟中县","湟源县","其他"],"海东":["平安县","民和回族土族自治县","乐都县","互助土族自治县","化隆回族自治县","循化撒拉族自治县","其他"],"海北":["门源回族自治县","祁连县","海晏县","刚察县","其他"],"黄南":["同仁县","尖扎县","泽库县","河南蒙古族自治县","其他"],"海南":["共和县","同德县","贵德县","兴海县","贵南县","其他"],"果洛":["玛沁县","班玛县","甘德县","达日县","久治县","玛多县","其他"],"玉树":["玉树县","杂多县","称多县","治多县","囊谦县","曲麻莱县","其他"],"梅西":["格尔木市","德令哈市","乌兰县","都兰县","天峻县","其他"]},"宁夏":{"银川":["兴庆区","西夏区","金凤区","永宁县","贺兰县","灵武市","其他"],"石嘴山":["大武口区","惠农区","平罗县","其他"],"吴忠":["利通区","红寺堡区","盐池县","同心县","青铜峡市","其他"],"固原":["原州区","西吉县","隆德县","泾源县","彭阳县","其他"],"中卫":["沙坡头区","中宁县","海原县","其他"]},"新疆":{"乌鲁木齐":["天山区","沙依巴克区","新市区","水磨沟区","头屯河区","达坂城区","米东区","乌鲁木齐县","其他"],"克拉玛依":["独山子区","克拉玛依区","白碱滩区","乌尔禾区","其他"],"吐鲁番":["吐鲁番市","鄯善县","托克逊县","其他"],"哈密":["哈密市","巴里坤哈萨克自治县","伊吾县","其他"],"昌吉":["昌吉市","阜康市","呼图壁县","玛纳斯县","奇台县","吉木萨尔县","木垒哈萨克自治县","其他"],"博尔塔拉":["博乐市","精河县","温泉县","其他"],"巴音郭楞":["库尔勒市","轮台县","尉犁县","若羌县","且末县","焉耆回族自治县","和静县","和硕县","博湖县","其他"],"阿克苏":["阿克苏市","温宿县","库车县","沙雅县","新和县","拜城县","乌什县","阿瓦提县","柯坪县","其他"],"克孜勒苏":["阿图什市","阿克陶县","阿合奇县","乌恰县","其他"],"喀什":["喀什市","疏附县","疏勒县","英吉沙县","泽普县","莎车县","叶城县","麦盖提县","岳普湖县","伽师县","巴楚县","塔什库尔干县塔吉克自治","其他"],"和田":["和田市","和田县","墨玉县","皮山县","洛浦县","策勒县","于田县","民丰县","其他"],"伊犁":["伊宁市","奎屯市","伊宁县","察布查尔锡伯自治县","霍城县","巩留县","新源县","昭苏县","特克斯县","尼勒克县","其他"],"塔城":["塔城市","乌苏市","额敏县","沙湾县","托里县","裕民县","和布克赛尔蒙古自治县","其他"],"阿勒泰":["阿勒泰市","布尔津县","富蕴县","福海县","哈巴河县","青河县","吉木乃县","其他"],"石河子":["新城街道","向阳街道","红山街道","老街街道","东城街道","北泉镇","石河子乡","兵团一五二团","其他"],"阿拉尔":["金银川路街道","幸福路街道","青松路街道","南口街道","托喀依乡工业园区","兵团七团","兵团八团","兵团十团","兵团十一团","兵团十二团","兵团十三团","兵团十四团","兵团十六团","兵团第一师水利水电工程处","兵团第一师塔里木灌区水利管理处","阿拉尔农场","兵团第一师幸福农场","中心监狱","其他"],"图木舒克":["齐干却勒街道","前海街道","永安坝街道","兵团四十四团","兵团四十九团","兵团五十团","兵团五十一团","兵团五十三团","兵团图木舒克市喀拉拜勒镇","兵团图木舒克市永安坝","其他"],"五家渠":["军垦路街道","青湖路街道","人民路街道","兵团一零一团","兵团一零二团","兵团一零三团","其他"]},"香港":{"香港":["中西区","东区","九龙城区","观塘区","南区","深水区","湾仔区","黄大仙区","油尖旺区","离岛区","葵青区","北区","西贡区","沙田区","屯门区","大埔区","荃湾区","元朗区","其他"]},"澳门":{"澳门":["花地玛堂区","圣安多尼堂区","大堂区","望德堂区","风顺堂区","嘉模堂区","圣方济各堂区","其他"]},"台湾":{"台湾":["台北市","高雄市","基隆市","台中市","台南市","新竹市","嘉义市","台北县","宜兰县","新竹县","桃园县","苗栗县","台中县","彰化县","南投县","嘉义县","云林县","台南县","高雄县","屏东县","台东县","花莲县","澎湖县","其他"]}}

/***/ }),
/* 225 */
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _c(
        "el-row",
        [
          _c("el-input", {
            staticStyle: { width: "150px" },
            attrs: { placeholder: "手机号" },
            model: {
              value: _vm.search.mobile,
              callback: function($$v) {
                _vm.$set(_vm.search, "mobile", $$v)
              },
              expression: "search.mobile"
            }
          }),
          _vm._v(" "),
          _c("el-input", {
            staticStyle: { width: "150px" },
            attrs: { placeholder: "QQ" },
            model: {
              value: _vm.search.qq,
              callback: function($$v) {
                _vm.$set(_vm.search, "qq", $$v)
              },
              expression: "search.qq"
            }
          }),
          _vm._v(" "),
          _c("el-input", {
            staticStyle: { width: "150px" },
            attrs: { placeholder: "微信" },
            model: {
              value: _vm.search.wx,
              callback: function($$v) {
                _vm.$set(_vm.search, "wx", $$v)
              },
              expression: "search.wx"
            }
          }),
          _vm._v(" "),
          _c(
            "el-button",
            { attrs: { type: "primary" }, on: { click: _vm.loadData } },
            [_vm._v("搜索")]
          ),
          _vm._v(" "),
          _c("el-button", { on: { click: _vm.reset } }, [_vm._v("重置")])
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "el-row",
        [
          _c(
            "el-button",
            { attrs: { type: "primary" }, on: { click: _vm.openCreateDialog } },
            [_vm._v("添加")]
          ),
          _vm._v(" "),
          _c("el-pagination", {
            attrs: {
              layout: "prev, pager, next",
              total: _vm.dataList.meta.total,
              "page-size": _vm.dataList.meta.per_page
            },
            on: { "current-change": _vm.paginate }
          })
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "el-row",
        [
          _c(
            "el-table",
            { attrs: { data: _vm.dataList.data, stripe: "" } },
            [
              _c("el-table-column", {
                attrs: { prop: "id", label: "ID", "min-width": "50" }
              }),
              _vm._v(" "),
              _c("el-table-column", {
                attrs: { prop: "mobile", label: "手机号", "min-width": "120" }
              }),
              _vm._v(" "),
              _c("el-table-column", {
                attrs: { prop: "qq", label: "QQ", "min-width": "100" }
              }),
              _vm._v(" "),
              _c("el-table-column", {
                attrs: { prop: "wx", label: "微信", "min-width": "100" }
              }),
              _vm._v(" "),
              _c("el-table-column", {
                attrs: { prop: "name", label: "姓名", "min-width": "80" }
              }),
              _vm._v(" "),
              _c("el-table-column", {
                attrs: { prop: "age", label: "年龄", "min-width": "60" }
              }),
              _vm._v(" "),
              _c("el-table-column", {
                attrs: { label: "性别", "min-width": "60" },
                scopedSlots: _vm._u([
                  {
                    key: "default",
                    fn: function(scope) {
                      return [
                        _vm._v(
                          "\n                    " +
                            _vm._s(_vm.genderLabel[scope.row.gender]) +
                            "\n                "
                        )
                      ]
                    }
                  }
                ])
              }),
              _vm._v(" "),
              _c("el-table-column", {
                attrs: { prop: "occupation", label: "职业", "min-width": "80" }
              }),
              _vm._v(" "),
              _c("el-table-column", {
                attrs: { prop: "province", label: "省", "min-width": "80" }
              }),
              _vm._v(" "),
              _c("el-table-column", {
                attrs: { prop: "city", label: "市", "min-width": "80" }
              }),
              _vm._v(" "),
              _c("el-table-column", {
                attrs: { label: "操作", "min-width": "200" },
                scopedSlots: _vm._u([
                  {
                    key: "default",
                    fn: function(scope) {
                      return [
                        _c(
                          "el-button",
                          {
                            attrs: { type: "warning" },
                            on: {
                              click: function($event) {
                                _vm.openUpdateDialog(scope)
                              }
                            }
                          },
                          [_vm._v("修改")]
                        ),
                        _vm._v(" "),
                        _c(
                          "el-button",
                          {
                            attrs: { type: "danger" },
                            on: {
                              click: function($event) {
                                _vm.openDeleteDialog(scope)
                              }
                            }
                          },
                          [_vm._v("删除")]
                        )
                      ]
                    }
                  }
                ])
              })
            ],
            1
          )
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "el-dialog",
        {
          attrs: { title: "创建", visible: _vm.dialogCreate.display },
          on: {
            "update:visible": function($event) {
              _vm.$set(_vm.dialogCreate, "display", $event)
            }
          }
        },
        [
          _c(
            "el-form",
            {
              ref: "createForm",
              attrs: { model: _vm.dialogCreate.data, rules: _vm.rules }
            },
            [
              _c(
                "el-form-item",
                { attrs: { label: "标签", labelWidth: "100px" } },
                [
                  _c(
                    "el-select",
                    {
                      attrs: { multiple: "" },
                      model: {
                        value: _vm.dialogCreate.data.tags,
                        callback: function($$v) {
                          _vm.$set(_vm.dialogCreate.data, "tags", $$v)
                        },
                        expression: "dialogCreate.data.tags"
                      }
                    },
                    _vm._l(_vm.tagList, function(item) {
                      return _c("el-option", {
                        key: item.id,
                        attrs: { value: item.id, label: item.name }
                      })
                    })
                  )
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                {
                  attrs: {
                    prop: "mobile",
                    label: "手机号",
                    labelWidth: "100px"
                  }
                },
                [
                  _c("el-input", {
                    model: {
                      value: _vm.dialogCreate.data.mobile,
                      callback: function($$v) {
                        _vm.$set(_vm.dialogCreate.data, "mobile", $$v)
                      },
                      expression: "dialogCreate.data.mobile"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { prop: "qq", label: "QQ", labelWidth: "100px" } },
                [
                  _c("el-input", {
                    model: {
                      value: _vm.dialogCreate.data.qq,
                      callback: function($$v) {
                        _vm.$set(_vm.dialogCreate.data, "qq", $$v)
                      },
                      expression: "dialogCreate.data.qq"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { prop: "wx", label: "微信", labelWidth: "100px" } },
                [
                  _c("el-input", {
                    model: {
                      value: _vm.dialogCreate.data.wx,
                      callback: function($$v) {
                        _vm.$set(_vm.dialogCreate.data, "wx", $$v)
                      },
                      expression: "dialogCreate.data.wx"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { prop: "name", label: "姓名", labelWidth: "100px" } },
                [
                  _c("el-input", {
                    model: {
                      value: _vm.dialogCreate.data.name,
                      callback: function($$v) {
                        _vm.$set(_vm.dialogCreate.data, "name", $$v)
                      },
                      expression: "dialogCreate.data.name"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { prop: "age", label: "年龄", labelWidth: "100px" } },
                [
                  _c("el-input", {
                    model: {
                      value: _vm.dialogCreate.data.age,
                      callback: function($$v) {
                        _vm.$set(_vm.dialogCreate.data, "age", _vm._n($$v))
                      },
                      expression: "dialogCreate.data.age"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { label: "性别", labelWidth: "100px" } },
                [
                  _c(
                    "el-select",
                    {
                      model: {
                        value: _vm.dialogCreate.data.gender,
                        callback: function($$v) {
                          _vm.$set(_vm.dialogCreate.data, "gender", $$v)
                        },
                        expression: "dialogCreate.data.gender"
                      }
                    },
                    [
                      _c("el-option", { attrs: { value: "1", label: "男" } }),
                      _vm._v(" "),
                      _c("el-option", { attrs: { value: "2", label: "女" } })
                    ],
                    1
                  )
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { label: "职业", labelWidth: "100px" } },
                [
                  _c("el-input", {
                    model: {
                      value: _vm.dialogCreate.data.occupation,
                      callback: function($$v) {
                        _vm.$set(_vm.dialogCreate.data, "occupation", $$v)
                      },
                      expression: "dialogCreate.data.occupation"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { label: "省", labelWidth: "100px" } },
                [
                  _c(
                    "el-select",
                    {
                      on: { change: _vm.provinceSelect },
                      model: {
                        value: _vm.dialogCreate.data.province,
                        callback: function($$v) {
                          _vm.$set(_vm.dialogCreate.data, "province", $$v)
                        },
                        expression: "dialogCreate.data.province"
                      }
                    },
                    _vm._l(_vm.provinces, function(item) {
                      return _c("el-option", {
                        key: item,
                        attrs: { value: item, label: item }
                      })
                    })
                  )
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { label: "市", labelWidth: "100px" } },
                [
                  _c(
                    "el-select",
                    {
                      model: {
                        value: _vm.dialogCreate.data.city,
                        callback: function($$v) {
                          _vm.$set(_vm.dialogCreate.data, "city", $$v)
                        },
                        expression: "dialogCreate.data.city"
                      }
                    },
                    _vm._l(_vm.cities, function(item) {
                      return _c("el-option", {
                        key: item,
                        attrs: { value: item, label: item }
                      })
                    })
                  )
                ],
                1
              )
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "div",
            {
              staticClass: "dialog-footer",
              attrs: { slot: "footer" },
              slot: "footer"
            },
            [
              _c(
                "el-button",
                {
                  on: {
                    click: function($event) {
                      _vm.dialogCreate.display = false
                    }
                  }
                },
                [_vm._v("取 消")]
              ),
              _vm._v(" "),
              _c(
                "el-button",
                { attrs: { type: "primary" }, on: { click: _vm.doCreate } },
                [_vm._v("确 定")]
              )
            ],
            1
          )
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "el-dialog",
        {
          attrs: { title: "更新", visible: _vm.dialogUpdate.display },
          on: {
            "update:visible": function($event) {
              _vm.$set(_vm.dialogUpdate, "display", $event)
            }
          }
        },
        [
          _c(
            "el-form",
            [
              _c(
                "el-form-item",
                { attrs: { label: "标签", labelWidth: "100px" } },
                [
                  _c(
                    "el-select",
                    {
                      attrs: { multiple: "" },
                      model: {
                        value: _vm.dialogUpdate.data.tags,
                        callback: function($$v) {
                          _vm.$set(_vm.dialogUpdate.data, "tags", $$v)
                        },
                        expression: "dialogUpdate.data.tags"
                      }
                    },
                    _vm._l(_vm.tagList, function(item) {
                      return _c("el-option", {
                        key: item.id,
                        attrs: { value: item.id, label: item.name }
                      })
                    })
                  )
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { label: "手机号", labelWidth: "100px" } },
                [
                  _c("el-input", {
                    model: {
                      value: _vm.dialogUpdate.data.mobile,
                      callback: function($$v) {
                        _vm.$set(_vm.dialogUpdate.data, "mobile", $$v)
                      },
                      expression: "dialogUpdate.data.mobile"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { label: "QQ", labelWidth: "100px" } },
                [
                  _c("el-input", {
                    model: {
                      value: _vm.dialogUpdate.data.qq,
                      callback: function($$v) {
                        _vm.$set(_vm.dialogUpdate.data, "qq", $$v)
                      },
                      expression: "dialogUpdate.data.qq"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { label: "微信", labelWidth: "100px" } },
                [
                  _c("el-input", {
                    model: {
                      value: _vm.dialogUpdate.data.wx,
                      callback: function($$v) {
                        _vm.$set(_vm.dialogUpdate.data, "wx", $$v)
                      },
                      expression: "dialogUpdate.data.wx"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { label: "姓名", labelWidth: "100px" } },
                [
                  _c("el-input", {
                    model: {
                      value: _vm.dialogUpdate.data.name,
                      callback: function($$v) {
                        _vm.$set(_vm.dialogUpdate.data, "name", $$v)
                      },
                      expression: "dialogUpdate.data.name"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { label: "年龄", labelWidth: "100px" } },
                [
                  _c("el-input", {
                    model: {
                      value: _vm.dialogUpdate.data.age,
                      callback: function($$v) {
                        _vm.$set(_vm.dialogUpdate.data, "age", $$v)
                      },
                      expression: "dialogUpdate.data.age"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { label: "性别", labelWidth: "100px" } },
                [
                  _c(
                    "el-select",
                    {
                      model: {
                        value: _vm.dialogUpdate.data.gender,
                        callback: function($$v) {
                          _vm.$set(_vm.dialogUpdate.data, "gender", $$v)
                        },
                        expression: "dialogUpdate.data.gender"
                      }
                    },
                    [
                      _c("el-option", { attrs: { value: 1, label: "男" } }),
                      _vm._v(" "),
                      _c("el-option", { attrs: { value: 2, label: "女" } })
                    ],
                    1
                  )
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { label: "职业", labelWidth: "100px" } },
                [
                  _c("el-input", {
                    model: {
                      value: _vm.dialogUpdate.data.occupation,
                      callback: function($$v) {
                        _vm.$set(_vm.dialogUpdate.data, "occupation", $$v)
                      },
                      expression: "dialogUpdate.data.occupation"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { label: "省", labelWidth: "100px" } },
                [
                  _c(
                    "el-select",
                    {
                      on: { change: _vm.provinceSelect },
                      model: {
                        value: _vm.dialogUpdate.data.province,
                        callback: function($$v) {
                          _vm.$set(_vm.dialogUpdate.data, "province", $$v)
                        },
                        expression: "dialogUpdate.data.province"
                      }
                    },
                    _vm._l(_vm.provinces, function(item) {
                      return _c("el-option", {
                        key: item,
                        attrs: { value: item, label: item }
                      })
                    })
                  )
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { label: "市", labelWidth: "100px" } },
                [
                  _c(
                    "el-select",
                    {
                      model: {
                        value: _vm.dialogUpdate.data.city,
                        callback: function($$v) {
                          _vm.$set(_vm.dialogUpdate.data, "city", $$v)
                        },
                        expression: "dialogUpdate.data.city"
                      }
                    },
                    _vm._l(_vm.cities, function(item) {
                      return _c("el-option", {
                        key: item,
                        attrs: { value: item, label: item }
                      })
                    })
                  )
                ],
                1
              )
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "div",
            {
              staticClass: "dialog-footer",
              attrs: { slot: "footer" },
              slot: "footer"
            },
            [
              _c(
                "el-button",
                {
                  on: {
                    click: function($event) {
                      _vm.dialogUpdate.display = false
                    }
                  }
                },
                [_vm._v("取 消")]
              ),
              _vm._v(" "),
              _c(
                "el-button",
                { attrs: { type: "primary" }, on: { click: _vm.doUpdate } },
                [_vm._v("确 定")]
              )
            ],
            1
          )
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "el-dialog",
        {
          attrs: { title: "删除", visible: _vm.dialogDelete.display },
          on: {
            "update:visible": function($event) {
              _vm.$set(_vm.dialogDelete, "display", $event)
            }
          }
        },
        [
          _c("label", [_vm._v("是否删除？")]),
          _vm._v(" "),
          _c(
            "div",
            {
              staticClass: "dialog-footer",
              attrs: { slot: "footer" },
              slot: "footer"
            },
            [
              _c(
                "el-button",
                {
                  on: {
                    click: function($event) {
                      _vm.dialogDelete.display = false
                    }
                  }
                },
                [_vm._v("取 消")]
              ),
              _vm._v(" "),
              _c(
                "el-button",
                { attrs: { type: "primary" }, on: { click: _vm.doDelete } },
                [_vm._v("确 定")]
              )
            ],
            1
          )
        ]
      )
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-15f9f8f1", module.exports)
  }
}

/***/ }),
/* 226 */
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(227)
}
var normalizeComponent = __webpack_require__(6)
/* script */
var __vue_script__ = __webpack_require__(229)
/* template */
var __vue_template__ = __webpack_require__(230)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-4eab50c4"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/assets/js/admin/pages/Site.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {  return key !== "default" && key.substr(0, 2) !== "__"})) {  console.error("named exports are not supported in *.vue files.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-4eab50c4", Component.options)
  } else {
    hotAPI.reload("data-v-4eab50c4", Component.options)
' + '  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),
/* 227 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(228);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(5)("cc02afee", content, false);
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-4eab50c4\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0&bustCache!./Site.vue", function() {
     var newContent = require("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-4eab50c4\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0&bustCache!./Site.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 228 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(4)(undefined);
// imports


// module
exports.push([module.i, "\n.el-form .el-input[data-v-4eab50c4] {\n    width: 500px\n}\n", ""]);

// exports


/***/ }),
/* 229 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
    data: function data() {
        return {
            dataList: {}
        };
    },
    created: function created() {
        this.loadData();
    },
    methods: {
        loadData: function loadData() {
            var self = this;
            axios.get(api.siteBasic).then(function (res) {
                self.dataList = res.data.data;
            });
        },
        submit: function submit() {
            var self = this;
            axios.post(api.siteBasic, self.dataList).then(function () {
                self.$message.success('成功');
                self.loadData();
            });
        }
    }
});

/***/ }),
/* 230 */
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", [
    _c("div", { staticClass: "panel" }, [
      _c("div", { staticClass: "panel-heading" }, [_vm._v("基本设置")]),
      _vm._v(" "),
      _c(
        "div",
        { staticClass: "panel-body" },
        [
          _c(
            "el-form",
            {
              ref: "form",
              attrs: { model: _vm.dataList, "label-width": "150px" }
            },
            [
              _c(
                "el-form-item",
                { attrs: { label: "域名" } },
                [
                  [
                    _c("el-input", {
                      model: {
                        value: _vm.dataList.domain,
                        callback: function($$v) {
                          _vm.$set(_vm.dataList, "domain", $$v)
                        },
                        expression: "dataList.domain"
                      }
                    })
                  ]
                ],
                2
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { label: "名称" } },
                [
                  _c("el-input", {
                    model: {
                      value: _vm.dataList.name,
                      callback: function($$v) {
                        _vm.$set(_vm.dataList, "name", $$v)
                      },
                      expression: "dataList.name"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                [
                  _c(
                    "el-button",
                    { attrs: { type: "primary" }, on: { click: _vm.submit } },
                    [_vm._v("提交")]
                  )
                ],
                1
              )
            ],
            1
          )
        ],
        1
      )
    ])
  ])
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-4eab50c4", module.exports)
  }
}

/***/ }),
/* 231 */
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(232)
}
var normalizeComponent = __webpack_require__(6)
/* script */
var __vue_script__ = __webpack_require__(234)
/* template */
var __vue_template__ = __webpack_require__(235)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-ab13eebc"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/assets/js/admin/pages/Admin.vue"
if (Component.esModule && Object.keys(Component.esModule).some(function (key) {  return key !== "default" && key.substr(0, 2) !== "__"})) {  console.error("named exports are not supported in *.vue files.")}

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-ab13eebc", Component.options)
  } else {
    hotAPI.reload("data-v-ab13eebc", Component.options)
' + '  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),
/* 232 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(233);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(5)("f600cb8a", content, false);
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-ab13eebc\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0&bustCache!./Admin.vue", function() {
     var newContent = require("!!../../../../../node_modules/css-loader/index.js!../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-ab13eebc\",\"scoped\":true,\"hasInlineConfig\":true}!../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0&bustCache!./Admin.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 233 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(4)(undefined);
// imports


// module
exports.push([module.i, "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n", ""]);

// exports


/***/ }),
/* 234 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
    data: function data() {
        return {
            dataList: {
                meta: {},
                search: {}
            },
            dialogCreate: {
                display: false,
                data: {}
            },
            dialogUpdate: {
                display: false,
                data: {}
            },
            dialogDelete: {
                display: false
            }
        };
    },
    created: function created() {
        this.loadData();
    },
    methods: {
        loadData: function loadData(params) {
            var self = this;
            axios.get(api.adminList, {
                params: _.merge(self.dataList.search, params)
            }).then(function (res) {
                self.dataList = res.data;
            });
        },
        paginate: function paginate(page) {
            _.merge(this.dataList, { search: { page: page } });
            this.loadData();
        },
        dataCreate: function dataCreate() {
            var self = this;
            axios.post(api.adminCreate, self.dialogCreate.data).then(function () {
                self.dialogCreate.data = {};
                self.dialogCreate.display = false;
                self.$message.success('成功');
                self.loadData();
            });
        },
        dataUpdate: function dataUpdate() {
            var self = this;
            axios.post(api.adminUpdate, _.assign({ id: self.dialogUpdate.row.id }, self.dialogUpdate.data)).then(function () {
                self.dialogUpdate.data = {};
                self.dialogUpdate.display = false;
                self.$message.success('成功');
                self.loadData();
            });
        },
        dataDelete: function dataDelete() {
            var self = this;
            axios.post(api.adminDelete, { id: self.dialogDelete.row.id }).then(function () {
                self.dialogDelete.display = false;
                self.$message.success('成功');
                self.loadData();
            });
        }
    }
});

/***/ }),
/* 235 */
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _c(
        "el-row",
        [
          _c(
            "el-button",
            {
              attrs: { type: "primary" },
              on: {
                click: function($event) {
                  _vm.dialogCreate.display = true
                }
              }
            },
            [_vm._v("添加")]
          ),
          _vm._v(" "),
          _c("el-pagination", {
            attrs: {
              layout: "prev, pager, next",
              total: _vm.dataList.meta.total,
              "page-size": _vm.dataList.meta.per_page
            },
            on: { "current-change": _vm.paginate }
          })
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "el-row",
        [
          _c(
            "el-table",
            { attrs: { data: _vm.dataList.data, stripe: "" } },
            [
              _c("el-table-column", {
                attrs: { prop: "name", label: "名称", width: "200" }
              }),
              _vm._v(" "),
              _c("el-table-column", {
                attrs: { prop: "email", label: "邮箱", width: "200" }
              }),
              _vm._v(" "),
              _c("el-table-column", {
                attrs: { label: "操作" },
                scopedSlots: _vm._u([
                  {
                    key: "default",
                    fn: function(scope) {
                      return [
                        _c(
                          "el-button",
                          {
                            attrs: { type: "warning" },
                            on: {
                              click: function($event) {
                                _vm.dialogUpdate.row = scope.row
                                _vm.dialogUpdate.display = true
                              }
                            }
                          },
                          [
                            _vm._v(
                              "\n                        修改密码\n                    "
                            )
                          ]
                        ),
                        _vm._v(" "),
                        _c(
                          "el-button",
                          {
                            attrs: { type: "danger" },
                            on: {
                              click: function($event) {
                                _vm.dialogDelete.row = scope.row
                                _vm.dialogDelete.display = true
                              }
                            }
                          },
                          [
                            _vm._v(
                              "\n                        删除\n                    "
                            )
                          ]
                        )
                      ]
                    }
                  }
                ])
              })
            ],
            1
          )
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "el-dialog",
        {
          attrs: { title: "创建", visible: _vm.dialogCreate.display },
          on: {
            "update:visible": function($event) {
              _vm.$set(_vm.dialogCreate, "display", $event)
            }
          }
        },
        [
          _c(
            "el-form",
            [
              _c(
                "el-form-item",
                { attrs: { label: "用户名", labelWidth: "100px" } },
                [
                  _c("el-input", {
                    model: {
                      value: _vm.dialogCreate.data.name,
                      callback: function($$v) {
                        _vm.$set(_vm.dialogCreate.data, "name", $$v)
                      },
                      expression: "dialogCreate.data.name"
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "el-form-item",
                { attrs: { label: "密码", labelWidth: "100px" } },
                [
                  _c("el-input", {
                    attrs: { type: "password" },
                    model: {
                      value: _vm.dialogCreate.data.password,
                      callback: function($$v) {
                        _vm.$set(_vm.dialogCreate.data, "password", $$v)
                      },
                      expression: "dialogCreate.data.password"
                    }
                  })
                ],
                1
              )
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "div",
            {
              staticClass: "dialog-footer",
              attrs: { slot: "footer" },
              slot: "footer"
            },
            [
              _c(
                "el-button",
                {
                  on: {
                    click: function($event) {
                      _vm.dialogCreate.display = false
                    }
                  }
                },
                [_vm._v("取 消")]
              ),
              _vm._v(" "),
              _c(
                "el-button",
                { attrs: { type: "primary" }, on: { click: _vm.dataCreate } },
                [_vm._v("确 定")]
              )
            ],
            1
          )
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "el-dialog",
        {
          attrs: { title: "更新", visible: _vm.dialogUpdate.display },
          on: {
            "update:visible": function($event) {
              _vm.$set(_vm.dialogUpdate, "display", $event)
            }
          }
        },
        [
          _c(
            "el-form",
            [
              _c(
                "el-form-item",
                { attrs: { label: "密码", labelWidth: "100px" } },
                [
                  _c("el-input", {
                    attrs: { type: "password" },
                    model: {
                      value: _vm.dialogUpdate.data.password,
                      callback: function($$v) {
                        _vm.$set(_vm.dialogUpdate.data, "password", $$v)
                      },
                      expression: "dialogUpdate.data.password"
                    }
                  })
                ],
                1
              )
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "div",
            {
              staticClass: "dialog-footer",
              attrs: { slot: "footer" },
              slot: "footer"
            },
            [
              _c(
                "el-button",
                {
                  on: {
                    click: function($event) {
                      _vm.dialogUpdate.display = false
                    }
                  }
                },
                [_vm._v("取 消")]
              ),
              _vm._v(" "),
              _c(
                "el-button",
                { attrs: { type: "primary" }, on: { click: _vm.dataUpdate } },
                [_vm._v("确 定")]
              )
            ],
            1
          )
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "el-dialog",
        {
          attrs: { title: "删除", visible: _vm.dialogDelete.display },
          on: {
            "update:visible": function($event) {
              _vm.$set(_vm.dialogDelete, "display", $event)
            }
          }
        },
        [
          _c("label", [_vm._v("是否删除？")]),
          _vm._v(" "),
          _c(
            "div",
            {
              staticClass: "dialog-footer",
              attrs: { slot: "footer" },
              slot: "footer"
            },
            [
              _c(
                "el-button",
                {
                  on: {
                    click: function($event) {
                      _vm.dialogDelete.display = false
                    }
                  }
                },
                [_vm._v("取 消")]
              ),
              _vm._v(" "),
              _c(
                "el-button",
                { attrs: { type: "primary" }, on: { click: _vm.dataDelete } },
                [_vm._v("确 定")]
              )
            ],
            1
          )
        ]
      )
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-ab13eebc", module.exports)
  }
}

/***/ }),
/* 236 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ })
],[83]);