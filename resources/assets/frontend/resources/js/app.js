window._ = require('lodash');

window.$ = window.jQuery = require('jquery');

window.axios = require('axios');

window.axios.defaults.headers.common = {
  'X-CSRF-TOKEN': global_vars.csrf_token,
  'X-Requested-With': 'XMLHttpRequest'
};

require('./common_helper');

import KommercioFrontend from './kommercio_frontend';

$(document).ready(function(){
  KommercioFrontend.init();
});