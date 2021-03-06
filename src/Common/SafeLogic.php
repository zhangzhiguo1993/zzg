<?php
namespace zzg\Common;
class SafeLogic{
    /** 
     * 执行过滤 
     * @param 1 linux/2 http/3 Db/ $group 
     * @param 保存路径以及文件名/文件名/null $projectName 
     */  
    static  function xss($group = 1,$projectName = NULL){  
        //正则条件   //<\\s*img\\b|
        $referer = empty ( $_SERVER ['HTTP_REFERER'] ) ? array () : array ($_SERVER ['HTTP_REFERER'] );  
        $getfilter = "'|<[^>]*?>|^\\+\/v(8|9)|\\b(and|or)\\b.+?(>|<|=|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*\\/?script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";  
        $postfilter = "^\\+\/v(8|9)|\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*\\/?script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";  
        $cookiefilter = "\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*\\/?script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";  
  
        // $ArrPGC=array_merge($_GET,$_POST,$_COOKIE);  
  
        //遍历过滤  
        foreach ( $_GET as $key => $value ) {  
            self::stopAttack ( $key, $value, $getfilter ,$group , $projectName,'get');  
        }  
        //遍历过滤  
        foreach ( $_POST as $key => $value ) {  
            self::stopAttack ( $key, $value, $postfilter ,$group , $projectName,'post');  
        }  
        //遍历过滤  
        foreach ( $_COOKIE as $key => $value ) {  
            self::stopAttack ( $key, $value, $cookiefilter ,$group , $projectName,'cookie');  
        }  
        //遍历过滤  
        foreach ( $referer as $key => $value ) {  
            self::stopAttack ( $key, $value, $getfilter ,$group , $projectName,'referer');  
        }  
    }  
  
    /** 
     * 匹配敏感字符串，并处理 
     * @param 参数key $strFiltKey 
     * @param 参数value $strFiltValue 
     * @param 正则条件 $arrFiltReq 
     * @param 项目名 $joinName 
     * @param 1 linux/2 http/3 Db/ $group 
     * @param 项目名/文件名/null $projectName 
     */  
    static function stopAttack($strFiltKey, $strFiltValue, $arrFiltReq,$group = 1,$projectName = NULL,$method_type) {  
  
            $strFiltValue = self::arr_foreach ( $strFiltValue );  
            //匹配参数值是否合法  
            if (preg_match ( "/" . $arrFiltReq . "/is", $strFiltValue ) == 1) {  
                //记录ip  
                $ip = "操作IP: ".$_SERVER["REMOTE_ADDR"];  
                //记录操作时间  
                $time = " 操作时间: ".strftime("%Y-%m-%d %H:%M:%S");  
                //记录详细页面带参数  
                $thePage = " 操作页面: ".self::request_uri();  
                //记录提交方式  
                $type = " 提交方式: ".$_SERVER["REQUEST_METHOD"];  
                //记录提交参数  
                $key = " 提交参数: ".$strFiltKey;  
                //记录参数  
                $value = " 提交数据: ".htmlspecialchars($strFiltValue);  
                //写入日志  
                $strWord = $ip.$time.$thePage.$type.$key.$value;  
                //过滤值 rin 20140310  
                if($method_type=='get'){  
                    $_GET[$strFiltKey] = preg_replace("/" . $arrFiltReq . "/is","",$strFiltValue);  
                }else if($method_type=='post'){  
                    $_POST[$strFiltKey] = preg_replace("/" . $arrFiltReq . "/is","",$strFiltValue);  
                }else if($method_type=='post'){  
                    $_COOKIE[$strFiltKey] = preg_replace("/" . $arrFiltReq . "/is","",$strFiltValue);  
                }else if($method_type=='post'){  
                    $_SERVER[$strFiltKey] = preg_replace("/" . $arrFiltReq . "/is","",$strFiltValue);  
                }  
                //保存为linux类型  
                if($group == 1){  
                    self::log_result_common($strWord,$projectName);  
                }  
                //保存为可web浏览  
                if($group == 2){  
                    $strWord .= "<br>";  
                    self::slog($strWord,$projectName);  
                }  
                //保存至数据库  
                if($group == 3){  
                    self::sDb($strWord);     
                }  
                //过滤参数  
                $_REQUEST[$strFiltKey] = '';  
                //这里不作退出处理  
                //exit;  
            }  
  
            //匹配参数是否合法  
            if (preg_match ( "/" . $arrFiltReq . "/is", $strFiltKey ) == 1) {  
                //记录ip  
                $ip = "操作IP: ".$_SERVER["REMOTE_ADDR"];  
                //记录操作时间  
                $time = " 操作时间: ".strftime("%Y-%m-%d %H:%M:%S");  
                //记录详细页面带参数  
                $thePage = " 操作页面: ".self::request_uri();  
                //记录提交方式  
                $type = " 提交方式: ".$_SERVER["REQUEST_METHOD"];  
                //记录提交参数  
                $key = " 提交参数: ".$strFiltKey;  
                //记录参数  
                $value = " 提交数据: ".htmlspecialchars($strFiltValue);  
                //写入日志  
                $strWord = $ip.$time.$thePage.$type.$key.$value;  
                //保存为linux类型  
                if($group == 1){  
                    self::log_result_common($strWord,$projectName);  
                }  
                //保存为可web浏览  
                if($group == 2){  
                    $strWord .= "<br>";  
                    self::slog($strWord,$projectName);  
                }  
                //保存至数据库  
                if($group == 3){  
                    self::sDb($strWord);     
                }  
                //过滤参数  
                $_REQUEST[$strFiltKey] = '';  
                //这里不作退出处理  
                //exit;  
            }  
        }  
  
