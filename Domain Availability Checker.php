<?php
/*
Plugin Name: Domain Availability Checker
Description: Check domain availability using WHM API.
Version: 1.0
Author: Anvixa
*/

// Hook for adding admin menus
add_action('admin_menu', 'domain_availability_checker_menu');

function domain_availability_checker_menu() {
    add_menu_page(
        'Domain Availability Checker',
        'Domain Checker',
        'manage_options',
        'domain_availability_checker',
        'domain_availability_checker_page'
    );
}

function domain_availability_checker_page() {
    ?>
    <div class="wrap">
        <h2>Domain Availability Checker</h2>
        <form method="post" action="">
            <label for="domain_name">Domain Name:</label>
            <input type="text" name="domain_name" id="domain_name" required />
            <input type="submit" name="check_availability" class="button button-primary" value="Check Availability" />
        </form>

        <?php
        if (isset($_POST['check_availability'])) {
            $domain_name = sanitize_text_field($_POST['domain_name']);

            // Replace with your WHM API credentials
            $whm_username = 'your_whm_username';
            $whm_password = 'your_whm_password';
            $whm_api_url = 'https://your_whm_server:2087/json-api';

            // WHM API request to check domain availability
            $api_params = array(
                'api.version' => 1,
                'cpanel_jsonapi_user' => $whm_username,
                'cpanel_jsonapi_apiversion' => '2',
                'cpanel_jsonapi_module' => 'DomainLookup',
                'cpanel_jsonapi_func' => 'getTldStatus',
                'domain' => $domain_name,
            );

            $api_url = add_query_arg($api_params, $whm_api_url);

            $response = wp_remote_get($api_url, array(
                'headers' => array(
                    'Authorization' => 'Basic ' . base64_encode($whm_username . ':' . $whm_password),
                ),
            ));

            if (is_array($response) && !is_wp_error($response)) {
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body);

                if ($data->cpanelresult->data[0]->status === '1') {
                    echo '<p class="available">Domain is available!</p>';
                } else {
                    echo '<p class="unavailable">Domain is not available.</p>';
                }
            } else {
                echo '<p class="error">Error checking domain availability.</p>';
            }
        }
        ?>
    </div>
    <?php
}
?>
