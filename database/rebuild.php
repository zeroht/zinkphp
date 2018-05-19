<?php
/**
 * 导出最新的数据库结构
 * 1.生成 db.json 数据库结构文件;
 * 2.生成 db.sql 数据库建库sql文件
 * 3.生成默认的Pdo及Model类文件
 *
 * @author:  thu
 * @version: 1.0.0
 * @change:
 *    1. 2016/6/13 @thu: 创建；
 */

define('APP_NAME', 'Dbbuilder');
define('PROJECT_NAME', 'Bingo');
define('SOURCE_DIR', dirname(__FILE__) . "/../../source/");

require_once SOURCE_DIR. "bootstrap.php";

use Zink\Widget\FileGenerator;
use Zink\Db\DB;
use Zink\Db\Result\Collection;

function buildDatabaseData()
{
    $db = DB::create();
    $tableInfos = $db->runSql("SHOW TABLE STATUS");
    $data = array();
    $sql = array();
    if ($tableInfos instanceof Collection){
        $data = $tableInfos->toArray();
        foreach ($data as $key => &$table){
            $tableName = $table['Name'];
            if (in_array($tableName, ['question_dirty','icp_user'])){
                unset($data[$key]);
                continue;
            }

            $columns = $db->runSql("SHOW FULL FIELDS FROM $tableName");
            $table['Columns'] = $columns->toArray();

            $createSql = $db->runSql("SHOW CREATE TABLE $tableName");
            $sqlData = $createSql->toArray();

            $create_sql = preg_replace('/AUTO_INCREMENT=\d+ /i', '', $sqlData[0]['Create Table']).";";
            $table['Create_sql'] = $create_sql;
            $sql[] = $create_sql;

            echo "Build pdo {$tableName} ... \n";
            createPdoClass($tableName);
            createModelClass($tableName);
        }
    }

    echo "Generate json And sql File ... \n";
    $jsonFile = dirname(__FILE__).'/db.json';
    $sqlFile = dirname(__FILE__).'/db.sql';
    if (!empty($data)){
        file_put_contents($jsonFile, json_encode($data));
    }

    if (!empty($sql)){
        file_put_contents($sqlFile, implode("\n\n", $sql));
    }

    echo "SUCCESS";
}

function createPdoClass($table)
{
    $dir = SOURCE_DIR.'Common/Model/Pdo/';
    return FileGenerator::createFileByTemplate($dir, $table);
}

function createModelClass($table)
{
    $segs = explode("_", $table);
    $model_segs = array();
    foreach ($segs as $seg){
        $model_segs[] = ucfirst($seg);
    }

    $filename = implode("", $model_segs).'Model';
    $dir = SOURCE_DIR.'Common/Model/';

    return FileGenerator::createFileByTemplate($dir, $filename, ['table' => $table]);
}
buildDatabaseData();
