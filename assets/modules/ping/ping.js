const axios = require('axios');
const Swal = require('sweetalert2');
require('./ping.scss');

let delay = 10000;
let url = '/ping';
let loop;

function loggedOut(){
	clearInterval(loop);
	$('body').prepend('<div id="expired-session"></div>');
	Swal.fire({
		'title': 'Session expired...',
	})
		.then(function(){
			window.location.reload();
		});
}

if($('#but-logout') && $('#but-logout').length){
    loop = setInterval(function(){
        axios.get(url)
            .then(function(res){
                if(res.data.msg != 'pong'){
					loggedOut();
                }
            })
            .catch(function(res){
                //console.log(res);
                if(res.response.status == 401){
					loggedOut();
                }
            })
    },delay);
}

module.exports = loop;
