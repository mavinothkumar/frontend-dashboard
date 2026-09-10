/**
 * Admin Scripts.
 *
 * @package Frontend Dashboard.
 */

jQuery( document ).ready(
	function ( $ ) {
		var bc_fed = $( '.bc_fed' );
		var body = $( 'body' );
		var fed_menu_ajax = $( 'form.fed_menu_ajax' );
		/**
		 * Admin Page / User Profile Setting Save/Edit.
		 */
		body.on(
			'submit', '.fed_ajax', function ( e ) {
				var form = $( this );
				if ( form.hasClass( 'fed_admin_menu' ) || form.closest( '#fed_field_builder_modal' ).length ) {
					return;
				}
				fed_toggle_loader();
				$.ajax(
					{
						type: 'POST',
						url: form.attr( 'action' ),
						data: form.serialize(),
						success: function ( results ) {
							fed_toggle_loader();
							fedAdminAlert.adminSettings( results );
						}

					}
				);

				e.preventDefault();
			}
		);

		body.on(
			'submit', '.fed_ajax_plugin_install', function ( e ) {
				var form = $( this );
				fed_toggle_loader();
				$.ajax(
					{
						type: 'POST',
						url: form.attr( 'action' ),
						data: form.serialize(),
						success: function ( results ) {
							console.log( results );
							fed_toggle_loader();
							if ( results.success ) {
								swal(
									{
										title: frontend_dashboard.alert.plugin_installed_successfully,
										text: frontend_dashboard.alert.redirecting,
										type: "success",
										showConfirmButton: false,
										timer: 3000,
										confirmButtonColor: '#0AAAAA',
									}
								).then(
									function () {
									},
									function () {
										window.location = results.data.activateUrl + '&fed_plugin_custom_activate=on'
									}
								);
							} else {
								swal(
									{
										title: results.data.errorMessage || frontend_dashboard.alert.something_went_wrong,
										type: "error",
										confirmButtonColor: '#DD6B55',
									}
								)
							}
						}

					}
				);

				e.preventDefault();
			}
		);

		body.on(
			'click', '.fed_is_delete', function ( e ) {
				var click = $( this );
				swal(
					{
						title: frontend_dashboard.alert.confirmation.title,
						text: frontend_dashboard.alert.confirmation.text,
						type: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#0AAAAA',
						cancelButtonColor: '#d33',
						confirmButtonText: 'Yes, Please'
					}
				).then(
					function ( result ) {
						if ( result ) {
							click.closest( 'form' ).submit();
						}
					}, function ( dismiss ) {
						if ( dismiss === 'cancel' ) {

						} else {
							throw dismiss;
						}
					}
				);

				e.preventDefault();
			}
		);

		/**
		 * Request with Confirmation
		 *
		 * @type {*}
		 */
		$( '.fed_ajax_confirmation' ).on(
			'submit', function ( e ) {
				var form = $( this );
				swal(
					{
						title: frontend_dashboard.alert.confirmation.title,
						text: frontend_dashboard.alert.confirmation.text,
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: frontend_dashboard.alert.confirmation.confirm,
						cancelButtonText: frontend_dashboard.alert.confirmation.cancel,
					}
				).then(
					function () {
						fed_toggle_loader();
						$.ajax(
							{
								type: 'POST',
								url: form.attr( 'action' ),
								data: form.serialize(),
								success: function ( results ) {
									fed_toggle_loader();
									fedAdminAlert.adminSettings( results );
								}

							}
						);
					}, function ( dismiss ) {
						if ( dismiss === 'cancel' ) {
							swal(
								{
									title: frontend_dashboard.alert.title_cancelled,
									type: "error",
									confirmButtonColor: '#0AAAAA'
								}
							);
						}
					}
				);
				e.preventDefault();
			}
		);

		body.on(
			'click', '.fed_upload_container', function ( e ) {
				var custom_uploader;
				var button_click = $( this );
				e.preventDefault();
				custom_uploader = wp.media.frames.file_frame = wp.media(
					{
						title: 'Upload',
						button: {
							text: 'Upload'
						},
						multiple: false
					}
				);
				// When a file is selected, grab the URL and set it as the text field's value
				custom_uploader.on(
					'select', function () {
						var attachment = custom_uploader.state().get( 'selection' ).first().toJSON();
						button_click.find( '.fed_upload_icon' ).addClass( 'hide' );
						button_click.find( '.fed_upload_input' ).val( attachment.id );
						button_click.find( '.fed_upload_image_container' ).html( "<img width=100 height=100 src=" + attachment.url + ">" );
					}
				);
				// Open the uploader dialog.
				custom_uploader.open();
			}
		);

		fed_menu_ajax.on(
			'click', '.fed_menu_save', function ( e ) {
				var form = $( this ).closest( 'form' );
				fed_toggle_loader();
				$.ajax(
					{
						type: 'POST',
						url: form.attr( 'action' ),
						data: { 'fed_action': 'save', 'data': form.serialize() },
						success: function ( results ) {
							// console.log(results);
							fed_toggle_loader();
							fedAdminAlert.adminSettings( results );
						}

					}
				);

				e.preventDefault();
			}
		);

		/**
		 * Delete Menu
		 */
		fed_menu_ajax.on(
			'click', '.fed_menu_delete', function ( e ) {
				var form = $( this ).closest( 'form' );
				swal(
					{
						title: frontend_dashboard.alert.confirmation.title,
						text: frontend_dashboard.alert.confirmation.text,
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: frontend_dashboard.alert.confirmation.confirm,
						cancelButtonText: frontend_dashboard.alert.confirmation.cancel,
						showLoaderOnConfirm: true
					}
				).then(
					function () {
						fed_toggle_loader();
						$.ajax(
							{
								type: 'POST',
								url: form.attr( 'action' ),
								data: { 'fed_action': 'delete', 'data': form.serialize() },
								success: function ( results ) {
									fedAdminAlert.adminSettings( results );
								}

							}
						);
						fed_toggle_loader();
					},
					function ( dismiss ) {
						if ( dismiss === 'cancel' ) {
							swal(
								{
									title: frontend_dashboard.alert.title_cancelled,
									type: "error",
									confirmButtonColor: '#0AAAAA'
								}
							);
						}
					}
				);
				e.preventDefault();
			}
		);

		/**
		 * Delete User Profile.
		 */
		$( 'form.fed_profile_ajax' ).on(
			'click', '.fed_profile_delete', function ( e ) {
				var form = $( this ).closest( 'form' );
				swal(
					{
						title: frontend_dashboard.alert.confirmation.title,
						text: frontend_dashboard.alert.confirmation.text,
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: frontend_dashboard.alert.confirmation.confirm,
						cancelButtonText: frontend_dashboard.alert.confirmation.cancel,
						showLoaderOnConfirm: true
					}
				).then(
					function () {
						$.ajax(
							{
								type: 'POST',
								url: form.attr( 'action' ),
								data: { 'fed_up_action': 'delete', 'data': form.serialize() },
								success: function ( results ) {
									fedAdminAlert.adminSettings( results );
								}

							}
						);
					},
					function ( dismiss ) {
						if ( dismiss === 'cancel' ) {
							swal(
								{
									title: frontend_dashboard.alert.title_cancelled,
									type: "error",
									confirmButtonColor: '#0AAAAA'
								}
							)
						}
					}
				);
				fed_toggle_loader();
				e.preventDefault();
			}
		);
		/**
		 * disallow symbols on input meta other than underscore.
		 */
		$( 'input.fed_admin_input_meta, input.fed_menu_slug' ).keypress(
			function ( e ) {
				var regex = new RegExp( "^[a-zA-Z0-9_]+$" );
				var str = String.fromCharCode( ! e.charCode ? e.which : e.charCode );
				if ( regex.test( str ) ) {
					return true;
				}

				e.preventDefault();
				return false;
			}
		);

		/**
		 * Based on the selection of Input type change the form fields.
		 */
		$( '.fed_add_edit_input_container' ).on(
			'click', '.fed_button', function ( e ) {
				var container = $( this ).closest( '.fed_add_edit_input_container' );
				var btn_container = $( this ).closest( '.fed_buttons_container' );
				var selected = $( this ).data( 'button' );
				var closest = container.find( '.fed_all_input_fields_container' );
				closest.find( '.fed_input_type_container' ).addClass( 'hide' );
				closest.find( 'input[name=input_type]' ).val( selected );
				btn_container.find( '.fed_button' ).removeClass( 'active' );
				$( this ).addClass( 'active' );
				$( '#fed_button_pointing_arrow' ).addClass( 'hide' );

				closest.find( '.fed_input_'+selected+'_container' ).removeClass( 'hide' );

				// switch ( selected ) {
				// 	case 'single_line':
				// 		closest.find( '.fed_input_single_line_container' ).removeClass( 'hide' );
				// 		break;
				// 	case 'number':
				// 		closest.find( '.fed_input_number_container' ).removeClass( 'hide' );
				// 		break;
				// 	case 'multi_line':
				// 		closest.find( '.fed_input_multi_line_container' ).removeClass( 'hide' );
				// 		break;
				// 	case 'email':
				// 		closest.find( '.fed_input_email_container' ).removeClass( 'hide' );
				// 		break;
				// 	case 'checkbox':
				// 		closest.find( '.fed_input_checkbox_container' ).removeClass( 'hide' );
				// 		break;
				// 	case 'select':
				// 		closest.find( '.fed_input_dropdown_container' ).removeClass( 'hide' );
				// 		break;
				// 	case 'radio':
				// 		closest.find( '.fed_input_radio_container' ).removeClass( 'hide' );
				// 		break;
				// 	case 'password':
				// 		closest.find( '.fed_input_password_container' ).removeClass( 'hide' );
				// 		break;
				// 	case 'url':
				// 		closest.find( '.fed_input_url_container' ).removeClass( 'hide' );
				// 		break;
				// 	case 'date':
				// 		closest.find( '.fed_input_date_container' ).removeClass( 'hide' );
				// 		break;
				// 	case 'file':
				// 		closest.find( '.fed_input_file_container' ).removeClass( 'hide' );
				// 		break;
				// 	case 'color':
				// 		closest.find( '.fed_input_color_container' ).removeClass( 'hide' );
				// 		break;
				// 	case 'wysiwyg':
				// 		closest.find( '.fed_input_wysiwyg_container' ).removeClass( 'hide' );
				// 		break;
				// 	case 'wp_editor':
				// 		closest.find( '.fed_input_wp_editor_container' ).removeClass( 'hide' );
				// 		break;
				//
				// }
				e.preventDefault();
			}
		);

		$( '.fed_add_edit_input_container .fed_button.active' ).trigger( 'click' );

		/**
		 * Auto populate Input Meta Key from Label Name
		 */
		function fedSlugifyInputMeta( text ) {
			if ( ! text ) return '';
			return text.toString()
				.toLowerCase()
				.trim()
				.replace( /[^a-z0-9_ ]/g, '' )
				.replace( /\s+/g, '_' )
				.replace( /_+/g, '_' )
				.replace( /^_+|_+$/g, '' )
				.substring( 0, 32 );
		}

		$( document ).on(
			'input keyup paste change', 'input[name="label_name"]', function () {
				var $labelInput = $( this );
				var $form = $labelInput.closest( 'form' );
				var $metaInput = $form.find( 'input[name="input_meta"]' );

				if ( ! $metaInput.length || $metaInput.prop( 'readonly' ) || $metaInput.hasClass( 'bg-slate-100' ) || $metaInput.data( 'locked' ) ) {
					return;
				}

				if ( $metaInput.data( 'fed-manual' ) && $metaInput.val() !== '' ) {
					return;
				}

				var slug = fedSlugifyInputMeta( $labelInput.val() );
				$metaInput.val( slug );
			}
		);

		$( document ).on(
			'input keyup', 'input[name="input_meta"]', function () {
				var $metaInput = $( this );
				if ( $metaInput.val() === '' ) {
					$metaInput.removeData( 'fed-manual' );
				} else {
					$metaInput.data( 'fed-manual', true );
				}
			}
		);

		/**
		 * Auto populate Menu Slug
		 */
		$( '.fed_menu_name' ).on(
			'change', function () {
				var value = $( this ).val().replace( /[^a-zA-Z0-9 ]/g, "" ).split( ' ' ).join( '_' ).replace( "^[a-zA-Z0-9_]+$", " " ).toLowerCase();
				$( this ).closest( 'form' ).find( '.fed_menu_slug' ).val( value );
			}
		);

		$( '#fed_admin_post_user_role_name' ).on(
			'change', function () {
				var value = $( this ).val().replace( /[^a-zA-Z0-9 ]/g, "" ).split( ' ' ).join( '-' ).replace( "^[a-zA-Z0-9_]+$", " " ).toLowerCase();
				$( '#fed_admin_post_user_role_slug' ).val( value );
			}
		);

		$( '#fed_admin_setting_tabs a' ).click(
			function ( e ) {
				e.preventDefault();
				if ( typeof $.fn.tab === 'function' ) {
					$( this ).tab( 'show' );
				}
			}
		);

		var hash = document.location.hash;
		var prefix = "tab_";
		if ( hash && typeof $.fn.tab === 'function' ) {
			$( '.nav-tabs a[href="' + hash.replace( prefix, "" ) + '"]' ).tab( 'show' );
		}
		// Change hash for page-reload.
		$( '.nav-tabs a' ).on(
			'shown', function ( e ) {
				window.location.hash = e.target.hash.replace( "#", "#" + prefix );
			}
		);

		/**
		 * User Profile Layout.
		 */
		$( '#fed_LSRB' ).on(
			'click', function ( e ) {
				$( '#fed_UPL_layout_container' ).html( fed.fed_LSRB );
				e.preventDefault();
			}
		);

		$( '.fed_multi_select' ).select2();

		$( '#fed_LBRS' ).on(
			'click', function ( e ) {
				$( '#fed_UPL_layout_container' ).html( fed.fed_LBRS );
				e.preventDefault();
			}
		);

		// --- Modern Icon Picker Modal Logic ---
		function getIconModal() {
			return $( '#fed_icon_picker_modal, .fed_show_fa_list' );
		}

		function openIconModal( targetName ) {
			var $modal = getIconModal();
			if ( ! $modal.length ) return;
			$modal.find( '#fed_menu_box_id' ).val( targetName || '' );
			$modal.removeClass( 'hidden opacity-0 pointer-events-none' )
				.addClass( 'opacity-100 pointer-events-auto flex' )
				.attr( 'style', 'display: flex !important; z-index: 999999 !important;' );
			$modal.find( '.fed-icon-modal-dialog' )
				.removeClass( 'scale-95 opacity-0' )
				.addClass( 'scale-100 opacity-100' );
			$modal.find( '#fed_global_icon_search' ).val( '' ).focus();
			$modal.find( '.fed_single_fa' ).show();
			var totalIcons = $modal.find( '.fed_single_fa' ).length;
			$modal.find( '#fed_global_icon_count_display' ).text( totalIcons + ' icons available' );
		}

		function closeIconModal() {
			var $modal = getIconModal();
			if ( ! $modal.length ) return;
			$modal.find( '.fed-icon-modal-dialog' )
				.removeClass( 'scale-100 opacity-100' )
				.addClass( 'scale-95 opacity-0' );
			setTimeout( function () {
				$modal.removeClass( 'opacity-100 pointer-events-auto flex' )
					.addClass( 'hidden opacity-0 pointer-events-none' )
					.attr( 'style', 'display: none !important;' );
			}, 150 );
		}

		// Trigger to Open Modal
		body.on( 'click', '[data-target=".fed_show_fa_list"], [data-target="#fed_icon_picker_modal"], #fed_trigger_icon_picker, .fed_icon_picker_trigger', function ( e ) {
			e.preventDefault();
			var target = $( this ).data( 'fed_menu_box_id' ) || $( this ).data( 'target_input' ) || $( this ).attr( 'name' ) || 'fed_form_menu_icon';
			openIconModal( target );
		} );

		// Close Modal
		body.on( 'click', '.fed_close_icon_modal', function ( e ) {
			e.preventDefault();
			closeIconModal();
		} );

		// Close on Backdrop Click
		body.on( 'click', '#fed_icon_picker_modal, .fed_show_fa_list', function ( e ) {
			if ( $( e.target ).is( '#fed_icon_picker_modal, .fed_show_fa_list' ) ) {
				closeIconModal();
			}
		} );

		// Close on Escape Key
		$( document ).on( 'keydown', function ( e ) {
			var $modal = getIconModal();
			if ( e.key === 'Escape' && $modal.is( ':visible' ) && ! $modal.hasClass( 'hidden' ) ) {
				closeIconModal();
			}
		} );

		// Live Search Filter
		body.on( 'input', '#fed_global_icon_search', function () {
			var $modal = getIconModal();
			var query = $( this ).val().toLowerCase().trim();
			var matched = 0;
			$modal.find( '.fed_single_fa' ).each( function () {
				var iconId = ( $( this ).data( 'id' ) || '' ).toLowerCase();
				if ( ! query || iconId.indexOf( query ) !== -1 ) {
					$( this ).show();
					matched++;
				} else {
					$( this ).hide();
				}
			} );
			$modal.find( '#fed_global_icon_count_display' ).text( matched + ' icons matching' );
		} );

		// Icon Selected
		body.on( 'click', '.fed_single_fa', function ( e ) {
			e.preventDefault();
			var iconClass = $( this ).data( 'id' );
			var $modal = getIconModal();
			var targetName = $modal.find( '#fed_menu_box_id' ).val();

			if ( targetName ) {
				var $target = $( '#' + targetName );
				if ( ! $target.length ) {
					$target = $( '.' + targetName );
				}
				if ( ! $target.length ) {
					$target = $( '[name="' + targetName + '"]' );
				}
				if ( $target.length ) {
					$target.val( iconClass ).trigger( 'change' ).trigger( 'input' );
				}
			}

			// Also update dashboard menu inputs if present
			if ( $( '#fed_form_menu_icon' ).length ) {
				$( '#fed_form_menu_icon' ).val( iconClass ).trigger( 'input' ).trigger( 'change' );
			}
			if ( $( '#fed_selected_icon_preview' ).length ) {
				$( '#fed_selected_icon_preview' ).html( '<i class="' + iconClass + '"></i>' );
			}

			closeIconModal();
		} );

		body.on(
			'click', '.fed_menu_save_button_toggle', function ( e ) {
				if ( $( '#fed_add_new_menu_container' ).hasClass( 'hide' ) ) {
					$( '#fed_add_new_menu_container' ).removeClass( 'hide' );
					$( this ).html( '<i class="fa fa-minus"></i> ' + frontend_dashboard.common.hide_add_new_menu );
				} else {
					$( '#fed_add_new_menu_container' ).addClass( 'hide' );
					$( this ).html( '<i class="fa fa-plus"></i> ' + frontend_dashboard.common.add_new_menu );
				}
				e.preventDefault();
			}
		);

		body.on(
			'change', 'select.fed_payment_cycles', function ( e ) {
				var selected = $( this ).val();
				var closest = $( this ).closest( '.fed_role_based_payment_cycle' );
				if ( selected === 'custom' ) {
					if ( closest.find( '.custom_payment_cycle_container' ).hasClass( 'hide' ) ) {
						closest.find( '.custom_payment_cycle_container' ).removeClass( 'hide' );
					}
				} else {
					closest.find( '.custom_payment_cycle_container' ).addClass( 'hide' );
				}
				e.preventDefault();
			}
		);

		// Search for user in Order.
		$( '.fed_order_search_add' ).on(
			'submit', function ( e ) {
				var form = $( this );
				$.ajax(
					{
						type: 'POST',
						url: form.attr( 'action' ),
						data: form.serialize(),
						success: function ( results ) {
							fedAdminAlert.adminSettings( results );
							if ( results.data.extra ) {
								$( '#email' ).val( results.data.extra.email );
								$( '#first_name' ).val( results.data.extra.first_name );
								$( '#last_name' ).val( results.data.extra.last_name );
								$( '#user_id' ).val( results.data.extra.user_id );
							}
						}
					}
				);
				e.preventDefault();
			}
		);

		$( 'form.fed_admin_add_orders' ).on(
			'submit', function ( e ) {
				var form = $( this ).closest( 'form' );
				fed_toggle_loader();
				$.ajax(
					{
						type: 'POST',
						url: form.attr( 'action' ),
						data: form.serialize(),
						success: function ( results ) {
							fed_toggle_loader();
							fedAdminAlert.adminAlertSettings( results );
						}

					}
				);

				e.preventDefault();
			}
		);

		$( 'select.fed_payment_cycles' ).trigger( 'change' );

		/**
		 * Hide Admin notice
		 */
		body.on(
			'click', '.fed_message_hide', function ( e ) {
				$( this ).closest( '.notice' ).hide();
				e.preventDefault();
			}
		);
		body.on(
			'click', '.fed_message_delete', function ( e ) {
				var form = $( this );
				$.ajax(
					{
						type: 'get',
						url: form.data( 'url' ),
						data: {},
						success: function ( results ) {
							form.closest( '.notice' ).hide();
							fedAdminAlert.adminSettings( results );
						}
					}
				);
				e.preventDefault();
			}
		);

		/**
		 * Admin Main Menu Search.
		 */
		$( '#fed_menu_search' ).on(
			'input', function ( e ) {
				var input = $( this );
				var parent = input.closest( '.fed_dashboard_menu_items_container' );
				var item = $( parent ).find( '.fed_dashboard_menu_single_item' );
				var filter = input.val().toLowerCase();
				if ( input.val().length > 0 ) {
					input.closest( '.fed_search_box' ).find( '.fed_menu_search_clear' ).removeClass( 'hide' );
				} else {
					input.closest( '.fed_search_box' ).find( '.fed_menu_search_clear' ).addClass( 'hide' );
				}
				item.each(
					function () {
						if ( ! $( this ).hasClassRegEx( filter ) && input.val().length > 0 ) {
							$( this ).fadeOut();
						} else {
							$( this ).show();
						}
					}
				);
				e.preventDefault();
			}
		);

		/**
		 * Admin Main Menu Clear
		 */
		$( '.fed_menu_search_clear' ).on(
			'click', function ( e ) {
				$( this ).closest( '.fed_search_box' ).find( 'input' ).val( '' );
				$( '#fed_menu_search' ).trigger( 'input' );
				e.preventDefault();
			}
		);

		/**
		 * Single line executions
		 */
		if ( typeof $.fn.popover === 'function' ) {
			body.popover(
				{
					selector: '[data-toggle="popover"]',
					trigger: 'focus'
				}
			);
		}

		if ( $( ".flatpickr" ).length ) {
			$( ".flatpickr" ).flatpickr( {} );
		}
		/**
		 * Initial Setup
		 */
		body.on(
			'click', '.fed_initial_setup_close', function ( e ) {
				var close = $( this );
				close.closest( '.fed_initial_setup_container' ).toggleClass( 'fed_hide' );
				e.preventDefault();
			}
		);

		body.on( 'change', '.fed_widget_taxonomy', function ( e ) {
			var change = $( this );
			var taxonomy = change.val();
			var url = change.data( 'url' );
			var terms = change.closest( '.fed_widget_items' ).find( '.fed_widget_term' );

			fed_toggle_loader();
			$.ajax( {
				type: 'POST',
				url: url,
				data: { taxonomy: taxonomy },
				success: function ( results ) {
					fed_toggle_loader();
					var output = [];
					if ( results.success && results.data.message ) {
						$.each( results.data.message, function ( key, value ) {
							output.push( '<option value="' + key + '">' + value + '</option>' );
						} );
					}
					terms.html( output.join( '' ) );
				}
			} );
			e.preventDefault();
		} );

		body.on( 'change', '.fed_widget_post_type', function ( e ) {
			var change = $( this );
			var post_type = change.val();
			var url = change.data( 'url' );
			var taxonomy = change.closest( '.fed_widget_items' ).find( '.fed_widget_taxonomy' );
			var terms = change.closest( '.fed_widget_items' ).find( '.fed_widget_term' );
			taxonomy.html( '' );
			terms.html( '<option value="">Please Select</option>' );
			fed_toggle_loader();
			$.ajax( {
				type: 'POST',
				url: url,
				data: { post_type: post_type },
				success: function ( results ) {
					console.log( results );
					// fed_toggle_loader();
					var output = [];
					if ( results.success && results.data.message ) {
						$.each( results.data.message, function ( key, value ) {
							output.push( '<option value="' + key + '">' + value + '</option>' );
						} );
					}
					taxonomy.html( output.join( '' ) );
				}
			} );

			e.preventDefault();
		} );


		$(
			function () {
				var hash = window.location.hash;
				if ( typeof $.fn.tab === 'function' ) {
					hash && $( 'ul.nav a[href="' + hash + '"]' ).tab( 'show' );

					$( '.nav-tabs a' ).click(
						function ( e ) {
							$( this ).tab( 'show' );
							var scrollmem = $( 'body' ).scrollTop() || $( 'html' ).scrollTop();
							window.location.hash = this.hash;
							$( 'html,body' ).scrollTop( scrollmem );
						}
					);
				}
			}
		);

		function fed_toggle_loader() {
			$( '.preview-area' ).toggleClass( 'hide' );
		}

		$( '#fed_sticky_subscribe' ).on(
			'show.bs.modal', function ( event ) {
				var button = $( event.relatedTarget );
				var email = button.data( 'email' );
				var modal = $( this );
				modal.find( '#fed_subscribe_email' ).val( email );
			}
		);

		$( '.fed_sticky_close' ).on(
			'click', function ( e ) {
				$( this ).closest( '.fed_sticky_help_bar' ).find( '.fed_sticky_items' ).addClass( 'hide' );
				$( this ).closest( '.fed_sticky_close_open' ).find( '.fed_sticky_open' ).removeClass( 'hide' );
				$( this ).addClass( 'hide' );
				e.preventDefault();
			}
		);
		$( '.fed_sticky_open' ).on(
			'click', function ( e ) {
				$( this ).closest( '.fed_sticky_help_bar' ).find( '.fed_sticky_items' ).removeClass( 'hide' );
				$( this ).closest( '.fed_sticky_close_open' ).find( '.fed_sticky_close' ).removeClass( 'hide' );
				$( this ).addClass( 'hide' );
				e.preventDefault();
			}
		);

		// Sorting Menu.
		var options = {
			placeholderCss: { 'background-color': '#ff8' },
			hintCss: { 'background-color': '#bbf' },
			onChange: function ( cEl ) {
				fed_toggle_loader();
				$.ajax(
					{
						type: 'POST',
						url: cEl.closest( '#fed_dashboard_menu_sort' ).data( 'url' ),
						data: {
							'fed_nonce': cEl.closest( '#fed_dashboard_menu_sort' ).data( 'nonce' ),
							'data': $( '#fed_dashboard_menu_sort' ).sortableListsToArray()
						},
						success: function ( results ) {
							fed_toggle_loader();
							if ( ( results.success ) === false ) {
								swal(
									{
										title: results.data.message || frontend_dashboard.alert.something_went_wrong,
										type: "error",
										confirmButtonColor: "#DD6B55"
									}
								).then(
									function () {
										if ( results.data.reload ) {
											if ( window.location == results.data.reload ) {
												location.reload();
											} else {
												window.location = results.data.reload
											}
										}
									}
								);
							}
						}
					}
				);
			}, onDragStart: function ( cEl ) {
				console.log( cEl );
			},
			complete: function ( cEl ) {
			},
			isAllowed: function ( cEl, hint, target ) {
				if ( target.parents( 'li' ).length == 1 || cEl.find( 'li' ).length > 0 ) {
					hint.css( 'background-color', '#ff9999' );
					swal(
						{
							title: 'Sorry! you can have only one Sub Menu Level',
							type: "warning",
							confirmButtonColor: '#0AAAAA',
						}
					);
					return false;
				}

				if ( target.hasClass( 'invalid_menu' ) || cEl.hasClass( 'invalid_menu' ) ) {
					hint.css( 'background-color', '#ff9999' );
					swal(
						{
							title: 'Sorry You cant change or insert the Invalid Menu Type',
							type: "warning",
							confirmButtonColor: '#0AAAAA',
						}
					);
					return false;
				}

				hint.css( 'background-color', '#99ff99' );
				return true;
			},
			opener: {
				active: true,
				as: 'html',
				close: '<i class="fa fa-minus c3"></i>',
				open: '<i class="fa fa-plus"></i>',
				openerCss: {
					'display': 'inline-block',
					'float': 'left',
					'margin-left': '-35px',
					'margin-right': '5px',
					'font-size': '1.1em'
				}
			},
			ignoreClass: 'clickable'
		};
		if ( $( '#fed_dashboard_menu_sort' ).length ) {
			$( '#fed_dashboard_menu_sort' ).sortableLists( options );
		}
		if ( $( '.fed_datatable' ).length ) {
			$( '.fed_datatable' ).dataTable( { "autoWidth": false, "order": [] } );
		}

	}
);

