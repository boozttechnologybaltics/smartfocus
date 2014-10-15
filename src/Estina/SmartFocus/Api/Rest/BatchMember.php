<?php

namespace Estina\SmartFocus\Api\Rest;

/**
 * Data Mass Update (Batch Member) REST API
 *
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class BatchMember extends AbstractRestService
{
    /**
     * @param string $server Web service host
     */
    public function setServer($server)
    {
        $this->setUrlPrefix(sprintf('https://%s/apibatchmember/services/rest', $server));
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
     * @return string|false
     */
    public function openConnection($server, $login, $password, $key)
    {
        $this->setServer($server);

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
     * @return string|false
     */
    public function closeConnection($token)
    {
        $response = $this->client->get(
            $this->getUrl("connect/close/$token")
        );

        return $response;
    }

    /**
     * Upload a File and Insert the Members
     *
     * This method uploads a file containing members and inserts them into the member table.
     *
     * @param string $token      Token
     * @param array  $filepath   Full path to the CSV file
     * @param string $dateformat Date format
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function insert($token, $filepath, $dateformat = 'dd/MM/yyyy')
    {
        if (!is_readable($filepath)) {
            throw new \InvalidArgumentException(sprintf('File %s is not readable', $filepath));
        }

        //Boundary seed
        $seed = $this->getBoundarySeed();

        $body = "--" . $seed . "\r\n";
        $body .= "Content-Disposition: form-data; name='insertUpload';\r\n";
        $body .= "Content-Type: text/xml\r\n\r\n";

        $body .= "<?xml version='1.0' encoding='UTF-8'?>\r\n";
        $body .= "<insertUpload>\r\n";
        $body .= "<fileName>" . $this->getFilename($filepath) . "</fileName>\r\n";
        $body .= "<fileEncoding>UTF-8</fileEncoding>\r\n";
        $body .= "<separator>" . $this->detectDelimiter($filepath) . "</separator>\r\n";
        $body .= "<dateFormat>" . $dateformat . "</dateFormat>\r\n";
        $body .= "<autoMapping>true</autoMapping>\r\n";
        $body .= "</insertUpload>\r\n";
        $body .= "--" . $seed . "\r\n";
        $body .= "Content-Disposition: form-data; name='inputStream';\r\n";
        $body .= "filename='" . $this->getFilename($filepath) . "'\r\n";
        $body .= "Content-Type: application/octet-stream\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= base64_encode(file_get_contents($filepath));
        $body .= "\r\n--" . $seed . "--\r\n";

        $response = $this->client->put(
            $this->getUrl("batchmemberservice/$token/batchmember/insertUpload"),
            array("Content-Type: multipart/form-data; boundary=" . $seed),
            $body
        );

        return $response;
    }

    /**
     * Return boundary seed
     *
     * @return string
     */
    private function getBoundarySeed()
    {
        return md5(mt_rand() . ' ' . microtime(true));
    }

    /**
     * @param string filepath
     *
     * @return string
     */
    private function getFilename($filepath)
    {
        return basename($filepath);
    }

    /**
     * @param string $file
     *
     * @return string - one of the following: , ; | tab
     */
    private function detectDelimiter($file)
    {
        $delimiters = array(',', ';', '|', "\t");
        $line = $this->getFirstLine($file);

        $count = 0;
        $result = $delimiters[0];
        foreach ($delimiters as $index => $delimiter) {
            $delimiterCount = substr_count($line, $delimiter);
            if ($delimiterCount > $count) {
                $count = $delimiterCount;
                $result = $delimiter;
            }
        }

        if ("\t" == $result) {
            $result = 'tab';
        }

        return $result;
    }

    /**
     * @param string $file
     *
     * @return string
     */
    private function getFirstLine($file)
    {
        $f = fopen($file, 'r');
        $result = fgets($f);
        fclose($f);

        return $result;
    }
}
