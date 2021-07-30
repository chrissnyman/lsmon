<?php
    namespace LSMon;
    
    use InfluxDB2\Client;
    use InfluxDB2\Model\WritePrecision;
    use InfluxDB2\Point;


    /**
     * Class IndexDBCloudClient
     * @package LSMon
     */
    class IndexDBCloudClient //extends DBClient
    {
        /**
         * @var DBClient;
         */
        private $dbConnection;

        private $url;
        private $token;
        private $org;
        private $bucket;

        public function __construct($url, $token, $org, $bucket)
        {
            $this->url = $url;
            $this->token = $token;
            $this->org = $org;
            $this->bucket = $bucket;
        }

        public function connect()
        {
            $this->dbConnection = new \InfluxDB2\Client([
                "url" => $this->url,
                "token" => $this->token,
            ]);
            return true;
        }
        public function write($data)
        {
            $writeApi = $this->dbConnection->createWriteApi();
    
            return $writeApi->write($data, WritePrecision::S, $this->bucket, $this->org);
        }

        public function query($query)
        {   
            // $query = "from(bucket: \"lsmon\") |> range(start: -1h)";
            $tables = $this->dbConnection->createQueryApi()->query($query, $this->org);
            return $tables;
        }

    }

?>