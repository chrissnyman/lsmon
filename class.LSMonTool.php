<?php
    namespace LSMon;
    
    use InfluxDB2\Client;
    use InfluxDB2\Model\WritePrecision;
    use InfluxDB2\Point;
    
    require 'class.IndexDBCloudClient.php';
    
    /**
     * Class LSMonTool
     * @package LSMon
     */
    class LSMonTool
    {
        /**
         * @var DBClient;
         */
        private $dbConnection;

        public function __construct($DBurl, $DBtoken, $DBorg, $DBbucket)
        {
            $this->dbConnection = new IndexDBCloudClient($DBurl, $DBtoken, $DBorg, $DBbucket);
        }

        public function init()
        {
            $this->dbConnection->connect();

        }

        public function runDownloadTest($path, $group_string, $host, $size = 1)
        {
            echo "runDownloadTest: $path";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $path);
            $task_start_time = microtime(true);
            $start_time = round(microtime(true),2);
            curl_exec($ch);
            $end_time = round(microtime(true),2);
            $duration = $end_time - $start_time;
            curl_close($ch);
            $mbps = round($size*8/$duration,3);
            $data = Point::measurement('download')
                ->addTag("host=".$host, $group_string)
                ->addField('mbps', $mbps)
                ->time($task_start_time);
            $this->dbConnection->write($data);
        }

        public function getSNMPIfTraffic($ip,$interface,$description)
        {

        }

        public function query($query)
        {   
            $tables = $this->dbConnection->createQueryApi()->query($query, $this->org);
            return $tables;
        }

    }

?>