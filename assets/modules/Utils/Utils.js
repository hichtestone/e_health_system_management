require('./Utils.scss');

const Axios = require('axios');



// fonctions utiles

function Utils(){


  let that = this;




  /*
    display utils
   */
  this.ajaxResBgEffect = function(element, success){
    let cMem = element.css('background-color');
    let c = (success ? 'green' : 'red');
    element.css('background-color',c);
    setTimeout(function(){
      element.css('background-color',cMem);
    },500)
  }





  /*
    -------------------------------------------------------
    -------------------------------------------------------
    ----------------- PHP-like ----------------------------
    -------------------------------------------------------
   */

  // first letter uppercase
  this.ucfirst = function(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
  };

  // get unique id
  this.uniqid = function(){
    return (new Date().getTime() + Math.floor((Math.random()*10000)+1)).toString(16);
  };



  /*
  -------------------------------------------------------
  -------------------------------------------------------
  ----------------- json job ----------------------------
  -------------------------------------------------------
 */

  // todo améliorer pour cas ou cle multiple
  this.jsonSerializeForm = function($form){
    let unindexed_array = $form.serializeArray();
    let indexed_array = {};

    $.map(unindexed_array, function(n, i){
      indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
  };

  // concatenate 2 json objects
  this.jsonConcat = function(o1, o2) {
    for (let key in o2) {
      o1[key] = o2[key];
    }
    return o1;
  };



  /*
-------------------------------------------------------
-------------------------------------------------------
---- wrapper ajax pour gérer expired session ----------
-------------------------------------------------------
*/
  this.ajax = function(options){

    // param obligatoires
    let json = {
      url: options.url,
      success: function(response){

        // session expired
        if(response.status == 99){
          let nbLoadingActive = CFloading.getNbActive();
          console.log('session timeout');
        }
        options.success(response);
      }
    };
    // params optionnels
    ['error','complete'].forEach(function(prop){
      if(typeof options[prop] != 'undefined'){
        json[prop] = function(response){
          options[prop](response);
        }
      }
    });
    ['type','data','processData','contentType'].forEach(function(prop){
      if(typeof options[prop] != 'undefined'){
        json[prop] = options[prop];
      }
    });
    $.ajax(json);
  }

}
module.exports = new Utils();
