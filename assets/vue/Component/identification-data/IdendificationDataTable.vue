<template>
  <div id="IdentificationDataTable" class="table-responsive">

    <!-- Identification des variables de suivi -->
    <div class="admin-block mt-3">

      <h5>
        {{ entity_EcrfSetting_section_variable }}
        <button class="btn btn-primary" id="variable-new" @click.prevent="initModal(null)" v-if="isGrantedWriteSchemaVisite && isGrantedProjectWrite">
          <i class="fa fa-plus"></i>
        </button>
      </h5>

      <!-- MODAL edit variable -->
      <div id="overlay" class="modal" tabindex="-1" role="dialog" v-show="displayModal">

        <div class="modal-dialog">

          <div class="modal-content">

            <div class="modal-header">

              <h6 v-if="displayModalMode === 'new'" class="modal-title">Creation de variable de suivi</h6>
              <h6 v-else-if="displayModalMode === 'edit'" class="modal-title">Modification de variable de suivi</h6>

              <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="displayModal=false">
                <span aria-hidden="true">&times;</span>
              </button>

            </div>

            <div class="modal-body p-4">

              <div class="form-group" v-if="errors">
                <ul v-for="error in errors">
                  <li class="c-red">{{ error }}</li>
                </ul>
              </div>

              <label>Ordre : </label>
              <FormulateInput name="order" v-model="modalItem.position"/>

              <label>Nom de la variable : <span class="c-red">*</span></label>
              <FormulateInput name="name" v-model="modalItem.label"/>

              <label>Format de variable : <span class="c-red">*</span></label>

              <FormulateInput
                  type="select"
                  :options="{string: 'Text', numeric: 'Numérique', date: 'Date', list: 'Liste déroulante'}"
                  placeholder="Choisir un format..."
                  name="variableType"
                  v-model="modalItem.format"
              />

              <FormulateInput
                  type="checkbox"
                  label="J'affiche cette variable dans la liste des patients"
                  name="hasPatient"
                  v-model="modalItem.hasPatient"
              />

              <FormulateInput
                  type="select"
                  label="Choisir une liste :"
                  :options=newList.concat(filterByListType(items))
                  placeholder="Choisir une liste..."
                  name="new"
                  v-if="modalItem.format === 'list'"
                  v-model="modalItem.list.select"
                  @change="selectOnChange($event)"
              />

              <div v-if="modalItem.list.select === 'Une nouvelle Liste'">

                <FormulateInput
                    name="newList-name"
                    label="Nom de la liste : "
                    validation="required"
                    v-model="modalItem.list.name"
                />

                <div class="row" v-for="(option, index) in modalItem.list.option" :key="option.id">

                  <div class="col-4">
                    <FormulateInput
                        name="option-label"
                        validation="required"
                        label="Label"
                        v-model="option.label"
                    />
                  </div>

                  <div class="col-4">
                    <FormulateInput
                        name="option-value"
                        validation="required"
                        label="Valeur"
                        v-model="option.value"
                    />
                  </div>

                  <div class="col-4">
                    <button type="button" class="btn btn-primary mt-3" @click="removeOption(index)">
                      <i class="fa fa-minus"></i>
                    </button>
                  </div>

                </div>

                <div class="row">
                  <div class="col-6">
                    <button type="button" class="btn btn-primary mt-2" @click="addOption()"><i class="fa fa-plus"></i>
                      Ajouter une option
                    </button>
                  </div>
                </div>

              </div>

              <div v-if="filterByListType(items).includes(modalItem.list.select)">

                <FormulateInput
                    name="list-name2"
                    label="Nom de la liste : "
                    disabled
                    v-model="list.name"
                />

                <FormulateInput
                    type="group"
                    name="list-options"
                    add-label="+ Ajouter une option"
                    disabled
                >
                  <div class="row" v-for="option in list.options">
                    <div class="col-6">
                      <FormulateInput
                          name="option-label2"
                          label="Label"
                          disabled
                          v-model="(Object.keys(JSON.parse(JSON.stringify(option))))[0]"
                      />
                    </div>
                    <div class="col-6">
                      <FormulateInput
                          name="option-value2"
                          label="Valeur"
                          disabled
                          v-model="(Object.values(JSON.parse(JSON.stringify(option))))[0]"
                      />
                    </div>
                  </div>
                </FormulateInput>

              </div>

              <div class="form-group">
                <span class="c-red float-left">* champ(s) obligatoire(s)</span>
              </div>

            </div>

            <div class="modal-footer">

              <button v-if="displayModalMode === 'new'" class="btn btn-primary" @click="save(modalItem)">Ajouter</button>
              <button v-else-if="displayModalMode === 'edit'" class="btn btn-primary" @click="save(modalItem)">Modifier</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="displayModal=false">Fermer</button>

            </div>

          </div>

        </div>

      </div>

      <div class="table-responsive">

        <table class="w-100">

          <tr>
            <th>{{ entity_EcrfSetting_field_order }}</th>
            <th>{{ entity_EcrfSetting_field_name }}</th>
            <th>{{ entity_EcrfSetting_field_type }}</th>
            <th>{{ entity_EcrfSetting_field_source }}</th>
            <th>{{ entity_EcrfSetting_field_hasPatient }}</th>
            <th>Action</th>
          </tr>

          <tr v-for="item in items" :key="item.id" v-if="item.isVariable && item.deletedAt === 'non'">
            <td>{{ item.position }}</td>
            <td>{{ item.label }}</td>
            <td>
              <span v-for="variable in item.variableType">
                <span v-if="variable.id === 4">
                  {{ variable.type|capitalize }}
                </span>
                <span v-else>
                  {{ variable.label|capitalize }}
                </span>
              </span>
            </td>
            <td v-if="isGrantedWriteEcrf && isGrantedProjectWrite">
              <FormulateInput
                  name="variable-source"
                  v-model="item.source"
                  @change="update(item)"
              />
            </td>
            <td v-else>
              <FormulateInput
                  name="variable-source"
                  v-model="item.source"
                  disabled
              />
            </td>
            <td v-if="isGrantedWriteSchemaVisite && isGrantedProjectWrite">
              <span v-if="item.label === 'Date de signature du consentement'">
                <FormulateInput
                    type="checkbox"
                    name="variable-hasPatient"
                    v-model="item.hasPatient"
                    disabled
                />
              </span>
              <span v-else>
                 <FormulateInput
                     type="checkbox"
                     name="variable-hasPatient"
                     v-model="item.hasPatient"
                     @change="update(item)"
                 />
              </span>
            </td>
            <td v-else>
              <FormulateInput
                  type="checkbox"
                  name="variable-hasPatient"
                  v-model="item.hasPatient"
                  disabled
              />
            </td>
            <td v-if="isGrantedWriteSchemaVisite && isGrantedProjectWrite">
              <span v-if="item.label === 'Date d\'inclusion' || item.label === 'Date de signature du consentement'">
                <i class="fa fa-trash c-grey disabled notAllowed" href="#" data-placement="left" data-toggle="tooltip" :title="entity_EcrfSetting_field_archived"></i>
                <i class="fa fa-edit c-grey disabled notAllowed" href="#" data-placement="left" data-toggle="tooltip" title="Variable utilisée modification impossible"></i>
              </span>
              <span v-else>
                <span v-if="item.deletedAt === 'non' && !item.canDeleted">
                  <i class="fa fa-trash" @click="archive(item)"></i>
                  <i class="fa fa-edit" @click="initModal(item)"></i>
                </span>
                <span v-else>
                  <i class="fa fa-trash c-grey disabled notAllowed" href="#" data-placement="left" data-toggle="tooltip" :title="entity_EcrfSetting_field_archived"></i>
                  <i class="fa fa-edit c-grey disabled notAllowed" href="#" data-placement="left" data-toggle="tooltip" title="Variable utilisée modification impossible"></i>
                </span>
              </span>
            </td>
          </tr>

        </table>

      </div>

    </div>

    <!-- Identification des dates de visites -->
    <div class="admin-block">
      <h5>{{ entity_EcrfSetting_section_visit }}</h5>
      <div class="table-responsive">
        <table class="w-100">
          <tr>
            <th>{{ entity_EcrfSetting_field_name }}</th>
            <th>{{ entity_EcrfSetting_field_type }}</th>
            <th>{{ entity_EcrfSetting_field_source }}</th>
            <th class="width12">{{ entity_EcrfSetting_field_hasPatient }}</th>
            <th class="width12">{{ entity_EcrfSetting_field_hasVisit }}</th>
          </tr>

          <tr v-for="item in items" :key="item.id" v-if="item.isVisit">
            <td>{{ item.label }}</td>
            <td><span v-for="variable in item.variableType">{{ variable.label|capitalize }}</span></td>

            <td v-if="isGrantedWriteEcrf && isGrantedProjectWrite">
              <FormulateInput
                  name="visit-source"
                  v-model="item.source"
                  @change="update(item)"
              />
            </td>
            <td v-else>
              <FormulateInput
                  name="visit-source"
                  v-model="item.source"
                  disabled
              />
            </td>

            <td>
              <span v-if="isGrantedWriteSchemaVisite && isGrantedProjectWrite">
                <FormulateInput
                    type="checkbox"
                    name="visit-hasPatient"
                    v-model="item.hasPatient"
                    @change="update(item)"
                />
              </span>
              <span v-else>
                <FormulateInput
                    type="checkbox"
                    name="visit-hasPatient"
                    v-model="item.hasPatient"
                    disabled
                />
              </span>
            </td>
            <td>
              <span v-if="isGrantedWriteSchemaVisite && isGrantedProjectWrite">
                <FormulateInput
                    type="checkbox"
                    name="visit-hasVisit"
                    v-model="item.hasVisit"
                    @change="update(item)"
                />
              </span>
              <span v-else>
                <FormulateInput
                    type="checkbox"
                    name="visit-hasVisit"
                    v-model="item.hasVisit"
                    disabled
                />
              </span>
            </td>
          </tr>
        </table>
      </div>
    </div>
    <!-- Identification des dates d'examen -->
    <div class="admin-block">
      <h5>{{ entity_EcrfSetting_section_exam }}</h5>
      <div class="table-responsive">
        <table class="w-100">
          <tr>
            <th>{{ entity_EcrfSetting_field_name }}</th>
            <th>{{ entity_EcrfSetting_field_type }}</th>
            <th>{{ entity_EcrfSetting_field_source }}</th>
            <th class="width12">{{ entity_EcrfSetting_field_hasPatient }}</th>
          </tr>

          <tr v-for="item in items" :key="item.id" v-if="item.isExam">
            <td>{{ item.label }}</td>
            <td><span v-for="variable in item.variableType">{{ variable.label|capitalize }}</span></td>

            <td v-if="isGrantedWriteEcrf && isGrantedProjectWrite">
              <FormulateInput
                  name="exam-source"
                  v-model="item.source"
                  @change="update(item)"
              />
            </td>
            <td v-else>
              <FormulateInput
                  name="exam-source"
                  v-model="item.source"
                  disabled
              />
            </td>

            <td>
              <span v-if="isGrantedWriteSchemaVisite && isGrantedProjectWrite">
                <FormulateInput
                    type="checkbox"
                    name="exam-hasPatient"
                    v-model="item.hasPatient"
                    @change="update(item)"
                />
              </span>
              <span v-else>
                <FormulateInput
                    type="checkbox"
                    name="exam-hasPatient"
                    v-model="item.hasPatient"
                    disabled
                />
              </span>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</template>

