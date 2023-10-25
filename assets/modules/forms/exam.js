let Main = require('./../Utils/main')

$(document).ready(function () {

	let examType = $('#exam_type')
	let examTypeReason = $('#exam_typeReason')

	Main.reasonDescription(examType, examTypeReason, 'examen paraclinique (imagerie)');

	examType.on('change', function () {
		Main.reasonDescription(examType, examTypeReason, 'examen paraclinique (imagerie)');
	})

})
