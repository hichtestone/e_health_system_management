<template>
  <div id="ReportBlock">
    <div class="admin-block mt-3">
      <div class="row">
        <div class="list-group">
          <button type="button" class="btn btn-light mr-2" id="input-text" @click.prevent="initModalText(null)">Champ de saisie de texte</button>
        </div>
        <div class="list-group">
          <button type="button" class="btn btn-light mr-2" id="input-table" @click.prevent="initModalTable(null)">Tableau</button>
        </div>
        <div class="list-group">
          <button type="button" class="btn btn-light mr-2" id="input-textarea" @click.prevent="initModalParagraph(null)">Paragraphe</button>
        </div>
        <div class="list-group">
          <button type="button" class="btn btn-light mr-2" id="input-header" @click.prevent="initModalHeader(null)">En-tête</button>
        </div>
        <div class="list-group float-right">
          <order-report-block-param id="OrderReportBlockParam"/>
        </div>
      </div>

      <div v-if="configs.length > 0">
        <div class="admin-block mt-3" v-for="config in configs">
          <h3>{{config.parameterName}}</h3>
          <!-- Texte -->
          <div class="row align-items-center mt-3" v-if="config.input === 'text'">
            <div class="col-9">
              <div class="form-group">
                <label :for="config.name">{{ config.label }}</label>
                <input type="text" class="form-control" :id="config.name">
              </div>
            </div>
            <div class="col-3">
              <button class="btn btn-primary" @click.prevent="initModalText(config)">Modifier <i class="fa fa-edit"></i> </button>
              <button class="btn btn-danger" @click.prevent="initModalDeleteParam(config)">Supprimer <i class="fa fa-trash"></i> </button>
            </div>
          </div>

          <!-- En-tête -->
          <div class="row align-items-center mt-3" v-if="config.input === 'header'">
            <div class="col-9" v-if="config.type === 'h1'">
              <h1 :id="config.name"> {{config.label}} </h1>
            </div>
            <div class="col-9" v-if="config.type === 'h2'">
              <h2 :id="config.name"> {{config.label}} </h2>
            </div>
            <div class="col-9" v-if="config.type === 'h3'">
              <h3 :id="config.name"> {{config.label}} </h3>
            </div>
            <div class="col-3">
              <button class="btn btn-primary" @click.prevent="initModalHeader(config)">Modifier <i class="fa fa-edit"></i> </button>
              <button class="btn btn-danger" @click.prevent="initModalDeleteParam(config)">Supprimer <i class="fa fa-trash"></i> </button>
            </div>
          </div>

          <!-- Paragraphe -->
          <div class="row align-items-center mt-3" v-if="config.input === 'textarea'">
            <div class="col-9">
              <div class="form-group">
                <p :id="config.name">{{ config.label }}</p>
              </div>
            </div>
            <div class="col-3">
              <button class="btn btn-primary" @click.prevent="initModalParagraph(config)">Modifier <i class="fa fa-edit"></i> </button>
              <button class="btn btn-danger" @click.prevent="initModalDeleteParam(config)">Supprimer <i class="fa fa-trash"></i> </button>
            </div>
          </div>

          <!-- Tableau -->
          <div class="row align-items-center mt-3" v-if="config.input === 'table'">
            <div class="col-9">
              <table class="table table-bordered">
                <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col" v-for="col in config.cells.cellCols">{{ col }}</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="row in config.cells.cellRows">
                  <th scope="row">{{ row }}</th>
                  <td v-for="col in config.cells.cellCols"></td>
                </tr>
                </tbody>
              </table>
            </div>
            <div class="col-3">
              <button class="btn btn-primary" @click.prevent="initModalTable(config)">Modifier <i class="fa fa-edit"></i> </button>
              <button class="btn btn-danger" @click.prevent="initModalDeleteParam(config)">Supprimer <i class="fa fa-trash"></i> </button>
            </div>
          </div>
        </div>
      </div>

      <div class="admin-block mt-3">
        <div class="row mt-3 align-items-center form-wrap-custom">
        </div>
      </div>

      <div class="row mt-3">
        <div class="col-3">
          <button class="btn btn-primary" @click="save(params)">Sauvegarder</button>
        </div>
      </div>

    </div>

    <div class="modal overlay" tabindex="-1" role="dialog" v-show="displayModalText">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h6 class="modal-title">Pop-up d'ajout/modification d'un chanp texte</h6>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="displayModalText=false">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body p-4">
            <div class="form-group" v-if="errors">
              <ul v-for="error in errors">
                <li class="c-red">{{ error }}</li>
              </ul>
            </div>

            <div class="form-group">
              <label>Nom de l'item : <span class="c-red">*</span> </label>
              <FormulateInput name="text" v-model="modalText.parameterName"/>
            </div>

            <div class="form-group">
              <label>Label du champ texte : <span class="c-red">*</span> </label>
              <FormulateInput name="text" v-model="modalText.label"/>
            </div>

            <div class="form-group">
              <span class="c-red float-left">* champ(s) obligatoire(s)</span>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="displayModalText=false">Fermer</button>
            <span v-if="configs.length > 0">
              <button class="btn btn-primary" @click="inputText(modalText)">Valider</button>
            </span>
            <span v-else>
              <button class="btn btn-primary" @click="inputText(null)">Valider</button>
            </span>
          </div>
        </div>
      </div>
    </div>

    <div class="modal overlay" tabindex="-1" role="dialog" v-show="displayModalHeader">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h6 class="modal-title">Pop-up d'ajout/modification d'un entête</h6>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="displayModalHeader=false">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body p-4">
            <div class="form-group" v-if="errors">
              <ul v-for="error in errors">
                <li class="c-red">{{ error }}</li>
              </ul>
            </div>

            <div class="form-group">
              <label>Nom de l'item : <span class="c-red">*</span> </label>
              <FormulateInput name="text" v-model="modalHeader.parameterName"/>
            </div>

            <div class="form-group">
              <label>Label de l'en-tête : <span class="c-red">*</span> </label>
              <FormulateInput name="text" v-model="modalHeader.label"/>
            </div>

            <div class="form-group">
              <label for="input-header-type">En-tête</label>
              <select class="form-control" id="input-header-type" v-model="modalHeader.type">
                <option selected value="h1">H1</option>
                <option value="h2">H2</option>
                <option value="h3">H3</option>
              </select>
            </div>

            <div class="form-group">
              <span class="c-red float-left">* champ(s) obligatoire(s)</span>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="displayModalHeader=false">Fermer</button>
            <button class="btn btn-primary" @click="inputHeader(modalHeader)">Valider</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal overlay" tabindex="-1" role="dialog" v-show="displayModalParagraph">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h6 class="modal-title">Pop-up d'ajout/modification d'un chanp paragraphe</h6>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="displayModalParagraph=false">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body p-4">
            <div class="form-group" v-if="errors">
              <ul v-for="error in errors">
                <li class="c-red">{{ error }}</li>
              </ul>
            </div>

            <div class="form-group">
              <label>Nom de l'item : <span class="c-red">*</span> </label>
              <FormulateInput name="text" v-model="modalParagraph.parameterName"/>
            </div>

            <div class="form-group">
              <label>Contenu du paragraphe : <span class="c-red">*</span> </label>
              <FormulateInput type="textarea" name="text" v-model="modalParagraph.label" rows="5"/>
            </div>

            <div class="form-group">
              <span class="c-red float-left">* champ(s) obligatoire(s)</span>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="displayModalParagraph=false">Fermer</button>
            <span v-if="configs.length > 0">
              <button class="btn btn-primary" @click="inputParagraph(modalParagraph)">Valider</button>
            </span>
            <span v-else>
              <button class="btn btn-primary" @click="inputParagraph(null)">Valider</button>
            </span>
          </div>
        </div>
      </div>
    </div>

    <div class="modal overlay" tabindex="-1" role="dialog" v-show="displayModalTable">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h6 class="modal-title">Pop-up d'ajout de nombre de lignes/colonnes d'un tableau</h6>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="displayModalTable=false">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body p-4">
            <div class="form-group" v-if="errors">
              <ul v-for="error in errors">
                <li class="c-red">{{ error }}</li>
              </ul>
            </div>

            <div class="form-group">
              <label>Nom de l'item : <span class="c-red">*</span> </label>
              <FormulateInput name="text" v-model="modalTable.parameterName"/>
            </div>

            <div class="form-group">
              <label>Nombre de ligne : <span class="c-red">*</span> </label>
              <FormulateInput name="rows" v-model="modalTable.row"/>
            </div>

            <div class="form-group">
              <label>Nombre de colonne : <span class="c-red">*</span> </label>
              <FormulateInput name="cols" v-model="modalTable.col"/>
            </div>

            <div class="form-group">
              <span class="c-red float-left">* champ(s) obligatoire(s)</span>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="displayModalTable=false">Fermer</button>
            <button class="btn btn-primary" @click="inputTable(null)">Ajouter</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal overlay" tabindex="-1" role="dialog" v-show="displayModalTableEdit">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h6 class="modal-title">Pop-up de modification du paramétre d'un tableau</h6>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="displayModalTableEdit=false">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body p-4">
            <div class="form-group" v-if="errors">
              <ul v-for="error in errors">
                <li class="c-red">{{ error }}</li>
              </ul>
            </div>

            <div class="form-group">
              <label>Nom de l'item : <span class="c-red">*</span> </label>
              <FormulateInput name="text" v-model="modalTable.parameterName"/>
            </div>

            <div class="row mb-1" v-for="(row, index) in modalTable.cells.cellRows">
                  <label>L'en-tête de ligne {{index+1}}  =>  <span class="c-red">* &nbsp; &nbsp;</span> </label>
                  <FormulateInput name="rows" v-model="modalTable.cells.cellRows[index]"/>
                </div>

                <div class="row mb-1" v-for="(col, index) in modalTable.cells.cellCols">
                  <label>L'en-tête de colonne {{index+1}}  =>  <span class="c-red">* &nbsp; &nbsp;</span> </label>
                  <FormulateInput name="cols" v-model="modalTable.cells.cellCols[index]"/>
                </div>

            <div class="form-group">
              <span class="c-red float-left">* champ(s) obligatoire(s)</span>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="displayModalTableEdit=false">Fermer</button>
            <button class="btn btn-primary" @click="inputTable(modalTable)">Modifier</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal overlay" tabindex="-1" role="dialog" v-show="displayModalDeleteParam">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h6 class="modal-title">Pop-up de suppression d'un item d'un bloc</h6>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="displayModalDeleteParam=false">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body p-4">
             <p>Êtes-vous sûr de vouloir supprimer cet item ? <br>
               Les informations de cet item ne pourront plus être récupérées </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="displayModalDeleteParam=false">Fermer</button>
            <button class="btn btn-primary" @click="deleteParam(modalDeleteParam)">Confirmer</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import OrderReportBlockParam from './OrderReportBlockParam';

