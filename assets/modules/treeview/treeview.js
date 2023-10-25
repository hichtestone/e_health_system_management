require('./treeview.scss');
require('jstree');


// init les events sur la page de login

function Treeview() {


    let dom_jstree = $('*[data-tree]')
    if (dom_jstree.length > 0) { 

        dom_jstree
            .on('changed.jstree', (e, data) => {

                // console.log(data.node)

                // Check all parent
                if ('undefined' !== typeof data.node && 'undefined' !== typeof data.action && 'select_node' === data.action) {
                    data.node.parents.forEach((parent) => {
                        dom_jstree.jstree('select_node', parent)
                    })
                }

                // Uncheck all children
                if ('undefined' !== typeof data.action && 'deselect_node' === data.action) {
                    data.node.children_d.forEach((child) => {
                        dom_jstree.jstree('deselect_node', child)
                    })
                }

                let attributeSelectfield = null;
                let attributeHiddenSelectfield = null;

                let i, j, r = []

                for(i = 0, j = data.selected.length; i < j; i++) {
                    let idNode = $('#'+(data.instance.get_node(data.selected[i])).id).attr('data-id')

                    if ('' !== jQuery.trim(idNode)) {
                        r.push(idNode)
                    }
                }





                if (typeof undefined !== typeof dom_jstree.attr('data-select-field-id') && false !== dom_jstree.attr('data-select-field-id')) {
                    attributeSelectfield = dom_jstree.attr('data-select-field-id')
                }
                if (typeof undefined !== typeof dom_jstree.attr('data-hidden-select-field-id') && false !== dom_jstree.attr('data-hidden-select-field-id')) {
                    attributeHiddenSelectfield = dom_jstree.attr('data-hidden-select-field-id')
                }

                if (null !== attributeHiddenSelectfield) {
                    $(`#${attributeHiddenSelectfield}`).val(r.join(','))
                }

                if (null !== attributeSelectfield) {
                    // Remove selected attribute on each <option>
                    $(`#${attributeSelectfield} option`).removeAttr('selected')

                    // Set selected attribute
                    r.forEach((v) => {
                        $(`#${attributeSelectfield} option[value="${v}"]`).attr('selected', 'selected')
                    })
                }



            })
            .jstree({
                "core": {
                    "themes":{
                        "icons":false
                    }
                },
                "plugins" : ["checkbox"],
                "checkbox" : {
                    cascade: "",
                    three_state: false
                }
            })

    }
}

module.exports = Treeview();

