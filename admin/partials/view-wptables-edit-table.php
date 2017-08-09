<?php
/**
 * WordPress Tables plugin.
 *
 * @package    WPTables
 * @author     Ian Sadovy <ian.sadovy@gmail.com>
 */
?>
<?php
$post = get_post($_GET['table']);
$meta_fields = get_post_meta($post->ID, 'wpt_fields', true);
$meta_options = get_post_meta($post->ID, 'wpt_options', true);
$fields = json_decode($meta_fields, true);
$options = json_decode($meta_options, true);
$types = array(
    'text' => __('Text', 'wptables'), 
    'number' => __('Number', 'wptables')
);
$shortcode = WPTables::shortcode_table($post->ID);

$themes = array(
    ''  => __('Light', 'wptables'),
    'jsgrid-theme-dark'   => __('Dark', 'wptables'),
    'jsgrid-theme-bluegray'   => __('Blue Grey', 'wptables'),
    'jsgrid-theme-navy'   => __('Navy', 'wptables'),
    'jsgrid-theme-green'  => __('Green', 'wptables'),
    'jsgrid-theme-purple'   => __('Purple', 'wptables'),
);
?>
    <div class="wrap">
        <h1 class="wp-heading-inline"><?= __('Edit Table', 'wptables') ?></h1>
        <a href="<?php echo WPTables::url(array('page' => 'wptables-add-new')); ?>" class="page-title-action"><?= __('Add New', 'wptables') ?></a>
        <form id="wpt-table" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <input type="hidden" name="action" value="wpt_update_table">
            <input type="hidden" name="table" value="<?php echo $post->ID; ?>">
            <?php wp_nonce_field( 'wpt-update-table-'.$post->ID); ?>
            <?php if (isset($_GET['error_msg'])) : ?>
            <div class="error">
                <p><?= $_GET['error_msg'] ?></p>
            </div>
            <?php endif; ?>
            <div id="titlediv">
                <div id="titlewrap">
                    <input type="text" name="title" size="30" value="<?php echo $post->post_title; ?>" id="title" spellcheck="true" autocomplete="off">
                </div>
                <div class="inside">
					<div >
						<strong><?= __('Shortcode', 'wptables') ?>:</strong>
						<code id="wpt-shortcode" class="wpt-shortcode"><?= $shortcode ?></code>
				‎		<button id="wpt-copy-shortcode" type="button" class="button button-small"><?= __('Copy', 'wptables') ?></button>
					</div>
				</div>
            </div>
            <div id="poststuff">
                <!-- Fields -->
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">
                        <h2 class="hndle ui-sortable-handle"><span><?= __('Fields', 'wptables') ?></span></h2>
                        <div class="inside wpt-fields-container">
                        	<table id="wpt-fields" class="wpt-table wpt-alter-rows">
                        		<tr class="wpt-header">
                        			<th width="5px"></th>
                                    <th></th>
                        			<th><?= __('Title', 'wptables') ?></th>
                        			<th><?= __('Type', 'wptables') ?></th>
                        			<th><?= __('Align', 'wptables') ?></th>
                        			<th><?= __('Width', 'wptables') ?></th>
									<th><?= __('CSS Class', 'wptables') ?></th>	
                        		</tr>
                        		<?php foreach ($fields as $field) : ?>
                    			<tr>
                    				<?php $name = $field['name']; ?>
                                    <td>
                                        <span class="wpt-drag-handle"></span>
                                    </td>
                                    <!-- visible -->
                                    <td>
                    					<center>
                    						<input type="checkbox" name="fields[<?= $name ?>][visible]" <?= $field['visible'] ? 'checked' : ''; ?>>
                    					</center>
                    				</td>
                    				<!-- title -->
                    				<td>
                    					<input type="text" name="fields[<?= $name ?>][title]" value="<?= $field['title']; ?>">
                    				</td>
                    				<!-- type -->
                    				<td>
                    					<select name="fields[<?= $name ?>][type]">
                    						<?php foreach ($types as $type_value => $type_label) : ?>
                							<option value="<?= $type_value; ?>" <?= $field['type'] == $type_value ? 'selected' : ''; ?>>
                								<?= $type_label; ?>
                							</option>
                    						<?php endforeach; ?>
                    					</select>
                    				</td>
                    				<!-- align -->
                    				<td>
                    					<?php $value = isset($field['align']) ? $field['align'] : ''; ?>
                    					<select name="fields[<?= $name ?>][align]">
                    						<option value="" 		<?= $value == '' ? 'selected' : ''; ?>><?= __('Default', 'wptables') ?></option>
                    						<option value="left"	<?= $value == 'left' ? 'selected' : ''; ?>><?= __('Left', 'wptables') ?></option>
                    						<option value="center"	<?= $value == 'center' ? 'selected' : ''; ?>><?= __('Center', 'wptables') ?></option>
                    						<option value="right"	<?= $value == 'right' ? 'selected' : ''; ?>><?= __('Right', 'wptables') ?></option>
                    					</select>
                    				</td>
                    				<!-- width -->
                    				<td>
                    					<?php $value = isset($field['width']) ? $field['width'] : ''; ?>
                    					<input type="number" name="fields[<?= $name ?>][width]" value="<?= $value; ?>" class="wpt-small-input">
                    				</td>
                    				<!-- css -->
                    				<td>
                    					<?php $value = isset($field['css']) ? $field['css'] : ''; ?>
                    					<input type="text" name="fields[<?= $name ?>][css]" value="<?= $value; ?>">
                    				</td>
                    			</tr>
                        		<?php endforeach; ?>
                        	</table>
                        </div>
                    </div>
                </div>
                <!-- Content -->
                <?php if ($post->post_mime_type == 'manual/data') :?>
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">
                        <h2 class="hndle ui-sortable-handle"><span><?= __('Content', 'wptables') ?></span></h2>
                        <div class="inside wpt-content-wrapper">
                            <table id="wpt-content" class="wpt-table wpt-alter-rows" width="100%">
                                <tr>
                                    <th width="30px">#</th>
                                    <?php foreach ($fields as $field): ?>
                                    <th><?= $field['title'] ?></th>
                                    <?php endforeach; ?>
                                    <th width="20px"></th>
                                </tr>
                                <?php 
                                    $data = json_decode($post->post_content, true); 
                                    $i = 1;
                                ?>
                                <?php foreach ($data as $row): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <?php foreach ($fields as $field): ?>
                                            <td>
                                                <input 
                                                    type="<?= $field['type'] ?>" 
                                                    name="data[<?= $i - 2 ?>][<?= $field['name'] ?>]" 
                                                    value="<?= $row[$field['name']] ?>">
                                                </input>
                                            </td>
                                        <?php endforeach; ?> 
                                        <td>
                                            <div class="wpt-remove-row" title="<?= __('Remove', 'wptables') ?>">
                                                <i class="dashicons-before dashicons-dismiss"></i>
                                            </div>
                                        </td>  
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                            <button id="wpt-add-row" type="button" class="button"><?= __('Add row', 'wptables') ?></button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <!-- Options -->
                <div class="meta-box-sortables ui-sortable" id="wpt-options">
                    <div class="postbox">
                        <h2 class="hndle ui-sortable-handle"><span><?= __('Options', 'wptables') ?></span></h2>
                        <div class="inside">
                            <div class="wpt-row">
                                <div class="wpt-col-2">
                                    <table class="wpt-table">
                                        <tr>
                                            <td><?= __('Allow Sorting', 'wptables') ?></td>
                                            <td>
                                                <?php $value = isset($options['sorting']) ? $options['sorting'] : false; ?>
                                                <input type="checkbox" name="config[sorting]" <?= $value ? 'checked' : '' ?>>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><?= __('Allow Selecting', 'wptables') ?></td>
                                            <td>
                                                <?php $value = isset($options['selecting']) ? $options['selecting'] : true; ?>
                                                <input type="checkbox" name="config[selecting]" <?= $value ? 'checked' : '' ?>>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><?= __('Show Header', 'wptables') ?></td>
                                            <td>
                                                <?php $value = isset($options['heading']) ? $options['heading'] : true; ?>
                                                <input type="checkbox" name="config[heading]" <?= $value ? 'checked' : '' ?>>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="wpt-col-2">
                                    <table class="wpt-table">
                                        <tr>
                                            <td><?= __('Paging', 'wptables') ?></td>
                                            <td>
                                                <?php $checked = isset($options['paging']) && $options['paging'] ? 'checked' : ''; ?>
                                                <input id="wpt-paging" type="checkbox" name="config[paging]" <?= $checked ?>>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><?= __('Page Size', 'wptables') ?></td>
                                            <td>
                                                <?php $disabled = isset($options['paging']) && $options['paging'] ? '' : 'disabled'; ?>
                                                <?php $pageSize = isset($options['pageSize']) ? $options['pageSize'] : 20; ?>
                                                <input id="wpt-page-size" value="<?= $pageSize ?>" type="number" name="config[pageSize]" class="wpt-w100" <?= $disabled ?>>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><?= __('Theme', 'wptables') ?></td>
                                            <td>
                                                <?php $selected = $options['theme']; ?>
                                                <select id="wpt-theme" name="config[theme]" class="wpt-w100">
                                                    <?php foreach($themes as $key => $theme): ?>
                                                        <option value="<?= $key ?>" <?= $selected == $key ? 'selected' : ''?>><?= $theme ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="submit" name="save" id="save" class="button button-primary button-large" value="<?= __('Save', 'wptables') ?>">
            <a href="<?= WPTables::url(array('action' => 'wpt_export_csv', 'table' => $post->ID), true, 'admin-post.php') ?>" class="button button-large">Export</a>
        </form>
        
        <h3><?= __('Table Preview', 'wptables') ?></h3>
        <?php echo do_shortcode($shortcode); ?>
    </div>

    <!-- form script -->
    <script type="text/javascript">
	jQuery(document).ready(function($) {
		$('#wpt-paging').change(onPagingChange);
        $('#wpt-copy-shortcode').click(onCopyShortcodeClick);
        $(".wpt-shortcode").click(onShortcodeClick);
        $('.wpt-remove-row').click(onRemoveRow);
        $('#wpt-add-row').click(onAddRow);
        $('#wpt-theme').change(onThemeChange);
		onPagingChange();
        updateRemoveRowButtons();
        
        function onShortcodeClick() {
            wpt_admin.selectText(this);
        }

		function onPagingChange() {
			var checked = $("#wpt-paging").prop('checked');
			$('#wpt-page-size').prop('disabled', !checked);
		}

		function onCopyShortcodeClick() {
			prompt("<?= __('Please copy and insert this shortcode to your page', 'wptables') ?>", "<?= $shortcode ?>");
		}

        function onRemoveRow() {
            $(this).closest('tr').remove();
            updateRemoveRowButtons();
            updateRowNumbers();
        }

        function updateRemoveRowButtons() {
            if ($('.wpt-remove-row').length <= 1) {
                $('.wpt-remove-row').hide();
            } else {
                $('.wpt-remove-row').show();
            }
        }

        function onAddRow() {
            var row = $("#wpt-content tr").eq(1).clone();
            $('input', row).val('');
            $('.wpt-remove-row', row).click(onRemoveRow);
            $("#wpt-content").append(row);
            updateRemoveRowButtons();
            updateRowNumbers();
        }

        function updateRowNumbers() {
            $("#wpt-content tr").each(function(idx, el) {
                $('td:first-child', el).text(idx);
                $('input', el).each(function(_, inp) {
                    var name = $(inp).attr('name');
                    name = name.replace(/data\[\d\]/, 'data[' + (idx - 1) + ']');
                    $(inp).attr('name', name);
                });
            });
        }

        function onThemeChange() {
            var theme = $(this).val();
            $(".jsgrid").attr('class', 'jsgrid ' + theme);
        }

        $("#wpt-fields tbody").sortable({
            items: "> tr:not(.wpt-header)",
            handle: ".wpt-drag-handle"
        });
	});
    </script>