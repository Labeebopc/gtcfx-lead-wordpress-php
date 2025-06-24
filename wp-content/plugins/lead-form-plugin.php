<!-- Starting the PHP code -->
<?php
/**
 * Plugin Name: Lead Form Plugin
 * Description: Adds a [lead_form] shortcod to submit leads to an external API.
 * Version: 1.0
 * Author: Labeeb
 */

add_shortcode('lead_form', 'lead_form_shortcode');

// <!-- Function declaration -->
function lead_form_shortcode() {

    // allowing the plugin to capture all HTML output and return it as a string
    ob_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lead_form_submitted'])) {
        $name   = sanitize_text_field($_POST['name'])
        $email  = sanitize_email($_POST['email'])
        $phone  = sanitize_text_field($_POST['phone'])
        $source = sanitize_text_field($_POST['source'])

        $response = wp_remote_post('https://gtcfx-lead-backend.onrender.com/api/leads', [
            'method'  => 'POST',
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => json_encode([
                'name'         => $name,
                'email'        => $email,
                'phone'        => $phone,
                'source'       => $source,
                'submitted_at' => current_time('Y-m-d H:i:s')
            ])
        ]);

        if (is_wp_error($response)) {
            echo '<p style="color:red;">Failed to submit lead. Please try again later.</p>'
        } else {
            echo '<p style="color:green;">Lead submitted successfully!</p>'
        }
    }

    // HTML Element
    ?>
    <form method="post">
        <input type="hidden" name="lead_form_submitted" value="1" />
        <p><input type="text" name="name" placeholder="Name" required></p>
        <p><input type="email" name="email" placeholder="Email" required></p>
        <p><input type="text" name="phone" placeholder="Phone" required></p>
        <p><input type="text" name="source" placeholder="Source" required></p>
        <p><button type="submit">Submit</button></p>
    </form>
    <?php

    return ob_get_clean()
}
