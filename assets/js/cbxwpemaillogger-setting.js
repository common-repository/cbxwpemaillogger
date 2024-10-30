(function ($) {
	'use strict';

	$(document).ready(function () {

		//Initiate Color Picker
		$('.wp-color-picker-field').wpColorPicker({

		});

		$('.selecttwo-select').select2({
			placeholder: cbxwpemaillogger_setting.please_select,
			allowClear: false
		});

		/*$('#cbxwpemaillogger_info_trig').on('click', function (e) {
			e.preventDefault();

			$('#cbxwpemaillogger_resetinfo').toggle();

		});*/

		// Switches option sections

		var activetab = '';
		if (typeof (localStorage) !== 'undefined') {
			//get
			activetab = localStorage.getItem('cbxwpemailloggeractivetab');
		}

		//if url has section id as hash then set it as active or override the current local storage value
		if (window.location.hash) {
			if ($(window.location.hash).hasClass('cbxwpemaillogger_group')) {
				activetab = window.location.hash;
				if (typeof (localStorage) !== 'undefined') {
					localStorage.setItem('cbxwpemailloggeractivetab', activetab);
				}
			}
		}

		console.log(activetab);

		if (activetab !== '' && $(activetab).length && $(activetab).hasClass('cbxwpemaillogger_group')) {
			$('.cbxwpemaillogger_group').hide();
			$(activetab).fadeIn();
		}


		if (activetab !== '' && $(activetab + '-tab').length) {
			$('.nav-tab-wrapper a.nav-tab').removeClass('nav-tab-active');
			$(activetab + '-tab').addClass('nav-tab-active');
		}

		$('.nav-tab-wrapper a').on('click', function(e) {
			e.preventDefault();

			var $this = $(this);

			$('.nav-tab-wrapper a.nav-tab').removeClass('nav-tab-active');
			$this.addClass('nav-tab-active').blur();

			var clicked_group = $(this).attr('href');

			if (typeof(localStorage) !== 'undefined') {
				localStorage.setItem('cbxwpemailloggeractivetab', $(this).attr('href'));
			}
			$('.cbxwpemaillogger_group').hide();
			$(clicked_group).fadeIn();
		});


		$('.wpsa-browse').on('click', function (event) {
			event.preventDefault();

			var self = $(this);

			// Create the media frame.
			var file_frame = wp.media.frames.file_frame = wp.media({
				title: cbxwpemaillogger_setting.upload_title,
				button: {
					text: cbxwpemaillogger_setting.please_select
				},
				multiple: false
			});

			file_frame.on('select', function () {
				var attachment = file_frame.state().get('selection').first().toJSON();

				self.prev('.wpsa-url').val(attachment.url);
			});

			// Finally, open the modal
			file_frame.open();
		});

		//sort photos
		/*var adjustment_photo;
		$('.multicheck_fields').sortable({
			vertical         : true,
			handle           : '.multicheck_field_handle',
			containerSelector: '.multicheck_fields',
			itemSelector     : '.multicheck_field',
			placeholder      : '<p class="multicheck_field_placeholder"/>',
		});*/

		//sort photos
		//var adjustment_photo;
		$('.multicheck_fields_sortable').sortable({
			vertical: true,
			handle: '.multicheck_field_handle',
			//containerSelector: '.multicheck_fields',
			itemSelector: '.multicheck_field',
			//placeholder      : '<p class="multicheck_field_placeholder"/>',
			placeholder: 'multicheck_field_placeholder',
		});



		//make the subheading single row
		$('.setting_subheading').each(function (index, element) {
			var $element = $(element);
			var $element_parent = $element.parent('td');
			$element_parent.attr('colspan', 2);
			$element_parent.prev('th').remove();
		});

		//make the subheading single row
		$('.setting_heading').each(function (index, element) {
			var $element = $(element);
			var $element_parent = $element.parent('td');
			$element_parent.attr('colspan', 2);
			$element_parent.prev('th').remove();
		});

		$('.cbxwpemaillogger_group').each(function (index, element) {
			var $element = $(element);
			var $form_table = $element.find('.form-table');
			$form_table.prev('h2').remove();
		});


		$('.cbxwpemaillogger_group').on('click', '.checkbox', function (event) {
			var mainParent = $(this).closest('.checkbox-toggle-btn');
			if ($(mainParent).find('input.checkbox').is(':checked')) {
				$(mainParent).addClass('active');
			} else {
				$(mainParent).removeClass('active');
			}

		});

		//one click save setting for the current tab
		$('#save_settings').on('click', function (e){
			e.preventDefault();

			//var $this = $(this);

			var $current_tab = $('.nav-tab.nav-tab-active');
			var $tab_id = $current_tab.data('tabid');

			$('#'+$tab_id).find('.submit_cbxwpemaillogger').trigger('click');
		});

		//extra for email plugin

		$('.form-table-fields-parent').sortable({
			vertical: true,
			handle: '.form-table-fields-parent-item-sort',
			//containerSelector: '.form-table-fields-parent',
			itemSelector: '.form-table-fields-parent-item',
			//placeholder      : '<p class="multicheck_field_placeholder"/>',
			placeholder: 'form-table-fields-parent-item-placeholder'
		});

		//open close the input fields
		$('.form-table-fields-parent').on('click', '.form-table-fields-parent-item-control', function (event) {
			var $this = $(this);
			//console.log($this);

			var $parent = $this.closest('.form-table-fields-parent-item');
			$parent.find('.form-table-fields-parent-item-wrap').toggle();
		});

		//delete the input
		$('.form-table-fields-parent').on('click', '.form-table-fields-parent-item-delete', function (event) {
			var $this = $(this);
			var $parent = $this.closest('.form-table-fields-parent-item');
			//$parent.remove();
			Ply.dialog({
				'confirm-step': {
					ui: 'confirm',
					data: {
						text: cbxwpemaillogger_setting.deleteconfirm,
						ok: cbxwpemaillogger_setting.deleteconfirmok, // button text
						cancel: cbxwpemaillogger_setting.deleteconfirmcancel
					},
					backEffect: '3d-flip[-180,180]'
				}
			}).always(function (ui) {
				if (ui.state) {
					// Ok
					$parent.remove();

				} else {
					// Cancel
					// ui.by — 'overlay', 'x', 'esc'
				}
			});
		});

		//add new input
		$('.form-table-fields-parent-wrap').on('click', '.form-table-fields-new', function (event) {
			event.preventDefault();

			var $this = $(this);
			var $parent = $this.closest('.form-table-fields-parent-wrap');
			var $items = $parent.find('.form-table-fields-parent');

            var $section_name   = $this.data('section_name');
            var $option_name    = $this.data('option_name');
            var $field_name     = $this.data('field_name');
            var $busy           = Number($this.data('busy'));
            var $index          = Number($this.data('index'));

			if (!$busy) {
				$this.data('busy', 1);

				$.ajax({
					type: 'post',
					dataType: 'json',
					url: cbxwpemaillogger_setting.ajaxurl,
					data: {
						action: 'cbxwpemaillogger_add_new_field',
						section_name: $section_name,
						option_name: $option_name,
						field_name: $field_name,
						security: cbxwpemaillogger_setting.nonce,
                        index: $index
					},
					success: function (data, textStatus, XMLHttpRequest) {
						/*Ply.dialog('alert', data.message);

						if (Number(data.success) == 1) {
							$this.closest('tr').remove();
						}*/


						$items.append(data['html']);

						$this.data('busy', 0);
						$this.data('index', Number(data['index']));

                        $('.cbx-hideshowpassword').hidePassword(true);

					}
				});
			}
		});

		//apply show/hide password feature to smtp password field
		$('.cbx-hideshowpassword').hidePassword(true);
		//$('input:password').hidePassword(true);

	});

})(jQuery);
