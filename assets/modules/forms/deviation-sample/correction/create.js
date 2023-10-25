require('./correction.scss');

let Deviation = require('../../../Utils/Deviation');

// manage efficiencyMeasure -------------------------------------------------------------------------------------------------------------------
let origin = $('#deviation_sample_correction_efficiencyMeasure')
let target = $('#deviation_sample_correction_notEfficiencyMeasureReason')

Deviation.efficiencyMeasureCorrection(origin, target, 'edit')

origin.on('change', function () {
	Deviation.efficiencyMeasureCorrection(origin, target, 'edit')
});
