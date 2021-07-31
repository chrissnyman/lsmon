#!/usr/local/bin/php
<?php
    require 'vendor/autoload.php';
    require 'class.LSMonTool.php';
    require 'config_local.php';
    
    $tool = new \LSMon\LSMonTool($DBurl, $DBtoken, $DBorg, $DBbucket);
    $tool->init();

    $args = getopt("s:m:");
    $host_list = explode(",","".$args["s"]);
    $task = "";
    switch ($args["m"]) {
        case "dl10": 
                $task = "runDownloadTest";
                $fileName = "10mb.tmp";
                $fileSizeMB = 10;
            break;
        case "dl100": 
                $task = "runDownloadTest";
                $fileName = "100mb.tmp";
                $fileSizeMB = 100;
            break;
        case "dl500": 
                $task = "runDownloadTest";
                $fileName = "500mb.tmp";
                $fileSizeMB = 500;
            break;

        case "iftraf":
                $tool->getSNMPIfTraffic("10.0.0.1","ether1","cpe - traffic download");
            break;
    }
    
    switch($task) {
        case "runDownloadTest" : 
                foreach ($host_list as $cur_host) {
                    switch ($cur_host) {
                        case "portal": 
                                $serverName = "https://portal.amobia.com/temp";
                            break;
                        case "jhbmon": 
                                $serverName = "http://jhb-monitoring.amobia.com/temp";
                            break;
                        default: 
                                $serverName = "https://portal.amobia.com/temp";
                            break;
                    }
                    $tool->runDownloadTest($serverName."/".$fileName,$args["s"]." - ".$fileSizeMB."MB",$hostname,$fileSizeMB);
                }
            break;
        case "getSNMPIfTraffic" : 
            $tool->getSNMPIfTraffic("10.0.0.1","ether1","cpe - traffic download");
            break;
    }
    // $tool->runDownloadTest("https://portal.amobia.com/temp/100mb.tmp","portal - 100mb",100);
?>