    /** 
     * 获取当前url带具体参数 
     * @return string 
     */  
    static function request_uri() {  
        if (isset ( $_SERVER ['REQUEST_URI'] )) {  
            $uri = $_SERVER ['REQUEST_URI'];  
        } else {  
            if (isset ( $_SERVER ['argv'] )) {  
                $uri = $_SERVER ['PHP_SELF'] . '?' . $_SERVER ['argv'] [0];  
            } else {  
                $uri = $_SERVER ['PHP_SELF'] . '?' . $_SERVER ['QUERY_STRING'];  
            }  
        }  
        return $uri;  
    }  
  
  
    /** 
     * 日志记录(linux模式) 
     * @param 保存内容 $strWord 
     * @param 保存文件名$strPathName 
     */  
    static function log_result_common($strWord, $strPathName = NULL) {  
        if($strPathName == NULL){  
            $strPath = "/var/tmp/";  
            $strDay = date('Y-m-d');  
            $strPathName = $strPath."common_log_".$strDay.'.log';  
        }  
  
        $fp = fopen($strPathName,"a");  
        flock($fp, LOCK_EX) ;  
        fwrite($fp,$strWord." date ".date('Y-m-d H:i:s',time())."\t\n");  
        flock($fp, LOCK_UN);  
        fclose($fp);  
    }    
  
    /** 
     * 写入日志(支持http查看) 
     * @param 日志内容 $strWord 
     * @param web页面文件名 $fileName 
     */  
    static function slog($strWord,$fileName = NULL) {  
        if($fileName == NULL){  
            $toppath = $_SERVER ["DOCUMENT_ROOT"] . "/log.htm";  
        }else{  
            $toppath = $_SERVER ["DOCUMENT_ROOT"] .'/'. $fileName;  
        }  
        $Ts = fopen ( $toppath, "a+" );  
        fputs ( $Ts, $strWord . "\r\n" );  
        fclose ( $Ts );  
    }  
  
    /** 
     * 写入日志(数据库) 
     * @param 日志内容 $strWord 
     */  
    static function sDb($strWord){  
        //....  
    }  
  
    /** 
     * 递归数组 
     * @param array $arr 
     * @return unknown|string 
     */  
    static function arr_foreach($arr) {
        static $str = '';  
        if (! is_array ( $arr )) {  
            return $arr;  
        }  
        foreach ( $arr as $key => $val ) {  
            if (is_array ( $val )) {  
                self::arr_foreach ( $val );  
            } else {  
                $str [] = $val;  
            }  
        }  
        return implode ( $str );  
    }  
}  
?>