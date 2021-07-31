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

        public function __construct()
        {
        }
        
        public function initDB($DBurl, $DBtoken, $DBorg, $DBbucket)
        {
            $this->dbConnection = new IndexDBCloudClient($DBurl, $DBtoken, $DBorg, $DBbucket);
            $this->dbConnection->connect();
        }

        public function runDownloadTest($path, $group_string, $host, $size = 1)
        {
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

        public function getSNMPIfTraffic($ipAddress, $community, $hostname, $interface, $interfaceDesc)
        {
            $task_start_time = microtime(true);
            $interfaceList = snmprealwalk($ipAddress, $community, "1.3.6.1.2.1.2.2.1.2",500000,3);
            foreach ($interfaceList as $interfaceOID => $interfaceName) {
                if ( $interfaceName == "STRING: \"$interface\"") {
                    $oidParts = explode(".",$interfaceOID);
                    $intID = $oidParts[count($oidParts)-1];

                    $inOctetParts = explode(": ", snmpget($ipAddress, $community, ".1.3.6.1.2.1.31.1.1.1.10.".$intID,500000,3));
                    $inOctet = $inOctetParts[1];
                    if ($inOctet == NULL) { $inOctet = 0; } else { $inOctet = floatval($inOctet); }
                    
                    $outOctetsParts = explode(": ", snmpget($ipAddress, $community, ".1.3.6.1.2.1.31.1.1.1.6.".$intID,500000,3));
                    $outOctets = $outOctetsParts[1];
                    if ($outOctets == NULL) { $outOctets = 0; } else { $outOctets = floatval($outOctets); }

                    $data = Point::measurement('iftraf')
                    ->addTag("host=".$hostname, $interfaceDesc." - Upload")
                    ->addField('Bytes', $inOctet*8)
                    ->time($task_start_time);
                    $this->dbConnection->write($data);

                    $data = Point::measurement('iftraf')
                    ->addTag("host=".$hostname, $interfaceDesc." - Download")
                    ->addField('Bytes', $outOctets*8)
                    ->time($task_start_time);
                    $this->dbConnection->write($data);
                }
            }
        }

        public function query($query)
        {   
            $tables = $this->dbConnection->createQueryApi()->query($query, $this->org);
            return $tables;
        }

    }

?>