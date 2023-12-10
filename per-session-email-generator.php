<?php 

 /* (see user settings below the INFO section before using.) */ 

/***** INFO *****
 * This is a php script that creates a custom email
 * address on the fly, so every user sees a temporary but
 * persistent contact email address on their website
 * that's (mostly) unique to them as an anti-spam meause.
 * 
 * It's an age-old problem: you want to display your email
 * address on your site, but if you do, spammers will
 * scrape it and spam you.
 * 
 * On my site https://kupietz.com, everywhere I need to
 * give my email address, even in <a
 * href="mailto:xxx@yyy.zzz"> tags, I include this php
 * script (in my case I'm using a wordpress plugin that
 * allows embedding PHP with a shortcode.)
 * 
 * The script generates a per-session email address based
 * on the user's browser info, IP address, and some other
 * session info. That way the email stays unchanged
 * through reloads, on different pages of the site, etc.
 * It's stored per session but using the user's info means
 * they can come back tomorrow with a new session and
 * still get the same email address. I do have it set to
 * change weekly just to make sure they rotate
 * occasionally.
 * 
 * Then, if a spammer crawls my site and grabs my email
 * address, instead of getting
 * myrealemailaddress@kupietz.com, they get something like
 * fmconsulting-e55@kupietz.com. Then when I start getting
 * spammed to death on fmconsulting-e55@kupietz.com, I can
 * just block that one address, and my main email address
 * is left unmolested.
 * 
 * WARNINGS, IMPORTANT:
 * 
 *     This script generates logs every time it creates a
 * new email address for a visitor, with the name
 * genEmail_blahblahblah.log, in my website's main
 * directory. These need to be periodically cleared out
 * manually. These are because when I get spammed on an
 * address, I have a morbid curiosity to look in the logs
 * and see the info of the visitor the address was
 * provided to.
 * 
 *     Obviously in this day and age you don't want to use
 * a catchall email address (well, at least, I don't.) So
 * you have to have some way of accepting all the various
 * combinations of addresses this might generate, without
 * leaving your email wide open with a catchall. I have a
 * way of doing this for myself, but for security reasons,
 * that one has to remain private. I would suggest, if you
 * don't want to use a catchall, having some sort of
 * filter that accepts any email addressed to one of the
 * prefixes specified in the script from which it
 * generates the first part of the script, which are
 * specified in the user options section at top.
 * 
 * This software is shared under the terms of the GNU 3.0 license.
 * A copy of the license must be included with all distributions.
 * 
 * Michael Kupietz
 * https://kupietz.com (business site)
 * https://github.com/kupietools (free software)
 * https://michaelkupietz.com (art & creativity)
 *
 */

/***** USER SETTINGS *****/

//One of these words will be used as the first part of the generated email addresses. Change this to a list of words suitable to you or your website's topic.
$wordArray=array("filemaker","fmcontact","response","consult","sitecontact","inquiries","fm","fmp","fmpro","web","fmpr","prospect","inquiry","web","intake","contact","fmcons","fmconsult","webresp");

/***** END USER SETTINGS  - don't modify anything below this line*****/



   if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 

