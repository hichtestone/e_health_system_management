let Deviation = require('../../../Utils/Deviation')

// manage add action in review crex NC System -------------------------------------------------------------------------------------------------------------------
// Liste des intervenants
let elSelectIntervenant = $('#deviation_system_review_action_intervener')

// Tail select intervenant responsable
Deviation.renderTailSelect(elSelectIntervenant, 'Intervenant', false)