export default  {
  name: "ReportBlock",
  components: {OrderReportBlockParam},
  data: function () {
    return {
      params: [],
      configs: [],
      errors: [],
      showModal: false,
      displayModalText: false,
      displayModalParagraph: false,
      displayModalHeader: false,
      displayModalTable: false,
      displayModalTableEdit: false,
      displayModalDeleteParam: false,
      modalText: {
        input: 'text',
        label: '',
        name: '',
        parameterName: ''
      },
      modalHeader: {
          input: 'header',
          type: '',
          label: '',
          name: '',
         parameterName: ''
      },
      modalParagraph: {
          input: 'textarea',
          label: '',
          name: '',
          parameterName: ''
      },
      modalTable: {
        input: 'table',
        row: '',
        col: '',
        cells: {
          cellRows: [],
          cellCols: [],
        },
        parameterName: ''
      },
      modalDeleteParam: {
        id: '',
      },
    };
  },
  mounted: function () {
    this.init();
  },
  methods: {
    // init
    init: function () {
      let uri = $('#report-block-edit-param-list').data('edit-block-param-list');
      this.$http.get(uri)
          .then(res => {
            this.configs = res.data;
          })
          .catch(err => {
            console.log(err);
          });
    },

    // init modal for input = text
    initModalText: function (input) {
      if (input === null) {
        this.modalText = {
          input: 'text',
          label: '',
          name: '',
          parameterName: ''
        };
      } else {
        this.modalText = Object.assign({}, input);
      }

      this.displayModalText = true;
    },

    // init modal for input = text
    initModalHeader: function (input) {
      if (input === null) {
        this.modalHeader = {
          input: '',
          label: '',
          name: '',
          parameterName: '',
        };
      } else {
        this.modalHeader = Object.assign({}, input);
      }

      this.displayModalHeader = true;
    },

    // init modal for input = paragraphe
    initModalParagraph: function (input) {
      if (input === null) {
        this.modalParagraph = {
          input: 'textarea',
          label: '',
          name: '',
          parameterName: '',
        };
      } else {
        this.modalParagraph = Object.assign({}, input);
      }

      this.displayModalParagraph = true;
    },

    // init modal for table
    initModalTable: function (input) {
      if (input === null) {
        this.modalTable = {
          input: 'table',
          row: '',
          col: '',
          cells: {
            cellRows: [],
            cellCols: [],
          },
          parameterName: ''
        };
        this.displayModalTable = true;
      } else {
        this.modalTable = Object.assign({}, input);
        this.displayModalTableEdit = true;
      }

    },

    // init modal for input = text
    initModalDeleteParam: function (input) {
      if (input === null) {
        this.modalDeleteParam = {
          id: '',
        };
      } else {
        this.modalDeleteParam = Object.assign({}, input);
      }

      this.displayModalDeleteParam = true;
    },

    // text param
    inputText: function (input) {
      this.errors = [];
      if (!this.modalText.parameterName.length) {
        this.errors.push('Le nom de l\'item est obligatoire');
      }

      if (!this.modalText.label.length) {
        this.errors.push('Le label est requise');
      }

      if (this.errors.length === 0) {
        this.displayModalText = false;
        console.log(input)
        if (input === null || input.name === '') {
          let inputText = {
            input: 'text',
            label: this.modalText.label,
            parameterName: this.modalText.parameterName,
            name: 'text-' + this.random(12),
          }
          console.log(inputText)
          this.params.push(inputText);

          $(".form-wrap-custom").append(
              '<div class="col-9">' +
                '<div class="form-group">' +
                  '<label for="'+inputText.name+'">'+ this.modalText.label +'</label>' +
                  '<input type="text" class="form-control" id="'+inputText.name+'">' +
                '</div>' +
              '</div>' +
              '<div class="col-3">' +
                '<button class="btn btn-primary disabled" data-placement="right" data-toggle="tooltip" title="Merci de sauvegarder pour pouvoir modifier" style="margin-right: 5px; cursor: not-allowed">Modifier <i class="fa fa-edit"></i> </button>' +
                '<button class="btn btn-danger disabled" data-placement="right" data-toggle="tooltip" title="Merci de sauvegarder pour pouvoir supprimer" style="cursor: not-allowed">Delete</button>' +
            '</div>'
          );
        } else {
          this.edit(input)
        }
      }
    },

    // header param
    inputHeader: function (input) {
      this.errors = [];

      if (!this.modalHeader.parameterName.length) {
        this.errors.push('Le nom de l\'item est obligatoire');
      }

      if (!this.modalHeader.type) {
        this.errors.push('Le type de l\'en-tête est obligatoire');
      }

      if (!this.modalHeader.label.length) {
        this.errors.push('Le label de l\'en-tête est obligatoire');
      }

      if (this.errors.length === 0) {
        this.displayModalHeader = false;
        if (input.name === '') {
          let inputHeader = {
            input: 'header',
            type: this.modalHeader.type,
            label: this.modalHeader.label,
            parameterName: this.modalHeader.parameterName,
            name: 'header-' + this.random(12),
          }
          this.params.push(inputHeader);

          $(".form-wrap-custom").append(
              '<div class="col-9">' +
              '<div class="form-group">' +
              '<' + this.modalHeader.type +' id="'+inputHeader.name+'">'+ this.modalHeader.label +'</' + this.modalHeader.type + '>' +
              '</div>' +
              '</div>' +
              '<div class="col-3">' +
              '<button class="btn btn-primary disabled" data-placement="right" data-toggle="tooltip" title="Merci de sauvegarder pour pouvoir modifier" style="margin-right: 5px; cursor: not-allowed">Modifier <i class="fa fa-edit"></i> </button>' +
              '<button class="btn btn-danger disabled" data-placement="right" data-toggle="tooltip" title="Merci de sauvegarder pour pouvoir supprimer" style="cursor: not-allowed">Delete</button>' +
              '</div>'
          );
        } else {
          this.edit(input)
        }
      }
    },

    // paragraph param
    inputParagraph: function (input) {
      this.errors = [];

      if (!this.modalParagraph.parameterName.length) {
        this.errors.push('Le nom de l\'item est obligatoire');
      }

      if (!this.modalParagraph.label.length) {
        this.errors.push('Le label est obligatoire');
      }

      if (this.errors.length === 0) {
        this.displayModalParagraph = false;
        if (input === null || input.name === '') {
          let inputParagraph = {
            input: 'textarea',
            label: this.modalParagraph.label,
            parameterName: this.modalParagraph.parameterName,
            name: 'textarea-' + this.random(12),
          }
          this.params.push(inputParagraph);

          $(".form-wrap-custom").append(
              '<div class="col-9">' +
                '<div class="form-group">' +
                  '<p id="'+inputParagraph.name+'" rows="5">'+ this.modalParagraph.label +'</p>' +
                '</div>' +
              '</div>' +
              '<div class="col-3">' +
                '<button class="btn btn-primary disabled" data-placement="right" data-toggle="tooltip" title="Merci de sauvegarder pour pouvoir modifier" style="margin-right: 5px; cursor: not-allowed">Modifier <i class="fa fa-edit"></i> </button>' +
                '<button class="btn btn-danger disabled" data-placement="right" data-toggle="tooltip" title="Merci de sauvegarder pour pouvoir supprimer" style="cursor: not-allowed">Delete</button>' +
            '</div>'
          );
        } else {
          this.edit(input)
        }
      }
    },

    // table param
    inputTable: function (input) {
      this.errors = [];

      if (!this.modalTable.parameterName.length) {
        this.errors.push('Le nom de l\'item est obligatoire');
      }
      // create block param
      if (!this.modalTable.row.length || !(/^\d*$/.test(this.modalTable.row))) {
        this.errors.push('Le nombre de ligne est entier et obligatoire');
      }

      if (!this.modalTable.col.length || !(/^\d*$/.test(this.modalTable.col))) {
        this.errors.push('Le nombre de colonne est entier et obligatoire');
      }

      // edit block param
      this.modalTable.cells.cellRows.map( (row, index) => {
        if (!row.length) {
          let key = index+1
          this.errors.push('Le champ "l\'en-tête de ligne ' + key + '" est obligatoire');
        }
      })

      this.modalTable.cells.cellCols.map( (col, index) => {
        if (!col.length) {
          let key = index+1
          this.errors.push('Le champ "l\'en-tête de colonne ' + key + '" est obligatoire');
        }
      })

      if (this.errors.length === 0) {
        this.displayModalTable = false;
        if (input === null) {
          let cellRows = [];
          let cellCols = [];

          for (let i = 0; i < this.modalTable.row; i++) {
            cellRows.push('row ' + i);
          }

          for (let j = 0; j < this.modalTable.col; j++) {
            cellCols.push('col ' + j);
          }

          let inputTable = {
            input: 'table',
            name: 'table-' + this.random(12),
            parameterName: this.modalTable.parameterName,
            row: this.modalTable.row,
            col: this.modalTable.col,
            cells: {
              cellRows,
              cellCols
            }
          };

          this.params.push(inputTable);

          let table = '<table class="table table-bordered mt-3">';
          table += '<thead> <tr> <th scope="col">#</th>';
          for (let c = 0; c < this.modalTable.col; c++) {
            table += '<th scope="col"> col ' + c + '</th>';
          }
          table += '</tr> </thead>';
          table += '<tbody>';

          for (let r = 0; r < this.modalTable.row; r++) {
            table += '<tr>';
            table += '<th scope="row"> row ' + r + '</th>';

            for (let c = 0; c < this.modalTable.col; c++) {
              table += '<td> </td>';
            }
            table += '</tr>';
          }
          table += '</tbody>';
          table += '</table>';


          $('.form-wrap-custom').append(
              '<div class="col-9">' +
                table +
              '</div>' +
              '<div class="col-3">' +
              '<button class="btn btn-primary disabled" data-placement="right" data-toggle="tooltip" title="Merci de sauvegarder pour pouvoir modifier" style="margin-right: 5px; cursor: not-allowed">Modifier <i class="fa fa-edit"></i> </button>' +
              '<button class="btn btn-danger disabled" data-placement="right" data-toggle="tooltip" title="Merci de sauvegarder pour pouvoir supprimer" style="cursor: not-allowed">Delete</button>' +
              '</div>'
          );
        } else {
          this.edit(input)
        }
      }
    },

    // edit block param
    edit: function(input) {
      console.log(input);
      let uri = $('#report-block-edit-param-edit').data('edit-block-param-edit');
      let redirectTo = $('#report-block-edit').data('edit-block');
      this.$http.post(uri, {data: input})
          .then(res => {
            window.location.replace(redirectTo)
          })
          .catch(err => {
            console.log(err);
          });
    },

    // delete block param
    deleteParam: function(input) {
      console.log(input);
      let uri = $('#report-block-edit-param-delete').data('edit-block-param-delete');
      let redirectTo = $('#report-block-edit').data('edit-block');
      this.$http.post(uri, {data: input})
          .then(res => {
            window.location.replace(redirectTo)
          })
          .catch(err => {
            console.log(err);
          });
    },

    // save block param
    save: function (params) {
      let uri = $('#report-block-edit-param-new').data('edit-block-param-new');
      let redirectTo = $('#report-block-edit').data('edit-block');
      this.$http.post(uri, {data: params})
          .then(res => {
            window.location.replace(redirectTo)
          })
          .catch(err => {
            console.log(err);
          });
    },

    // random id, for, name for input
    random: function (desiredMaxLength) {
      let randomNumber = '';
      for (let i = 0; i < desiredMaxLength; i++) {
        randomNumber += Math.floor(Math.random() * 10);
      }
      return randomNumber;
    }
  }
};
</script>
<style scoped lang="scss">

.modal-dialog {
  margin-top: 200px;
}

.overlay {
  display: block;
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(0, 0, 0, 0.4);
  overflow-y: auto;
}

ul li:hover {
  color: #fff;
  background-color: #007bff;
  border-color: #007bff;
  cursor: pointer;
}

</style>
