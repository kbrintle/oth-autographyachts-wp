<?php
class Functions
{
    public function email_broker_post_complete()
    {
        // Build POST request
        $lead = new stdClass();
        $lead->notes = $_POST['notes'];
        $lead->firstname = $_POST['firstname'];
        $lead->lastname = $_POST['lastname'];
        $lead->email = $_POST['email'];
        $lead->phone = $_POST['phone'];
        $lead->location = $_POST['location'];
        $lead->year = $_POST['year'];
        $lead->make = $_POST['make'];
        $lead->model = $_POST['model'];
        $lead->hin = $_POST['hin'];
        $lead->source = $_POST['source'];
        $lead->referrer = $_SERVER["HTTP_REFERER"];

        if ($_POST['tradein']) {
            if (strlen($_POST['tradein']["year"]) > 0)
                $lead->tradein_year = $_POST['tradein']["year"];
            if (strlen($_POST['tradein']["make"]) > 0)
                $lead->tradein_model = $_POST['tradein']["model"];
            if (strlen($_POST['tradein']["model"]) > 0)
                $lead->tradein_make = explode("-", $_POST['tradein']["make"])[1];
        }

        $submit_object = new stdClass();
        $submit_object = $lead;
        $submit_object->username = "oth";
        $submit_object->password = "f09eb636703e5c248ecc";
        $submit_object = json_encode($submit_object);

        $url = 'https://crm.offthehookyachts.com/api/post-broker-web-lead';

        if (strpos($_SERVER['SERVER_NAME'], 'development') !== false) {
            $url = 'https://crm.oth.craftwebshop.com/api/post-broker-web-lead';
        }

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $submit_object);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Content-Type: application/json']);
        $response = curl_exec($curl);
        wp_safe_redirect(esc_url($lead->referrer.'#sent'));
        exit;
    }
}