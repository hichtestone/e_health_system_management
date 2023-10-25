'use strict';

const io = require('socket.io-client');
const Swal = require('sweetalert2');
const CFLoading = require('../CFloading/CFloading');
let socket;

let arrTrad = {
	"logout_expired": {
		en: 'Your session has expired...',
		fr: 'Votre session a expiré...'
	},
	"logout_logout": {
		en: 'You have been disconnected...',
		fr: 'Vous avez été déconnecté...'
	},
	prelogout: {
		en: 'You will be disconnected in __TIME__ seconds.<br />Please, move your mouse to extend your session.',
		fr: 'Vous allez être déconnecté dans __TIME__ secondes.<br />Bougez la souris pour étendre votre session.'
	},
	refresh: {
		en: 'Please refresh the page',
		fr: 'Rafraichir la page'
	},
	"refresh_but": {
		en: 'Refresh',
		fr: 'Rafraichir'
	}
};

window.addEventListener('DOMContentLoaded', () => {

	let jsConfigEl = document.getElementById('js-infos');
	let locale = document.documentElement.lang;

	if (jsConfigEl !== null) {

		socket = io(jsConfigEl.getAttribute('data-socket-host'));
		let loggedIn = false;
		let popupPreLogout = false;
		let preLogoutHandler;
		let preLougoutPopup;

		// socket connection
		let p1 = new Promise((resolve) => {
			socket.on('connect', () => {
				socket.on('disconnect', () => {
					console.log('You have been disconnected');
				});
				resolve();
			});
		});

		Promise.all([p1])
			.then(() => {
				let appId = jsConfigEl.getAttribute('data-this-app');
				let deviceToken = jsConfigEl.getAttribute('data-device-token');
				socket.emit('handshake', {appId: appId, deviceToken: deviceToken});

				// authenticated
				socket.on('handshakeOK', () => {
					loggedIn = true;

					// logout button
					document.getElementById('but-logout').addEventListener('click', () => {
						socket.emit('logout');
						socket.close();
					});

					// popup disconnected in 30 sec
					socket.on('preLogout', function (countDown) {
						console.log(countDown);
						popupPreLogout = true;
						preLougoutPopup = Swal.fire({
							html: `<p>${arrTrad.prelogout[locale].replace('__TIME__', `<strong>${countDown}</strong>`)}</p>`,
							onBeforeOpen: () => {
								// set compteur
								Swal.showLoading();
								preLogoutHandler = setInterval(() => {
									Swal.getContent().querySelector('strong').textContent = Math.round(Swal.getTimerLeft() / 1000);
								}, 100);
								// set cancel event
								socket.on('cancelPreLogout', function () {
									preLougoutPopup.close();
								});
							},
							onClose: () => {
								clearInterval(preLogoutHandler);
								$(window).off('mousemove.close');
							},
							timer: countDown*1000,
							title: 'Your session will expired soon'
						});
					});

					// popup logged out
					socket.on('logout', (data) => {
						let action = typeof data === 'undefined' ? 'expired' : data.action;
						clearInterval(preLogoutHandler);
						loggedIn = false;
						socket.close();
						if (preLougoutPopup) {
							preLougoutPopup.close();
						}
						Swal.fire({
							allowEnterKey: false,
							allowEscapeKey: false,
							allowOutsideClick: false,
							confirmButtonText: arrTrad.refresh_but[locale],
							html: `<p>${arrTrad.refresh[locale]}</p>`,
							onClose: function () {
								CFLoading.start();
								window.location.reload();
							},
							title: arrTrad[`logout_${action}`][locale]
						});
					});

					// ping server
					let betweenAliveTime = 2000;
					let lastAlive = Date.now();
					window.addEventListener('mousemove', () => {
						if (loggedIn === true) {
							if (popupPreLogout) {
								clearInterval(preLogoutHandler);
								preLougoutPopup.close();
								popupPreLogout = false;
							}
							if (Date.now() - lastAlive > betweenAliveTime) {
								lastAlive = Date.now();
								socket.emit('alive', {});
							}
						}
					});
				});

			})
			.catch(err => {
				console.log(err);
			});

	}

});

module.exports = socket;
