<?php
// gestion_image.php for php in /home/lam/piscine/duguay_b
// 
// Made by DUGUAY Brice
// Login   <duguay_b@etna-alternance.net>
// 
// Started on  Fri Apr 15 17:14:00 2016 DUGUAY Brice
// Last update Sat Apr 16 12:22:50 2016 DUGUAY Brice
//
function resize($path, $size)
{
  if (getimagesize($path) === false)
    return (false);
  if (exif_imagetype($path) == 2)
    $r_img = imagecreatefromjpeg($path);
  if (exif_imagetype($path) == 1)
    $r_img = imagecreatefromgif($path);
  if (exif_imagetype($path) == 3)
    $r_img = imagecreatefrompng($path);
  list($width, $height) = getimagesize($path);
  $redim = $size / $height;
  $newwidth = $width * $redim;
  $newheight = $height * $redim;
  $r_im = imagecreatetruecolor($newwidth, $newheight);
  imagecopyresized($r_im, $r_img, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
  return ($r_im);
}
function tab_for_ligne($tab_s,$size_ligne, &$index, $max)
{
  $count = 0;
  $tab_l = array();
  $i = 0;
  while ($count < $size_ligne && isset($tab_s[$index]))
    {
      $count += imagesx($tab_s[$index]);
      $tab_l[$i] = $tab_s[$index];
      $index++;
      $i++;
      if ($index == $max)
	return ($tab_l);
    }
  $index--;
  $i--;
  unset($tab_l[$i]);
  return ($tab_l);
}
function create_ligne($tab, $size_ligne)
{
  $r_im = imagecreatetruecolor($size_ligne, imagesy($tab[0]));
  $count = 0;
  $i = 0;
  $grey = imagecolorallocate($r_im, 125, 125, 125);
  $r_m = imagefill($r_im, 0, 0, $grey);
  while (isset($tab[$i]))
    {
      $count += imagesx($tab[$i]);
      $i++;
    }
  $R = $size_ligne - $count;
  $num_inter = count($tab) + 1;
  $size_inter = $R / $num_inter;
  $i = 0;
  $decale = 0;
  while (isset($tab[$i]))
    {
      if ($i > 0)
	$size_pres = imagesx($tab[$i - 1]);
      else
	$size_pres = 0;
      $decale += ($size_pres + $size_inter);
      imagecopy($r_im, $tab[$i], $decale, 0, 0, 0, imagesx($tab[$i]), imagesy($tab[$i]));
      $i++;
    }
  return ($r_im);
}
function create_mosa($tab, $width_l, $size_ligne)
{
  $r_im = imagecreatetruecolor($size_ligne, $width_l * count($tab));
  $count = 0;
  $i = 0;
  $decale = 0;
  while (isset($tab[$i]))
    {
      if ($i > 0)
        $size_pres = $width_l;
      else
        $size_pres = 0;
      $decale += $size_pres;
      imagecopy($r_im, $tab[$i], 0, $decale, 0, 0, imagesx($tab[$i]), imagesy($tab[$i]));
      $i++;
    }
  return ($r_im);
}