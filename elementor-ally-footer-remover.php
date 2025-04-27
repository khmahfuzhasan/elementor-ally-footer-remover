<?php
/**
 * Plugin Name: Elementor Ally Footer Remover
 * Plugin URI: https://github.com/khmahfuzhasan/elementor-ally-footer-remover
 * Description: Remove the Elementor Ally Footer Icon and Accessibility Statement inside shadow root with a customizable delay.
 * Version: 1.6
 * Author: Mahfuz Hasan
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Add settings menu
add_action('admin_menu', function() {
    add_options_page('Elementor Ally Footer Remover', 'Elementor Ally Footer Remover', 'manage_options', 'elementor-ally-footer-remover', 'eafr_settings_page');
});

// Register settings
add_action('admin_init', function() {
    register_setting('eafr_settings_group', 'eafr_delay_time');
    register_setting('eafr_settings_group', 'eafr_enable_removal_footer_icon');
    register_setting('eafr_settings_group', 'eafr_enable_removal_accessibility');
    register_setting('eafr_settings_group', 'eafr_custom_target_selector');
    register_setting('eafr_settings_group', 'eafr_footer_height');
    register_setting('eafr_settings_group', 'eafr_footer_custom_description');
    register_setting('eafr_settings_group', 'eafr_footer_text_color');
    register_setting('eafr_settings_group', 'eafr_footer_text_align'); // Register new setting for text alignment
});

// Settings page content
function eafr_settings_page() {
    ?>
    <div class="wrap">
        <h1>Elementor Ally Footer Remover</h1>
        <form method="post" action="options.php">
            <?php settings_fields('eafr_settings_group'); ?>
            <?php do_settings_sections('eafr_settings_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Delay Time (milliseconds)</th>
                    <td><input type="number" name="eafr_delay_time" value="<?php echo esc_attr(get_option('eafr_delay_time', 1000)); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Enable Footer Icon Removal</th>
                    <td>
                        <input type="checkbox" name="eafr_enable_removal_footer_icon" value="1" <?php checked(1, get_option('eafr_enable_removal_footer_icon', 1)); ?> />
                        <label>Remove Elementor Ally Footer Icon</label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Enable Accessibility Statement Removal</th>
                    <td>
                        <input type="checkbox" name="eafr_enable_removal_accessibility" value="1" <?php checked(1, get_option('eafr_enable_removal_accessibility', 0)); ?> />
                        <label>Remove Accessibility Statement</label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Custom Target Selector</th>
                    <td>
                        <input type="text" name="eafr_custom_target_selector" value="<?php echo esc_attr(get_option('eafr_custom_target_selector', '')); ?>" placeholder="Enter custom CSS selector inside shadow root..." style="width: 400px;" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Footer Height (px)</th>
                    <td>
                        <input type="number" name="eafr_footer_height" value="<?php echo esc_attr(get_option('eafr_footer_height', 0)); ?>" placeholder="Example: 50" />
                        <p class="description">Optional: Add a spacer div with custom height inside the footer element.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Custom Footer Description</th>
                    <td>
                        <textarea name="eafr_footer_custom_description" rows="5" style="width: 100%;"><?php echo esc_textarea(get_option('eafr_footer_custom_description', '')); ?></textarea>
                        <p class="description">Custom description to be displayed inside the footer.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Text Color</th>
                    <td>
                        <input type="color" name="eafr_footer_text_color" value="<?php echo esc_attr(get_option('eafr_footer_text_color', '#000000')); ?>" />
                        <p class="description">Select a color for the custom description text.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Text Alignment</th>
                    <td>
                        <select name="eafr_footer_text_align">
                            <option value="left" <?php selected(get_option('eafr_footer_text_align', 'left'), 'left'); ?>>Left</option>
                            <option value="center" <?php selected(get_option('eafr_footer_text_align', 'left'), 'center'); ?>>Center</option>
                            <option value="right" <?php selected(get_option('eafr_footer_text_align', 'left'), 'right'); ?>>Right</option>
                        </select>
                        <p class="description">Select text alignment for the custom description.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Inject JavaScript in frontend
add_action('wp_footer', function() {
    $delay = intval(get_option('eafr_delay_time', 1000));
    $remove_footer_icon = get_option('eafr_enable_removal_footer_icon');
    $remove_accessibility = get_option('eafr_enable_removal_accessibility');
    $custom_selector = trim(get_option('eafr_custom_target_selector')); 
    $footer_height = intval(get_option('eafr_footer_height', 0));
    $footer_description = get_option('eafr_footer_custom_description', '');
    $footer_text_color = get_option('eafr_footer_text_color', '#000000');
    $footer_text_align = get_option('eafr_footer_text_align', 'left'); // Get the alignment option

    if (!$remove_footer_icon && !$remove_accessibility && empty($custom_selector) && !$footer_height && empty($footer_description)) {
        return;
    }
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const shadowHost = document.querySelector('#ea11y-root');
            if (shadowHost && shadowHost.shadowRoot) {
                <?php if ($remove_footer_icon): ?>
                    const footerIcon = shadowHost.shadowRoot.querySelector('.ea11y-widget-footer a:nth-child(2)');
                    if (footerIcon) {
                        footerIcon.remove();
                    }
                <?php endif; ?>

                <?php if ($remove_accessibility): ?>
                    const accessibilityStatement = shadowHost.shadowRoot.querySelector('.ea11y-widget-footer a:nth-child(1)');
                    if (accessibilityStatement) {
                        accessibilityStatement.remove();
                    }
                <?php endif; ?>

                <?php if (!empty($custom_selector)): ?>
                    const customTarget = shadowHost.shadowRoot.querySelector('<?php echo esc_js($custom_selector); ?>');
                    if (customTarget) {
                        customTarget.remove();
                    }
                <?php endif; ?>

                <?php if ($footer_height): ?>
                    const footerElement = shadowHost.shadowRoot.querySelector('footer.ea11y-widget-footer');
                    if (footerElement) {
                        const spacerDiv = document.createElement('div');
                        spacerDiv.className = 'called-footer-height';
                        spacerDiv.style.height = '<?php echo $footer_height; ?>px';
                        footerElement.appendChild(spacerDiv);
                    }
                <?php endif; ?>

                <?php if (!empty($footer_description)): ?>
                    const descriptionDiv = document.createElement('div');
                    descriptionDiv.className = 'custom-footer-description';
                    descriptionDiv.innerHTML = '<?php echo esc_js($footer_description); ?>';
                    descriptionDiv.style.color = '<?php echo esc_js($footer_text_color); ?>';
                    descriptionDiv.style.textAlign = '<?php echo esc_js($footer_text_align); ?>'; // Set the text alignment
                    descriptionDiv.style.width = '100%'; // Make description div 100% width
                    const footerElement = shadowHost.shadowRoot.querySelector('footer.ea11y-widget-footer');
                    if (footerElement) {
                        footerElement.appendChild(descriptionDiv);
                    }
                <?php endif; ?>
            }
        }, <?php echo $delay; ?>);
    });
    </script>
    <?php
});
