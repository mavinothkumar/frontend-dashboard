<?php
/**
 * Table Inspector Layout.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Table Field Inspector.
 *
 * @param array  $row
 * @param string $action
 * @param array  $menu_options
 */
function fed_admin_input_fields_table( $row, $action, $menu_options ) {
	$is_active    = ( isset( $row['input_type'] ) && 'table' === $row['input_type'] );
	$table_val    = isset( $row['input_value'] ) ? $row['input_value'] : 'Column 1,Column 2,Column 3|2';
	$parts        = explode( '|', $table_val );
	$col_string   = isset( $parts[0] ) ? trim( $parts[0] ) : 'Column 1,Column 2,Column 3';
	$default_rows = isset( $parts[1] ) ? max( 1, (int) $parts[1] ) : 2;
	$columns      = array_values( array_filter( array_map( 'trim', explode( ',', $col_string ) ) ) );
	if ( empty( $columns ) ) {
		$columns = array( 'Column 1', 'Column 2', 'Column 3' );
	}

	$extended = isset( $row['extended'] ) ? ( is_string( $row['extended'] ) ? maybe_unserialize( $row['extended'] ) : $row['extended'] ) : array();
	if ( ! is_array( $extended ) ) {
		$extended = array();
	}
	$table_mode     = isset( $extended['table_mode'] ) && 'readonly' === $extended['table_mode'] ? 'readonly' : 'editable';
	$table_template = isset( $extended['table_template'] ) && ! empty( $extended['table_template'] ) ? $extended['table_template'] : 'bordered';
	?>
	<div class="fed_input_type_container fed_input_table_container space-y-7 <?php echo $is_active ? '' : 'hide hidden'; ?>" data-field-type="table">
		<form method="post"
				class="fed_admin_menu fed_ajax space-y-7"
				action="<?php echo esc_url( admin_url( 'admin-ajax.php?action=fed_admin_setting_up_form' ) ); ?>">

			<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo fed_loader();
			?>

			<!-- Hidden serialised value that the save handler reads -->
			<input type="hidden" name="input_value" id="fed_table_schema_hidden" value="<?php echo esc_attr( $table_val ); ?>" />

			<!-- Card: Basic Field Settings -->
			<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6 sm:space-y-7">
				<div class="flex items-center gap-3.5 pb-5 border-b border-slate-100">
					<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0 shadow-2xs">
						<i class="fas fa-table"></i>
					</div>
					<div>
						<h3 class="text-sm sm:text-base font-bold text-slate-900 m-0"><?php esc_html_e( 'Table Grid Field', 'frontend-dashboard' ); ?></h3>
						<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Dynamic tabular data field with support for user data entry or static read-only presentation.', 'frontend-dashboard' ); ?></p>
					</div>
				</div>

				<div class="space-y-5">
					<?php fed_get_admin_up_label_input_order( $row ); ?>
					<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
						<?php fed_get_admin_up_input_meta( $row ); ?>
						<?php fed_get_placeholder_field( $row ); ?>
					</div>
					<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
						<?php fed_get_class_field( $row ); ?>
						<?php fed_get_id_field( $row ); ?>
					</div>

					<!-- Table Behavior & Template Configurations -->
					<div class="pt-5 border-t border-slate-100 space-y-4">
						<h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-2">
							<i class="fas fa-sliders-h text-indigo-500"></i>
							<?php esc_html_e( 'Table Behavior & Template', 'frontend-dashboard' ); ?>
						</h4>
						<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
							<div class="space-y-1.5">
								<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Table Mode', 'frontend-dashboard' ); ?></label>
								<select name="extended[table_mode]" id="fed_table_mode_select" class="w-full text-xs font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 outline-none focus:border-indigo-500 focus:bg-white transition-all" style="min-height:38px;">
									<option value="editable" <?php selected( $table_mode, 'editable' ); ?>><?php esc_html_e( 'User Input (Editable by User)', 'frontend-dashboard' ); ?></option>
									<option value="readonly" <?php selected( $table_mode, 'readonly' ); ?>><?php esc_html_e( 'Read-Only (Display Table Data)', 'frontend-dashboard' ); ?></option>
								</select>
								<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Read-only mode displays fixed data without inputs. User Input mode saves responses per user.', 'frontend-dashboard' ); ?></p>
							</div>
							<div class="space-y-1.5">
								<label class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Table Template / Style', 'frontend-dashboard' ); ?></label>
								<select name="extended[table_template]" id="fed_table_template_select" class="w-full text-xs font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 outline-none focus:border-indigo-500 focus:bg-white transition-all" style="min-height:38px;">
									<option value="bordered" <?php selected( $table_template, 'bordered' ); ?>><?php esc_html_e( 'Bordered Grid (Standard with Borders)', 'frontend-dashboard' ); ?></option>
									<option value="borderless" <?php selected( $table_template, 'borderless' ); ?>><?php esc_html_e( 'Clean Borderless (Minimal Dividers)', 'frontend-dashboard' ); ?></option>
									<option value="striped" <?php selected( $table_template, 'striped' ); ?>><?php esc_html_e( 'Zebra Striped (Dark Header & Tinted Rows)', 'frontend-dashboard' ); ?></option>
									<option value="compact" <?php selected( $table_template, 'compact' ); ?>><?php esc_html_e( 'Modern Compact (Tight Spacing)', 'frontend-dashboard' ); ?></option>
								</select>
								<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Choose visual layout and styling template for the frontend table.', 'frontend-dashboard' ); ?></p>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Card: Visual Column Builder -->
			<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6">
				<div class="flex items-center gap-3.5 pb-4 border-b border-slate-100">
					<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0 shadow-2xs">
						<i class="fas fa-columns"></i>
					</div>
					<div>
						<h3 class="text-sm sm:text-base font-bold text-slate-900 m-0"><?php esc_html_e( 'Column & Row Builder', 'frontend-dashboard' ); ?></h3>
						<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Add columns with their headers, then set how many default rows appear on the frontend.', 'frontend-dashboard' ); ?></p>
					</div>
				</div>

				<!-- Column list -->
				<div>
					<label class="block text-xs font-bold text-slate-700 mb-3"><?php esc_html_e( 'Column Headers', 'frontend-dashboard' ); ?></label>
					<div id="fed_table_columns_list" class="space-y-2.5 mb-4">
						<?php foreach ( $columns as $idx => $col_name ) : ?>
							<div class="fed-table-col-row flex items-center gap-2.5">
								<span class="flex items-center justify-center w-6 h-6 rounded-lg bg-slate-100 text-slate-400 text-xs font-bold shrink-0 fed-table-col-num"><?php echo (int) $idx + 1; ?></span>
								<input
									type="text"
									class="fed-table-col-input flex-1 text-xs text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 outline-none focus:border-indigo-500 focus:bg-white transition-all"
									style="min-height:38px;"
									placeholder="<?php esc_attr_e( 'Column name', 'frontend-dashboard' ); ?>"
									value="<?php echo esc_attr( $col_name ); ?>"
									data-col-index="<?php echo (int) $idx; ?>"
								/>
								<button type="button" class="fed-table-col-remove w-8 h-8 rounded-xl bg-rose-50 text-rose-500 hover:bg-rose-100 flex items-center justify-center shrink-0 transition-all cursor-pointer" title="<?php esc_attr_e( 'Remove', 'frontend-dashboard' ); ?>">
									<i class="fas fa-times text-xs"></i>
								</button>
							</div>
						<?php endforeach; ?>
					</div>
					<button type="button" id="fed_table_add_column" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 text-xs font-bold transition-all cursor-pointer">
						<i class="fas fa-plus"></i>
						<?php esc_html_e( 'Add Column', 'frontend-dashboard' ); ?>
					</button>
				</div>

				<!-- Default Rows -->
				<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 pt-5 border-t border-slate-100 items-start">
					<div class="space-y-1.5">
						<label for="fed_table_default_rows" class="block text-xs font-bold text-slate-700"><?php esc_html_e( 'Default Rows', 'frontend-dashboard' ); ?></label>
						<input
							type="number"
							id="fed_table_default_rows"
							min="1"
							max="50"
							value="<?php echo (int) $default_rows; ?>"
							class="w-full text-xs text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 outline-none focus:border-indigo-500 focus:bg-white transition-all"
							style="min-height:38px;"
						/>
						<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Number of rows to generate for data entry/display.', 'frontend-dashboard' ); ?></p>
					</div>
					<div class="sm:col-span-2 pt-1">
						<div id="fed_table_mode_help_banner" class="p-3.5 bg-indigo-50/70 rounded-2xl border border-indigo-100 text-xs text-indigo-700 flex items-start gap-2.5">
							<i class="fas fa-info-circle mt-0.5 shrink-0 text-indigo-500"></i>
							<span id="fed_table_mode_help_text"><?php esc_html_e( 'Fill cell data in the live preview below. In Read-Only mode, users see this exact data. In User Input mode, these serve as initial defaults.', 'frontend-dashboard' ); ?></span>
						</div>
					</div>
				</div>

				<!-- Live Preview -->
				<div class="pt-5 border-t border-slate-100">
					<div class="flex items-center justify-between gap-3 mb-3">
						<label class="block text-xs font-bold text-slate-700 flex items-center gap-2 m-0">
							<i class="fas fa-eye text-indigo-500"></i>
							<?php esc_html_e( 'Live Preview & Default Data Entry', 'frontend-dashboard' ); ?>
						</label>
						<span id="fed_table_preview_mode_badge" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[11px] font-bold"></span>
					</div>

					<div id="fed_table_preview_wrapper" class="overflow-x-auto rounded-2xl border border-slate-200 bg-white transition-all">
						<table class="w-full text-xs border-collapse" id="fed_table_preview">
							<thead>
								<tr class="bg-slate-100 border-b border-slate-200">
									<?php foreach ( $columns as $col_name ) : ?>
										<th class="px-4 py-2.5 text-left text-[11px] font-bold text-slate-700 uppercase tracking-wider whitespace-nowrap"><?php echo esc_html( $col_name ); ?></th>
									<?php endforeach; ?>
								</tr>
							</thead>
							<tbody>
								<?php for ( $r = 0; $r < min( $default_rows, 4 ); $r++ ) : ?>
									<tr class="border-b border-slate-100 last:border-0">
										<?php foreach ( $columns as $col_name ) : ?>
											<td class="px-3 py-2">
												<div class="h-7 bg-slate-50 border border-slate-200 rounded-lg"></div>
											</td>
										<?php endforeach; ?>
									</tr>
								<?php endfor; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<?php
			fed_get_admin_up_display_permission( $row, $action );
			fed_get_admin_up_role_based( $row, $action, $menu_options );
			fed_get_input_type_and_submit_btn( 'table', $action, $row );
			?>
		</form>
	</div>

	<script>
	(function($) {
		'use strict';
		var $c = $('.fed_input_table_container');

		function getExistingCellValues() {
			var raw = $c.find('#fed_table_schema_hidden').val();
			var parts = raw ? raw.split('|') : [];
			if (parts.length > 2 && parts[2]) {
				try { return JSON.parse(parts[2]); } catch (e) {}
			}
			return {};
		}

		function fedTblSchema() {
			var cols = [];
			$c.find('.fed-table-col-input').each(function() {
				var v = $.trim($(this).val());
				if (v !== '') cols.push(v);
			});
			var rows = parseInt($c.find('#fed_table_default_rows').val(), 10) || 1;
			var cellValues = getExistingCellValues();
			$c.find('.fed-table-cell-input').each(function() {
				var r = $(this).data('row');
				var col = $(this).data('col');
				var val = $(this).val();
				if (val !== '') {
					cellValues['row_' + r + '_' + col] = val;
				} else {
					delete cellValues['row_' + r + '_' + col];
				}
			});

			var schema = cols.join(',') + '|' + rows;
			if (Object.keys(cellValues).length > 0) {
				schema += '|' + JSON.stringify(cellValues);
			}
			
			$c.find('#fed_table_schema_hidden').val(schema);
			fedTblPreview(cols, rows, cellValues);
			fedTblRenum();
		}

		function fedTblPreview(cols, rows, cellValues) {
			var $t = $c.find('#fed_table_preview');
			var $wrap = $c.find('#fed_table_preview_wrapper');
			var mode = $c.find('#fed_table_mode_select').val() || 'editable';
			var tpl = $c.find('#fed_table_template_select').val() || 'bordered';

			var $badge = $c.find('#fed_table_preview_mode_badge');
			var $helpText = $c.find('#fed_table_mode_help_text');
			if (mode === 'readonly') {
				$badge.removeClass('bg-indigo-50 text-indigo-700 border-indigo-200/80')
						.addClass('bg-amber-50 text-amber-700 border border-amber-200/80')
						.html('<i class="fas fa-lock text-[10px]"></i> <?php esc_html_e( 'Read-Only Mode', 'frontend-dashboard' ); ?>');
				$helpText.text('<?php esc_html_e( 'Read-Only Mode: Users cannot edit this table on the frontend. The data you enter in the preview cells below will be shown as static table text.', 'frontend-dashboard' ); ?>');
			} else {
				$badge.removeClass('bg-amber-50 text-amber-700 border-amber-200/80')
						.addClass('bg-indigo-50 text-indigo-700 border border-indigo-200/80')
						.html('<i class="fas fa-pen text-[10px]"></i> <?php esc_html_e( 'User Input Mode', 'frontend-dashboard' ); ?>');
				$helpText.text('<?php esc_html_e( 'User Input Mode: Users can edit cells on the frontend. Any data entered below will serve as initial default values.', 'frontend-dashboard' ); ?>');
			}

			$wrap.removeClass('border-0 shadow-none border-slate-200');
			if (tpl === 'borderless') {
				$wrap.addClass('border-0 shadow-none');
			} else {
				$wrap.addClass('border border-slate-200');
			}

			var thRowClass = 'bg-slate-100 border-b border-slate-200';
			var thCellClass = 'px-4 py-2.5 text-left text-[11px] font-bold text-slate-700 uppercase tracking-wider whitespace-nowrap';
			
			if (tpl === 'striped') {
				thRowClass = 'bg-slate-800 text-white';
				thCellClass = 'px-4 py-2.5 text-left text-[11px] font-bold text-white uppercase tracking-wider whitespace-nowrap border-r border-slate-700 last:border-r-0';
			} else if (tpl === 'borderless') {
				thRowClass = 'bg-transparent border-b-2 border-slate-200';
				thCellClass = 'px-4 py-2.5 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap';
			} else if (tpl === 'compact') {
				thRowClass = 'bg-slate-100/90 border-b border-slate-200';
				thCellClass = 'px-3 py-2 text-left text-[10px] font-bold text-slate-600 uppercase tracking-wider whitespace-nowrap border-r border-slate-200/60 last:border-r-0';
			} else {
				thRowClass = 'bg-slate-50/90 border-b border-slate-200';
				thCellClass = 'px-4 py-2.5 text-left text-[11px] font-bold text-slate-700 uppercase tracking-wider whitespace-nowrap border-r border-slate-200/60 last:border-r-0';
			}

			var th = '<tr class="' + thRowClass + '">';
			if (cols.length === 0) {
				th += '<th class="px-4 py-2.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider"><?php esc_html_e( 'Add columns above…', 'frontend-dashboard' ); ?></th>';
			} else {
				$.each(cols, function(i, c) {
					th += '<th class="' + thCellClass + '">' + $('<span>').text(c).html() + '</th>';
				});
			}
			th += '</tr>';
			$t.find('thead').html(th);
			
			var tb = '';
			for (var r = 0; r < rows; r++) {
				var trClass = 'border-b border-slate-100 last:border-0 hover:bg-slate-50';
				if (tpl === 'striped') {
					trClass = (r % 2 === 1 ? 'bg-slate-50/70 ' : 'bg-white ') + 'border-b border-slate-100 last:border-0 hover:bg-indigo-50/30';
				}
				tb += '<tr class="' + trClass + '">';
				if (cols.length === 0) {
					tb += '<td class="px-3 py-2"><div class="h-7 bg-slate-50 border border-slate-200 rounded-lg opacity-50"></div></td>';
				} else {
					$.each(cols, function(cIdx, c) {
						var cellKey = 'row_' + r + '_' + cIdx;
						var val = cellValues && cellValues[cellKey] ? cellValues[cellKey] : '';
						var tdPad = (tpl === 'compact') ? 'px-2 py-1.5' : 'px-3 py-2';
						var borderClass = (tpl === 'borderless') ? '' : ' border-r border-slate-100 last:border-r-0';
						tb += '<td class="' + tdPad + borderClass + '"><input type="text" data-row="' + r + '" data-col="' + cIdx + '" class="fed-table-cell-input w-full text-xs text-slate-700 bg-white border border-slate-200 rounded-lg px-2.5 outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 transition-all" style="min-height:30px;" placeholder="' + $('<span>').text(c).html() + '" value="' + $('<span>').text(val).html() + '" /></td>';
					});
				}
				tb += '</tr>';
			}
			$t.find('tbody').html(tb);
		}

		function fedTblRenum() {
			$c.find('.fed-table-col-row').each(function(i) {
				$(this).find('.fed-table-col-num').text(i + 1);
				$(this).find('.fed-table-col-input').attr('data-col-index', i);
			});
		}

		$c.on('click', '#fed_table_add_column', function() {
			var tpl = '<div class="fed-table-col-row flex items-center gap-2.5">' +
				'<span class="flex items-center justify-center w-6 h-6 rounded-lg bg-slate-100 text-slate-400 text-xs font-bold shrink-0 fed-table-col-num">+</span>' +
				'<input type="text" class="fed-table-col-input flex-1 text-xs text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3.5 outline-none focus:border-indigo-500 focus:bg-white transition-all" style="min-height:38px;" placeholder="<?php esc_attr_e( 'Column name', 'frontend-dashboard' ); ?>" value="" />' +
				'<button type="button" class="fed-table-col-remove w-8 h-8 rounded-xl bg-rose-50 text-rose-500 hover:bg-rose-100 flex items-center justify-center shrink-0 transition-all cursor-pointer" title="<?php esc_attr_e( 'Remove', 'frontend-dashboard' ); ?>"><i class="fas fa-times text-xs"></i></button>' +
				'</div>';
			$c.find('#fed_table_columns_list').append(tpl);
			$c.find('#fed_table_columns_list .fed-table-col-row:last .fed-table-col-input').focus();
			fedTblSchema();
		});

		$c.on('click', '.fed-table-col-remove', function() {
			if ($c.find('.fed-table-col-row').length <= 1) {
				$c.find('.fed-table-col-input').first().val('').focus();
			} else {
				$(this).closest('.fed-table-col-row').remove();
			}
			fedTblSchema();
		});

		$c.on('input change', '.fed-table-col-input, #fed_table_default_rows, #fed_table_mode_select, #fed_table_template_select', function() {
			fedTblSchema();
		});
		
		$c.on('input change', '.fed-table-cell-input', function() {
			var raw = $c.find('#fed_table_schema_hidden').val();
			var parts = raw ? raw.split('|') : [];
			var cellValues = {};
			if (parts.length > 2 && parts[2]) {
				try { cellValues = JSON.parse(parts[2]); } catch (e) {}
			}
			
			var r = $(this).data('row');
			var col = $(this).data('col');
			var val = $(this).val();
			if (val !== '') {
				cellValues['row_' + r + '_' + col] = val;
			} else {
				delete cellValues['row_' + r + '_' + col];
			}
			
			var schema = (parts[0] || '') + '|' + (parts[1] || 1);
			if (Object.keys(cellValues).length > 0) {
				schema += '|' + JSON.stringify(cellValues);
			}
			$c.find('#fed_table_schema_hidden').val(schema);
		});

		var initialRaw = $c.find('#fed_table_schema_hidden').val();
		var initialParts = initialRaw ? initialRaw.split('|') : [];
		var initialCellValues = {};
		if (initialParts.length > 2 && initialParts[2]) {
			try { initialCellValues = JSON.parse(initialParts[2]); } catch (e) {}
		}
		var initialCols = [];
		$c.find('.fed-table-col-input').each(function() {
			var v = $.trim($(this).val());
			if (v !== '') initialCols.push(v);
		});
		var initialRows = parseInt($c.find('#fed_table_default_rows').val(), 10) || 1;
		fedTblPreview(initialCols, initialRows, initialCellValues);

	})(jQuery);
	</script>
	<?php
}