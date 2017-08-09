<?php
/**
 * WordPress Tables plugin.
 *
 * @package    WPTables
 * @author     Ian Sadovy <ian.sadovy@gmail.com>
 */
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?= esc_html(get_admin_page_title()); ?></h1>
    <form id="wpt-table" method="post" action="<?= esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
        <?php if (isset($_GET['error_msg'])) : ?>
        <div class="error">
            <p><?= $_GET['error_msg'] ?></p>
        </div>
        <?php endif; ?>
        <input type="hidden" name="action" value="wpt_add_new_table">
        <?php wp_nonce_field( 'wpt-add-new-table'); ?>
        <div id="titlediv">
            <div id="titlewrap">
                <?php $title = isset($_GET['title']) ? $_GET['title'] : ""; ?>
                <input type="text" name="title" size="30" value="<?= $title ?>" id="title" spellcheck="true" autocomplete="off">
            </div>
        </div>
        <div id="poststuff">
            <div class="meta-box-sortables ui-sortable">
                <div class="postbox">
                    <h2 class="hndle ui-sortable-handle"><span><?= __('Data Source', 'wptables') ?></span></h2>
                    <div class="inside">
                        <table class="fixed wpt-table" width="100%">
                            <tr>
                                <td class="column-1" scope="row" width="100px">
                                    <label for="wpt-format"><?= __('Data Format', 'wptables') ?>:</label>
                                </td>
                                <td class="column-2">
                                    <select id="wpt-format" name="format" class="wpt-medium">
                                        <option value="manual"><?= __('Manual', 'wptables') ?></option>
                                        <option value="csv"><?= __('CSV', 'wptables') ?></option>
                                        <option value="mysql"><?= __('MySQL', 'wptables') ?></option>
                                    </select>
                                    
                                </td>
                            </tr>
                            <tr class="wpt-border-bottom">
                                <td colspan="2">
                                    <p class="wpt-hint">
                                        <i class="dashicons-before dashicons-info"></i>
                                        <span id="wpt-hint-manual"> 
                                        <?php 
                                            _e('Manual mode is a perfect choice for a small table. You will be able to enter and edit data in a spreadsheet-like interface.', 'wptables');
                                        ?>
                                        </span>
                                        <span id="wpt-hint-csv" class="wpt-hide"> 
                                        <?php 
                                            _e('Create a table using existing CSV file. You can also export Excel file to CSV or copy/paste data in CSV format.', 'wptables');
                                        ?>
                                        </span>
                                        <span id="wpt-hint-mysql" class="wpt-hide"> 
                                        <?php 
                                            _e('Create a table from existing data in your MySQL database. Such data will be dynamically loaded every time into the table.', 'wptables');
                                        ?>
                                        </span>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td class="column-1" scope="row">
                                    <label for=""><?= __('Data Source', 'wptables') ?>:</label>
                                </td>
                                <td class="column-2" id="wpt-data-source-types">
                                    <div id="wpt-data-source-manual" class="wpt-hide">
                                        <label for="wpt-manual-cols">
                                            <input id="wpt-manual-cols" name="input-cols" type="number" value="5" class="wpt-medium">
                                            <?= __('columns', 'wptables') ?>
                                        </label>
                                        <br>
                                        <label for="wpt-manual-rows">
                                            <input id="wpt-manual-rows" name="input-rows" type="number" value="5" class="wpt-medium">
                                            <?= __('rows', 'wptables') ?>
                                        </label>
                                    </div>
                                    <div id="wpt-data-source-csv" class="wpt-hide">
                                        <label for="wpt-csv-file">
                                            <input id="wpt-csv-file" name="input-type-csv" type="radio" value="file" checked>
                                            <?= __('File Upload', 'wptables') ?>
                                        </label>
                                        <br>
                                        <label for="wpt-csv-url">
                                            <input id="wpt-csv-url" name="input-type-csv" type="radio" value="url">
                                            <?= __('URL', 'wptables') ?>
                                        </label>
                                        <br>
                                        <label for="wpt-csv-text">
                                            <input id="wpt-csv-text" name="input-type-csv" type="radio" value="text">
                                            <?= __('Manual Input', 'wptables') ?>
                                        </label>
                                    </div>
                                    <div id="wpt-data-source-mysql" class="wpt-hide">
                                        <label for="wpt-mysql-db">
                                            <input id="wpt-mysql-db" name="input-type-mysql" type="radio" value="db-table" checked>
                                            <?= __('Database Table', 'wptables') ?>
                                        </label>
                                        <br>
                                        <!-- <label for="wpt-mysql-query">
                                            <input id="wpt-mysql-query" name="input-type-mysql" type="radio" value="sql-query">SQL Query
                                        </label> -->
                                    </div>
                                </td>
                            </tr>
                            <tr id="wpt-data-source-type-file" class="wpt-data-source-type wpt-hide" >
                                <td class="column-1" scope="row">
                                    <label for=""><?= __('Select File', 'wptables') ?>:</label>
                                </td>
                                <td class="column-2">
                                    <input type="file" name="data-file" accept=".csv">
                                </td>
                            </tr>
                            <tr id="wpt-data-source-type-url" class="wpt-data-source-type wpt-hide" >
                                <td class="column-1" scope="row">
                                    <label for=""><?= __('File URL', 'wptables') ?>:</label>
                                </td>
                                <td class="column-2">
                                    <input type="text" name="data-url" style="width: 100%;">
                                </td>
                            </tr>
                            <tr id="wpt-data-source-type-text" class="wpt-data-source-type wpt-hide" >
                                <td class="column-1" scope="row">
                                    <label for=""><?= __('Insert Data', 'wptables') ?>:</label>
                                </td>
                                <td class="column-2">
                                    <textarea name="data-text" rows="15" cols="40" class="large-text"></textarea>
                                </td>
                            </tr>
                            <tr id="wpt-data-source-type-db-table" class="wpt-data-source-type wpt-hide" >
                                <td class="column-1" scope="row">
                                    <label for=""><?= __('Select Table', 'wptables') ?>:</label>
                                </td>
                                <td class="column-2">
                                    <?php 
                                        global $wpdb;
                                        $sql = "SHOW TABLES LIKE '%'";
                                        $results = $wpdb->get_results($sql);
                                    ?>
                                    <select name="data-db-table">
                                        <?php foreach($results as $index => $value): ?>
                                        <?php foreach($value as $name) : ?>
                                            <option value="<?= $name ?>"><?= $name ?></option>
                                        <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr id="wpt-data-source-type-sql-query" class="wpt-data-source-type wpt-hide" >
                                <td class="column-1" scope="row">
                                    <label for=""><?= __('Insert Query', 'wptables') ?>:</label>
                                </td>
                                <td class="column-2">
                                    <textarea name="data-query" rows="5" cols="40" class="large-text"></textarea>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <input type="submit" name="save" id="save" class="button button-primary button-large" value="<?= __('Create', 'wptables') ?>">
    </form>
</div>
<!-- form script -->
<script type="text/javascript">
(function($) {
    var format = 'csv';
    var type = 'file';

    $('#wpt-format').change(onFormatChange);
    onFormatChange();

    $('#wpt-data-source-types input[type=radio]').change(onDataSourceTypeChange);
    onDataSourceTypeChange();

    function onFormatChange() {
        format = $('#wpt-format').val();
        $("#wpt-data-source-types").children().hide();
        $("#wpt-data-source-" + format).show();
        $('.wpt-hint span').hide();
        $('.wpt-hint #wpt-hint-' + format).show();

        onDataSourceTypeChange();
    }

    function onDataSourceTypeChange(e) {
        type = $('input[name=input-type-'+format+']:checked').val();
        $(".wpt-data-source-type").hide();
        $("#wpt-data-source-type-" + type).show();
        console.log(type);
    }
})(jQuery);
</script>
