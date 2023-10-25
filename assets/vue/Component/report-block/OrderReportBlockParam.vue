<template>
  <div id="OrderReportBlockParam">
    <a class="btn btn-primary" @click.prevent="initModal(null)">Repositionner les items</a>

    <div id="overlay" class="modal" tabindex="-1" role="dialog" v-show="displayModal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Répositionner les items</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="displayModal=false">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form>
              <div class="form-group">
                <draggable v-model="items" @change="onMove">
                  <transition-group>
                    <ul v-for="item in items" :key="item.id" id="sort">
                      <li class="drap-and-drop">{{item.label}}</li>
                    </ul>
                  </transition-group>
                </draggable>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="displayModal=false">Annuler</button>
            <button type="button" class="btn btn-primary" @click="save(items)" v-if="items.length>0">Valider</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal-backdrop fade show" v-show="displayModal"></div>
  </div>
</template>

<script>

module.exports = {

  name: "OrderReportBlockParam",

  data: function() {
    return {
      items: [],
      modalItem: {
        id: null,
        ordering: '',
        label: '',
      },
      displayModal: false,
    }
  },
  methods: {
    save: function(items) {
      let uri = $('#report-block-edit-param-ordering').data('edit-block-param-ordering');
      let redirectTo = $('#report-block-edit').data('edit-block');
      this.$http.post(uri, {data: items})
          .then(res => {
            window.location.replace(redirectTo)
          })
          .catch(err => {
            console.log(err);
          });

    },
    initModal: function() {
      // List de visit
      let uri = $('#report-block-edit-param-order').data('edit-block-param-order');
      this.$http.get(uri)
          .then(res => {
            this.items = res.data
          });

      this.displayModal = true;
    },

    onMove: function(evt) {
      this.reorder(evt.oldIndex, evt.newIndex)
    },

    reorder(oldIndex, newIndex) {
      // move the item in the underlying array
      this.items.splice(newIndex, 0, this.items.splice(oldIndex, 1)[0]);
      // update order property based on position in array
      this.items.forEach(function(item, index){
        item.ordering = index;
      });
    },
    // Get translated text from yaml
    getDomTranslation(key) {
      if (null == document.querySelector('.data_translate').getAttribute(key)) {
        return key;
      }
      return document.querySelector('.data_translate').getAttribute(key);
    },
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

ul {
  list-style: none;
  padding: 0;
}

li {
  background: #eee;
  margin: 1px;
  padding: 5px 10px;
}

.sort-ghost {
  opacity: 0.3;
  transition: all 0.7s ease-out;
}

.drap-and-drop {
  cursor: grabbing
}

</style>
