<?php

require_once 'Autoloader.php';

use Db\Db;

const WEB_APP_VERSION = "1.0.0-rel-debug";
const WEB_APP_NAME = "Aergibide";
const WEB_APP_DOMAIN = "aergibide.lph.local";

const DB_HOST = "db";
const DB_USER = "docker";
const DB_DATABASE = "docker";
const DB_PASSWORD = "CHANGE_ME";
const EMAIL_API_KEY = "ESTA_EN_LA_DOCUMENTACION";

const APP_ROOT = __DIR__ . '/';
// !!!!!!!!!!!!!!! PONER EN FALSE EN PRODUCCION !!!!!!!!!!!!!!!!!!!!!
const DEBUG_MODE = true;

if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

Db::setInstance(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

function getBuildInfo(): array
{
    $branch = getCurrentBranch();
    $commit = getCurrentCommitHash();
    $commitDate = getCurrentBranchDatetime($branch);

    return [
        'branch' => $branch,
        'commit' => $commit,
        'commitDate' => $commitDate,
        'buildName' => WEB_APP_NAME." v".WEB_APP_VERSION."-$branch-$commit ($commitDate)",
    ];
}

function getCurrentBranch(): string
{
    $data = file_get_contents(APP_ROOT.'.git/HEAD');
    $ar  = explode( "/", $data );
    $ar = array_reverse($ar);
    return  trim ("" . @$ar[0]) ;
}

function getCurrentBranchDatetime($branch='master' ): string
{
    $fname = sprintf( APP_ROOT.'.git/refs/heads/%s', $branch );
    $time = filemtime($fname);
    if($time != 0 ){
        return date("Y-m-d H:i:s", $time);
    }else{
        return  date("Y-m-d H:i:s", time());
    }
}

function getCurrentCommitHash(): string
{
    $path = APP_ROOT.'.git/';

    $head = trim(substr(file_get_contents($path . "HEAD"), 4));

    return trim(substr(file_get_contents($path . $head), 0, 7));
}