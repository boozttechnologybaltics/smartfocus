<?php

namespace Estina\SmartFocus\Api\Rest;

/**
 * Individual Member Management REST API
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class Member extends AbstractRestService
{
    /**
     * @param string $server Web service host
     */
    public function setServer($server)
    {
        $this->setUrlPrefix(sprintf('https://%s/apimember/services/rest', $server));
    }

    /**
     * This method provides a session token when given valid credentials
     *
     * The token is valid for 60 minutes. To avoid problems with expired
     * tokens, it is recommended that you close your connection after an API
     * call and open a new connection for a new API call.
     *
     * @param string $server   Web service host
     * @param string $login    The login provided for API access
     * @param string $password API password
     * @param string $key      The manager key copied from SmartFocus
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function openConnection($server, $login, $password, $key)
    {
        $this->setServer($server);

        $password = urlencode($password);

        $response = $this->client->get(
            $this->getUrl("connect/open/$login/$password/$key")
        );

        return $response;
    }

    /**
     * This method terminates the session token
     *
     * @param string $token token
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function closeConnection($token)
    {
        $response = $this->client->get(
            $this->getUrl("connect/close/$token")
        );

        return $response;
    }

    /**
     * Inserts a new member
     *
     * This method inserts a new member using only the email address as input
     * (i.e. all other fields will remain empty).
     *
     * @param string $token Token
     * @param string $email Email
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function insertMemberByEmailAddress($token, $email)
    {
        $response = $this->client->get(
            $this->getUrl("member/insert/$token/$email")
        );

        return $response;
    }

    /**
     * Inserts a new member
     *
     * This method inserts a new member using the email address as input and
     * providing profile data for other fields of the Member table.
     *
     * @param string $token Token
     * @param string $xml   XML input body
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function insertMember($token, $xml)
    {
        $response = $this->client->post(
            $this->getUrl("member/insertMember/$token"),
            $xml
        );

        return $response;
    }

    /**
     * Updates a member(s)
     *
     * This method updates multiple fields of the member(s). Any criteria can
     * be used to find the member(s) including one of the fields to be updated.
     *
     * Note: It should be noted that if the criteria used is the same for
     * multiple members, all of these members will be updated.
     *
     * @param string $token Token
     * @param string $xml   XML input body
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function updateMember($token, $xml)
    {
        $response = $this->client->post(
            $this->getUrl("member/updateMember/$token"),
            $xml
        );

        return $response;
    }

    /**
     * Inserts or updates member data
     *
     * This method searches a specified column of the Member table for a
     * particular value used to identify a member in order to update the
     * member's data. If the member is not found, a new member is created
     * (provided that the email address is given). Any criteria can be used to
     * find the member including one of the fields to be updated.
     *
     * Note: It should be noted that if the criteria used is the same for
     * multiple members, all of these members will be updated.
     *
     * @param string $token Token
     * @param string $xml   XML input body
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function insertOrUpdateMember($token, $xml)
    {
        $response = $this->client->post(
            $this->getUrl("member/insertOrUpdateMember/$token"),
            $xml
        );

        return $response;
    }

    /**
     * Updates member by email address
     *
     * This method updates a single field of a member in the Member table using
     * the email address to identify the member. To update another field,
     * another call should be made.
     *
     * Note: If multiple members share the same email address, the field will
     * be updated for all members.
     *
     * @param string $token Token
     * @param string $email Email address
     * @param string $field The field that will be updated.
     * @param string $value The value with which to update the field
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function updateMemberByEmailAddress($token, $email, $field, $value)
    {
        $response = $this->client->get(
            $this->getUrl("member/update/$token/$email/$field/$value")
        );

        return $response;
    }

    /**
     * Retrieves member job status
     *
     * This method retrieves the job status (i.e. the status of the member
     * insertion or update) using the job ID.
     *
     * @param string $token Token
     * @param int    $jobId The job ID
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function getMemberJobStatus($token, $jobId)
    {
        $response = $this->client->get(
            $this->getUrl("member/getMemberJobStatus/$token/$jobId")
        );

        return $response;
    }

    /**
     * Retrieves member(s) by email address
     *
     * This method retrieves a list of members who have the given email address
     *
     * @param string $token Token
     * @param string $email Email
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function getMemberByEmail($token, $email)
    {
        $response = $this->client->get(
            $this->getUrl("member/getMemberByEmail/$token/$email")
        );

        return $response;
    }

    /**
     * Retrieves member(s) by cell phone
     *
     * This method retrieves a list of members who have the given cellphone
     * number
     *
     * @param string $token     Token
     * @param string $cellphone The cellphone number of the member
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function getMemberByCellphone($token, $cellphone)
    {
        $response = $this->client->get(
            $this->getUrl("member/getMemberByCellphone/$token/$cellphone")
        );

        return $response;
    }

    /**
     * Retrieves member by ID
     *
     * This method uses the member ID to retrieve the details of a member
     *
     * @param string $token    Token
     * @param int    $memberId The ID of the member you want to retrieve
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function getMemberById($token, $memberId)
    {
        $response = $this->client->get(
            $this->getUrl("member/getMemberById/$token/$memberId")
        );

        return $response;
    }

    /**
     * Retrieves members by page
     *
     * This method retrieves all members page by page.
     * Each page contains 10 members
     *
     * @param string $token Token
     * @param int    $page  The page number
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function getMembersByPage($token, $page)
    {
        $response = $this->client->get(
            $this->getUrl("member/getListMembersByPage/$token/$page")
        );

        return $response;
    }

    /**
     * Retrieves members by criteria
     *
     * This method retrieves a list of members who match the given criteria
     *
     * @param string $token Token
     * @param string $xml   XML input body
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function getMembersByCriteria($token, $xml)
    {
        $response = $this->client->post(
            $this->getUrl("member/getMembers/$token"),
            $xml
        );

        return $response;
    }

    /**
     * Retrieves member table column names
     *
     * This method retrieves the list of fields (i.e. database column names)
     * available in the Member table
     *
     * @param string $token Token
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function getMemberTableColumnNames($token)
    {
        $response = $this->client->get(
            $this->getUrl("member/descMemberTable/$token")
        );

        return $response;
    }

    /**
     * Unsubscribes member by email address
     *
     * This method unsubscribes the member(s) matching the given email address
     *
     * @param string $token Token
     * @param string $email Email
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function unsubscribeMemberByEmail($token, $email)
    {
        $response = $this->client->get(
            $this->getUrl("member/unjoinByEmail/$token/$email")
        );

        return $response;
    }

    /**
     * Unsubscribes member(s) by cell phone
     *
     * This method unsubscribes one or more members who match a given
     * cellphone number
     *
     * @param string $token     Token
     * @param string $cellphone The cellphone number of the member
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function unsubscribeMemberByCellphone($token, $cellphone)
    {
        $response = $this->client->get(
            $this->getUrl("member/unjoinByCellphone/$token/$cellphone")
        );

        return $response;
    }

    /**
     * Unsubscribes member by ID
     *
     * This method unsubscribes the member matching the given ID
     *
     * @param string $token    Token
     * @param int    $memberId The ID of the member you want to retrieve
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function unsubscribeMemberById($token, $memberId)
    {
        $response = $this->client->get(
            $this->getUrl("member/unjoinByMemberId/$token/$memberId")
        );

        return $response;
    }

    /**
     * Unsubscribes member(s) by value
     *
     * This method unsubscribes one or more members who match the given criteria
     *
     * @param string $token Token
     * @param string $xml   XML input body
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function unsubscribeMemberByValue($token, $xml)
    {
        $response = $this->client->post(
            $this->getUrl("member/unjoinMember/$token"),
            $xml
        );

        return $response;
    }

    /**
     * Resubscribes member by email address
     *
     * This method re-subscribes an unsubscribed member using his/her email
     * address. If there are multiple members with the same email address,
     * they will all be re-subscribed
     *
     * Note: The number of rejoins per day is limited to 50 per day to avoid
     * massive rejoins and illegal usage of this method. When the limit is
     * reached, you will receive the following error message:
     * "You have reached the maximum number of rejoins per day."
     *
     * @param string $token Token
     * @param string $email Email
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function resubscribeMemberByEmail($token, $email)
    {
        $response = $this->client->get(
            $this->getUrl("member/rejoinByEmail/$token/$email")
        );

        return $response;
    }

    /**
     * Resubscribes member(s) by cell phone
     *
     * Re-subscribes an unsubscribed member using his/her cell phone number.
     * If there are multiple members with the same number, they will all be
     * re-subscribed
     *
     * Note: The number of rejoins per day is limited to 50 per day to avoid
     * massive rejoins and illegal usage of this method. When the limit is
     * reached, you will receive the following error message:
     * "You have reached the maximum number of rejoins per day."
     *
     * @param string $token     Token
     * @param string $cellphone The cellphone number of the member
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function resubscribeMemberByCellphone($token, $cellphone)
    {
        $response = $this->client->get(
            $this->getUrl("member/rejoinByCellphone/$token/$cellphone")
        );

        return $response;
    }

    /**
     * Resubscribes member by ID
     *
     * This method re-subscribes the member matching the given ID
     *
     * Note: The number of rejoins per day is limited to 50 per day to avoid
     * massive rejoins and illegal usage of this method. When the limit is
     * reached, you will receive the following error message:
     * "You have reached the maximum number of rejoins per day."
     *
     * @param string $token    Token
     * @param int    $memberId The ID of the member you want to retrieve
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function resubscribeMemberById($token, $memberId)
    {
        $response = $this->client->get(
            $this->getUrl("member/rejoinByMemberId/$token/$memberId")
        );

        return $response;
    }
}
