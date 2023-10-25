require('./correction.scss')

let Deviation = require('../../../Utils/Deviation')

// manage efficiencyMeasure -------------------------------------------------------------------------------------------------------------------
let origin = $('#deviation_system_correction_efficiencyMeasure')
let target = $('#deviation_system_correction_notEfficiencyMeasureReason')

Deviation.efficiencyMeasureCorrection(origin, target, 'edit')

origin.on('change', function () {
	Deviation.efficiencyMeasureCorrection(origin, target, 'edit')
});
