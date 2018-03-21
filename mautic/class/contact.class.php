<?php

/**
 * Contacts Class
 *
 * @package     Minha BVS - Alertas
 * @author      Wilson da Silva Moura (mourawil@paho.org)
 * @copyright   BIREME/PAHO/WHO
 *
 */

use Mautic\MauticApi;

class Contacts {
    /**
     * Mautic API context.
     *
     * @access protected
     * @var object
     */
    protected $api;

    /**
     * Contacts API context.
     *
     * @access protected
     * @var object
     */
    protected $contactApi;

    /**
     * Create api context by passing in the Contacts context, the $auth
     * object from below and the base URL to the Mautic server
     */
    public function __construct() {
        global $auth;
        global $apiURL;

        $this->api = new MauticApi();
        $this->contactApi = $this->api->newApi('contacts', $auth, $apiURL);
    }

    /**
     * Get contact data from Mautic.
     *
     * @param int $contactID Contact ID
     * @return array
     */
    public function getContact($contactID) {
        $contact = $this->contactApi->get($contactID);
        return $contact;
    }

    /**
     * Get contact list from Mautic.
     *
     * @param string $search String or search command to filter entities by.
     * @param int $start Starting row for the entities returned. Defaults to 0.
     * @param int $limit Limit number of entities to return. Defaults to the system configuration for pagination (30).
     * @param string $orderBy Column to sort by. Can use any column listed in the response.
     * @param string $orderByDir Sort direction: asc or desc.
     * @param bool $publishedOnly Only return currently published entities.
     * @param bool $minimal Return only array of entities without additional lists in it.
     * @return array
     */
    public function listContacts($searchFilter, $start=0, $limit=0, $orderBy='firstname', $orderByDir='asc', $publishedOnly=FALSE, $minimal=TRUE) {
        $contacts = $this->contactApi->getList($searchFilter, $start, $limit, $orderBy, $orderByDir, $publishedOnly, $minimal);
        return $contacts;
    }

    /**
     * Create contact in Mautic.
     *
     * @param array $user User data
     * @param boolean $mautic_format Mautic format data
     * @return array
     */
    public function createContact($user, $mautic_format=FALSE) {
        if ( !$mautic_format ) {
            $user = array(
                'firstname'   => $user['userFirstName'],
                'lastname'    => $user['userLastName'],
                'email'       => ( empty($user['userEMail']) ) ? $user['userID'] : $user['userEMail'],
                'country'     => $user['userCountry'],
                'send_alert'  => TRUE,
                'my_vhl_user' => TRUE,
                'my_vhl_id'   => $user['sysUID']
            );
        }

        $contact = $this->contactApi->create($user);
        
        return $contact;
    }

    /**
     * Create a batch of contacts.
     *
     * @param array $parameters
     * @return array|mixed
     */
    public function createBatch($parameters) {
        $contacts = $this->contactApi->createBatch($parameters);
        return $contacts;
    }

    /**
     * Edit contact in Mautic.
     *
     * @param array $user User data
     * @return array
     */
    public function editContact($id, $user, $createIfNotExists=FALSE) {
        $contact = $this->contactApi->edit($id, $user, $createIfNotExists);
        return $contact;
    }

    /**
     * Edit a batch of contacts.
     *
     * @param array $parameters
     * @param bool  $createIfNotExists
     * @return array|mixed
     */
    public function editBatch($parameters, $createIfNotExists=FALSE) {
        $contacts = $this->contactApi->editBatch($parameters, $createIfNotExists);
        return $contacts;
    }

    /**
     * Delete contact in Mautic.
     *
     * @param int $contactID Contact ID
     * @return array
     */
    public function deleteContact($contactID) {
        $contact = $this->contactApi->delete($contactID);
        return $contact;
    }

    /**
     * Delete a batch of contacts.
     *
     * @param $ids
     * @return array|mixed
     */
    public function deleteBatch($ids) {
        $contacts = $this->contactApi->deleteBatch($ids);
        return $contacts;
    }
}