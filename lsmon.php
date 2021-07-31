#!/usr/local/bin/php
<?php
    require 'vendor/autoload.php';
    require 'class.LSMonTool.php';
    require 'config_local.php';
    
    $tool = new \LSMon\LSMonTool();
    $tool->initDB($DBurl, $DBtoken, $DBorg, $DBbucket);

    $args = getopt("s:m:");
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
                $task = "getSNMPIfTraffic";
            break;
    }
    
    switch($task) {
        case "runDownloadTest" : 
                $host_list = explode(",","".$args["s"]);
                foreach ($host_list as $cur_host) {
                    $tool->runDownloadTest($serverList[$cur_host]."/".$fileName,$cur_host." - ".$fileSizeMB."MB",$hostname,$fileSizeMB);
                }
            break;
        case "getSNMPIfTraffic" : 
                $tool->getSNMPIfTraffic("10.0.0.1","lsmon",$hostname,"pppoe-AmobiaMetroFibre","AmobiaMetroFibre");
            break;
    }
?>
