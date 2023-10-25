<template>

  <div id="ListPatientTable">

    <a class="btn btn-primary" @click.prevent="initModal(null)"
       v-if="isGrantedWrite && (eCRFType === 'Autre' || eCRFType === 'Papier') && isGrantedProjectWrite">{{ entity_PatientTracking_action_new }}
    </a>

    <a class="btn btn-primary" v-if="isGrantedRead && items.table.length>0">
      <download-csv :data="items.export">{{ entity_PatientTracking_action_export}}</download-csv>
    </a>

    <a class="btn btn-secondary" @click="resetFilter" v-if="items.table.length>0">{{ entity_PatientTracking_action_reset }}</a>

    <div class="mt-3 row" v-for="(row, key) in items.table" v-if="key <= 0">
      <div class="col-2" v-for="(val, index) in row"
           v-if="!['idPatient', 'valuefilled_count', 'isArchived'].includes(index) && filter(items.table, index).length>0 ">
        <FormulateInput
            type="select"
            :options="filter(items.table, index)"
            :placeholder="index"
            v-on:change="filterTable($event, index)"
        />
      </div>
    </div>

    <v-table class="table mt-3" :data="items.table">

      <thead slot="head" v-for="(item, key) in items.table" v-if="key <= 0">
        <v-th sort-key="key2" v-for="(val, index) in item" :key="index.id" v-if="!['idPatient', 'valuefilled_count', 'isArchived'].includes(index)">{{ index.replace(/['"]+/g, '') }}</v-th>
        <v-th sort-key="action">Action</v-th>
      </thead>

      <tbody slot="body" slot-scope="{displayData}">

      <tr v-for="row in displayData">

        <!-- Col Donnees du patient - ne pas afficher si 'idPatient', 'valuefilled_count' -->
        <td v-for="(val, key) in row" v-if="!['idPatient', 'valuefilled_count', 'isArchived'].includes(key)">

          <span v-if="typeof val === 'object'">

                <!-- Type Date -->
                <span v-if="val.type === '2'">
                  {{ val.value|formatDate('dd-mm-yyyy') }}
                </span>

              <!-- Type texte, entier -->
                <span v-if="['1', '3'].includes(val.type)">
                  {{ val.value }}
                </span>

              <span v-if="'4' === val.type">
                <span v-if="val.code.length > 0">
                  {{ Object.values(val.code[0]).toString() }}
                </span>
              </span>

            </span>

          <!-- Type System -->
          <span v-else>
            {{ displayValue(val) }}
          </span>
        </td>
        <!-- ./Col Donnees du patient -->

        <!-- Col Action -->
        <td>
            <span>
              <i
                  class="fa fa-edit" @click="initModalUpdate(row)"
                  v-if="isGrantedWrite && (eCRFType === 'Autre' || eCRFType === 'Papier') && row.archived === 'Non' && isGrantedProjectWrite"
              ></i>
            </span>
          <span v-if="row.archived === 'Non'">
                <i
                    class="fa fa-archive" @click="archive(row)"
                    v-if="row.isArchived && isGrantedArchive && (eCRFType === 'Autre' || eCRFType === 'Papier') && isGrantedProjectWrite"
                ></i>
              </span>
          <span v-else>
                <i
                    class="fa fa-box-open" @click="restore(row)"
                    v-if="isGrantedArchive && (eCRFType === 'Autre' || eCRFType === 'Papier')"
                ></i>
              </span>
        </td>
        <!-- ./Col Action -->

      </tr>

      </tbody>

    </v-table>

    <div id="overlay" class="modal" tabindex="-1" role="dialog" v-show="displayModal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ entity_PatientTracking_action_modal_new }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="displayModal=false">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-group" v-if="errors">
                <ul v-for="error in errors">
                  <li class="c-red">{{ error }}</li>
                </ul>
              </div>
              <div class="form-group">
                <label for="center">{{ entity_PatientTracking_field_center }} <span class="c-red">*</span></label>
                <multiselect
                    id="center"
                    name="center"
                    :multiple="false"
                    v-model="modalItem.center"
                    :options="filter(centers, null)"
                    track-by="id"
                    :hide-selected="false"
                    required="true"
                />
              </div>

              <div class="form-group">
                <label for="patient">{{ entity_PatientTracking_field_patient }} <span class="c-red">*</span></label>
                <input id="patient" name="cond-label" class="form-control" v-model="modalItem.patient" type="text"
                       required :placeholder="entity_PatientTracking_field_patient"/>
              </div>

              <div class="form-group">
                <label for="consentAt">{{ entity_PatientTracking_field_consent }} <span class="c-red">*</span></label>
                <input id="consentAt" v-model="modalItem.consentAt" format="dd-MM-yyyy" type="date"
                       class="form-control"/>
              </div>

              <div class="form-group">
                <label for="inclusionAt">{{ entity_PatientTracking_field_inclusion }}</label>
                <input id="inclusionAt" v-model="modalItem.inclusionAt" format="dd-MM-yyyy" type="date"
                       class="form-control"/>
              </div>

              <div class="form-group">
                <span class="c-red float-left">*  Champ(s) obligatoire(s)</span>
              </div>

            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" @click="save(modalItem)">{{ entity_PatientTracking_action_modal_add }}</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="displayModal=false">{{ entity_PatientTracking_action_modal_cancel }}</button>
          </div>
        </div>
      </div>
    </div>

    <div id="overlay2" class="modal" tabindex="-1" role="dialog" v-show="displayModalUpdate">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ entity_PatientTracking_action_modal_edit }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    @click="displayModalUpdate=false">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group" v-if="errorsUpdate">
              <ul v-for="error in errorsUpdate">
                <li class="c-red">{{ error }}</li>
              </ul>
            </div>
            <form>
              <div class="row">
                <div class="col-4">
                  <label for="center">{{ entity_PatientTracking_field_center }} <span class="c-red">*</span> </label>
                </div>
                <div class="col-8">
                  <multiselect
                      id="update-center"
                      name="update-center"
                      :multiple="false"
                      v-model="modalItemUpdate.center"
                      :options="filter(centers, null)"
                      track-by="id"
                      :hide-selected="false"
                      required="true"
                  />
                </div>
              </div>
              <div class="row">
                <div class="col-4">
                  <label for="patient">{{ entity_PatientTracking_field_patient}} <span class="c-red">*</span></label>
                </div>
                <div class="col-8">
                  <input id="update-patient" name="update-patient" class="form-control" v-model="modalItemUpdate.patient"
                         type="text" :placeholder="entity_PatientTracking_field_center"/>
                </div>
              </div>

              <div v-for="(val, key) in modalItemUpdate.variables">

                <div class="row mt-2" v-if="val[0] === '2'">
                  <div class="col-4">
                    <label :for="key">{{ key }} <span class="c-red" v-if="key === 'Date de signature du consentement'">*</span></label>
                  </div>
                  <div class="col-8" v-if="val[2] !== '2'">
                    <input :id="key" v-model="modalItemUpdate.variables[key][1]" type="date" class="form-control"  />
                  </div>
                  <div class="col-8" v-else>
                    <input :id="key" v-model="val[1]" type="date" class="form-control" disabled/>
                  </div>
                </div>

                <div class="row mt-2" v-if="val[0] === '1' || val[0] === '3'">
                  <div class="col-4">
                    <label :for="key">{{ key }}</label>
                  </div>
                  <div class="col-8">
                    <input :id="key" name="update-patient" class="form-control" v-model="val[1]" type="text" :placeholder="key"/>
                  </div>
                </div>

                <div class="row mt-2" v-if="val[0] === '4'">
                  <div class="col-4">
                    <label></label>
                    <label :for="key">{{ key }}</label>
                  </div>
                  <div class="col-6">
                    <select v-model="val[1]" class="form-control">
                      <option value=""></option>
                      <option v-for="option in val[2]" v-bind:value="option.code">
                        {{ option.label }}
                      </option>
                    </select>
                  </div>
                </div>

              </div>

              <div class="form-group">
                <span class="c-red float-left">* Champ(s) obligatoire(s)</span>
              </div>

            </form>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" @click="update(modalItemUpdate)">
              {{ entity_PatientTracking_action_modal_add }}
            </button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="displayModalUpdate=false">
              {{ entity_PatientTracking_action_modal_cancel }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal-backdrop fade show" v-show="displayModal"></div>
    <div class="modal-backdrop fade show" v-show="displayModalUpdate"></div>

  </div>

</template>

<script>

module.exports = {

  name: "ListPatientTable",

  data: function () {
    return {
      eCRFType: this.getDomParameterTwig('data-ecrf-type'),

      isGrantedWrite: this.getDomParameterTwig('data-isGrantedWrite'),
      isGrantedRead: this.getDomParameterTwig('data-isGrantedRead'),
      isGrantedArchive: this.getDomParameterTwig('data-isGrantedArchive'),
      isGrantedProjectWrite: this.getDomParameterTwig('data-isGrantedProjectWrite'),

      entity_PatientTracking_action_new: this.getDomTranslation('data-entity-PatientTracking-action-new'),
      entity_PatientTracking_action_export: this.getDomTranslation('data-entity-PatientTracking-action-export'),
      entity_PatientTracking_action_reset: this.getDomTranslation('data-entity-PatientTracking-action-reset'),
      entity_PatientTracking_action_modal_add: this.getDomTranslation('data-entity-PatientTracking-action-modal-add'),
      entity_PatientTracking_action_modal_edit: this.getDomTranslation('data-entity-PatientTracking-action-modal-edit'),
      entity_PatientTracking_action_modal_new: this.getDomTranslation('data-entity-PatientTracking-action-modal-new'),
      entity_PatientTracking_action_modal_cancel: this.getDomTranslation('data-entity-PatientTracking-action-modal-cancel'),
      entity_PatientTracking_field_patient: this.getDomTranslation('data-entity-PatientTracking-field-patient'),
      entity_PatientTracking_field_center: this.getDomTranslation('data-entity-PatientTracking-field-center'),
      entity_PatientTracking_field_consent: this.getDomTranslation('data-entity-PatientTracking-field-consent'),
      entity_PatientTracking_field_inclusion: this.getDomTranslation('data-entity-PatientTracking-field-inclusion'),
      general_start_mandatory: this.getDomTranslation('data-general-start-mandatory'),

      entity_PhaseSettingStatus_planned: this.getDomTranslation('data-entity-PhaseSettingStatus-planned'),
      entity_PhaseSettingStatus_screened: this.getDomTranslation('data-entity-PhaseSettingStatus-screened'),
      entity_PhaseSettingStatus_screeningFailure: this.getDomTranslation('data-entity-PhaseSettingStatus-screeningFailure'),
      entity_PhaseSettingStatus_signedICF: this.getDomTranslation('data-entity-PhaseSettingStatus-signedICF'),
      entity_PhaseSettingStatus_ongoing: this.getDomTranslation('data-entity-PhaseSettingStatus-ongoing'),
      entity_PhaseSettingStatus_followUp: this.getDomTranslation('data-entity-PhaseSettingStatus-followUp'),
      entity_PhaseSettingStatus_completed: this.getDomTranslation('data-entity-PhaseSettingStatus-completed'),
      entity_PhaseSettingStatus_withdrawals: this.getDomTranslation('data-entity-PhaseSettingStatus-withdrawals'),
      entity_PhaseSettingStatus_lostFollowUp: this.getDomTranslation('data-entity-PhaseSettingStatus-lostFollowUp'),
      entity_PhaseSettingStatus_eos: this.getDomTranslation('data-entity-PhaseSettingStatus-eos'),

      errors: [],
      errorsUpdate: [],
      items: {
        export: [],
        table: []
      },
      displayModal: false,
      displayModalUpdate: false,
      modalItem: {
        id: null,
        center: [],
        patient: '',
        consentAt: null,
        inclusionAt: null
      },

      modalItemUpdate: {
        idPatient: '',
        patient: '',
        center: '',
        variables: {}
      },
      centers: [],
      currentPage: 1,
      totalPages: 0,
      list: [],
      tmp: '',
      hasCenterPatient: false,
      editing: false,
    };
  },

  props: {
    projectId: {
      type: Number,
      required: true
    },
  },

  mounted: function () {
    this.refresh();
  },

  methods: {

    /**
     * Réinitialiser les filtres
     */
    resetFilter: function () {

      this.refresh();
      window.location.href = '/projects/' + this.projectId + '/patientTracking/patient';
    },

    /**
     * Filtres
     */
    filterTable: function (evt, index) {

      let val = evt.target.value;

      this.items.table = this.items.table.filter(function (e) {

        if (typeof e[index] === 'object') {
          return e[index]['value'] === val;
        } else {
          return e[index] === val;
        }
      });
    },

    /**
     * Liste de PatientData
     */
    refresh: function () {
      let uri = '/projects/' + this.projectId + '/patientTracking/patient/patientData';
      this.$http.get(uri)
          .then(res => {
            this.items = res.data;

            console.log(this.items)

          });
    },

    /**
     * Formulaire de creation d'un patient
     * @param {(Patient|null)} item
     */
    initModal: function (item) {

      let uri = '/projects/' + this.projectId + '/patientTracking/patient/center';
      this.$http.get(uri)
          .then(res => {
            this.centers = res.data.filter((center) => center);
          });

      if (item === null) {
        this.modalItem = {
          id: null,
          center: [],
          patient: '',
          consentAt: null,
          inclusionAt: null
        };
      } else {
        for (const [key, val] of Object.entries(item)) {
          switch (key) {
          case 'idPatient':
            this.modalItem.id = val;
            break;
          case 'patient':
            this.modalItem.patient = val;
            break;
          case 'centre':
            this.modalItem.center = val;
            break;
          case 'Date de signature du consentement':
            this.modalItem.consentAt = val.value;
            break;
          case 'Date d\'inclusion':
            this.modalItem.inclusionAt = val.value;
            break;

          }
          this.modalItem = Object.assign({}, item);
        }
      }

      this.displayModal = true;
    },

    /**
     * Créer un patient
     * @param {(Patient|null)} item
     */
    save: function (item) {
      this.errors = [];

      if (!this.modalItem.center.length) {
        this.errors.push('Le numéro du centre est obligatoire');
      }
      if (!this.modalItem.patient) {
        this.errors.push('Le numéro de patient est obligatoire');
      }
      if (!this.modalItem.consentAt) {
        this.errors.push('La date de signature du consentement est obligatoire');
      }

      if (this.errors.length === 0) {
        if (item.id === null) {
          let result = [];
          let uri = '/projects/' + this.projectId + '/patientTracking/' + item.center + '/center/' + item.patient + '/patient';
          this.$http.get(uri)
              .then(res => {
                result = res.data;
              })
              .then(() => {
                if (result.count === '1') {
                  this.errors.push('Le couple Centre/Patient existe déjà.');
                }

                if (this.errors.length === 0) {
                  let url = '/projects/' + this.projectId + '/patientTracking/patient/new';
                  this.$http.post(url, {data: item})
                      .then(res => {
                        this.displayModal = false;
                        this.refresh();
                      })
                      .catch(err => {
                        console.log(err);
                      });
                }
              })
              .catch(err => {
                console.log(err);
              });

        }
      }

    },

    /**
     * Modal de modification d'un patient
     */
    initModalUpdate: function (variable) {

      let uri = '/projects/' + this.projectId + '/patientTracking/patient/center';
      this.$http.get(uri).then(res => {this.centers = res.data.filter((center) => center)});

      for (const [key, value] of Object.entries(variable)) {
        switch (key) {

        case 'idPatient':
          this.modalItemUpdate.idPatient = value;
          break;

        case 'patient':
          this.modalItemUpdate.patient = value;
          break;

        case 'centre':
          this.modalItemUpdate.center = value;
          break;

        default:
          if (typeof value === 'object') {

            if (value.type !== '4') {
              let arr = []
              let finalDate = null !== value.value ? value.value :  null

              arr[key] = [value.type, finalDate, value.status]
              Object.assign(this.modalItemUpdate.variables, arr)

            } else {
              let arr = []
              arr[key] = [value.type, value.value, value.option]
              Object.assign(this.modalItemUpdate.variables, arr)
            }
          }

          break;
        }
      }

      this.displayModalUpdate = true;
    },

    /**
     * Validation de modification d'un patient
     */
    update: function (item) {
      let consent = ''
      for (const [key, value] of Object.entries(this.modalItemUpdate.variables)) {

        if (key === 'Date de signature du consentement') {
          consent = value[1];
        }

      }

      this.errorsUpdate = [];

      if (!this.modalItemUpdate.center.length) {
        this.errorsUpdate.push('Le numéro du centre est obligatire');
      }

      if (!this.modalItemUpdate.patient) {
        this.errorsUpdate.push('Le numéro de patient est obligatire');
      }

      if ('' === consent) {
        this.errorsUpdate.push('La date de signature du consentement est obligatoire');
      }

      if (this.errorsUpdate.length === 0) {
        let result = [];
        let uri = '/projects/' + this.projectId + '/patientTracking/' + item.center + '/center/' + item.patient + '/patient';
        this.$http.get(uri)
            .then(res => {
              result = res.data;
            })
            .then(() => {
              let url = '/projects/' + this.projectId + '/patientTracking/patient/' + item.idPatient + '/update';
              if (result.count === '1') {
                if (result.idPatient === item.idPatient) {
                  this.$http.put(url, {data: item})
                      .then(res => {
                        this.displayModalUpdate = false;
                        this.refresh();
                      })
                      .catch(err => {
                        console.log(err);
                      });
                } else {
                  this.errorsUpdate.push('Le couple Centre/Patient existe déjà.');
                }
              } else {
                this.$http.put(url, {data: item})
                    .then(res => {
                      this.displayModalUpdate = false;
                      this.refresh();
                    })
                    .catch(err => {
                      console.log(err);
                    });
              }
            })
            .catch(err => {
              console.log(err);
            });
      }
    },

    /**
     * Les numéros de centres
     * @param item
     * @param key
     */
    filter(item, key = null) {
      let options = [];
      if (null === key) {
        options = item.map(center => {
          return `${center.number}`;
        });
      } else {
        options = item.map(filter => {
          let val = filter[key];
          if (typeof val === 'object') {
            return `${val.value}`;
          } else {
            return val;
          }
        });
      }

      //enlever les doublants
      return options.filter((elem, index, self) => index === self.indexOf(elem) && elem !== '');
    },

    /**
     * Archiver un patient
     * @param {(Patient|null)} item
     */
    archive: function (item) {
      const uri = '/projects/' + this.projectId + '/patientTracking/patient/' + item.idPatient + '/archive';
      this.$http.get(uri)
          .then(res => {
            if (res.data.status === 1) {
              //this.items.table.splice(this.items.table.findIndex(element => element.id === item.id),1);
              this.refresh();
            } else {
              alert(res.data.msg);
            }
          })
          .catch(err => {
            console.log(err);
          });
    },

    /**
     * Desarchiver un patient
     * @param {(Patient|null)} item
     */
    restore: function (item) {
      const uri = '/projects/' + this.projectId + '/patientTracking/patient/' + item.idPatient + '/restore';
      this.$http.get(uri)
          .then(res => {
            if (res.data.status === 1) {
              //  this.items.table.push(this.items.table.findIndex(element => element.id === item.id),1);
              this.refresh();
            } else {
              alert(res.data.msg);
            }
          })
          .catch(err => {
            console.log(err);
          });
    },

    // Get parameter text from twig
    getDomParameterTwig(key) {
      return document.querySelector('[' + key + ']').getAttribute(key);
    },

    // Get translated text from yaml
    getDomTranslation(key) {
      if (null == document.querySelector('.data_translate').getAttribute(key)) {
        return key;
      }
      return document.querySelector('.data_translate').getAttribute(key);
    },

    convertDate(inputFormat) {
      function pad(s) { return (s < 10) ? '0' + s : s; }
      let d = new Date(inputFormat)
      return [pad(d.getDate()), pad(d.getMonth()+1), d.getFullYear()].join('/')
    },

    displayValue(val) {

      if (val === 'entity.PhaseSettingStatus.planned') {
        return this.entity_PhaseSettingStatus_planned
      }

      if (val === 'entity.PhaseSettingStatus.screened') {
        return this.entity_PhaseSettingStatus_screened
      }

      if (val === 'entity.PhaseSettingStatus.screeningFailure') {
        return this.entity_PhaseSettingStatus_screeningFailure
      }

      if (val === 'entity.PhaseSettingStatus.signedICF') {
        return this.entity_PhaseSettingStatus_signedICF
      }

      if (val === 'entity.PhaseSettingStatus.ongoing') {
        return this.entity_PhaseSettingStatus_ongoing
      }

      if (val === 'entity.PhaseSettingStatus.followUp') {
        return this.entity_PhaseSettingStatus_followUp
      }

      if (val === 'entity.PhaseSettingStatus.completed') {
        return this.entity_PhaseSettingStatus_completed
      }

      if (val === 'entity.PhaseSettingStatus.withdrawals') {
        return this.entity_PhaseSettingStatus_withdrawals
      }

      if (val === 'entity.PhaseSettingStatus.lostFollowUp') {
        return this.entity_PhaseSettingStatus_lostFollowUp
      }

      if (val === 'entity.PhaseSettingStatus.eos') {
        return this.entity_PhaseSettingStatus_eos
      }

      if (val === null) {
        return ''
      }

      return val
    }
  }
};

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

#overlay2 {
  display: block;
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(0, 0, 0, 0.4);
  overflow-y: auto;
}

.width6em {
  width: 6em;
}

</style>
