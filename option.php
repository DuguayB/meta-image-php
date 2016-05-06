<?php
// option.php for php in /home/duguayb/piscine/php/jour2/duguay_b/option
// 
// Made by DUGUAY Brice
// Login   <duguay_b@etna-alternance.net>
// 
// Started on  Tue Apr 12 11:51:05 2016 DUGUAY Brice
// Last update Sat Apr 16 12:27:51 2016 DUGUAY Brice
//
require("gestion_image.php");
function is_in_str($c ,$str)
{
  $i = 0;
  while (isset($str[$i]))
    {
      if ($str[$i] == $c)
	return (true);
      $i++;
    }
  return (false);
}
function false_combi($tab)
{
  $tab_v = array("g", "j", "l", "p","-");
  $tab_ft = array("g", "j", "p");
  $tab_fn = array("n", "N");
  $flagt = false;
  $flagn = false;
  foreach ($tab as $val)
    {
      $i = 0;
      while (isset($val[$i]))
        {
          if (in_array($val[$i], $tab_v) == false)
            return (false);
	  if (in_array($val[$i], $tab_ft) && $flagt)
            return (false);
	  if (in_array($val[$i], $tab_fn) && $flagn)
            return (false);
	  if (in_array($val[$i], $tab_ft))
            $flagt = true;
          if (in_array($val[$i], $tab_fn))
            $flagn = true;
          $i++;
        }
    }
  return (true);
}
function val_option($argv)
{
  $i = 1;
  $j = 0;
  $tab = array();
  while (isset($argv[$i]))
    {
      if ($argv[$i][0] == "-")
	{
	  $tab[$j] = $argv[$i];
	  $j = $j + 1;
	}
      $i = $i + 1;
    }
  return (false_combi($tab));
}
function option($argv)
{
  $i = 1;
  $j = 0;
  $tab = array();
  while (isset($argv[$i]))
    {
      if ($argv[$i][0] == "-")
        {
          $tab[$j] = $argv[$i];
          $j = $j + 1;
        }
      $i = $i + 1;
    }
  return ($tab);
}
function as_lopt($tabo)
{
  $flagl = false;
  foreach ($tabo as $val)
    {
      if (is_in_str("l" ,$val))
        $flagl = true;
    }
  return ($flagl);
}
function argument($argv)
{
  $i = 1;
  $j = 0;
  $tab = array();
  while (isset($argv[$i]))
    {
      if ($argv[$i][0] != "-")
        {
          $tab[$j] = $argv[$i];
          $j = $j + 1;
        }
      $i = $i + 1;
    }
  return ($tab);
}
function usage($argv, $argc, $tabo, $tabarg)
{
  if (val_option($argv) == false)
    {
      echo "php imagepanel.php [-j/g/p] [-l number] lien1 [lien2 [...]] base\n";
      return (false);
    }
  if (count($tabarg) < 2)
    {
      echo "php imagepanel.php [-j/g/p] [-l number] lien1 [lien2 [...]] base\n";
      return (false);
    }
  $flagl = as_lopt($tabo);
  if ($flagl && count($tabarg) < 3)
    {
      echo "php imagepanel.php [-j/g/p] [-l number] lien1 [lien2 [...]] base\n";
      return (false);
    }
  if (((int)$tabarg[0]) == 0 && $flagl)
  {
    echo "php imagepanel.php [-j/g/p] [-l number] lien1 [lien2 [...]] base\n";
    return (false);
  }
  return (true);
}
function is_url($str)
{
  return (filter_var($str, FILTER_VALIDATE_URL));
}
function launch($argv, $argc)
{
  $tabo =  option($argv);
  $tabarg =  argument($argv);
  if(usage($argv, $argc, $tabo, $tabarg) == false)
    return (false);
  $i = 0;
  if (as_lopt($tabo))
    $i = 1;
  $allparse = "";
  $tab = array();
  $j = 0;
  while ($i < count($tabarg) - 1)
    {
      if (is_url($tabarg[$i]))
	$allparse = lecture_url($tabarg[$i]);
      else
	$allparse = lecture($tabarg[$i]);
      preg_match_all("/<img[^>]*src=\"(.*)\"/U", $allparse, $tab_pathimg);
      if ($allparse !== false)
	$tab[$tabarg[$i]] = $tab_pathimg;
      $i++;
    }
  $tab = parse_tab($tab);
  $i = 0;
  while (isset($tab[$i]))
    {
      if (resize($tab[$i], 150) !== false)
	$tab[$i] = resize($tab[$i], 150);
      else
	unset($tab[$i]);
      $i++;
    }
  $k = 1;
  $i = 0;
  $tab = array_values($tab);
  while ($k < (count($tab) / 20) + 1)
    {
      $j = 0;
      $tab_all_l = array();
      while (($i < 20 * $k) && isset($tab[$i + 1]))
	{ 
	  $tabl = tab_for_ligne($tab, 1300, $i, 20);
	  $tab_all_l[$j] = create_ligne($tabl, 1300);
	  $j++;
	}
      imagejpeg(create_mosa($tab_all_l, 150, 1300), $tabarg[count($tabarg) - 1]."".$k.".jpeg");
      $k++;
      }
  echo count($tab)." images trouvees.\n";
}
function parse_tab($tab)
{
  $newtab = array();
  foreach ($tab as $key => $val)
      $newtab[$key] = $val[1];
  $newtab2 = array();
  $i = 0;
  foreach ($newtab as $key => $val)
    {
      foreach ($val as $path)
	{
	  $tak = pathinfo($path);
	  if ((is_url($path) || file_exists($path)) && isset ($tak["extension"]))
	    {
	      $newtab2[$i] = $path;
	      $i++;
	    }
	  else
	    {
	      if (is_url($key))
		$path = $key."/".$path;
	      else
		{
		  $info = pathinfo($key);
		  $path = $info["dirname"]."/".$path;
		}
	      $tak = pathinfo($path);
	      if ((is_url($path) || file_exists($path)) && isset ($tak["extension"]))
		{
		  $newtab2[$i] = $path;
		  $i++;
		}
	    }
	}
    }
  return ($newtab2);
}
function error($path)
{
  $error = "";
  if (!file_exists($path))
    $error = "imagepanel: {$path}: No such file or directory\n";
  else if (is_dir($path))
    $error = "imagepanel: {$path}: Is a directory\n";
  else if (!is_readable($path))
    $error = "imagepanel: {$path}: Permission denied\n";
  if ($error != "")
    {
      echo $error;
      return (0);
    }
  else
    return (1);
}
function lecture($path)
{
  $file = false;
  if (error($path))
    {
      $file = file_get_contents($path);
      if (!($file))
	echo "imagepanel: {$path}: Cannot open file\n";
    }
  return ($file);
}
function error_url($url)
{
  $file_headers = @get_headers($url);
  $error = "";
  if ($file_headers[0] == 'HTTP/1.1 404 Not Found')
    $error = "imagepanel: {$url}: Not Found";
  if ($error != "")
    {
      echo $error;
      return (false);
    }
  else
    return (true);
}
function lecture_url($url)
{
  $file = false;
  if (error_url($url))
    {
      $file = file_get_contents($url);
      if (!($url))
        echo "imagepanel: {$url}: Cannot open file\n";
    }
  return ($file);
}