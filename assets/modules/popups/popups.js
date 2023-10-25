const Swal = require('sweetalert2');

require('./popups.scss');



/**
 * applique un event on click sur .swal-trigger. Expected:
 *   data-title: popup title
 *   href: id html container
 *   data-type: popup type
 * @constructor
 */
function Popups(){


    // popup ajax, il suffit de mettre un attribut data-popup="url" à un élément
    let ajaxPopupTrigger = Array.prototype.slice.call(document.querySelectorAll('[data-ajax-popup]'), 0);
    if (ajaxPopupTrigger.length > 0) {
        ajaxPopupTrigger.forEach( $el => {
            $el.style.cursor = 'pointer';

            $el.addEventListener('click', function() {
                let elTrigger = $el;
                CFloading.start();
                Utils.ajax({
                    type: 'GET',
                    url: elTrigger.getAttribute('data-ajax-popup'),
                    success: function (res) {
                        // define Swal options
                        let data = {
                            html: res,
                        };

                        for(let prop of ['title','type']){
                            if(elTrigger.getAttribute('data-ap-'+prop)){
                                data[prop] = elTrigger.getAttribute('data-ap-'+prop);
                            }
                        }
                        Swal.fire(data);
                    },
                    error: function (data) {
                        console.log(data);
                    },
                    complete: function () {
                        CFloading.stop();
                    }
                });
            });
        });
    }

}

module.exports = new Popups();