var fedAdminAlert = {
	showToast: function ( message, isError ) {
		var $toast = jQuery( '#fed_toast_notification' );
		if ( ! $toast.length ) {
			jQuery( 'body' ).append(
				'<div id="fed_toast_notification" class="fixed bottom-6 right-6 transform translate-y-16 opacity-0 transition-all duration-300 pointer-events-none flex items-center gap-3 bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl border border-slate-700" style="z-index: 99999999 !important;">' +
					'<span id="fed_toast_icon" class="text-emerald-400 text-base"><i class="fas fa-check-circle"></i></span>' +
					'<span id="fed_toast_message" class="text-xs font-semibold tracking-wide"></span>' +
				'</div>'
			);
			$toast = jQuery( '#fed_toast_notification' );
		}
		var $msg = $toast.find( '#fed_toast_message' );
		var $icon = $toast.find( '#fed_toast_icon' );

		$msg.text( message || ( isError ? 'An error occurred.' : 'Settings saved successfully.' ) );
		if ( isError ) {
			$icon.html( '<i class="fas fa-exclamation-circle"></i>' ).removeClass( 'text-emerald-400' ).addClass( 'text-rose-400' );
			$toast.addClass( 'border-rose-500/50' );
		} else {
			$icon.html( '<i class="fas fa-check-circle"></i>' ).removeClass( 'text-rose-400' ).addClass( 'text-emerald-400' );
			$toast.removeClass( 'border-rose-500/50' );
		}

		$toast.removeClass( 'translate-y-16 opacity-0 pointer-events-none' ).addClass( 'translate-y-0 opacity-100' );
		
		if ( window.fedToastTimer ) {
			clearTimeout( window.fedToastTimer );
		}
		window.fedToastTimer = setTimeout( function () {
			$toast.removeClass( 'translate-y-0 opacity-100' ).addClass( 'translate-y-16 opacity-0 pointer-events-none' );
		}, 3500 );
	},

	adminSettings: function ( results ) {
		var isSuccess = results && results.success;
		var msg = '';
		if ( results && results.data && results.data.message ) {
			msg = results.data.message;
		} else if ( typeof frontend_dashboard !== 'undefined' && frontend_dashboard.alert ) {
			msg = isSuccess ? 'Settings saved successfully.' : ( results === false ? frontend_dashboard.alert.invalid_form_submission : frontend_dashboard.alert.something_went_wrong );
		} else {
			msg = isSuccess ? 'Settings saved successfully.' : 'An error occurred.';
		}

		fedAdminAlert.showToast( msg, ! isSuccess );

		if ( results && results.data && results.data.reload ) {
			setTimeout( function () {
				if ( window.location == results.data.reload ) {
					window.location.reload();
				} else {
					window.location = results.data.reload;
				}
			}, 800 );
		}
	},

	adminAlertSettings: function ( results ) {
		var isSuccess = results && results.success;
		var error = '';
		if ( results && results.data && results.data.message ) {
			if ( results.data.message instanceof Array ) {
				error = results.data.message.join( ', ' );
			} else {
				error = results.data.message;
			}
		} else if ( typeof frontend_dashboard !== 'undefined' && frontend_dashboard.alert ) {
			error = isSuccess ? 'Settings saved successfully.' : frontend_dashboard.alert.invalid_form_submission;
		} else {
			error = isSuccess ? 'Settings saved successfully.' : 'An error occurred.';
		}

		fedAdminAlert.showToast( error, ! isSuccess );
	}
};

