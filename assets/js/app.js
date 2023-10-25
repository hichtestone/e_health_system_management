// ------------ any CSS you import will output into a single css file (app.css in this case)
import '../css/app.scss'

// JQuery UI
window.$ = global.$ = require('webpack-jquery-ui')

// JQuery
var $ = require('jquery')
window.$ = $;
window.jQuery = $;

// Bootsrap
require('popper.js')
require('bootstrap')

// internal modules
global.CFloading = require('../modules/CFloading/CFloading')
global.Utils = require('../modules/Utils/Utils')

//Query-builder

// Swal
// global.Swal = require('sweetalert2');

require('../modules/forms/datepicker')

// Jquery QueryBuilder
require('jQuery-QueryBuilder/dist/css/query-builder.default.min.css')
require('jQuery-QueryBuilder/dist/js/query-builder')
require('../modules/forms/query_builder')

// swal link
require('../modules/sw-link/sw-link')
require('../modules/listgen/ListGen')
require('../modules/CFSwitchableForm/CFSwitchableForm')

// forms
require('../modules/forms/user')
require('../modules/forms/project')
require('../modules/forms/profile')
require('../modules/forms/publication')
require('../modules/forms/funding')
require('../modules/forms/meeting')
require('../modules/forms/training')
require('../modules/forms/submission')
require('../modules/forms/interlocutor')
require('../modules/forms/center')
require('../modules/forms/document')
require('../modules/forms/institution')
require('../modules/forms/rule')
require('../modules/forms/project-dates')
require('../modules/forms/audit-trail')
require('../modules/forms/contact')
require('../modules/forms/drug')
require('../modules/forms/version')
require('../modules/forms/report/report')
require('../modules/forms/exam')

// VUEJS Component
require('../vue/index')

require('../modules/forms/courbe')

//add chartjs
require('../modules/chart/Chart.bundle')
require('../modules/chart/Chart')
require('../modules/chart/Chart.bundle.min')
require('../modules/chart/Chart.min')
require('../modules/chart/show-courbe')

// Flatpickr
require("flatpickr/dist/themes/material_blue.css")
global.flatpickr = require('flatpickr')

// internal classes
const popups 				= require('../modules/popups/popups')
const popupsConfirmation 	= require('../modules/popups/popups-confirmation')
const treeview 				= require('../modules/treeview/treeview')
const Textarray 			= require('../modules/forms/text-array')

// const ping 					= require('../modules/ping/ping')

$(function () {

	$(document).ready(function () {

		setTimeout(function(){
			// localization
			let locales = ['en', 'fr'];
			locales.forEach(function (locale) {
				// flatpickr
				if (locale !== 'en') {
					// require('flatpickr/dist/l10n/' + locale + '.js');
				}
			});
		}, 20000);

		$('#mpFlash').fadeIn(1000);

		setTimeout(function () {

			$('#mpFlash').fadeOut(2000)
		}, 2000);

		$('[data-toggle="tooltip"]').tooltip();
	})
})

const routes = require('../../public/js/fos_js_routes.json')
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js'
Routing.setRoutingData(routes)

// Setting Routing as global there
global.Routing = Routing

// ETMF
require('./app-etmf')
