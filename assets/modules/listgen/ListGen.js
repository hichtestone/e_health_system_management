"use strict";

/**
 * @type $
 */

const TailSelect = require('tail.select');
require('./ListGen.scss');
let Utils = require('../Utils/Utils');
let CFloading = require('../CFloading/CFloading');
let axios = require('axios');
let swal = require('sweetalert2');
let flatpickr = require('flatpickr');

// todo transformer en module constructeur

function oListGen() {

	function ajaxResBgEffect(element, success) {
		let cMem = element.css('background-color');
		let c = (success == 1 ? 'green' : 'red');
		element.css('background-color', c);
		setTimeout(function () {
			element.css('background-color', cMem);
		}, 500);
	}


	let ListGen = function (lgElement) {
		this.table = lgElement.find('table');
		this.toolbarTop = lgElement.find('.toolbar-top');
		this.toolbarBottom = lgElement.find('.toolbar-bottom');
		if (typeof this.table.attr('data-params') != 'undefined') {
			this.params = JSON.parse(this.table.attr('data-params'));
		}
		this.exportButton = lgElement.find('a[data-lg-export]');
		let me = this;
		//console.log(me.params);

		// rafraichi le tableau
		me.refresh = function () {
			me.refreshFilterData();
			me.params.trigger = 'sort';
			//console.log(me.params);
			CFloading.start();
			Utils.ajax({
				complete: function () {
					CFloading.stop();
				},
				data: me.params,
				error: function (data) {
					console.log('error');
					console.log(data);
				},
				success: function (data) {
					// on refresh le tbody et la toolbar
					me.params = data.params;
					me.table.find('tbody').html(data.html.table);
					//that.toolbarTop.html(data.html['toolbar-top']);
					me.toolbarBottom.html(data.html['toolbar-bottom']);

					// on reset les events du tbody
					me.setActionEvents();
					me.setMultiActionEvents();
					me.refreshSortPriority();
					me.setEventPagination();
				},
				type: 'GET',
				url: me.params['ajaxUrl']
			});
		};


		// reset sort
		me.resetSort = function () {
			let i = 0;
			while (i < me.params.sorts.length) {
				me.params.sorts[i].priority = 0;
				me.params.sorts[i].order = '';
				i++;
			}
			me.table.find('th i')
				.removeClass('fa-sort-up')
				.removeClass('fa-sort-down')
				.addClass('fa-sort');
			me.refreshSortPriority();
		};


		// get sort
		me.getSort = function (iCol) {
			return me.params.sorts[iCol];
		};


		// set sort
		me.setSort = function (iCol, order) {
			// on interverti les priorités
			let i = 0;
			while (i < me.params.sorts.length) {
				if (me.params.sorts[i].priority != 0) {
					if (me.params.sorts[iCol].priority == 0 || me.params.sorts[i].priority < me.params.sorts[iCol].priority) {
						me.params.sorts[i].priority++;
					}
				}
				i++;
			}
			// prio 1 pour le nouveau
			me.params.sorts[iCol] = {order: order, priority: 1};
		};


		// remove sort
		me.removeSort = function (iCol) {
			// on remonte tous ceux qui avaient une prio plus faible
			let i = 0;
			while (i < me.params.sorts.length) {
				if (me.params.sorts[i].priority != 0) {
					if (me.params.sorts[i].priority > me.params.sorts[iCol].priority) {
						me.params.sorts[i].priority--;
					}
				}
				i++;
			}
			// remove
			me.params.sorts[iCol] = {order: '', priority: 0};
		};


		// get data from form
		me.refreshFilterData = function () {
			let $form = me.toolbarTop.find('form');
			me.params.filters = Utils.jsonSerializeForm($form);
		};


		// set event on filter buttons
		me.setFilterEvents = function () {
			// Instance de flatpickr existe
			if (null !== document.querySelector('.flatpickr-range')) {
				$(".flatpickr-range").flatpickr('.flatpickr-range', {
					locale: document.documentElement.lang,
					mode: 'range'
				});
			}
			me.toolbarTop.find('form').submit(function () {

				let $this = $(this);
				let submitBut = $(document.activeElement);
				me.params.page = 1;
				me.params.trigger = submitBut.attr('name');
				if (me.params.trigger == 'reset') {
					$this.find('input:text,select').val('').trigger('change');
					$this.find('input:radio, input:checkbox').prop('checked', false);
					me.resetSort();
				}
				me.refreshFilterData();
				me.refresh();

				return false;
			});
		};


		// event pagination
		me.setEventPagination = function (first) {
			let selectPageEl = me.toolbarBottom.find('select[name="page"]');
			selectPageEl.change(function () {
				me.params.page = $(this).val();
				me.refresh();
			});
			let selectNbPerPageEl = me.toolbarBottom.find('select[name="show"]');
			selectNbPerPageEl.change(function () {
				me.params.nbPerPage = $(this).val();
				me.refresh();
			});
			if (!first) {
				/*me.toolbarBottom.find('select').select2({ // todo pareil avec select.tail
					minimumResultsForSearch: 20
				});*/
			}
		};

		// set events on multiaction checkboxes and select
		me.setMultiActionEvents = function() {
			let select = $('#lg-actions');
			if(select){
				// checkboxes
				let mainCb = me.table.find('th input[type="checkbox"]');
				let checkboxes = me.table.find('td input[type="checkbox"]');

				checkboxes.click(function(){
					mainCb.prop('checked', false);
					// Attribut checked du checkbox
					$(this).attr('checked', $(this).prop('checked'));
				});

				mainCb.click(function(){
					checkboxes.each(function(){
						let cb = $(this);
						// Attribut checked du checkbox
						cb.attr('checked', mainCb.prop('checked'));
					});
				});

				// click sur action
				if(me.params.nCall === 1){
					select.change(function(){
						// recuperer l'option selectionné
						// ensuite la valeur de l'attribut redirect
						// redirect window.href
						if(select.val() !== ''){

							// Set a nouveau la variable checkbox apres ajout d'attribut checked="checked"
							checkboxes = me.table.find('td input[type="checkbox"]');

							// on créé les data
							let json = {
								items: [],
							};
							// console.log(`checkboxes.length: ${checkboxes.length}`)
							checkboxes.each(function(){
								let cb = $(this);
								let tr = cb.closest('tr');
								//console.log(`${cb.closest('td').html()} - ${cb.attr('checked')} vs ${cb.prop('checked')} `)
								if(cb.prop('checked')){
									json['items'].push(tr.data());
								}
							});

							CFloading.start();
							let redirectArg = select.find(':selected').attr('data-redirect');
							if(redirectArg){
								window.location.href = select.val()+'?'+redirectArg+'='+encodeURI(JSON.stringify(json['items']));
								return false;
							}

							axios.post(select.val(),json)
								.then(function(res){
									if(res.data.status === 1){
										if(typeof res.data.popup !== 'undefined'){
											let data = res.data.popup;
											data.onOpen = (popup) => {
												// Gestion datepicker
												$(".js-datepicker").datepicker({
													dateFormat: 'dd/mm/yy',
													autoclose: true,
													closeText: 'Fermer',
													prevText: '&#x3c;Préc',
													nextText: 'Suiv&#x3e;',
													currentText: 'Aujourd\'hui',
													monthNames: ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin',
														'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre'],
													monthNamesShort: ['Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Jun',
														'Jul', 'Aou', 'Sep', 'Oct', 'Nov', 'Dec'],
													dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
													dayNamesShort: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
													dayNamesMin: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
												});
												TailSelect(".tail-select", {
													search: true
												});
											}
											swal.fire(data);
										}else{
											me.refresh();
											mainCb.prop('checked', false);
										}

									}else{
										console.log(res);
										alert(res.data.error);
									}
								})
								.catch(function(err){
									console.log(err);
								})
								.finally(function(){
									CFloading.stop();
									select.val('');
								});
						}
					});
				}

			}
		}

		// set event on action buttons
		me.setActionEvents = function (target) {
			// if tr not defined, event is applied on whole table
			if (typeof target == 'undefined') {
				target = me.table;
			}

			target.find('a.lg-ajax').unbind('click');
			target.find('a.lg-ajax').click(function () {

				let element = $(this);

				// get data of a, td and tr
				let tr = element.closest('tr');
				let json = element.data();
				json = Utils.jsonConcat(json, element.closest('td').data());
				json = Utils.jsonConcat(json, tr.data());
				json.ListGen = 1;

				// ajax
				CFloading.start();
				Utils.ajax({
					complete: function () {
						CFloading.stop();
					},
					data: json,
					error: function (data) {
						console.log('error');
						console.log(data);
					},
					success: function (data) {

						if (data.status == 0) {
							alert(data.msg);
						}

						// update html if needed
						if (typeof data.html != 'undefined') {
							// tr
							if (typeof data.html.tr != 'undefined') {
								if (data.html.tr == 'remove') {
									tr.remove();
								} else {
									tr.html(data.html.tr);
								}
							} else {
								// a
								if (typeof data.html.a != 'undefined') {
									element.html(data.html.a);
								}
								// td
								if (typeof data.html.td != 'undefined') {
									let i = 0;
									while (i < data.html.td.length) {
										let td = tr.find('td').eq(data.html.td[i].pos);
										if (typeof data.html.td[i].html != 'undefined') {
											td.html(data.html.td[i].html);
										}
										i++;
									}
								}
							}
							if (typeof data.html.afterUpdEffect != 'undefined') {
								let i = 0;
								while (i < data.html.afterUpdEffect.length) {
									let el = tr.find('td').eq(data.html.afterUpdEffect[i]);
									ajaxResBgEffect(el, data.status);
									i++;
								}
							}
						}
						// reset events
						me.setActionEvents(tr);

					},
					type: 'POST',
					url: this.href
				});
				return false;
			});
		};


		// set event on sort buttons
		me.setSortEvents = function () {
			me.table.find('.lg-sort').click(function () {

				let th = $(this).closest('th');
				let iCol = th.parent().children().index(th);
				if (me.getSort(iCol).order == 'ASC') {
					me.setSort(iCol, 'DESC');
					$(this).find('i')
						.removeClass('fa-sort-down')
						.addClass('fa-sort-up');
				} else if (me.getSort(iCol).order == 'DESC') {
					me.removeSort(iCol);
					$(this).find('i')
						.removeClass('fa-sort-up')
						.addClass('fa-sort');
				} else {
					me.setSort(iCol, 'ASC');
					$(this).find('i')
						.removeClass('fa-sort')
						.addClass('fa-sort-down');
				}

				me.refresh();
				return false;
			});
		};


		// refresh the display of sort priority
		me.refreshSortPriority = function () {
			let i = 0;
			me.table.find('th').each(function () {
				let sup = $(this).find('sup');
				if (sup.length) {
					if (me.params.sorts[i].order != '') {
						$(this).find('sup').html(me.params.sorts[i].priority);
					} else {
						$(this).find('sup').html('');
					}
				}
				i++;
			});
		};

		this.setExportEvent = function(){
			this.exportButton.click(function(){
				me.refreshFilterData();
				me.params.trigger = 'export';
				let url = axios.getUri({
					method: 'get',
					url: me.params['ajaxUrl'],
					params: me.params
				});
				$(this).attr('href', url);
				me.params.trigger = '';
			});
		}

		// initialize events
		me.setFilterEvents();
		me.setSortEvents();
		me.refreshSortPriority();
		me.setExportEvent();
		me.setActionEvents();
		me.setMultiActionEvents();
		me.setEventPagination(true);

	};


	// on créé une instance par tableau ListGen
	// get all listgen
	let tableList = $('.lg-table-wrap');

	// associate one instance per table
	tableList.each(function () {
		new ListGen($(this));
	});
}

/*window.addEventListener('DOMContentLoaded', () => {
	oListGen();
});*/

module.exports = oListGen();
