const axios = require('axios');
let swal = require('sweetalert2');
let CFloading = require('../../CFloading/CFloading');
document.addEventListener('DOMContentLoaded', function () {

    $('#deviation-sample').click(function(){
        CFloading.start();
        axios.get(this.href)
            .then(response => {
                swal.fire({
                    showCloseButton: true,
                    showConfirmButton: false,
                    html: response.data.html,
                    onOpen(popup) {

                    },
                });
            })
            .catch(res => {
                console.log(res);
            })
            .finally(() => {
                CFloading.stop();
            });
        return false;
    });

    $('#deviations-sample-new').click(function(){
        CFloading.start();
        axios.get(this.href)
            .then(response => {
                swal.fire({
                    showCloseButton: true,
                    showConfirmButton: false,
                    html: response.data.html,
                    onOpen(popup) {

                    },
                });
            })
            .catch(res => {
                console.log(res);
            })
            .finally(() => {
                CFloading.stop();
            });
        return false;
    });

});
