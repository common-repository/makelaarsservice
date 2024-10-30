<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://penthion.nl
 * @since      1.0.0
 *
 * @package    Makelaarsservice
 * @subpackage Makelaarsservice/admin/partials
 */

global $wpdb;

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap nl-pnt-ms-admin">
    <div class="hero nl-pnt-ms-hero-top">&nbsp;</div>
    <div class="hero is-white nl-pnt-ms-hero-main">
        <div class="hero-body"> 
            <p class="has-text-centered">
                <img class="nl-pnt-ms-logo" src="https://trial.makelaarsservice.nl/assets/ms_logo-e102e94dfddf62361968adc58b411ad9114975be06bbc00f1b86c8b5506c8129.png" alt="Makelaarsservice">
            </p>
        </div>
    </div>
    <div id="nl-pnt-ms-admin-message"></div>

    <div class="tabs is-toggle">
        <ul>
            <li class="nl-pnt-ms-tab is-active" data-tab="tab-tokens">
                <a>
                    <span class="icon is-small"><i class="fa fa-key" aria-hidden="true"></i></span>
                    <span><?php _e( 'Tokens', 'makelaarsservice' )?></span>
                </a>
            </li>
            <li class="nl-pnt-ms-tab" data-tab="tab-options">
                <a>
                    <span class="icon is-small"><i class="fa fa-cog" aria-hidden="true"></i></span>
                    <span><?php _e( 'Instellingen', 'makelaarsservice' )?></span>
                </a>
            </li>
        </ul>
    </div>

    <section id="tab-tokens" class="tab-content is-active">
        <table id="nl-pnt-ms-token-table" class="wp-list-table widefat striped">
            <thead>
            <tr>
                <!-- <th>ID</th> -->
                <th><?php _e( 'Naam', 'makelaarsservice' )?></th>
                <th><?php _e( 'Token', 'makelaarsservice' )?></th>
                <th><?php _e( 'Status', 'makelaarsservice' )?></th>
                <th><?php _e( 'Laatst bijgewerkt', 'makelaarsservice' )?></th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <form class="nl-pnt-ms-form-add-token" method="post">
                <tr>
                    <td><input type="text" disabled></td>
                    <td id="nl-pnt-ms-new-token">
                        <span class="nl-pnt-ms-error-text"></span>
                        <input type="text" name="token"></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><button class="button-primary" type="submit"><?php _e( 'Toevoegen', 'makelaarsservice' )?></button></td>
                </tr>
            </form>
            <?php
            // Get all tokens from database and add table row with token info for each token.
            $result = $wpdb->get_results( "SELECT * FROM $this->table_name" );
            foreach($result as $print) {
                // Get the is_active status info
                $check = $this->is_active( $print->token_id );

                $updated_at = date( "d-m-Y H:i:s", strtotime( $print->updated_at ) );
                echo "
                        <tr>
                            <!--<td>$print->token_id</td>-->
                            <td>$print->name ($print->agent_id)</td>
                            <td>$print->token</td>
                            <td>
                                <div class='nl-pnt-ms-statusbox nl-pnt-ms-bg-$check[color]'>
                                    <span>$check[message]</span>
                                </div>        
                            </td>
                            <td>
                                <span class='nl-pnt-ms-updated_at' data-token-id='$print->token_id'>$updated_at</span>
                            </td>
                            <td>
                                <button class='nl-pnt-ms-btn-icon nl-pnt-ms-bg-success nl-pnt-ms-update' data-token-id='$print->token_id' data-token-name='$print->name' ".($print->is_active == 0 ? 'disabled' : '').">
                                    <span class='dashicons dashicons-update'></span>
                                </button>

                                <button class='nl-pnt-ms-btn-icon nl-pnt-ms-bg-warning nl-pnt-ms-pause' data-token-id='$print->token_id' data-token-name='$print->name'>
                                    <span class='dashicons dashicons-controls-" . ($check['status'] == 2 ? 'play' : 'pause') . "'></span>
                                </button>

                                <button class='nl-pnt-ms-btn-icon nl-pnt-ms-bg-danger nl-pnt-ms-delete' data-token-id='$print->token_id' data-token-name='$print->name'>
                                    <span class='dashicons dashicons-trash'></span>
                                </button>
                            </td>
                        </tr>
                    ";

            };
            ?>
            </tbody>
        </table>
    </section>

    <section id="tab-options" class="tab-content">
        <form id="nl-pnt-ms-form-settings" method="POST" action="options.php">
            <?php
            //Initialize WordPress settings fields as defined in admin class
            settings_fields( 'makelaarsservice' );
            do_settings_sections( 'makelaarsservice' );
            ?>
            <input type="submit" value="<?php _e( 'Opslaan', 'makelaarsservice' )?>" class="button-primary">
        </form>
    </section>
</div>