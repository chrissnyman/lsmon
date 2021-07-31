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
                    $tool->runDownloadTest($serverList[$cur_host]."/".$fileName,$cur_host." - ".$fileSizeMB."MB",$hostname,$fileSizeMB);
                }
            break;
        case "getSNMPIfTraffic" : 
            $tool->getSNMPIfTraffic("10.0.0.1","ether1","cpe - traffic download");
            break;
    }
    // $tool->runDownloadTest("https://portal.amobia.com/temp/100mb.tmp","portal - 100mb",100);
?>
