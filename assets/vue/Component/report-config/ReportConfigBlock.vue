<template>
  <div id="ReportConfigBlock">

    <div class="admin-block">
      <div class="sf-read" v-for="config in configs.config">
        <h5>Configuration actuelle <br> </h5>
        <div>
          <div class="row">
            <div class="col-6">
              <p> <span class="font-weight-bold">Type de rapport: </span> {{config.type}}</p>
            </div>
          </div>
          <div class="row">
            <div class="col-6">
              <p> <span class="font-weight-bold">Type de visite: </span> {{config.visit}}</p>
            </div>
          </div>
          <div class="row">
            <div class="col-6">
              <p> <span class="font-weight-bold">Nom du rapport: </span> {{config.name}}</p>
            </div>
          </div>
          <div class="row">
            <div class="col-6">
              <p> <span class="font-weight-bold">Version n°: </span> {{config.version}}</p>
            </div>
          </div>
          <div class="row">
            <div class="col-6">
              <p> <span class="font-weight-bold">Configuration n°: </span> {{config.config}}</p>
            </div>
          </div>
        </div>

        <h5>Configuration des blocs de saisie
          <a class="btn btn-primary float-right" @click="isEditing = !isEditing" v-if="!isEditing && null === config.endedAt && configs.blocks.length > 0 && canEdit === true">Modifier la configuration</a>
          <a class="btn btn-secondary float-right notEditing" v-else-if="!isEditing && null === config.endedAt && configs.blocks.length > 0 && canEdit === false">
            <i href data-placement="right" data-toggle="tooltip" title="Impossible de modifier car la configuration est obsolete">Modifier la configuration</i>
          </a>
          <a class="btn btn-primary float-right" @click="save" v-else-if="isEditing && null === config.endedAt">Enregistrer la configuration</a>
          <a class="btn btn-secondary float-right mr-2" @click="isEditing = false" v-if="isEditing && null === config.endedAt">Annuler la configuration</a>
        </h5>
        <div class="row mt-5">
          <div class="col-3"></div>
          <div class="col-6">
            <div class="table table-bordered table-sm">
              <table class="w-100">
                <thead>
                <tr>
                  <th scope="col" class="text-center">Nom bloc</th>
                  <th scope="col" class="text-center">Type</th>
                  <th scope="col" class="text-center">Afficher</th>
                </tr>
                </thead>
                <tbody>

                <tr v-for="block in configs.blocks" v-if="block.sys">
                  <td class="text-center">{{ block.name }}</td>
                  <td class="text-center">Système</td>
                  <td class="text-center" v-if="block.active"><i class="fa fa-check c-grey"></i></td>
                  <td class="text-center" v-else><i class="fa fa-times c-grey"></i></td>
                </tr>
                <tr v-for="block in configs.blocks" v-if="block.sys === false">
                  <td class="text-center">{{ block.name }}</td>
                  <td class="text-center" v-if="block.sys">Système</td>
                  <td class="text-center" v-else>Custom</td>
                  <td class="text-center editing" v-if="block.active && isEditing" @click="change(block)"><i class="fa fa-check c-green"></i></td>
                  <td class="text-center editing" v-if="!block.active && isEditing" @click="change(block)"><i class="fa fa-times c-red"></i></td>
                  <td class="text-center" v-if="block.active && !isEditing"><i class="fa fa-check c-grey"></i></td>
                  <td class="text-center" v-if="!block.active && !isEditing"><i class="fa fa-times c-grey"></i></td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="col-3"></div>
        </div>
      </div>
    </div>

    <div class="admin-block">
      <div class="sf-read">
        <h5 class="mt-5">Historique des configurations</h5>
        <div class="row mt-3">
          <div class="col-12">
            <div class="table table-bordered table-sm table-responsive">
              <table class="w-100">
                <thead>
                <tr v-for="(history, key) in configs.histories" v-if="key <= 0">
                  <th scope="col" class="text-center">Configuration n°</th>
                  <th scope="col" class="text-center">Version du modèle</th>
                  <th scope="col" class="text-center">Configuré par </th>
                  <th scope="col" class="text-center">Début</th>
                  <th scope="col" class="text-center">Fin</th>
                  <th scope="col" class="text-center" v-for="block in history.block" v-if="!block.sys">{{ block.name}}</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="history in configs.histories">
                  <td class="text-center">{{ history.config }}</td>
                  <td class="text-center">version {{ history.version }}</td>
                  <td class="text-center">{{ history.configuredBy }}</td>
                  <td class="text-center">{{ history.startedAt|formatDate('dd-mm-yyyy') }}</td>
                  <td class="text-center">{{ history.endedAt|formatDate('dd-mm-yyyy') }}</td>
                  <td class="text-center" v-for="block in history.block" v-if="!block.sys">
                  <span v-if="block.active">
                    <i class="fa fa-check c-grey"></i>
                  </span>
                    <span v-else>
                    <i class="fa fa-times c-grey"></i>
                  </span>
                  </td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

export default  {
  name: "ReportConfigBlock",
  props: ['reportConfigVersionId', 'projectId', 'canEdit'],
  data: function () {
    return {
      isEditing: false,
      configs: {
        config: [],
        blocks: [],
        histories: []
      },
      newConfigs : {
        config: [],
        blocks: [],
        histories: []
      },
      errors: [],
      project: this.projectId,
      reportConfigVersion: this.reportConfigVersionId,
    };
  },
  mounted: function () {
    this.init();
  },
  methods: {
    // init
    init: function () {
      let uri = Routing.generate('project.settings.report.config.version.show.blocks', { projectID: this.project, reportConfigVersionID: this.reportConfigVersion })
      console.log(uri)
      this.$http.get(uri)
          .then(res => {
            this.configs.blocks = this.sortByKey(res.data.blocks, 'ordering');
            this.configs.histories = res.data.histories;
            this.configs.config = res.data.config;
          })
          .catch(err => {
            console.log(err);
          });
    },

    change: function (block) {
      if (block.id !== null) {
        block.active = block.active !== true
        this.newConfigs.blocks.push(block);
      }
    },

    save: function () {
      let uri = Routing.generate('project.settings.report.config.version.show.blocks.edit', { projectID: this.project, reportConfigVersionID: this.reportConfigVersion })
      this.$http.post(uri, {data: this.getUniqueListBy([...this.configs.blocks, ...this.newConfigs.blocks], 'id')})
          .then(res => {
            this.isEditing = false
            let configId = parseInt(this.reportConfigVersion)+1
            let redirectTo = Routing.generate('project.settings.report.config.version.show', { projectID: this.project, reportConfigVersionID: configId })

            window.location.replace(redirectTo)
          })
          .catch(err => {
            console.log(err);
          });
    },

    getUniqueListBy: function(arr, key) {
      return [...new Map(arr.map(item => [item[key], item])).values()]
    },

    sortByKey(array, key) {
      return array.sort(function (a, b) {
        let x = a[key];
        let y = b[key];
        return ((x < y) ? -1 : ((x > y) ? 1 : 0));
      });
    }

  }
};
</script>

<style scoped lang="scss">
.editing {
  cursor: pointer;
}

.notEditing {
  cursor: not-allowed;
}
</style>
