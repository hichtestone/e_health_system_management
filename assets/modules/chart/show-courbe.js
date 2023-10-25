let moment = require('moment');
document.addEventListener('DOMContentLoaded', function() {

    // Lancer uniquement dans Param courbe
    if (null == document.getElementById('chart')) {
        return;
    }

    let data = {}

    // Id du projet en cours
    let project_id = null !== document.querySelector('[data-project-id]') ? document.querySelector('[data-project-id]').getAttribute('data-project-id') : 0;


    let curbe_inclusion = getDomTranslation('data-entity-CourbeSetting-curbe-inclusion');
    let curbe_real = getDomTranslation('data-entity-CourbeSetting-curbe-real');
    let curbe_theoretical = getDomTranslation('data-entity-CourbeSetting-curbe-theoretical');

    // Ne rien faire si le projet non trouve
    if (0 === project_id) {
        return;
    }

    let courbeID = null !== document.querySelector('[data-courbe-id]') ? document.querySelector('[data-courbe-id]').getAttribute('data-courbe-id') : 0;

    // Ne rien faire si le projet non trouve
    if (0 === courbeID) {
        return;
    }

   let url = Routing.generate('project.courbe.setting.show.ajax', { id: project_id, courbe: courbeID })

   $.get(url ,data).then(function (data) {

        let ctx = document.getElementById('myChart').getContext('2d');
        let myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.abcisse,
            datasets: [
                {
                    label: curbe_real !=='undefined' ? curbe_real : "Courbe réelle",

                    data: data.nbrepatient,
                    backgroundColor: [
                        'rgba(255 , 255, 255, 0.2)', // Todo Parametriser

                    ],
                    borderColor: [
                        'rgba(0, 0, 255, 1)', // Todo Parametriser

                    ],
                    borderWidth: 1
                },
                {
                label: curbe_theoretical !=='undefined' ? curbe_theoretical : "Courbe théorique",

                data:  data.point,
                backgroundColor: [
                    'rgba(255 , 255, 255, 0.2)', // Todo Parametriser

                ],
                borderColor: [
                    'rgb(255,105,132)', // Todo Parametriser

                ],
                borderWidth: 1
            }

            ]
        },

        options: {
            title: {
                display: true,
                text: curbe_inclusion !=='undefined' ? curbe_inclusion : "Courbe d'inclusion"
            },


            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
         });

       let image = myChart.toBase64Image();

       document.getElementById('btn-download').onclick = function() {
           // Trigger the download
           let a = document.createElement('a');
           a.href = myChart.toBase64Image();
           a.download = 'courbe.png';
           a.click();
       }
   })


    // Get translated text from yaml
    function getDomTranslation(key) {

        if (null == document.querySelector('.data_translate').getAttribute(key)) {
            return key;
        }
        return document.querySelector('.data_translate').getAttribute(key);
    }

});
