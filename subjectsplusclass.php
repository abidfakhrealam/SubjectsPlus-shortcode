<?php

class SubjectsPlusInfo {

    private $spKey;
    private $spUrl;
    private $spQuery;
    private $spEmail;
    private $spDepartment;
    private $spPersonnel;
    private $spMax;
    private $spType;
    private $spDisplay;

    const QUERY_STAFF = 'staff';

    public function setSpKey($spKey) {
        $this->spKey = $spKey;
    }

    public function getSpKey() {
        return $this->spKey;
    }

    public function setSpUrl($spUrl) {
        $this->spUrl = $spUrl;
    }

    public function getSpUrl() {
        return $this->spUrl;
    }

    public function setSpQuery($spQuery) {
        $this->spQuery = $spQuery;
    }

    public function getSpQuery() {
        return $this->spQuery;
    }

	private function buildApiUrl() {
		$apiUrl = $this->spUrl; // Use the provided API URL

		switch ($this->spQuery) {
			case self::QUERY_STAFF:
				$apiUrl .= '/sp/api/staff/';
				if (!empty($this->spEmail)) {
					$apiUrl .= 'email/' . urlencode($this->spEmail) . '/';
				} elseif (!empty($this->spDepartment)) {
					$apiUrl .= 'department/' . urlencode($this->spDepartment) . '/';
				} elseif ($this->spPersonnel === 'all') {
					$apiUrl .= 'personnel/all/';
				}
				$apiUrl .= 'max/' . urlencode($this->spMax) . '/';
				break;

			// Add cases for other types if needed

			default:
				// Handle other cases or throw an error if necessary
				break;
		}

		$apiUrl .= 'key/' . $this->spKey;
		error_log("API URL: " . $apiUrl); // Log API URL
		return $apiUrl;
	}


    private function performApiRequest() {
        $query = $this->buildApiUrl();
        error_log("Attempting API Request to: " . $query); // Log the API request
        $response = wp_remote_get($query);

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            error_log("API Request Error: $error_message"); // Log API request error
            echo "Error: Unable to retrieve data. Please check your API key and URL.";
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        error_log("API HTTP Response Body: " . $body); // Log the response body for debugging

        $http_code = wp_remote_retrieve_response_code($response);
        error_log("API HTTP Response Code: " . $http_code); // Log the HTTP response code

        if ($http_code !== 200) {
            echo "Error: Unexpected HTTP response code $http_code.";
            return false;
        }

        return $body;
    }

    private function displayTable($staffMembers) {
        // Format and display data in a table
        $output = '<table>';
        $output .= '<tr><th>Name</th><th>Email</th><th>Title</th><th>Mobile No.</th><th>About me</th></tr>';

        if (!empty($staffMembers)) {
            foreach ($staffMembers as $staffMember) {
                $output .= '<tr>';
                $output .= '<td>' . esc_html($staffMember['fname'] . ' ' . $staffMember['lname']) . '</td>';
                $output .= '<td>' . esc_html($staffMember['email']) . '</td>';
                $output .= '<td>' . esc_html($staffMember['title']) . '</td>';
                $output .= '<td>' . esc_html($staffMember['tel']) . '</td>';
                $output .= '<td>' . esc_html($staffMember['bio']) . '</td>';
                $output .= '</tr>';
            }
        } else {
            $output .= '<tr><td colspan="3">No data found.</td></tr>';
        }

        $output .= '</table>';

        echo $output;
    }

    private function displayCard($staffMembers) {
        // Format and display data in a card layout
        $output = '<div class="sp-card-container">';

        if (!empty($staffMembers)) {
            foreach ($staffMembers as $staffMember) {
                $output .= '<div class="sp-card">';
                $output .= '<h3>' . esc_html($staffMember['fname'] . ' ' . $staffMember['lname']) . '</h3>';
                $output .= '<p><strong>Email:</strong> ' . esc_html($staffMember['email']) . '</p>';
                $output .= '<p><strong>Title:</strong> ' . esc_html($staffMember['title']) . '</p>';
                $output .= '<p><strong>Mobile No.:</strong> ' . esc_html($staffMember['tel']) . '</p>';
                $output .= '<p><strong>About me:</strong> ' . esc_html($staffMember['bio']) . '</p>';
                $output .= '</div>';
            }
        } else {
            $output .= '<p>No data found.</p>';
        }

        $output .= '</div>';

        echo $output;
    }

    public function setupSpQuery($atts, $display) {
        $this->spType = sanitize_string($atts['service']);
        $this->spDisplay = sanitize_string($display); // Use the provided display format

        // Set a default max if max attribute isn't used
        $atts['max'] = !empty($atts['max']) ? sanitize_string($atts['max']) : '99';

        switch ($this->spType) {
            case self::QUERY_STAFF:
                $this->spQuery = self::QUERY_STAFF;
                $this->spEmail = isset($atts['email']) ? sanitize_string($atts['email']) : '';
                $this->spDepartment = isset($atts['department']) ? sanitize_string($atts['department']) : '';
                $this->spPersonnel = isset($atts['personnel']) && $atts['personnel'] === 'all' ? 'all' : '';
                $this->spMax = $atts['max'];

                $body = $this->performApiRequest();

                if ($body !== false) {
                    $staffInfo = json_decode($body, true);

                    if ($staffInfo === null) {
                        $jsonErrorMessage = json_last_error_msg();
                        error_log("JSON Decoding Error: $jsonErrorMessage"); // Log JSON decoding error
                        echo "Error: Unable to decode JSON response.";
                        return;
                    }

                    if (!isset($staffInfo['staff-member'])) {
                        echo "Error: Invalid JSON response or missing 'staff-member' key.";
                        return;
                    }

                    $staffMembers = $staffInfo['staff-member'];

                    // Call the appropriate method based on the display format
                    if ($this->spDisplay === 'card') {
                        $this->displayCard($staffMembers);
                    } else {
                        $this->displayTable($staffMembers);
                    }
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
    return urlencode(sanitize_text_field($string));
}

// Example usage:
// $subjectsPlusInfo = new SubjectsPlusInfo();
// $subjectsPlusInfo->setSpKey('your_api_key');
// $subjectsPlusInfo->setSpUrl('your_api_url');
// $subjectsPlusInfo->setupSpQuery(['service' => 'staff', 'max' => '10'], 'table');

?>
