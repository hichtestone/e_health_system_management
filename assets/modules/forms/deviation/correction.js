require('./correction.scss');

$(document).ready(function() {
    efficiencyMeasureFunc();

    $('#deviation_correction_efficiencyMeasure').on('change', function () {
        efficiencyMeasureFunc();
    })

    function efficiencyMeasureFunc() {
        let efficiencyMeasure = $('#deviation_correction_efficiencyMeasure').val()
        if (efficiencyMeasure == '1' || efficiencyMeasure == '2') {
            $('#deviation_correction_notEfficiencyMeasureReason').removeAttr('disabled');
        } else {
            $('#deviation_correction_notEfficiencyMeasureReason').attr("disabled", "disabled");
            $('#deviation_correction_notEfficiencyMeasureReason').attr("required", "false");
            $('#deviation_correction_notEfficiencyMeasureReason').val(null);

        }
    }

});
