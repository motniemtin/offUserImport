<?php
namespace Motniemtin\offUserImport;
/****************************************************************************
Software: evnMeterCrawler
Version:  0.x
Date:     2021
Author:   Lam nguyen <motniemtin@gmail.com>
License:  Copyright
****************************************************************************/
use Exception;
use League\Csv\Writer;
use League\Csv\Reader;

class offUserImport
{
    var $storePath;
    var $text;
    var $data = array();
    var $header;
    var $records;
    function __construct()
    {
    }
    function setTemplate($csvTemplateFile)
    {
        //load the CSV document from a file path
        $csv = Reader::createFromPath($csvTemplateFile, 'r');
        $csv->setHeaderOffset(0);
        $this->header = $csv->getHeader();
    }
    function convertFile($csvFile, $type)
    {
        $pathinfo = pathinfo($csvFile);
        $this->data = array();
        if (!file_exists($csvFile))
        {
            throw new Exception('Không tìm được file !');
        }
        //load the CSV document from a file path
        $csv = Reader::createFromPath($csvFile, 'r');
        $csv->setHeaderOffset(0);

        $header = $csv->getHeader(); //returns the CSV header record
        $records = $csv->getRecords(); //returns all the CSV records as an Iterator object
        $id = 0;
        $outputRecords = array();
        $arrayEmails = array();
        $fileId = 1;
        foreach ($records as $record)
        {
            if($record['id']){
                $email = $this->boDau($record['id']).".".$this->firstLetters($this->boDau($record['ho'])).$this->boDau($record['ten']);
            }else{
                $email = $this->firstLetters($this->boDau($record['ho'])).$this->boDau($record['ten']);
            }
            
            $email = strtolower($email);
            $repeatId = 1;
            while(in_array($email, $arrayEmails)) {
                $email.=$repeatId;
                $repeatId==1;
            }
            $arrayEmails[] = $email;
            $email = $email."@ngoquyentamky.edu.vn";
            
            $outputRecords[] = array(
                $email,
                $this->boDau($record['ten']),
                $this->boDau($record['ho']),
                $this->boDau($record['ho']." ".$record['ten']),
                $type,
                $record['id'],
                '',
                '',
                '',
                '',
                $email,
                $this->boDau(trim($record['diachi'])),
                '',
                '',
                '',
                ''
            );
            $id += 1;
            if ($id == 249)
            {
                $csv = Writer::createFromString();
                $csv->insertOne($this->header);
                $csv->insertAll($outputRecords);
                file_put_contents($this->storePath."/".$pathinfo['filename']."_".$fileId.".csv", $csv->getContent());
                $id = 0;
                $fileId+=1;
                $outputRecords = array();
            }
        }
        if(count($outputRecords)>0){
            $csv = Writer::createFromString();
            $csv->insertOne($this->header);
            $csv->insertAll($outputRecords);
            file_put_contents($this->storePath."/".$pathinfo['filename']."_".$fileId.".csv", $csv->getContent());
        }
    }

    //Đặt đường dẫn lưu Cookie của User
    function setStorePath($storePath)
    {
        $storePath = realpath($storePath);
        if (!is_dir($storePath))
        {
            throw new Exception('Store Path không đúng !');
        }
        $storePath = rtrim($storePath, '/');
        $this->storePath = $storePath;
    }
    function firstLetters($string){
        $string = trim($string);
        $strings = explode(" ", $string);
        $firstChars = "";
        foreach($strings as $string){
            $firstChars .= $string[0];
        }
        return $firstChars;
    }
    function boDau($str)
    {
        $str=trim($str);
        $str=trim($str, '"');
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );
        foreach ($unicode as $nonUnicode => $uni){
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        $str = str_replace(',', "-", $str);
        $str = str_replace('  ', ' ', $str);
        $str = str_replace('  ', ' ', $str);
        $str = str_replace('  ', ' ', $str);
        return $str;

    }
}

