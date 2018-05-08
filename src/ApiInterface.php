<?php

namespace ACMS;

/**
 * Interface ApiInterface.
 *
 * @package ACMS
 */
interface ApiInterface
{

    /**
     * Get a Credential.
     *
     * @param string $id
     *
     * @return \stdClass Response.
     */
    public function getCredential($id);

    /**
     * Get API Key.
     *
     * @return string
     */
    public function getApiKey();

    /**
     * Set API Key.
     *
     * @param string $key
     */
    public function setApiKey($key);

    /**
     * Get Credentials.
     *
     * @param string|null $group_id
     * @param string|null $email
     * @param string|null $page_size
     * @param string|int $page
     *
     * @return \stdClass Response.
     */
    public function getCredentials($group_id = null, $email = null, $page_size = null, $page = 1);

    /**
     * Creates a Credential given an existing Group.
     *
     * @param string $recipient_name
     * @param string $recipient_email
     * @param string $course_id
     * @param string|null $issued_on
     * @param string|null $expired_on
     * @param \stdClass|null $custom_attributes
     *
     * @return \stdClass Response.
     */
    public function createCredential(
        $recipient_name,
        $recipient_email,
        $course_id,
        $issued_on = null,
        $expired_on = null,
        $custom_attributes = null
    );

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
    );

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
    );

    /**
     * Strip out keys with a null value from an object http://stackoverflow.com/a/15953991.
     *
     * @param array|\stdClass $object
     *
     * @return \stdClass
     */
    public function stripEmptyKeys($object);

    /**
     * Delete a Credential.
     *
     * @param string $id
     *
     * @return \stdClass Response.
     */
    public function deleteCredential($id);

    /**
     * Create a new Group.
     *
     * @param string $name
     * @param string $course_name
     * @param string $course_description
     * @param string|null $course_link
     *
     * @return \stdClass Response.
     */
    public function createGroup($name, $course_name, $course_description, $course_link = null);

    /**
     * Get a Group.
     *
     * @param string $id
     *
     * @return \stdClass Response.
     */
    public function getGroup($id);

    /**
     * Get all Groups.
     *
     * @param string $page_size
     * @param string|int $page
     *
     * @return \stdClass Response.
     */
    public function getGroups($page_size = null, $page = 1);

    /**
     * Update a Group.
     *
     * @param string $id
     * @param string|null $name
     * @param string|null $course_name
     * @param string|null $course_description
     * @param string|null $course_link
     * @param string|null $design_id
     *
     * @return \stdClass Response.
     */
    public function updateGroup(
        $id,
        $name = null,
        $course_name = null,
        $course_description = null,
        $course_link = null,
        $design_id = null
    );

    /**
     * Delete a Group.
     *
     * @param string $id
     *
     * @return \stdClass Response.
     */
    public function deleteGroup($id);

    /**
     * Get all Designs.
     *
     * @param string $page_size
     * @param string|int $page
     *
     * @return \stdClass Response.
     */
    public function getDesigns($page_size = null, $page = 1);

    /**
     * Creates a Grade evidence item on a given credential.
     *
     * @param string $grade - value must be between 0 and 100
     * @param string $description
     * @param string $credential_id
     * @param bool $hidden
     *
     * @return \stdClass Response.
     */
    public function createEvidenceItemGrade($grade, $description, $credential_id, $hidden = false);

    /**
     * Creates an evidence item on a given credential.
     * This is a general method used by more specific evidence item creations.
     *
     * @param array|\stdClass $evidence_item
     * @param $credential_id
     *
     * @return \stdClass Response.
     */
    public function createEvidenceItem($evidence_item, $credential_id);

    /**
     * Creates a Grade evidence item on a given credential.
     *
     * @param string $start_date
     * @param string $end_date
     * @param string $credential_id
     * @param bool $hidden
     *
     * @return \stdClass Response.
     */
    public function createEvidenceItemDuration($start_date, $end_date, $credential_id, $hidden = false);

    /**
     * Creates a Transcript evidence item on a given credential.
     *
     * @param array $transcript - Hash of key values.
     * @param string $credential_id
     * @param bool $hidden
     *
     * @return \stdClass Response.
     */
    public function createEvidenceItemTranscript(array $transcript, $credential_id, $hidden = false);

    /**
     * Generate a Single Sign On Link for a recipient for a particular credential.
     * @param string|null $credential_id
     * @param string|null $recipient_id
     * @param string|null $recipient_email
     * @param string|null $wallet_view
     * @param string|null $group_id
     * @param string|null $redirect_to
     *
     * @return \stdClass Response.
     */
    public function recipientSsoLink(
        $credential_id = null,
        $recipient_id = null,
        $recipient_email = null,
        $wallet_view = null,
        $group_id = null,
        $redirect_to = null
    );

    /**
     * Send an array of batch requests.
     *
     * @param array $requests
     *
     * @return \stdClass Response.
     */
    public function sendBatchRequests($requests);
}
