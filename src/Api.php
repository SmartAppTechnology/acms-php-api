<?php

namespace ACMS;

use GuzzleHttp\Client;

/**
 * API Wrappers
 */
class Api implements ApiInterface
{

    /**
     * Api key.
     *
     * @var string
     */
    private $apiKey;

    /**
     * Endpoint.
     *
     * @var string
     */
    private $apiEndpoint = "https://api.accredible.com/v1/";

    /**
     * Construct API instance.
     *
     * @param string $api_key
     * @param boolean|null $test
     */
    public function __construct($api_key, $test = null)
    {
        $this->setApiKey($api_key);

        if (null !== $test) {
            $this->apiEndpoint = "https://staging.accredible.com/v1/";
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCredential($id)
    {
        $client = new Client();

        $params = array('headers' => array('Authorization' => 'Token token="' . $this->getApiKey() . '"'));

        $response = $client->get($this->apiEndpoint . 'credentials/' . $id, $params);

        $result = json_decode($response->getBody());
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * {@inheritdoc}
     */
    public function setApiKey($key)
    {
        $this->apiKey = $key;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials($group_id = null, $email = null, $page_size = null, $page = 1)
    {
        $client = new Client();

        $params = array('headers' => array('Authorization' => 'Token token="' . $this->getApiKey() . '"'));
        $params['query'] = array(
            'group_id' => $group_id,
            'email' => rawurlencode($email),
            'page_size' => $page_size,
            'page' => $page,
        );

        $response = $client->get($this->apiEndpoint . 'all_credentials', $params);

        $result = json_decode($response->getBody());
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function createCredential(
        $recipient_name,
        $recipient_email,
        $course_id,
        $issued_on = null,
        $expired_on = null,
        $custom_attributes = null
    ) {

        $data = array(
            "credential" => array(
                "group_id" => $course_id,
                "recipient" => array(
                    "name" => $recipient_name,
                    "email" => $recipient_email
                ),
                "issued_on" => $issued_on,
                "expired_on" => $expired_on,
                "custom_attributes" => $custom_attributes
            )
        );

        $client = new Client();

        $params = array('Authorization' => 'Token token="' . $this->getApiKey() . '"');

        $response = $client->post($this->apiEndpoint . 'credentials', array(
            'headers' => $params,
            'json' => $data
        ));

        $result = json_decode($response->getBody());

        return $result;
    }

    /**
     * Creates a Credential given an existing Group. This legacy method uses achievement names rather than group IDs.
     *
     * @param string $recipient_name
     * @param string $recipient_email
     * @param string $achievement_name
     * @param string|null $issued_on
     * @param string|null $expired_on
     * @param string|null $course_name
     * @param string|null $course_description
     * @param string|null $course_link
     * @param \stdClass|null $custom_attributes
     *
     * @return \stdClass Response.
     */
    public function createCredentialLegacy(
        $recipient_name,
        $recipient_email,
        $achievement_name,
        $issued_on = null,
        $expired_on = null,
        $course_name = null,
        $course_description = null,
        $course_link = null,
        $custom_attributes = null
    ) {

        $data = array(
            "credential" => array(
                "group_name" => $achievement_name,
                "recipient" => array(
                    "name" => $recipient_name,
                    "email" => $recipient_email
                ),
                "issued_on" => $issued_on,
                "expired_on" => $expired_on,
                "custom_attributes" => $custom_attributes,
                "name" => $course_name,
                "description" => $course_description,
                "course_link" => $course_link
            )
        );

        $client = new Client();

        $params = array('Authorization' => 'Token token="' . $this->getApiKey() . '"');

        $response = $client->post($this->apiEndpoint . 'credentials', array(
            'headers' => $params,
            'json' => $data
        ));

        $result = json_decode($response->getBody());

        return $result;
    }

    /**
     * Updates a Credential.
     *
     * @param string|int $id
     * @param string|null $recipient_name
     * @param string|null $recipient_email
     * @param string|null $course_id
     * @param string|null $issued_on
     * @param string|null $expired_on
     * @param \stdClass|null $custom_attributes
     *
     * @return \stdClass Response.
     */
    public function updateCredential(
        $id,
        $recipient_name = null,
        $recipient_email = null,
        $course_id = null,
        $issued_on = null,
        $expired_on = null,
        $custom_attributes = null
    ) {

        $data = array(
            "credential" => array(
                "group_id" => $course_id,
                "recipient" => array(
                    "name" => $recipient_name,
                    "email" => $recipient_email
                ),
                "issued_on" => $issued_on,
                "expired_on" => $expired_on,
                "custom_attributes" => $custom_attributes
            )
        );
        $data = $this->stripEmptyKeys($data);

        $client = new Client();

        $params = array('Authorization' => 'Token token="' . $this->getApiKey() . '"');

        $response = $client->put($this->apiEndpoint . 'credentials/' . $id, array(
            'headers' => $params,
            'json' => $data
        ));

        $result = json_decode($response->getBody());

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function stripEmptyKeys($object)
    {

        $json = json_encode($object);
        $json = preg_replace('/,\s*"[^"]+":null|"[^"]+":null,?/', '', $json);
        $object = json_decode($json);

        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCredential($id)
    {
        $client = new Client();

        $options = array('headers' => array('Authorization' => 'Token token="' . $this->getApiKey() . '"'));
        $response = $client->delete($this->apiEndpoint . 'credentials/' . $id, $options);

        $result = json_decode($response->getBody());

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function createGroup($name, $course_name, $course_description, $course_link = null)
    {
        $data = array(
            "group" => array(
                "name" => $name,
                "course_name" => $course_name,
                "course_description" => $course_description,
                "course_link" => $course_link
            )
        );

        $client = new Client();

        $response = $client->post($this->apiEndpoint . 'issuer/groups', array(
            'headers' => array('Authorization' => 'Token token="' . $this->getApiKey() . '"'),
            'json' => $data
        ));

        $result = json_decode($response->getBody());

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroup($id)
    {
        $client = new Client();
        $options = array('headers' => array('Authorization' => 'Token token="' . $this->getApiKey() . '"'));
        $response = $client->get($this->apiEndpoint . 'issuer/groups/' . $id, $options);

        $result = json_decode($response->getBody());
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroups($page_size = null, $page = 1)
    {
        $client = new Client();
        $options = array(
            'headers' => array('Authorization' => 'Token token="' . $this->getApiKey() . '"'),
            'query' => array(
                'page_size' => $page_size,
                'page' => $page,
            ),
        );

        $response = $client->get($this->apiEndpoint . 'issuer/all_groups', $options);

        $result = json_decode($response->getBody());
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function updateGroup(
        $id,
        $name = null,
        $course_name = null,
        $course_description = null,
        $course_link = null,
        $design_id = null
    ) {

        $data = array(
            "group" => array(
                "name" => $name,
                "course_name" => $course_name,
                "course_description" => $course_description,
                "course_link" => $course_link,
                "design_id" => $design_id
            )
        );
        $data = $this->stripEmptyKeys($data);

        $client = new Client();

        $response = $client->put($this->apiEndpoint . 'issuer/groups/' . $id, array(
            'headers' => array('Authorization' => 'Token token="' . $this->getApiKey() . '"'),
            'json' => $data
        ));

        $result = json_decode($response->getBody());

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteGroup($id)
    {
        $client = new Client();

        $options = array('headers' => array('Authorization' => 'Token token="' . $this->getApiKey() . '"'));
        $response = $client->delete($this->apiEndpoint . 'issuer/groups/' . $id, $options);

        $result = json_decode($response->getBody());

        return $result;
    }

   /**
    * {@inheritdoc}
    */
    public function getDesigns($page_size = null, $page = 1)
    {
        $client = new Client();

        $options = array(
            'headers' => array('Authorization' => 'Token token="' . $this->getApiKey() . '"'),
            'query' => array(
                'page_size' => $page_size,
                'page' => $page,
            ),
        );
        $response = $client->get($this->apiEndpoint . 'issuer/all_designs?', $options);

        $result = json_decode($response->getBody());
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function createEvidenceItemGrade($grade, $description, $credential_id, $hidden = false)
    {

        if (is_numeric($grade) && intval($grade) >= 0 && intval($grade) <= 100) {
            $evidence_item = array(
                "evidence_item" => array(
                    "description" => $description,
                    "category" => "grade",
                    "string_object" => (string)$grade,
                    "hidden" => $hidden
                )
            );

            $result = $this->createEvidenceItem($evidence_item, $credential_id);

            return $result;
        } else {
            throw new \InvalidArgumentException("$grade must be a numeric value between 0 and 100.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createEvidenceItem($evidence_item, $credential_id)
    {

        $client = new Client();

        $response = $client->post($this->apiEndpoint . 'credentials/' . $credential_id . '/evidence_items', array(
            'headers' => array('Authorization' => 'Token token="' . $this->getApiKey() . '"'),
            'json' => $evidence_item
        ));

        $result = json_decode($response->getBody());

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function createEvidenceItemDuration($start_date, $end_date, $credential_id, $hidden = false)
    {

        $duration_info = array(
            'start_date' => date("Y-m-d", strtotime($start_date)),
            'end_date' => date("Y-m-d", strtotime($end_date)),
            'duration_in_days' => floor((strtotime($end_date) - strtotime($start_date)) / 86400)
        );

        // multi day duration
        if ($duration_info['duration_in_days'] && $duration_info['duration_in_days'] != 0) {
            $evidence_item = array(
                "evidence_item" => array(
                    "description" => 'Completed in ' . $duration_info['duration_in_days'] . ' days',
                    "category" => "course_duration",
                    "string_object" => json_encode($duration_info),
                    "hidden" => $hidden
                )
            );

            $result = $this->createEvidenceItem($evidence_item, $credential_id);

            return $result;
            // it may be completed in one day
        } elseif ($duration_info['start_date'] != $duration_info['end_date']) {
            $duration_info['duration_in_days'] = 1;

            $evidence_item = array(
                "evidence_item" => array(
                    "description" => 'Completed in 1 day',
                    "category" => "course_duration",
                    "string_object" => json_encode($duration_info),
                    "hidden" => $hidden
                )
            );

            $result = $this->createEvidenceItem($evidence_item, $credential_id);

            return $result;
        } else {
            throw new \InvalidArgumentException("Enrollment duration must be greater than 0.");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createEvidenceItemTranscript(array $transcript, $credential_id, $hidden = false)
    {

        $transcript_items = array();

        foreach ($transcript as $key => $value) {
            $transcript_items[] = array(
                'category' => $key,
                'percent' => $value
            );
        }

        $evidence_item = array(
            "evidence_item" => array(
                "description" => 'Course Transcript',
                "category" => "transcript",
                "string_object" => json_encode($transcript_items),
                "hidden" => $hidden
            )
        );

        $result = $this->createEvidenceItem($evidence_item, $credential_id);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function recipientSsoLink(
        $credential_id = null,
        $recipient_id = null,
        $recipient_email = null,
        $wallet_view = null,
        $group_id = null,
        $redirect_to = null
    ) {

        $data = array(
            "credential_id" => $credential_id,
            "recipient_id" => $recipient_id,
            "recipient_email" => $recipient_email,
            "wallet_view" => $wallet_view,
            "group_id" => $group_id,
            "redirect_to" => $redirect_to,
        );

        $data = $this->stripEmptyKeys($data);

        $client = new Client();

        $response = $client->post($this->apiEndpoint . 'sso/generate_link', array(
            'headers' => array('Authorization' => 'Token token="' . $this->getApiKey() . '"'),
            'json' => $data
        ));

        $result = json_decode($response->getBody());

        return $result;
    }

    /**
     * Send an array of batch requests.
     *
     * @param array $requests
     *
     * @return \stdClass Response.
     */
    public function sendBatchRequests($requests)
    {
        $client = new Client();

        $response = $client->post($this->apiEndpoint . 'batch', array(
            'headers' => array('Authorization' => 'Token token="' . $this->getApiKey() . '"'),
            'json' => array("ops" => $requests, "sequential" => true)
        ));

        $result = json_decode($response->getBody());

        return $result;
    }

    /**
     * Backward compatibility for underscore method calls.
     *
     * @param string $name
     * @param mixed $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        $method = static::dashesToCamelCase($name);
        if (!method_exists($this, $method)) {
            throw new \BadMethodCallException(sprintf('Call to undefined method %s::%s()', get_class($this), $method));
        }

        return call_user_func_array([$this, $method], $arguments);
    }

    /**
     * Convert Underscored string to camelCase.
     * https://stackoverflow.com/questions/2791998/convert-dashes-to-camelcase-in-php
     *
     * @param string $string
     * @param bool $capitalizeFirstCharacter
     * @return string
     */
    private static function dashesToCamelCase($string, $capitalizeFirstCharacter = false)
    {

        $str = str_replace('_', '', ucwords($string, '_'));

        if (!$capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }

        return $str;
    }
}
