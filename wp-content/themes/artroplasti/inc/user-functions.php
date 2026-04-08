<?php
/**
 * User Functions
 */

if (!defined('ABSPATH')) {
    exit;
}

// Custom user meta fields
function artroplasti_add_user_meta_fields($user) {
    ?>
    <h3>Artroplasti Üyelik Bilgileri</h3>
    <table class="form-table">
        <tr>
            <th><label for="membership_type">Üyelik Tipi</label></th>
            <td>
                <select name="membership_type" id="membership_type">
                    <option value="standard" <?php selected(get_user_meta($user->ID, 'membership_type', true), 'standard'); ?>>Standart</option>
                    <option value="premium" <?php selected(get_user_meta($user->ID, 'membership_type', true), 'premium'); ?>>Premium</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="membership_status">Üyelik Durumu</label></th>
            <td>
                <select name="membership_status" id="membership_status">
                    <option value="active" <?php selected(get_user_meta($user->ID, 'membership_status', true), 'active'); ?>>Aktif</option>
                    <option value="inactive" <?php selected(get_user_meta($user->ID, 'membership_status', true), 'inactive'); ?>>Pasif</option>
                    <option value="pending" <?php selected(get_user_meta($user->ID, 'membership_status', true), 'pending'); ?>>Beklemede</option>
                </select>
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'artroplasti_add_user_meta_fields');
add_action('edit_user_profile', 'artroplasti_add_user_meta_fields');

// Save user meta fields
function artroplasti_save_user_meta_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    if (isset($_POST['membership_type'])) {
        update_user_meta($user_id, 'membership_type', sanitize_text_field($_POST['membership_type']));
    }

    if (isset($_POST['membership_status'])) {
        update_user_meta($user_id, 'membership_status', sanitize_text_field($_POST['membership_status']));
    }
}
add_action('personal_options_update', 'artroplasti_save_user_meta_fields');
add_action('edit_user_profile_update', 'artroplasti_save_user_meta_fields');

// Check if user has active membership
function artroplasti_has_active_membership($user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    $membership_status = get_user_meta($user_id, 'membership_status', true);
    return ($membership_status === 'active');
}

// Get user membership type
function artroplasti_get_membership_type($user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    return get_user_meta($user_id, 'membership_type', true);
}
