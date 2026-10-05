<?php

namespace FED\Services\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class TableField
 *
 * Dynamic multi-column tabular data grid field (supports user input and read-only modes).
 */
class TableField extends BaseField {

	protected $type = 'table';

	public function render() {
		$extended       = $this->extended;
		$table_mode     = isset( $extended['table_mode'] ) && 'readonly' === $extended['table_mode'] ? 'readonly' : 'editable';
		$table_template = isset( $extended['table_template'] ) && ! empty( $extended['table_template'] ) ? $extended['table_template'] : 'bordered';

		$value = $this->value;
		if ( ! empty( $value ) && is_string( $value ) ) {
			$value = maybe_unserialize( $value );
		}
		if ( ! is_array( $value ) ) {
			$value = array();
		}

		$schema_raw = is_string( $this->options ) ? $this->options : '';
		$parts      = explode( '|', (string) $schema_raw );

		$table_header        = isset( $parts[0] ) && '' !== trim( $parts[0] ) ? explode( ',', trim( $parts[0] ) ) : array( __( 'Column 1', 'frontend-dashboard' ), __( 'Column 2', 'frontend-dashboard' ) );
		$table_rows          = isset( $parts[1] ) ? max( 1, (int) $parts[1] ) : 2;
		$default_cell_values = isset( $parts[2] ) ? json_decode( $parts[2], true ) : array();
		if ( ! is_array( $default_cell_values ) ) {
			$default_cell_values = array();
		}

		$wrapper_classes = 'fed_table_wrapper w-full block overflow-hidden rounded-2xl bg-white shadow-2xs ' . esc_attr( $this->class_name );
		$th_classes      = 'px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider select-none whitespace-nowrap';
		$tr_classes      = 'transition-colors';
		$td_classes      = 'p-2.5 sm:p-3 align-middle';

		switch ( $table_template ) {
			case 'borderless':
				$wrapper_classes = 'fed_table_wrapper w-full block overflow-hidden rounded-2xl bg-white shadow-none ' . esc_attr( $this->class_name );
				$th_classes     .= ' text-slate-500 bg-transparent border-b-2 border-slate-200';
				$tr_classes     .= ' hover:bg-slate-50/50 border-b border-slate-100 last:border-0';
				$td_classes      = 'px-4 py-3 align-middle';
				break;

			case 'striped':
				$wrapper_classes .= ' border border-slate-200/90';
				$th_classes      .= ' text-white bg-slate-800 border-b border-slate-700 border-r border-slate-700/60 last:border-r-0';
				$tr_classes      .= ' hover:bg-indigo-50/30 even:bg-slate-50/70 odd:bg-white border-b border-slate-100 last:border-0';
				$td_classes      .= ' border-r border-slate-100 last:border-r-0';
				break;

			case 'compact':
				$wrapper_classes = 'fed_table_wrapper w-full block overflow-hidden rounded-xl border border-slate-200 bg-white shadow-2xs ' . esc_attr( $this->class_name );
				$th_classes      = 'px-3 py-2 text-left text-[11px] font-bold text-slate-600 uppercase tracking-wider select-none whitespace-nowrap bg-slate-100/90 border-b border-slate-200 border-r border-slate-200/60 last:border-r-0';
				$tr_classes     .= ' hover:bg-slate-50/80 border-b border-slate-100 last:border-0';
				$td_classes      = 'p-2 border-r border-slate-100 last:border-r-0 align-middle';
				break;

			case 'bordered':
			default:
				$wrapper_classes .= ' border border-slate-200/90';
				$th_classes      .= ' text-slate-700 bg-slate-50/90 border-b border-slate-200/90 border-r border-slate-200/60 last:border-r-0';
				$tr_classes      .= ' hover:bg-indigo-50/20 even:bg-slate-50/40 border-b border-slate-100 last:border-0';
				$td_classes      .= ' border-r border-slate-100 last:border-r-0';
				break;
		}

		$th = '';
		foreach ( $table_header as $idx => $header ) {
			$header_text = trim( $header );
			$th         .= '<th class="' . esc_attr( $th_classes ) . '">';
			$th         .= '<div class="flex items-center gap-2">';
			if ( 'striped' !== $table_template ) {
				$th .= '<span class="w-1.5 h-1.5 rounded-full bg-indigo-500 shrink-0"></span>';
			}
			$th .= '<span class="truncate">' . esc_html( $header_text ) . '</span>';
			$th .= '</div>';
			$th .= '</th>';
		}

		$td = '';
		for ( $row = 0; $row < $table_rows; $row++ ) {
			$td .= '<tr class="' . esc_attr( $tr_classes ) . '">';
			foreach ( $table_header as $key => $header ) {
				$header_text  = trim( $header );
				$user_val_key = 'row_' . $row . '_' . $key;

				if ( 'readonly' === $table_mode ) {
					$cell_value = isset( $default_cell_values[ $user_val_key ] ) ? $default_cell_values[ $user_val_key ] : '';
					if ( '' === $cell_value && isset( $value[ $user_val_key ] ) ) {
						$cell_value = $value[ $user_val_key ];
					}

					$text_size = ( 'compact' === $table_template ) ? 'text-[11px]' : 'text-xs';
					$td       .= '<td class="' . esc_attr( $td_classes ) . '">';
					$td       .= '<div class="px-2 py-1 ' . esc_attr( $text_size ) . ' font-medium text-slate-800 min-h-[30px] flex items-center">';
					if ( '' !== $cell_value ) {
						$td .= esc_html( $cell_value );
					} else {
						$td .= '<span class="text-slate-300 font-normal italic">—</span>';
					}
					$td .= '</div>';
					$td .= '</td>';
				} else {
					if ( isset( $value[ $user_val_key ] ) && '' !== $value[ $user_val_key ] ) {
						$user_value = $value[ $user_val_key ];
					} else {
						$user_value = isset( $default_cell_values[ $user_val_key ] ) ? $default_cell_values[ $user_val_key ] : '';
					}
					$_name = ! empty( $this->name ) ? $this->name . '[' . $user_val_key . ']' : '';

					$input_padding = ( 'compact' === $table_template ) ? 'px-2.5 py-1.5 text-[11px]' : 'px-3.5 py-2.5 text-xs';
					$td           .= '<td class="' . esc_attr( $td_classes ) . '">';
					$td           .= '<input type="text" ' . ( $this->is_readonly ? 'readonly="readonly"' : '' ) . ' ' . ( $this->is_disabled ? 'disabled="disabled"' : '' ) . ' name="' . esc_attr( $_name ) . '" value="' . esc_attr( $user_value ) . '" placeholder="' . esc_attr( $header_text ) . '" class="w-full font-medium text-slate-800 bg-white hover:bg-slate-50/80 focus:bg-white border border-slate-200/90 focus:border-indigo-500 rounded-xl ' . esc_attr( $input_padding ) . ' outline-none focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder:text-slate-300" ' . ( $this->is_required ? 'required="required"' : '' ) . ' />';
					$td           .= '</td>';
				}
			}
			$td .= '</tr>';
		}

		ob_start();
		?>
		<div class="<?php echo esc_attr( $wrapper_classes ); ?>" 
		<?php
		if ( ! empty( $this->id_name ) ) :
			?>
			id="<?php echo esc_attr( $this->id_name ); ?>"<?php endif; ?>>
			<div class="overflow-x-auto w-full">
				<table class="w-full text-left text-xs border-collapse m-0">
					<thead>
						<tr>
							<?php echo $th; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</tr>
					</thead>
					<tbody class="divide-y divide-slate-100">
						<?php echo $td; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}