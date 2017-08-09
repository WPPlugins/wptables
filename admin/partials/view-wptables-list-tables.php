<?php
/**
 * WordPress Tables plugin.
 *
 * @package    WPTables
 * @author     Ian Sadovy <ian.sadovy@gmail.com>
 */
?>
<?php 
global $wp_query;
$args = array_merge( $wp_query->query_vars, array( 'post_type' => 'wptables_table' ) );
$args['posts_per_page'] = -1;
query_posts( $args );
$have_posts = false;
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?= esc_html(get_admin_page_title()); ?></h1>
    <a href="<?= WPTables::url(array('page' => 'wptables-add-new')); ?>" class="page-title-action"><?= __('Add New', 'wptables') ?></a>
    <form id="wpt-list-form">
        <table class="wp-list-table widefat fixed striped posts">
            <thead>
                <tr>
                    <th width="40px" id="id"><?= __('ID', 'wptables') ?></th>
                    <th id="title"><?= __('Title', 'wptables') ?></th>
                    <th width="160px"><?= __('Shortcode', 'wptables') ?></th>
                    <th width="160px" id="author"><?= __('Author', 'wptables') ?></th>
                    <th width="120px" id="author"><?= __('Date', 'wptables') ?></th>
                </tr>
            </thead>
            <tbody id="the-list">
                <?php while ( have_posts() ) : the_post(); $have_posts = true;?>
                <?php $url_edit = WPTables::url(array('page' => 'wptables', 'action' => 'edit', 'table' => get_the_ID())); ?>
                <?php $url_export = WPTables::url(array('action' => 'wpt_export_csv', 'table' => get_the_ID()), true, 'admin-post.php'); ?>
                <?php $url_delete = WPTables::url(array('action' => 'wpt_delete_table', 'table' => get_the_ID()), true, 'admin-post.php'); ?>
                <tr>
                    <td><?= get_the_ID() ?></td>
                    <td>
                        <strong>
                            <a href="<?= $url_edit ?>"><?php the_title(); ?></a>
                        </strong>
                        <div class="row-actions">
                            <span class="edit">
                                <a href="<?= $url_edit ?>"><?= __('Edit', 'wptables') ?></a> | 
                            </span>
                            <span class="export">
                                <a href="<?= $url_export ?>" target="_blank"><?= __('Export', 'wptables') ?></a> | 
                            </span>
                            <span class="trash">
                                <a class="wpt-delete" href="<?= $url_delete ?>"><?= __('Delete', 'wptables') ?></a>
                            </span>
                       </div>
                    </td>
                    <td><code class="wpt-shortcode"><?= WPTables::shortcode_table(get_the_ID()) ?></code></td>
                    <td><?= get_the_author() ?></td>
                    <td>
                        <?= __('Modified', 'wptables') ?><br>
                        <abbr title="<?= get_the_date() ?> <?= get_the_time() ?>"><?= get_the_date() ?></abbr>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if (!$have_posts) : ?>
                    <tr>
                        <td colspan="5">
                            <p style="text-align: center;padding: 40px 10px;"><?php _e( 'Please add new table using the button below.', 'wptables' ); ?>
                                <br>
                                <br>
                                <a href="<?= WPTables::url(array('page' => 'wptables-add-new')); ?>" class="page-title-action"><?= __('Add New Table', 'wptables') ?></a>
                            </p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th width="25px" id="id"><?= __('ID', 'wptables') ?></th>
                    <th width="50%" id="title"><?= __('Title', 'wptables') ?></th>
                    <th width="160px"><?= __('Shortcode', 'wptables') ?></th>
                    <th id="author"><?= __('Author', 'wptables') ?></th>
                    <th id="author"><?= __('Date', 'wptables') ?></th>
                </tr>
            </tfoot>
        </table>

    </form>
</div>

<?php wp_reset_query(); ?>

<script type="text/javascript">
    (function($){
        $(".wpt-delete").click(onDeleteClick);
        $(".wpt-shortcode").click(onShortcodeClick);
        
        function onDeleteClick(e) {
            if(!confirm("<?= __('Do you really want to delete this table?', 'wptables') ?>")) {
                e.preventDefault();
            }
        }
        function onShortcodeClick() {
            wpt_admin.selectText(this);
        }
    })(jQuery);
</script>