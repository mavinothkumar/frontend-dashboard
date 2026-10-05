<?php

namespace FED\Services\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class FileField
 *
 * Media file upload and attachment picker element.
 */
class FileField extends BaseField {

	protected $type = 'file';

	public function render() {
		$name      = $this->name;
		$value     = (int) $this->value;
		$img_url   = $value ? wp_get_attachment_image_url( $value, 'medium' ) : '';
		$file_name = $value ? get_the_title( $value ) : '';
		if ( empty( $file_name ) && $value ) {
			$file_name = basename( (string) get_attached_file( $value ) );
		}

		$has_file  = ! empty( $img_url ) || ( $value > 0 );
		$unique_id = 'fed_upload_' . wp_generate_password( 6, false );

		ob_start();
		?>
		<div class="fed-media-uploader-box w-full" id="<?php echo esc_attr( $unique_id ); ?>">
			<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ? $value : '' ); ?>" class="fed-media-id-input" />
			
			<!-- Empty State / Dropzone trigger -->
			<div class="fed-media-dropzone cursor-pointer rounded-2xl border-2 border-dashed border-slate-200/90 bg-slate-50/60 p-5 hover:border-indigo-400 hover:bg-indigo-50/20 transition-all flex items-center justify-center text-center gap-3 <?php echo $has_file ? 'hidden' : ''; ?>">
				<div class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-indigo-600 flex items-center justify-center text-lg shadow-2xs">
					<i class="fas fa-cloud-upload-alt"></i>
				</div>
				<div class="text-left">
					<p class="text-xs font-bold text-slate-800 m-0"><?php esc_html_e( 'Choose Image or File', 'frontend-dashboard' ); ?></p>
					<p class="text-[11px] text-slate-400 m-0"><?php esc_html_e( 'Click to browse WordPress Media Library', 'frontend-dashboard' ); ?></p>
				</div>
			</div>

			<!-- Preview State -->
			<div class="fed-media-preview-card rounded-2xl border border-slate-200/90 bg-white p-3 shadow-2xs flex items-center justify-between gap-3 <?php echo ! $has_file ? 'hidden' : ''; ?>">
				<div class="flex items-center gap-3 overflow-hidden">
					<?php if ( $img_url ) : ?>
						<img src="<?php echo esc_url( $img_url ); ?>" alt="" class="w-14 h-14 object-cover rounded-xl border border-slate-200 bg-slate-50 shrink-0 fed-preview-img" />
					<?php else : ?>
						<div class="w-14 h-14 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shrink-0 fed-preview-fallback border border-indigo-100">
							<i class="fas fa-image"></i>
						</div>
					<?php endif; ?>
					<div class="overflow-hidden min-w-0">
						<span class="text-xs font-bold text-slate-800 block truncate fed-preview-title"><?php echo esc_html( $file_name ?: __( 'Selected Image', 'frontend-dashboard' ) ); ?></span>
						<span class="text-[10px] text-indigo-600 font-medium block flex items-center gap-1 mt-0.5">
							<i class="fas fa-check-circle text-[9px]"></i>
							<?php esc_html_e( 'Media Attached', 'frontend-dashboard' ); ?>
						</span>
					</div>
				</div>
				<div class="flex items-center gap-2 shrink-0">
					<button type="button" class="fed-change-media-btn px-2.5 py-1 text-xs font-semibold rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">
						<?php esc_html_e( 'Change', 'frontend-dashboard' ); ?>
					</button>
					<button type="button" class="fed-remove-media-btn p-1.5 text-xs text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="<?php esc_attr_e( 'Remove file', 'frontend-dashboard' ); ?>">
						<i class="fas fa-trash-alt"></i>
					</button>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}