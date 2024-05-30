<?php
// +----------------------------------------------------------------------
// | EwoMail
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://ewomail.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://ewomail.com/license.html)
// +----------------------------------------------------------------------
// | Author: Jun <gyxuehu@163.com>
// +----------------------------------------------------------------------
if(!defined("PATH")) exit;

/**
 * 创建邮件地址路径
 * */
function createMailDir($name,$domain)
{
	$str1 = substr($name,0,1);
	$str2 = substr($name,1,1);
	$str3 = substr($name,2,1);
	if(!$str3){
		$str3 = $str2;
	}
	$date = new Date();
	$format = $date->format("%Y%m%d");
	$dir = C('maildir')."/vmail/$domain/$str1/$str2/$str3/$name.$format";
	return $dir;
}

/**
 * 修改账号密码
 */
Rout::get('create_email',function(){
	$email = strtolower(iany('email'));
	$password = trim($_REQUEST['password']);
	$active = intval(iany('active'));
	$limits = intval(iany('limits'));
	$limitg = intval(iany('limitg'));
	$uname = iany('uname');
	$tel = iany('tel');
	
	if(!check_email($email)){
		E::error(L(1107).L(90103));
	}
	
	$arr = explode('@',$email);
	$name = $arr[0];
	$domain = $arr[1];
	
	if(strlen($name)<1){
		E::error(3074);
	}
	
	$domainRow = App::$db->getOne("select * from ".table("domains")." where name='$domain'");
	if(!$domainRow){
		E::error(3016);
	}
	
	if(App::$db->count("select count(id) from ".table("users")." where email='$email'")){
		E::error(3017);
	}
	
	$newData = [
		'domain_id'=>$domainRow['id'],
		'password'=>md5($password),
		'email'=>$email,
		'maildir'=>createMailDir($name,$domain),
		'uname'=>$uname,
		'tel'=>$tel,
		'active'=>$active,
		'limits'=>$limits,
		'limitg'=>$limitg,
		'ctime'=>App::$format,
	];
	App::$db->insert('users',$newData);
	

    E::success('success');
});