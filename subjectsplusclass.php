<?php

class subjectsplus_info {

    private $sp_key;
    private $sp_url;
    private $sp_output_xml;
    private $sp_staff; 
    private $sp_query;
    private $sp_email;
    private $sp_department;
    private $sp_personnel;
    private $sp_max;
    private $sp_type;
    private $sp_display;

    // Getter and setter for the API key
    public function set_sp_key($sp_key) {
        $this->sp_key = $sp_key;
    }

    public function get_sp_key() {
        return $this->sp_key;
    }

    // Getter and setter for the API base URL
    public function set_sp_url($sp_url) {
        $this->sp_url = $sp_url;
    }

    public function get_sp_url() {
        return $this->sp_url;
    }

    // Getter and setter for the SP query
    public function set_sp_query($sp_query) {
        $this->sp_query = $sp_query;
    }

    public function get_sp_query() {
        return $this->sp_query;
    }

    private function build_api_url() {
        $api_url = $this->sp_url;

        switch ($this->sp_query) {
            case 'staff':
                $api_url .= '/sp/api/staff/';
                if (!empty($this->sp_email)) {
                    $api_url .= 'email/' . urlencode($this->sp_email) . '/';
                } elseif (!empty($this->sp_department)) {
                    $api_url .= 'department/' . urlencode($this->sp_department) . '/';
                } elseif ($this->sp_personnel === 'all') {
                    $api_url .= 'personnel/all/';
                }
                $api_url .= 'max/' . urlencode($this->sp_max) . '/';
                break;

            // Add cases for other types if needed

            default:
                // Handle other cases or throw an error if necessary
                break;
        }

        $api_url .= 'key/' . $this->sp_key;
        error_log("API URL: " . $api_url); // Log API URL
        return $api_url;
    }

    public function do_sp_staff_query_table() {
        $query = $this->build_api_url();
        error_log("Attempting API Request to: " . $query); // Log the API request
        $response = wp_remote_get($query);

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            error_log("API Request Error: $error_message"); // Log API request error
            echo "Error: Unable to retrieve data. Please check your API key and URL.";
            return;
        }

        $body = wp_remote_retrieve_body($response);
        error_log("API HTTP Response Body: " . $body); // Log the response body for debugging

        $http_code = wp_remote_retrieve_response_code($response);
        error_log("API HTTP Response Code: " . $http_code); // Log the HTTP response code

        if ($http_code !== 200) {
            echo "Error: Unexpected HTTP response code $http_code.";
            return;
        }

        $staff_info = json_decode($body, true);

        if ($staff_info === null) {
            $json_error_message = json_last_error_msg();
            error_log("JSON Decoding Error: $json_error_message"); // Log JSON decoding error
            echo "Error: Unable to decode JSON response.";
            return;
        }

        if (!isset($staff_info['staff-member'])) {
            echo "Error: Invalid JSON response or missing 'staff-member' key.";
            return;
        }

        $staff_members = $staff_info['staff-member'];

        // Format and display data in a table
        $output = '<table>';
        $output .= '<tr><th>Name</th><th>Email</th><th>Title</th><th>Mobile No.</th><th>About me</th></tr>';

        if (!empty($staff_members)) {
            foreach ($staff_members as $staff_member) {
                $output .= '<tr>';
                $output .= '<td>' . esc_html($staff_member['fname'] . ' ' . $staff_member['lname']) . '</td>';
                $output .= '<td>' . esc_html($staff_member['email']) . '</td>';
                $output .= '<td>' . esc_html($staff_member['title']) . '</td>';
                $output .= '<td>' . esc_html($staff_member['tel']) . '</td>';
                $output .= '<td>' . esc_html($staff_member['bio']) . '</td>';
                $output .= '</tr>';
            }
        } else {
            $output .= '<tr><td colspan="3">No data found.</td></tr>';
        }

        $output .= '</table>';

        echo $output;
    }

    public function do_sp_staff_query_card() {
        $query = $this->build_api_url();
        error_log("Attempting API Request to: " . $query); // Log the API request
        $response = wp_remote_get($query);

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            error_log("API Request Error: $error_message"); // Log API request error
            echo "Error: Unable to retrieve data. Please check your API key and URL.";
            return;
        }

        $body = wp_remote_retrieve_body($response);
        error_log("API HTTP Response Body: " . $body); // Log the response body for debugging

        $http_code = wp_remote_retrieve_response_code($response);
        error_log("API HTTP Response Code: " . $http_code); // Log the HTTP response code

        if ($http_code !== 200) {
            echo "Error: Unexpected HTTP response code $http_code.";
            return;
        }

        $staff_info = json_decode($body, true);

        if ($staff_info === null) {
            $json_error_message = json_last_error_msg();
            error_log("JSON Decoding Error: $json_error_message"); // Log JSON decoding error
            echo "Error: Unable to decode JSON response.";
            return;
        }

        if (!isset($staff_info['staff-member'])) {
            echo "Error: Invalid JSON response or missing 'staff-member' key.";
            return;
        }

        $staff_members = $staff_info['staff-member'];

        // Format and display data in a card layout
        $output = '<div class="sp-card-container">';

        if (!empty($staff_members)) {
            foreach ($staff_members as $staff_member) {
                $output .= '<div class="sp-card">';
                $output .= '<h3>' . esc_html($staff_member['fname'] . ' ' . $staff_member['lname']) . '</h3>';
                $output .= '<p><strong>Email:</strong> ' . esc_html($staff_member['email']) . '</p>';
                $output .= '<p><strong>Title:</strong> ' . esc_html($staff_member['title']) . '</p>';
                $output .= '<p><strong>Mobile No.:</strong> ' . esc_html($staff_member['tel']) . '</p>';
                $output .= '<p><strong>About me:</strong> ' . esc_html($staff_member['bio']) . '</p>';
                $output .= '</div>';
            }
        } else {
            $output .= '<p>No data found.</p>';
        }

        $output .= '</div>';

        echo $output;
    }

    public function setup_sp_query($atts, $display) {
        $this->sp_type = sanitize_string($atts['service']);
        $this->sp_display = sanitize_string($display); // Use the provided display format

        // Set a default max if max attribute isn't used
        if (empty($atts['max'])) {
            $atts['max'] = '99';
        }

        switch ($this->sp_type) {
            case 'staff':
                $this->sp_query = 'staff';
                $this->sp_email = isset($atts['email']) ? sanitize_string($atts['email']) : '';
                $this->sp_department = isset($atts['department']) ? sanitize_string($atts['department']) : '';
                $this->sp_personnel = isset($atts['personnel']) && $atts['personnel'] === 'all' ? 'all' : '';
                $this->sp_max = sanitize_string($atts['max']);

                // Call the appropriate method based on the display format
                if ($this->sp_display === 'card') {
                    $this->do_sp_staff_query_card();
                } else {
                    $this->do_sp_staff_query_table();
                }
                break;

            // Add cases for other types if needed

            default:
                echo "Error: Invalid service type.";
                break;
        }
    }
}

function sanitize_string($string) {
    return urlencode(filter_var($string, FILTER_SANITIZE_STRING));
}
?>
