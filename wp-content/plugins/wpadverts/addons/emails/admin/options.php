<?php
/**
 * Displays Emails Module Options Page
 * 
 * This file is a template for wp-admin / Classifieds / Options / Emails panel. 
 * 
 * It is being loaded by Adext_Emails_Admin::options() method.
 * 
 * @see Adext_Emails_Admin::options()
 * @since 1.3
 */
?>
<div class="wrap">
    <h2 class="nav-tab-wrapper">
        <a href="<?php esc_attr_e( remove_query_arg( array( 'edit', 'emaction' ) ) ) ?>" class="nav-tab"><?php _e("Email Templates", "adverts") ?></a>
        <a href="<?php esc_attr_e( add_query_arg( array('emaction'=>'options') ) ) ?>" class="nav-tab nav-tab-active"><?php _e("Options", "adverts") ?></a>
    </h2>

    <?php adverts_admin_flash() ?>

    <form action="" method="post" class="adverts-form">
        <table class="form-table">
            <tbody>
            <?php echo adverts_form_layout_config($form) ?>
            </tbody>
        </table>

        <p class="submit">
            <input type="submit" value="<?php esc_attr_e($button_text) ?>" class="button-primary" name="Submit"/>
        </p>

    </form>

</div>

