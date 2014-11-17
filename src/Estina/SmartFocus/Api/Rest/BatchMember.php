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
     * Builds XML string for insert function
     *
     * @param array  $filepath   Full path to the CSV file
     * @param string $dateformat Date format
     * @param bool   $dedup      Skip duplicates (default: true)
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function buildInsertXml($filepath, $dateformat = 'yyyy-MM-dd', $dedup = true)
    {
        if (!is_readable($filepath)) {
            throw new \InvalidArgumentException(sprintf('File %s is not readable', $filepath));
        }

        //Boundary seed
        $seed = $this->getBoundarySeed();
        $delimiter = $this->detectDelimiter($filepath);

        $xml = "--" . $seed . "\r\n";
        $xml .= "Content-Type: text/xml\r\n";
        $xml .= "Content-Disposition: form-data; name='insertUpload'\r\n\r\n";

        $xml .= "<?xml version='1.0' encoding='UTF-8'?>\r\n";
        $xml .= "<insertUpload>\r\n";
        $xml .= "<fileName>" . $this->getFilename($filepath) . "</fileName>\r\n";
        $xml .= "<fileEncoding>UTF-8</fileEncoding>\r\n";
        $xml .= "<separator>" . ("\t" == $delimiter) ? 'tab' : $delimiter . "</separator>\r\n";
        $xml .= "<dateFormat>" . $dateformat . "</dateFormat>\r\n";
        $xml .= "<autoMapping>true</autoMapping>\r\n";

        if ($dedup) {
            $xml .= "<dedup>\r\n";
            $xml .= "<criteria>LOWER(EMAIL)</criteria>\r\n";
            $xml .= "<order>first</order>\r\n";
            $xml .= "<skipUnsubAndHBQ>true</skipUnsubAndHBQ>\r\n";
            $xml .= "</dedup>\r\n";
        }

        $xml .= "</insertUpload>\r\n";
        $xml .= "--" . $seed . "\r\n";
        $xml .= "Content-Type: application/octet-stream\r\n";
        $xml .= "Content-Disposition: form-data; name='inputStream'; filename='" . $this->getFilename($filepath) . "'\r\n";
        $xml .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $xml .= base64_encode(file_get_contents($filepath));
        $xml .= "\r\n--" . $seed . "--\r\n";

        return $xml;
    }

    /**
     * Upload a File and Insert the Members
     *
     * This method uploads a file containing members and inserts them into the member table.
     *
     * @param string $token Token
     * @param string $xml   XML body
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function insert($token, $xml)
    {
        $seed = $this->extractSeed($xml);
        if (empty($seed)) {
            throw new \InvalidArgumentException('Cannot find boundary seed, invalid XML');
        }

        $response = $this->client->put(
            $this->getUrl("batchmemberservice/$token/batchmember/insertUpload"),
            array("Content-Type: multipart/form-data; boundary=" . $seed),
            $xml
        );

        return $response;
    }

    /**
     * Builds XML string for update function
     *
     * @param array  $filepath   Full path to the CSV file
     * @param string $dateformat Date format
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function buildUpdateXml($filepath, $dateformat = 'yyyy-MM-dd')
    {
        if (!is_readable($filepath)) {
            throw new \InvalidArgumentException(sprintf('File %s is not readable', $filepath));
        }

        //Boundary seed
        $seed = $this->getBoundarySeed();
        $delimiter = $this->detectDelimiter($filepath);

        $xml = "--" . $seed . "\r\n";
        $xml .= "Content-Type: text/xml\r\n";
        $xml .= "Content-Disposition: form-data; name='mergeUpload'\r\n\r\n";

        $xml .= "<?xml version='1.0' encoding='UTF-8'?>\r\n";
        $xml .= "<mergeUpload>\r\n";
        $xml .= "<fileName>" . $this->getFilename($filepath) . "</fileName>\r\n";
        $xml .= "<fileEncoding>UTF-8</fileEncoding>\r\n";
        $xml .= "<separator>" . (("\t" == $delimiter) ? 'tab' : $delimiter) . "</separator>\r\n";
        $xml .= "<dateFormat>" . $dateformat . "</dateFormat>\r\n";
        $xml .= "<criteria>LOWER(EMAIL)</criteria>\r\n";
        $xml .= "<mapping>\r\n";

        $fields = $this->getFirstLine($filepath);
        $fields = trim($fields);
        $fields = explode($delimiter, $fields);
        foreach ($fields as $col => $field) {
            $xml .= sprintf(
                "<column><colNum>%d</colNum><fieldName>%s</fieldName>%s</column>\r\n",
                ($col+1),
                $field,
                ('EMAIL' === $field) ? '' : '<toReplace>true</toReplace>'
            );
        }

        $xml .= "</mapping>\r\n";
        $xml .= "</mergeUpload>\r\n";
        $xml .= "--" . $seed . "\r\n";
        $xml .= "Content-Type: application/octet-stream\r\n";
        $xml .= "Content-Disposition: form-data; name='inputStream'; filename='" . $this->getFilename($filepath) . "'\r\n";
        $xml .= "Content-Transfer-Encoding: base64\r\n\r\n";

        // we need to drop the first row, as it's only used for mapping
        $content = file_get_contents($filepath);
        $content = substr($content, (strpos($content, "\n") + 1));

        $xml .= base64_encode($content);
        $xml .= "\r\n--" . $seed . "--\r\n";

        return $xml;
    }

    /**
     * Upload a File and Merge the Members with the Existing Members
     *
     * This method uploads a file containing members and merges them with those in the member table.
     *
     * @param string $token Token
     * @param string $xml   XML body
     *
     * @return mixed - XML string or FALSE on failure
     */
    public function update($token, $xml)
    {
        $seed = $this->extractSeed($xml);
        if (empty($seed)) {
            throw new \InvalidArgumentException('Cannot find boundary seed, invalid XML');
        }

        $response = $this->client->put(
            $this->getUrl("batchmemberservice/$token/batchmember/mergeUpload"),
            array("Content-Type: multipart/form-data; boundary=" . $seed),
            $xml
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
     * @param string $xml
     *
     * @return string
     */
    private function extractSeed($xml)
    {
        $seed = substr($xml, 2, (strpos($xml, "\r\n")));

        return trim($seed);
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
