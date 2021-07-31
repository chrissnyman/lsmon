#!/usr/local/bin/php
<?php
    require 'vendor/autoload.php';
    require 'class.LSMonTool.php';
    require 'config_local.php';
    
    $tool = new \LSMon\LSMonTool($DBurl, $DBtoken, $DBorg, $DBbucket);
    $tool->init();

    $args = getopt("s:m:");
    switch ($args["s"]) {
        case "portal": $server_name = "https://portal.amobia.com/temp";
            break;
        case "jhbmon": $server_name = "http://jhb-monitoring.amobia.com/temp";
            break;
        default: $server_name = "https://portal.amobia.com/temp";
            break;
    }

    switch ($args["m"]) {
        case "dl10": $tool->runDownloadTest($server_name."/10mb.tmp",$args["s"]." - 10mb",$hostname,10);
            break;
        case "dl100": $tool->runDownloadTest($server_name."/100mb.tmp",$args["s"]." - 100mb",$hostname,100);
            break;
        case "dl500": $tool->runDownloadTest($server_name."/500mb.tmp",$args["s"]." - 500mb",$hostname,500);
            break;

        case "iftraf":
                $tool->getSNMPIfTraffic("10.0.0.1","ether1","cpe - traffic download");
            break;
    }
    // $tool->runDownloadTest("https://portal.amobia.com/temp/100mb.tmp","portal - 100mb",100);
?>
