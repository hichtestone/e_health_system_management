<template>

  <div id="ListVisitTable">

    <a class="btn btn-primary" v-if="isGrantedRead && items.length>0">
     <download-csv :data="exporter">
        {{ entity_VisitPatient_field_export }}
      </download-csv>
    </a>
    <a class="btn btn-success" @click.prevent="initModal(null)" v-if="isGrantedWrite && items.length>0 && isGrantedProjectWrite">{{ entity_VisitPatient_field_modal_title}}</a>

    <div class="mt-3 row" v-if="items.length>0">
      <div class="col-2">
        <FormulateInput
            name="filter-center"
            type="select"
            :options="filter(items, 'center')"
            placeholder="N°Centre"
            v-model="filters.center.value"
        />
      </div>
      <div class="col-2">
        <FormulateInput
            name="filter-patient"
            type="select"
            :options="filter(items, 'patient')"
            placeholder="N°Patient"
            v-model="filters.patient.value"
        />
      </div>
      <div class="col-2">
        <FormulateInput
            name="filter-visit"
            type="select"
            :options="filter(items, 'visit')"
            placeholder="Visit"
            v-model="filters.visit.value"
        />
      </div>
      <div class="col-2">
        <FormulateInput
            name="filter-status"
            type="select"
            :options="filter(items, 'statusLabel')"
            placeholder="Statut"
            v-model="filters.statusLabel.value"
        />
      </div>
      <div class="col-2">
        <FormulateInput
            name="filter-phase"
            type="select"
            :options="filter(items, 'phase')"
            placeholder="Phase"
            v-model="filters.phase.value"
        />
      </div>

      <div class="col-2">
        <FormulateInput
            name="filter-occuredAt"
            type="select"
            :options="filter(items, 'occuredAt')"
            placeholder="Date réelle"
            v-model="filters.occuredAt.value"
        />
      </div>

      <div class="col-2">
        <FormulateInput
            format="dd/mm/yyyy"
            name="filter-monitoredAt"
            type="select"
            :options="filter(items, 'monitoredAt')"
            placeholder="Date prévisionnelle"
            v-model="filters.monitoredAt.value"
        />
      </div>

    </div>
    <div class="mt-3 row" v-if="items.length>0">
      <div class="col-4">
        <a class="btn btn-secondary" @click="resetFilter" v-if="items.length>0">{{ entity_VisitPatient_field_reset }}</a>
      </div>
    </div>

    <v-table
        class="table mt-3"
        :data="items"
        :filters="filters"
        >
      <thead slot="head">
          <th>
            <input type="checkbox" @click="selectAll" v-model="allSelected" v-if="isGrantedProjectWrite && isGrantedProjectWrite">
          </th>
          <v-th sort-key="center">{{ entity_VisitPatient_field_center }}</v-th>
          <v-th sort-key="patient">{{ entity_VisitPatient_field_patient }}</v-th>
          <v-th sort-key="visit">{{ entity_VisitPatient_field_visit }}</v-th>
          <v-th sort-key="phase">{{ entity_VisitPatient_field_phase }}</v-th>
          <v-th sort-key="monitoredAt">{{ entity_VisitPatient_field_monitoredAt }}</v-th>
          <v-th sort-key="occuredAt">{{ entity_VisitPatient_field_occuredAt }} </v-th>
          <v-th sort-key="status">{{ entity_VisitPatient_field_status }}</v-th>
      </thead>
      <tbody slot="body" slot-scope="{displayData}">
        <v-tr
            v-for="row in displayData"
            :key="row.guid"
            :row="row">
          <td>
            <span v-if="row.status !== '3' && row.status !== '4' && isGrantedProjectWrite">
              <input type="checkbox" v-model="visitIds" @click="select" :value="row">
            </span>
            <span v-else>
              <input type="checkbox" v-model="visitIds" @click="select" :value="row" disabled checked="false">
            </span>
          </td>
          <td>{{ row.center }}</td>
          <td>{{ row.patient }}</td>
          <td>{{ row.visit }}</td>
          <td>{{ row.phase }}</td>
          <td>
            <span v-if="isGrantedWrite && (eCRFType === 'Autre' || eCRFType === 'Papier') && isGrantedProjectWrite">
               <input
                   type="date"
                   v-model="row.monitoredAt"
                   format="dd-MM-yyyy"
                   class="form-control"
                   disabled
               />
            </span>
            <span v-else>
               <input
                   type="date"
                   v-model="row.monitoredAt"
                   format="dd-MM-yyyy"
                   class="form-control"
                   disabled
               />
            </span>
          </td>
          <td>
            <span v-if="isGrantedWrite && (eCRFType === 'Autre' || eCRFType === 'Papier') && isGrantedProjectWrite">
              <span v-if="row.status === '2'">
                <input
                    type="date"
                    v-model="row.occuredAt"
                    format="dd-MM-yyyy"
                    class="form-control"
                    disabled
                />
              </span>
               <span v-if="row.status !== '2'">
                 <input
                     type="date"
                     v-model="row.occuredAt"
                     format="dd-MM-yyyy"
                     @input="dateSelected(row)"
                     class="form-control"
                 />
              </span>
            </span>
            <span v-else>
               <input
                   type="date"
                   v-model="row.occuredAt"
                   format="dd-MM-yyyy"
                   class="form-control"
                   disabled
               />
            </span>
          </td>
          <td>
            <span v-if="isGrantedProjectWrite">
              <span v-if="row.status === '2'" @click="changeStatus(row)">
                <i class="fas fa-check-circle fa-color-green"></i> {{ row.label }}
              </span>

              <span v-if="row.status === '1'" @click="changeStatus(row)">
                <i class="fas fa-exclamation-triangle fa-color-yellow"></i> {{ row.label }} <span class="badge badge-danger" v-if="row.badge !== ''">{{ row.badge }}</span>
              </span>
            </span>

            <span v-else>
              <span v-if="row.status === '2'">
                <i class="fas fa-check-circle fa-color-green"></i> {{ row.label }}
              </span>

              <span v-if="row.status === '1'">
                <i class="fas fa-exclamation-triangle fa-color-yellow"></i> {{ row.label }} <span class="badge badge-danger" v-if="row.badge !== ''">{{ row.badge }}</span>
              </span>
            </span>

            <span v-if="row.status === '3'">
              <i class="fas fa-exclamation-triangle fa-color-red"></i> {{ row.label }}
            </span>

            <span v-if="row.status === '4'">
               <span class="badge badge-danger" v-if="row.badge !== ''">{{ row.badge }}</span>
            </span>
          </td>
        </v-tr>
      </tbody>
    </v-table>

    <div id="overlay" class="modal" tabindex="-1" role="dialog" v-show="displayModal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ entity_VisitPatient_field_modal_title }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="displayModal=false">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-group">
                <FormulateInput
                    name="status"
                    type="select"
                    :options="filter(status, 'label')"
                    v-model="modalItem.label"
                />
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" @click="save(modalItem, visitIds)" v-if="modalItem.label && visitIds.length>0">
              {{ entity_VisitPatient_field_modal_add }}</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="displayModal=false">{{ entity_VisitPatient_field_modal_cancel }}</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal-backdrop fade show" v-show="displayModal"></div>

  </div>