<script>

/**
 * @typedef {{id: int, label: string}} VariableType
 * @typedef {{id: int, label: string, value: string, position: string, source: string, hasPatient: boolean,
 * hasVisit:boolean, isVisit: boolean, isExam: boolean, isVariable: boolean, variableType: {VariableType[]}}} PatientVariable
 */

module.exports = {

  name: "IdentificationDataTable",

  data: function () {
    return {
      // eCRFType
      eCRFType: this.getDomParameterTwig('data-ecrf-type'),
      // From translations/messages.en.yml
      entity_EcrfSetting_section_variable: this.getDomTranslation('data-entity-EcrfSetting-section-variable'),
      entity_EcrfSetting_section_visit: this.getDomTranslation('data-entity-EcrfSetting-section-visit'),
      entity_EcrfSetting_section_exam: this.getDomTranslation('data-entity-EcrfSetting-section-exam'),
      entity_EcrfSetting_field_order: this.getDomTranslation('data-entity-EcrfSetting-field-order'),
      entity_EcrfSetting_field_name: this.getDomTranslation('data-entity-EcrfSetting-field-name'),
      entity_EcrfSetting_field_source: this.getDomTranslation('data-entity-EcrfSetting-field-source'),
      entity_EcrfSetting_field_archived: this.getDomTranslation('data-entity-EcrfSetting-field-archived'),
      entity_EcrfSetting_field_type: this.getDomTranslation('data-entity-EcrfSetting-field-type'),
      entity_EcrfSetting_field_hasPatient: this.getDomTranslation('data-entity-EcrfSetting-field-hasPatient'),
      entity_EcrfSetting_field_hasVisit: this.getDomTranslation('data-entity-EcrfSetting-field-hasVisit'),

      // L'utilisateur a-t-il tous les droits nécessaires pour créer une variable de suivi
      isGrantedWrite: this.getDomParameterTwig('data-isGrantedWrite'),

      // L'user a le role: "Création/Edition schéma visite"
      isGrantedWriteSchemaVisite: this.getDomParameterTwig('data-isGrantedWriteSchemaVisite'),

      // L'user a le role: "Identification des données eCRF"
      isGrantedWriteEcrf: this.getDomParameterTwig('data-isGrantedWriteEcrf'),

      // check si on peut entrer dans un projet en écriture
      isGrantedProjectWrite: this.getDomParameterTwig('data-isGrantedProjectWrite'),

      items: [],
      displayModal: false,
      displayModalMode: null,
      modalItem: {
        id: null,
        label: '',
        position: '',
        hasPatient: false,
        isVariable: true,
        format: [],
        list: {
          select: '',
          name: '',
          option: []
        }
      },
      list: {
        name: '',
        options: {}
      },

      types: [],
      newList: ['Une nouvelle Liste'],
      tempValue: null,
      editing: false,
      errors: [],
      visits: []
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
     * Ajouter une option
     */
    addOption: function () {
      this.modalItem.list.option.push({
        label: '',
        value: ''
      });
    },

    /**
     * supprimer une option
     */
    removeOption: function (index) {
      this.modalItem.list.option.splice(index, 1);
    },


    /**
     * Liste de PatientVariable
     */
    refresh: function () {
      let results = {};
      let uri = '/projects/' + this.projectId + '/settings/variable/json';
      this.$http.get(uri)
          .then(res => {
            results = res.data.sort(function (a, b) {
              return a.position - b.position;
            });

            this.items = results
          })
          .catch(err => {
            console.log(err);
          });
    },

    /**
     * @param {(PatientVariable|null)} item
     */
    initModal: function (item) {

      if (item === null) {

        this.modalItem = {
          id: null,
          label: '',
          position: '',
          hasPatient: false,
          isVariable: true,
          format: [],
          list: {
            select: '',
            name: '',
            option: []
          }
        };

        this.displayModalMode = 'new'

      } else {

        this.modalItem = Object.assign({}, item);

        if (this.modalItem.format === undefined) {
          this.modalItem.format = this.modalItem.variableType[0].label
        }

        if (this.modalItem.list === undefined) {
          this.modalItem.list = {
            select: '',
            name: '',
            option: []
          }
        }

        this.displayModalMode = 'edit'

      }
      this.displayModal = true;
    },

    // Update patientVariable
    update(item) {

      console.log(item)
      this.$http
          .put("/projects/" + this.projectId + "/settings/variable/" + item.id + "/edit", {
            item: item,
          })
          .then((response) => {
            response.data;
          })
          .catch(function (error) {
            console.log(error);
          });
    },

    filterByListType(object) {
      let options = [];
      // PatientVariable dont  variableType = list
      options = object.map(patientVariable => {
        if (`${patientVariable.variableType[0].id}` === '4') {
          return `${patientVariable.variableType[0].label}`;
        } else {
          return '';
        }
      });

      //enlever les doublants
      return options.filter((elem, index, self) => index === self.indexOf(elem) && elem !== '');
    },


    filterByList(lists) {
      let options = [];
      // PatientVariable dont  variableType = list
      options = lists.map(list => {
        return `${list.label}`;
      });


      //enlever les doublants
      return options.filter((elem, index, self) => index === self.indexOf(elem) && elem !== '');
    },

    /**
     * Créer une variable
     * @param {(PatientVariable|null)} item
     */
    save: function (item) {

      this.errors = [];

      if (!(/^\d*$/.test(this.modalItem.position))) {
        this.errors.push('L\'ordre est au format numérique');
      }
      if (!this.modalItem.label.length) {
        this.errors.push('Le nom de la variable est obligatoire');
      }
      if (!this.modalItem.format.length) {
        this.errors.push('Le format de la variable est obligatoire');
      }

      if (this.errors.length === 0) {

        if (item.id === null) {

          let result = [];
          let uri = '/projects/' + this.projectId + '/settings/variable/' + item.label + '/doublant';
          this.$http.get(uri)
              .then(res => {
                result = res.data;
              })
              .then(() => {
                if (result.count > '0') {
                  this.errors.push('Une variable, une visite ou un examen porte déjà ce nom');
                }

                if (this.errors.length === 0) {
                  let uri = '/projects/' + this.projectId + '/settings/variable/new';
                  this.$http.post(uri, {data: item})
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

        } else {

          let uri = '/projects/' + this.projectId + '/settings/variable/' + item.id + '/edit';

          this.$http.put(uri, {item: item})
            .then(() => {
              this.displayModal = false;
              this.refresh();
            })
            .catch(err => {
              console.log(err);
            });
        }
      }

    },

    // Get variable list
    selectOnChange: function (event) {

      let uri = '/projects/' + this.projectId + '/settings/variable/list-options/' + event.target.value + '/show';
      this.$http
          .get(uri)
          .then(
              response => {
                this.list.name = response.data.name;
                this.list.options = response.data.options;

              }
          )
          .catch(function (error) {
            console.log(error);
          });
    },

    /**
     * Archiver une variable
     * @param {(PatientVariable|null)} item
     */
    archive: function (item) {

      const uri = '/projects/' + this.projectId + '/settings/variable/' + item.id + '/archive';
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
     * Desarchivze une variable
     * @param {(PatientVariable|null)} item
     */
    restore: function (item) {

      const uri = '/projects/' + this.projectId + '/settings/variable/' + item.id + '/restore';
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

    sortList(items) {

      items.sort(function (a, b) {
        return a.position - b.position;
      });
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

.notAllowed {
  cursor: not-allowed;
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

.width12 {
  width: 12%;
}

</style>
