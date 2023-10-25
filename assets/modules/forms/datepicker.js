// Timepicker
require('../../modules/timepicker/timepicker.css');
require('../../modules/timepicker/timepicker.js');

document.addEventListener('DOMContentLoaded', function() {

	// Gestion datepicker
	$(".js-datepicker").datepicker({
		dateFormat: 'dd/mm/yy',
		minDate: new Date(1900, 1 - 1, 1),
		autoclose: true,
		closeText: 'Fermer',
		prevText: '&#x3c;Préc',
		nextText: 'Suiv&#x3e;',
		currentText: 'Aujourd\'hui',
		monthNames: ['Janvier','Fevrier','Mars','Avril','Mai','Juin',
			'Juillet','Aout','Septembre','Octobre','Novembre','Decembre'],
		monthNamesShort: ['Jan','Fev','Mar','Avr','Mai','Jun',
			'Jul','Aou','Sep','Oct','Nov','Dec'],
		dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
		dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
		dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
	});

	$(".js-datepicker-time").timepicker({
		interval: 15,

	});

})
