let Etmf = require('./../../Utils/Etmf');

$(document).ready(function () {

	let levels 		= $('#artefact_artefactLevels')
	let extension 	= $('#artefact_extension')
	let mailgroups 	= $('#artefact_mailgroups')

	Etmf.renderTailSelect(levels, 'Niveau')

	Etmf.renderTailSelect(extension, 'Extension (pdf, excel, csv,...)')
	Etmf.renderTailSelect(mailgroups, 'Liste de diffusion')
});