jQuery.fed_toggle_loader = function ($) {
	jQuery( '.preview-area' ).toggleClass( 'hide' );
	if ( jQuery( '.fed_loader_message' ).length ) {
		window.setTimeout(
			function () {
				jQuery( '.fed_loader_message' ).toggleClass( 'hide' );
			}, 2000
		);
	}
};

( function ( $ ) {
	$.fn.hasClassRegEx = function ( regex ) {
		var classes = $( this ).attr( 'class' );

		if ( ! classes || ! regex ) {
			return false;
		}

		classes = classes.split( ' ' );
		var len = classes.length;

		for ( var i = 0; i < len; i++ ) {
			if ( classes[ i ].toLowerCase().match( regex ) ) {
				return true;
			}
		}

		return false;
	};
} )( jQuery );

/**
 * Interactive Choices & Options Repeater Builder
 */
( function ( $ ) {
	'use strict';

	function slugifyOption( text ) {
		return text.toString().toLowerCase().trim()
			.replace( /\s+/g, '_' )
			.replace( /[^\w\-]+/g, '' )
			.replace( /\-\-+/g, '_' )
			.replace( /^_+/, '' )
			.replace( /_+$/, '' );
	}

	function escapeHtml( str ) {
		return $( '<div>' ).text( str || '' ).html();
	}

	function updateBuilderState( $builder ) {
		if ( ! $builder || ! $builder.length ) return;
		var type = $builder.data( 'type' ) || 'select';
		var isMulti = $builder.find( '.fed-multi-select-toggle' ).is( ':checked' );
		var choices = [];

		$builder.find( '.fed-choice-row' ).each( function ( index ) {
			var $row = $( this );
			$row.find( '.fed-row-num' ).text( index + 1 );
			var label = $.trim( $row.find( '.fed-choice-label' ).val() );
			var key   = $.trim( $row.find( '.fed-choice-key' ).val() );

			if ( ! key && label ) {
				key = slugifyOption( label );
			}
			if ( label || key ) {
				choices.push( {
					key: key || label,
					label: label || key
				} );
			}
		} );

		// Sync hidden JSON into the form
		$builder.find( '.fed-choices-raw-sync' ).val( JSON.stringify( choices ) );

		// Update count badge
		$builder.find( '.fed-choices-count-badge' ).text( choices.length + ' Choices' );

		// Update Live Preview Area
		var $preview = $builder.find( '.fed-preview-render-area' );
		if ( type === 'select' ) {
			if ( isMulti ) {
				var selectHtml = '<div class="w-full space-y-1.5">' +
					'<select class="w-full rounded-xl border border-slate-200 bg-white text-xs text-slate-800 p-2 outline-none cursor-pointer fed-live-multi-select" multiple="multiple" style="width:100% !important;">';
				if ( choices.length === 0 ) {
					selectHtml += '<option disabled class="text-slate-400 italic">No choices configured yet</option>';
				} else {
					$.each( choices, function ( i, item ) {
						var isSelected = ( i === 0 || i === 1 ) ? 'selected="selected"' : '';
						selectHtml += '<option value="' + escapeHtml( item.key ) + '" ' + isSelected + '>' + escapeHtml( item.label ) + '</option>';
					} );
				}
				selectHtml += '</select>' +
					'<span class="text-[10px] text-slate-400 block italic">Interactive Select2 preview — click to pick choices & search</span>' +
				'</div>';
				$preview.html( selectHtml );

				if ( typeof $.fn.select2 !== 'undefined' ) {
					try {
						$preview.find( '.fed-live-multi-select' ).select2( {
							width: '100%',
							placeholder: 'Click to select options...',
							dropdownCssClass: 'bc_fed_select2_dropdown',
							containerCssClass: 'bc_fed_select2'
						} );
					} catch ( err ) {
						// fallback to native multi-select
					}
				}
			} else {
				var selectHtml = '<select class="w-full rounded-xl border border-slate-200 bg-white text-xs text-slate-800 p-2.5 outline-none cursor-pointer hover:border-indigo-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 transition-all fed-live-preview-select">';
				selectHtml += '<option value="">-- ' + ( typeof frontend_dashboard !== 'undefined' && frontend_dashboard.select_option ? frontend_dashboard.select_option : 'Select Option' ) + ' --</option>';
				if ( choices.length === 0 ) {
					selectHtml += '<option disabled class="text-slate-400 italic">No choices configured yet</option>';
				} else {
					$.each( choices, function ( i, item ) {
						selectHtml += '<option value="' + escapeHtml( item.key ) + '">' + escapeHtml( item.label ) + '</option>';
					} );
				}
				selectHtml += '</select>';
				$preview.html( selectHtml );
			}
		} else {
			var radioHtml = '<div class="flex flex-wrap gap-2.5 text-xs text-slate-700">';
			if ( choices.length === 0 ) {
				radioHtml += '<span class="text-slate-400 italic text-xs">No choices configured yet</span>';
			} else {
				$.each( choices, function ( i, item ) {
					radioHtml += '<label class="inline-flex items-center gap-1.5 p-1.5 px-3 bg-slate-50 hover:bg-slate-100/80 border border-slate-200 rounded-xl cursor-pointer transition-all">';
					radioHtml += '<input type="radio" name="preview_radio_' + $builder.attr( 'id' ) + '" class="text-indigo-600 cursor-pointer" ' + ( i === 0 ? 'checked' : '' ) + ' />';
					radioHtml += '<span class="font-medium text-slate-700">' + escapeHtml( item.label ) + '</span></label>';
				} );
			}
			radioHtml += '</div>';
			$preview.html( radioHtml );
		}
	}

	window.fedInitChoicesBuilders = function () {
		$( '.fed-choices-builder' ).each( function () {
			updateBuilderState( $( this ) );
		} );
	};

	$( document ).ready( function () {
		fedInitChoicesBuilders();
	} );

	$( document ).ajaxComplete( function () {
		fedInitChoicesBuilders();
	} );

	// Auto-slugify on Label Input
	$( document ).on( 'input', '.fed-choice-label', function () {
		var $row = $( this ).closest( '.fed-choice-row' );
		var $keyInput = $row.find( '.fed-choice-key' );
		var isManual = $keyInput.data( 'manual-edit' );
		if ( ! isManual ) {
			var val = $( this ).val();
			$keyInput.val( slugifyOption( val ) );
		}
		updateBuilderState( $( this ).closest( '.fed-choices-builder' ) );
	} );

	// Mark key as manually edited if user types into Key input
	$( document ).on( 'input', '.fed-choice-key', function () {
		$( this ).data( 'manual-edit', true );
		updateBuilderState( $( this ).closest( '.fed-choices-builder' ) );
	} );

	// Add Option button
	$( document ).on( 'click', '.fed-btn-add-choice', function ( e ) {
		e.preventDefault();
		var $builder = $( this ).closest( '.fed-choices-builder' );
		var $list = $builder.find( '.fed-choices-list' );
		var rowCount = $list.find( '.fed-choice-row' ).length + 1;

		var rowHtml = '<div class="fed-choice-row group flex items-center gap-2 p-2 bg-slate-50/60 hover:bg-slate-50 border border-slate-200/80 rounded-2xl transition-all">' +
			'<div class="fed-row-num w-6 h-6 rounded-lg bg-white border border-slate-200/90 text-[10px] font-bold text-slate-500 flex items-center justify-center shrink-0 shadow-2xs">' + rowCount + '</div>' +
			'<div class="grid grid-cols-1 sm:grid-cols-2 gap-2 flex-1">' +
				'<div><input type="text" class="fed-choice-label w-full rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 outline-none transition-all" placeholder="e.g. Option Label" value="" /></div>' +
				'<div><input type="text" class="fed-choice-key w-full rounded-xl border border-slate-200 bg-white/80 px-3 py-1.5 text-xs font-mono text-slate-600 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/10 outline-none transition-all" placeholder="e.g. option_key" value="" /></div>' +
			'</div>' +
			'<div class="flex items-center gap-1 shrink-0">' +
				'<button type="button" class="fed-choice-duplicate-btn p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all cursor-pointer" title="Duplicate"><i class="fas fa-copy text-xs"></i></button>' +
				'<button type="button" class="fed-choice-delete-btn p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-all cursor-pointer" title="Delete Option"><i class="fas fa-trash-alt text-xs"></i></button>' +
			'</div>' +
		'</div>';

		var $newRow = $( rowHtml ).appendTo( $list );
		$newRow.find( '.fed-choice-label' ).focus();
		$list.scrollTop( $list[0].scrollHeight );
		updateBuilderState( $builder );
	} );

	// Duplicate Option button
	$( document ).on( 'click', '.fed-choice-duplicate-btn', function ( e ) {
		e.preventDefault();
		var $row = $( this ).closest( '.fed-choice-row' );
		var $builder = $( this ).closest( '.fed-choices-builder' );
		var $clone = $row.clone();
		var currentLabel = $row.find( '.fed-choice-label' ).val();
		var currentKey = $row.find( '.fed-choice-key' ).val();
		$clone.find( '.fed-choice-label' ).val( currentLabel ? currentLabel + ' (Copy)' : '' );
		$clone.find( '.fed-choice-key' ).val( currentKey ? currentKey + '_copy' : '' );
		$clone.find( '.fed-choice-key' ).data( 'manual-edit', true );
		$row.after( $clone );
		updateBuilderState( $builder );
	} );

	// Delete Option button
	$( document ).on( 'click', '.fed-choice-delete-btn', function ( e ) {
		e.preventDefault();
		var $builder = $( this ).closest( '.fed-choices-builder' );
		var $list = $builder.find( '.fed-choices-list' );
		if ( $list.find( '.fed-choice-row' ).length <= 1 ) {
			$list.find( '.fed-choice-label' ).val( '' );
			$list.find( '.fed-choice-key' ).val( '' );
		} else {
			$( this ).closest( '.fed-choice-row' ).remove();
		}
		updateBuilderState( $builder );
	} );

	// Clear All button
	$( document ).on( 'click', '.fed-btn-clear-all', function ( e ) {
		e.preventDefault();
		var $builder = $( this ).closest( '.fed-choices-builder' );
		$builder.find( '.fed-choices-list' ).empty();
		$builder.find( '.fed-btn-add-choice' ).trigger( 'click' );
	} );

	// Bulk Drawer Toggle
	$( document ).on( 'click', '.fed-btn-bulk-toggle', function ( e ) {
		e.preventDefault();
		var $builder = $( this ).closest( '.fed-choices-builder' );
		$builder.find( '.fed-bulk-drawer' ).toggleClass( 'hidden' );
		$builder.find( '.fed-bulk-textarea' ).focus();
	} );

	$( document ).on( 'click', '.fed-btn-bulk-cancel', function ( e ) {
		e.preventDefault();
		$( this ).closest( '.fed-bulk-drawer' ).addClass( 'hidden' );
	} );

	// Bulk Append / Replace
	function processBulkChoices( $builder, replace ) {
		var $drawer = $builder.find( '.fed-bulk-drawer' );
		var text = $.trim( $drawer.find( '.fed-bulk-textarea' ).val() );
		if ( ! text ) return;

		var $list = $builder.find( '.fed-choices-list' );
		if ( replace ) {
			$list.empty();
		}

		var lines = text.split( /\r\n|\r|\n/ );
		$.each( lines, function ( i, line ) {
			line = $.trim( line );
			if ( ! line ) return;
			var key = '', label = '';
			if ( line.indexOf( '=>' ) !== -1 ) {
				var p = line.split( '=>' );
				key = $.trim( p[0] ); label = $.trim( p[1] );
			} else if ( line.indexOf( '|' ) !== -1 ) {
				var p = line.split( '|' );
				key = $.trim( p[0] ); label = $.trim( p[1] );
			} else if ( line.indexOf( ',' ) !== -1 ) {
				var p = line.split( ',' );
				key = $.trim( p[0] ); label = $.trim( p[1] );
			} else if ( line.indexOf( ':' ) !== -1 ) {
				var p = line.split( ':' );
				key = $.trim( p[0] ); label = $.trim( p[1] );
			} else {
				label = line;
				key = slugifyOption( line );
			}
			if ( ! key ) key = slugifyOption( label );
			if ( ! label ) label = key;

			var rowHtml = '<div class="fed-choice-row group flex items-center gap-2 p-2 bg-slate-50/60 hover:bg-slate-50 border border-slate-200/80 rounded-2xl transition-all">' +
				'<div class="fed-row-num w-6 h-6 rounded-lg bg-white border border-slate-200/90 text-[10px] font-bold text-slate-500 flex items-center justify-center shrink-0 shadow-2xs">0</div>' +
				'<div class="grid grid-cols-1 sm:grid-cols-2 gap-2 flex-1">' +
					'<div><input type="text" class="fed-choice-label w-full rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 outline-none transition-all" placeholder="e.g. Option Label" value="' + escapeHtml( label ) + '" /></div>' +
					'<div><input type="text" class="fed-choice-key w-full rounded-xl border border-slate-200 bg-white/80 px-3 py-1.5 text-xs font-mono text-slate-600 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/10 outline-none transition-all" placeholder="e.g. option_key" value="' + escapeHtml( key ) + '" /></div>' +
				'</div>' +
				'<div class="flex items-center gap-1 shrink-0">' +
					'<button type="button" class="fed-choice-duplicate-btn p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all cursor-pointer" title="Duplicate"><i class="fas fa-copy text-xs"></i></button>' +
					'<button type="button" class="fed-choice-delete-btn p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-all cursor-pointer" title="Delete Option"><i class="fas fa-trash-alt text-xs"></i></button>' +
				'</div>' +
			'</div>';
			$list.append( rowHtml );
		} );

		$drawer.find( '.fed-bulk-textarea' ).val( '' );
		$drawer.addClass( 'hidden' );
		updateBuilderState( $builder );
	}

	$( document ).on( 'click', '.fed-btn-bulk-append', function ( e ) {
		e.preventDefault();
		processBulkChoices( $( this ).closest( '.fed-choices-builder' ), false );
	} );

	$( document ).on( 'click', '.fed-btn-bulk-replace', function ( e ) {
		e.preventDefault();
		processBulkChoices( $( this ).closest( '.fed-choices-builder' ), true );
	} );

	// Multi-select toggle live preview sync
	$( document ).on( 'change', '.fed-multi-select-toggle', function () {
		updateBuilderState( $( this ).closest( '.fed-choices-builder' ) );
	} );

} )( jQuery );