</template>

<script>

/**
 * @typedef {{id: int, name: string}} Center
 * @typedef {{id: int, number: string, consentAt: string, inclusionAt: string, center: {Center[]}}} Patient
 */

module.exports = {

  name: "ListVisitTable",

  data: function() {
    return {
      // From translations/messages.en.yml
      entity_VisitPatient_field_center: this.getDomTranslation('data-entity-VisitPatient-field-center'),
      entity_VisitPatient_field_patient: this.getDomTranslation('data-entity-VisitPatient-field-patient'),
      entity_VisitPatient_field_visit: this.getDomTranslation('data-entity-VisitPatient-field-visit'),
      entity_VisitPatient_field_phase: this.getDomTranslation('data-entity-VisitPatient-field-phase'),
      entity_VisitPatient_field_monitoredAt: this.getDomTranslation('data-entity-VisitPatient-field-monitoredAt'),
      entity_VisitPatient_field_occuredAt: this.getDomTranslation('data-entity-VisitPatient-field-occuredAt'),
      entity_VisitPatient_field_status: this.getDomTranslation('data-entity-VisitPatient-field-status'),
      entity_VisitPatient_field_export: this.getDomTranslation('data-entity-VisitPatient-field-export'),
      entity_VisitPatient_field_reset: this.getDomTranslation('data-entity-VisitPatient-field-reset'),
      entity_VisitPatient_field_modal_title: this.getDomTranslation('data-entity-VisitPatient-field-modal-title'),
      entity_VisitPatient_field_modal_add: this.getDomTranslation('data-entity-VisitPatient-field-modal-add'),
      entity_VisitPatient_field_modal_cancel: this.getDomTranslation('data-entity-VisitPatient-field-modal-cancel'),
      // eCRFType
      eCRFType: this.getDomParameterTwig('data-ecrf-type'),
      // L'utilisateur a-t-il tous les droits nécessaires pour créer une variable de suivi
      isGrantedWrite: this.getDomParameterTwig('data-isGrantedWrite'),
      isGrantedRead: this.getDomParameterTwig('data-isGrantedRead'),
      // check si on peut entrer dans un projet en écriture
      isGrantedProjectWrite: this.getDomParameterTwig('data-isGrantedProjectWrite'),
      errors: [],
      items: [],
      exporter: [],
      filters: {
        center: { value: '', keys: ['center'] },
        patient: { value: '', keys: ['patient'] },
        visit: { value: '', keys: ['visit'] },
        phase: { value: '', keys: ['phase'], custom: this.phaseFilter },
        monitoredAt: { value: '', keys: ['monitoredAt'] },
        occuredAt: { value: '', keys: ['occuredAt'] },
        statusLabel: { value: '', keys: ['statusLabel'] },
      },
      dateTemp: "",
      selectedRows: [],
      status : [],
      modalItem: {
        id: null,
        label: ''
      },
      displayModal: false,
      selected: [],
      allSelected: false,
      visitIds: []
    }
  },

  props: {
    projectId: {
      type: Number,
      required: true
    }
  },

  beforeMount() {

    CFloading.start()
  },

  mounted: function(){

    this.refresh();
  },

  methods: {

    phaseFilter (filterValue, row) {
      return filterValue === '' ? true : row.phase === filterValue
    },

    selectAll: function() {
      this.visitIds = [];
      let all = (this.allSelected) ? false : true;
      if (all) {
        this.items = this.items.filter((item) => item.status === '1' || item.status === '2');
        for (let i in this.items) {
          this.visitIds.push(this.items[i])
        }
      } else {
        this.allSelected = false
        this.refresh();
      }
    },

    select: function() {
      this.allSelected = false;
    },

    changeStatus : function(row) {
      let uri = '/projects/' + this.projectId + '/patientTracking/visit/status/update';
      this.$http.put(uri, {
        data: row,

      }).then(res => {
        this.refresh();
      }).catch(err => {
        console.log(err);
      });
    },

    save: function(status, rows) {
      let uri = '/projects/' + this.projectId + '/patientTracking/visit/status/edit';
      this.$http.put(uri, {
        status: status,
        data: rows,
        }).then(res => {
            this.displayModal = false;
            this.refresh()
        }).catch(err => {
          console.log(err);
        });
    },

    initModal: function() {
      // List de visit-patient "status"
      let uri = '/projects/' + this.projectId + '/patientTracking/visit/status';
      this.$http.get(uri)
          .then(res => {
            this.status = res.data
          });

      this.displayModal = true;
    },

    resetFilter: function () {
      this.filters = {
        center: { value: '', keys: ['center'] },
        patient: { value: '', keys: ['patient'] },
        visit: { value: '', keys: ['visit'] },
        phase: { value: '', keys: ['phase'] },
        monitoredAt: { value: '', keys: ['monitoredAt'] },
        occuredAt: { value: '', keys: ['occuredAt'] },
        statusLabel: { value: '', keys: ['statusLabel'] },
      };
    },

    dateSelected (item) {
      this.$http
          .put("/projects/" + this.projectId + "/patientTracking/visit/" + item.id + "/edit", {
            item: item,
          })
          .then((response) => {
            response.data;
            this.refresh();
          })
          .catch(function (error) {
            console.log(error);
          });
    },

    /**
     * Liste de VisitPatient
     */
    refresh: function() {

      let uri = '/projects/' + this.projectId + '/patientTracking/visit/visitPatient';
      this.$http.get(uri).then(res => {
        this.items = res.data.filter(function (el) {
          return el != null;
        });
        CFloading.stop()
      });

      // NE PAS DECOMMENTER SANS AUTORISATION de Rémy

       // uri = '/projects/' + this.projectId + '/patientTracking/visit/visitPatient/export';
       // this.$http.get(uri).then(res => {
       //   this.exporter = res.data
       // });
    },

    filter(items, key) {
      let options = [];

      switch (key) {
        case 'center':
          options = items.map(item => {
            return `${item.center}`
          });
          break;
        case 'patient':
          options = items.map(item => {
            return `${item.patient}`
          });
          break;
        case 'visit':
          options = items.map(item => {
            return `${item.visit}`
          });
          break;
        case 'phase':
          options = items.map(item => {
            return `${item.phase}`
          });
          break;
        case 'status':
          options = items.map(item => {
            return `${item.status}`
          });
          break;
        case 'statusLabel':
          options = items.map(item => {
            return `${item.statusLabel}`
          });
          break;
        case 'label':
          options = items.map(item => {
            return `${item.label}`
          });
          break;
        case 'occuredAt':
          options = items.map(item => {
            return `${item.occuredAt}`
          });
          break;
        case 'monitoredAt':
          options = items.map(item => {
              return `${item.monitoredAt}`
          });
          break;
        default:
          console.log(`Default`);
      }

      options = this.clean(options);
      return options.filter((elem , index, self) => index === self.indexOf(elem) &&  elem !== '');
    },

    // Get parameter text from twig
    getDomParameterTwig(key) {
      return document.querySelector('['+key+']').getAttribute(key)
    },

    // Get translated text from yaml
    getDomTranslation(key) {
      if (null == document.querySelector('.data_translate').getAttribute(key)) {
        return key;
      }
      return document.querySelector('.data_translate').getAttribute(key);
    },

     clean(obj) {
       for (let propName in obj) {
        if (obj[propName] === 'null' || obj[propName] === undefined) {
          delete obj[propName];
        }
      }
      return obj
    },

    convertDate(inputFormat) {
      function pad(s) { return (s < 10) ? '0' + s : s; }
      let d = new Date(inputFormat)
      return [pad(d.getDate()), pad(d.getMonth()+1), d.getFullYear()].join('/')
    }
  }
}
</script>

<style scoped lang="scss">
table {
  i {
    cursor: pointer;

    &:hover {
      filter: brightness(0.8);
    }
  }
}

.modal-dialog {
  margin-top: 200px;
}

#overlay {
  display: block;
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(0, 0, 0, 0.4);
  overflow-y: auto;
}

.fa-color-green {
  color: #28a745;
}

.fa-color-red {
  color: #dc3545;
}

.fa-color-yellow {
  color: #ffc107;
}
</style>