/* for debugging only
 * ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

if (!isset($_SESSION['theEmailx'])) //only run once per session

{


$theOutput ="";
$n=10; $m=2; //Why is this here? I don't remember



if(!function_exists("getUserIpAddr")) {
//don't redeclare functions. Mostly useful for when I disable the "if (!isset($_SESSION['theEmail']))" to test during development.
function getUserIpAddr(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        //ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function getUA() {
  if(!isset($_SERVER['HTTP_USER_AGENT'])) {return "none";}
else
{  return $_SERVER['HTTP_USER_AGENT'];}
}

function getName($n,$wordArray) { 

 $theBrowserDetails="";
    foreach (getallheaders() as $name => $value) {
    $theBrowserDetails .= "$name: $value\n";
}



$file="genEmail.log";

// Append a new person to the file
$current = 'Date - '.date("c")."\n";
$current .= 'Detected IP - '.$_SERVER['REMOTE_ADDR']."\n";
$current .= 'User Real IP - '.getUserIpAddr()."\n";
$theUA = getUA();
$current .=  $theBrowserDetails."\n";
$current .=  'getUA() - '.$theUA."\n";
$salt="thisHereSalt467letsMakeThingsObscure2960";

$theIP=$_SERVER['REMOTE_ADDR'].getUserIpAddr();

$theIPhash=md5($theIP.$salt.$theUA);
$IPhashInt = (int) filter_var($theIPhash, FILTER_SANITIZE_NUMBER_INT);
//added (int) on 2023jul31 because upgrade to php 8.4 cause errors without explicit coercion to integer

$connectors="------_____0123456789";
//was $connectors="------....._____0123456789";
$wordArrayLength=sizeof($wordArray);
//$wordIndex=rand(0,$wordArrayLength-1); this was random. We'll seed using browser info.
$wordIndex=$IPhashInt%$wordArrayLength;
$word=$wordArray[$wordIndex];
$wordHash=md5($word.$salt); 
$wordHashInt = (int)substr(filter_var($wordHash, FILTER_SANITIZE_NUMBER_INT),-10); //need to limit size because biggest available integer in PHP is 9223372036854775807. 10 digits should do
$theIPhashReversed=md5($theUA.$theIP.$salt);
$IPhashReversedInt=(int)substr(filter_var($theIPhashReversed, FILTER_SANITIZE_NUMBER_INT),-10);
//added (int) on 2023jul31 because upgrade to php 8.4 cause errors without explicit coercion to integer
$theThirdHashMD5=md5($theUA.date('Wy').$theUA); //Wy = weekofyear then year.
$theThirdHashInt=(int)substr(filter_var($theThirdHashMD5, FILTER_SANITIZE_NUMBER_INT),-10);
//$randomDigitOne = rand(1, 9);  Some people didn't have persistent sessions for some reason so were seeing different email addresses on every page because of this random factor.
//$randomDigitTwo = rand(1, 9);
$randomDigitOne = (int)1+$IPhashInt%18;
$randomDigitTwo = (int)(1+$IPhashReversedInt+$theThirdHashInt)%18;
$randomComboOne = $randomDigitOne * $randomDigitTwo + $randomDigitOne + 1;
$randomComboTwo = $randomDigitOne * 10 + $randomDigitTwo+1 ;
$randomComboThree = $randomDigitOne  + $randomDigitTwo*10;
$thisIndex = (int)($wordHashInt+$randomDigitOne-$randomDigitTwo)%(strlen($connectors));
$connectorChar=$connectors[$thisIndex];

/* echo "wh ".$wordHashInt."<br>"; */
/* enable this block for troublehsooting, dumps variables to console
$err = " <!-- wordArrayLength ". $wordArrayLength;
$err .= " wordIndex ".$wordIndex;
$err .=  " wordArray[$wordIndex] " . $wordArray[$wordIndex];
$err .= " word ". $word . " wordHash " . $wordHash;
$err .= " wordHashInt " . $wordHashInt;
$err .= " wordHashIntTEST " . $wordHashIntTEST;
$err .= " theIPhashReversed " . $theIPhashReversed;
$err .= " IPhashReversedInt " . $IPhashReversedInt;
$err .= " theThirdHashMD5 " . $theThirdHashMD5;
$err .= " theThirdHashInt " . $theThirdHashInt;
$err .= " IPhashReversedInt+theThirdHashInt" . $IPhashReversedInt+$theThirdHashInt;
$err .= " IPhashReversedInt+theThirdHashInt%18" . ($IPhashReversedInt+$theThirdHashInt)%18;
$err .= " randomDigitOne " . $randomDigitOne;
$err .= " randomDigitTwo " . $randomDigitTwo;
$err .= " randomComboOne " . $randomComboOne;
$err .= " randomComboTwo " . $randomComboTwo;
$err .= " randomComboThree " . $randomComboThree;
$err .= " thisIndex " . $thisIndex;
$err .= " connectorChar " . $connectorChar;
$err .= " connectors[$thisIndex]; " . $connectors[$thisIndex] . " -->";
 echo "<script>console.log('Debug Objects: " . $err . "' );</script>"; 
//end debugging block 
*/
 
$wordHashFinal = md5(10+(($randomComboThree*($wordHashInt%$randomComboTwo+abs($randomComboOne-$randomComboTwo))) % (2+($randomComboOne*10) % ($randomComboTwo+1)))); /* 10+ prevents three-digit codes... got 666 for one! */

$wordHashHashed = (int)filter_var($wordHashFinal, FILTER_SANITIZE_NUMBER_INT)[-2];
 $theResult =  $word.$connectorChar.($wordHashHashed+($randomDigitOne*$randomDigitTwo));
 $current .= "generated email: ".$theResult."\n";
$current .= "------------------\n";

  file_put_contents($file, $current,FILE_APPEND);
 
 if (filesize($file) > 500*1024) {
		$filename2 = "$file".date("c");
		rename($file, $filename2);
		touch($file); chmod($file,0666);
	}

    return $theResult; 

} 

} //end func declartaions

// Write the contents back to the file

$theOutput=getName($n,$wordArray);

  $_SESSION['theEmail'] = $theOutput;
}
else
{
  $theOutput = $_SESSION['theEmail'];
}
echo $theOutput."@kupietz.com";

?> 
