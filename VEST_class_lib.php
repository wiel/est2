<?php

/*

PHP Classes for Speech Visualization
====================================
A set of PHP classes to visualize the output of the speech analysis program
PRAAT.
Also usable for getting statistical data out of the output of PRAAT.
Made for the purpose of Visual E-learning based Speech Therapy (VEST).

Version 1.01

Copyright (C) 2007 Free Software Foundation, Inc. <http://fsf.org/>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published 
by the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

Written by Wiel Seuskens <gnu@wiels.com> 2013-2015

Basic working
=============
A VestVisualization object is feeded with a wav audio file.
PRAAT is invoked to generate intensity and pitch data from this audio file.
These data are stored in an array of VestPoint objects.
Various variables can be manipulated to define the visualization.
The data are used to generate subsequent frames for the animation, this is done
with the Imagick PHP extension, which generates targa images.
Ffmpeg is used to generate a mp4 animation from these targa images.
Example of working system: http://wiels.com/vest/v3/

System requirements
===================
- PHP5 (within Apache2 web server on a linux system) capable of executing 
  commands with 'exec' and a write enabled 'images' and 'animations' directories
  in docroot.
- PRAAT, see http://www.fon.hum.uva.nl/praat/ (for creating pitch and intensity
  information out of audio files).
- vest.praat script, included (for getting the right data out of PRAAT).
- Imagick PHP extension, see http://php.net/manual/en/book.imagick.php (for
  creating individual images).
- ffmpeg, see http://www.ffmpeg.org/ (for creating video with h.264 video codec.
  and aac (mp4) audio codec in mp4 container out of individual images).
- optional: some ttf (preferable verdana.ttf and verdanab.ttf) fonts (for text 
  in static "animation").

Thanks to
========= 
Mario Ganzeboom for improving the precission of the praat script.
The time of the audio frames in the original praat script was set to 0.05 
seconds * frame number (frametime = framenr * shift).
This was incorrect, it is changed now to the actual frame time 
(frametime = Get time from frame... framenr).
Mario also mentioned an error in praat 
(https://uk.groups.yahoo.com/neo/groups/praat-users/conversations/topics/3472) 
and suggested to change the praat script to avoid these errors.
	select Sound 'name$'
	To Pitch (ac)... shift minpitch 15 no 0.03 0.45 0.01 0.35 0.14 600
should be replaced by
	select Sound 'name$'
	To Pitch... 0.01 minpitch 750
	q25 = Get quantile... 0 0 0.25 Hertz
	q75 = Get quantile... 0 0 0.75 Hertz
	miniPitch = q25 * 0.75
	maxiPitch = q75 * 1.5
	select Sound 'name$'
	To Pitch (ac)... shift miniPitch 15 off 0.03 0.45 0.01 0.35 0.14 maxiPitch
As Vest_class_lib already masks these errors with a median filter, this 
improvement is not (yet) implemented.

History
=======
Version 1.01 [2015-05-15]
  Some adoptations to newer PHP/Imagick versions, minor bug fixes and some new 
    functions to increase flexibility.
	in class VestPoint
	  function set_pitchmedian      ($pitchmedian)      not private anymore
	  function set_pitchmedian5     ($pitchmedian5)     not private anymore
	  function set_intensitymedian  ($intensitymedian)  not private anymore
	  function set_intensitymedian5 ($intensitymedian5) not private anymore
	setFillAlpha (deprecated) replaced by setFillOpacity 
	setStrokeAlpha (deprecated) replaced by setStrokeOpacity
	some checks on empty arrays (errors appeared with crippled audio files)
	some checks on division by zero (errors appeared with crippled audio files)
	function writelog () added.
	function set_logfile () added.
	function get_logfile () added.
	function set_shadow () added.
	function set_imgformat ($imgformat) added.
	function get_imgformat () added.
	function set_ffmpeg () added.
	function set_qtfaststart () added.
	function set_opacity () added.
	function get_opacity () added.
	function set_usrdir () added.
	function get_usrdir () added.
	added '-pix_fmt yuv420p' to ffmpeg command to make animations playable on 
	  other browsers than Chrome, for this option width and height both have to 
	  be even, preferable width should be devidable by 4 (vlc on pc likes this).
	adapted framerate to real measures (was fixed 20), see remark Mario Ganzeboom.

Version 1.00 [2013-09-26]
  initial Version.


The VestVisualization class
===========================
__construct ($basename, $praatexe, $praatscript)
     $basename: name without extension of audio file to be visualized (basename
          of 'foobar.wav' is 'foobar').
     $praatexe: path to the executable praat program.
     $praatscript: path to the vest.praat script (included in this package)
          that will course praat to generate intensity and pitch data of the
          audio file.

calculate_mean ()
     calculates means from the audio file: pitch mean, intensity mean, pitch 
     median mean, intensity median mean, pitch mean corrected, intensity mean 
     corrected, pitch median mean corrected intensity median mean corrected.
     See individual 'get mean' methods for explanation of each 'mean' value.

calculate_medians ()
     calculates 'median' over 3 measure points, 'median5' over 5 measure points
     of intensity and pitch of all measure points of the audio file.

calculate_values ()
     calculates minimum and maximum values of intensity and pitch of all measure 
     points of the audio file: minimum pitch median, maximum pitch median, 
     minimum pitch, maximum pitch, minimum intensity median, maximum intensity
     median.

create_animation ()
     generates animbasename.mp4 video from the animbasename.wav audio file with
     the given settings of the VestVisualization object.

create_static_background ($canvas, $pitch_high, $textfont, $boldfont) [private]
     creates the background with rulers for 'static' animation.
     $canvas: Imagick object to draw the background on.
     $pitch_high: the highest pitch that is expected to be drawn.
     $textfont: path to the ttf font that will be used for plain text
     $boldfont: path to the ttf font that will be used for bold text

create_xml ()
     creates xml file with measured intensity and pitch values from the audio
     file using PRAAT program and vest.praat script.
 
get_animbasename ()
     returns the name without extension of the animation file that will be
     generated.

get_animname ()
     returns the name with extension of the animation file that will be
     generated.

get_basecolor ()
     returns the base grey value (0-255) of the fill color of the shapes
     (disc, cross, star) to be drawn.

get_basename ()
     returns the name without extension of audio file to be visualized.

get_boldfont ()
     returns the path to the ttf font that will be used as the bold font.

get_colorerrvis ()
     returns the mode of the error color visualization ('continuous' or
     'discrete')

get_disccol_b ()
     returns the blue value (0-255) of the fill color of the shapes
     (disc, cross, star) to be drawn.

get_disccol_g ()
     returns the green value (0-255) of the fill color of the shapes
     (disc, cross, star) to be drawn.

get_disccol_r ()
     returns the red value (0-255) of the fill color of the shapes
     (disc, cross, star) to be drawn.

get_drawguides ()
     returns the distance between vertical guide lines in the animation.
     0 means no guide lines.

get_firstx ()
     returns the value of the child element 'time' of the first 'res' element 
     in the xml file which has a child element 'intensity' with a value > 0.

get_height ()
     returns the height in pixels of the animation.

get_heightscale ()
     returns the ratio with which the pitch is multiplied to calculate the
     vertical position where the shape in the animation will be drawn.

get_imgformat ()
     returns image file format (extension)

get_intensityerrvis ()
     returns true if intensity error visualization is on, false if off.

get_intensitylow ()
     returns the minimum intensity that will be drawn in a none error 
     shape/color (if error visualization is on).

get_intensitymean ()
     returns the mean value of all intensity values in the audio file.

get_intensitymeancor ()
     returns the mean value of all intensity values of measure points in the 
     audio file which have a pitch value.

get_intensitymedianmean ()
     returns the mean value of all intensity median values in the audio file.

get_intensitymedianmeancor ()
     returns the mean value of all intensity median values of measure points in 
     the audio file which have a pitch value.

get_intensitythreshold ()
     returns the minimum value of the intensity that will be drawn in the
     animation.

get_lastx ()
     returns the last measure point that will be drawn in the animation.

get_lowy ()
     returns the vertical position of the lowest shape that will be drawn.

get_maxintensitymedian ()
     returns the highest intensity median value of the audio file.

get_maxpitch ()
     returns the highest pitch value of the audio file.

get_maxpitchmedian ()
     returns the highest pitch median value of the audio file.

get_minintensitymedian ()
     returns the lowest intensity median value of the audio file.

get_minpitch ()
     returns the lowest pitch value of the audio file.

get_minpitchmedian ()
     returns the lowest pitch median value of the audio file.

get_opacity ($opacity)
     returns the opacity (float) of the drawn shapes.

get_pitcherrvis ()
     returns true if pitch error visualization is on, false if off.

get_pitchhigh ()
     returns the maximum pitch that will be drawn in a none error shape/color
     (if error visualization is on).

get_pitchmean ()
     returns the mean value of all pitch values in the audio file.

get_pitchmeancor ()
     returns the mean value of all pitch values of measure points in the 
     audio file which have a pitch value.

get_pitchmedianmean ()
     returns the mean value of all pitch median values in the audio file.

get_pitchmedianmeancor ()
     returns the mean value of all pitch median values of measure points in 
     the audio file which have a pitch value.

get_pitchthreshold ()
     returns the value of the pitch where the shape error visualization changes
     dramatically (from octagonal shape to cross shape).

get_points()
     returns array of VestPoint objects (measure points) of the audio file.

get_pointscount ()
     returns the number of VestPoint objects (measure points) of the audio file.

get_postframes ()
     returns the number of frames that will additional be drawn at the end of 
     the animation. These frames will be the same as the last generated frame.

get_praatexe ()
     returns the path to the executable PRAAT program.

get_praatscript ()
     returns the path to the vest.praat script.

get_preframes ()
     returns the number of frames that will additional be drawn at the beginning
     of the animation. These frames will only contain the background.

get_rdeviation ()
     returns the number that determines the degree of deviation from the base 
     color of the shape in error visualization.

get_render ()
     returns true or false. This flag is only for external use, the class 
     itself doesn't use this flag.

get_shapeerrvis ()
     returns the error visualization mode:
     continuous - error visualization gets stronger when error is bigger
     discrete - same error visualization for all errors

get_textfont ()
     returns path to the ttf font that will be used for plain text in static 
     "animation"

get_usrdir ()
    returns unique temporary subdirectory name for temporary generated images and animations.
    (generated by set_usrdir() if no subdirectory name is set)

get_type ()
     returns type of visualization:
     'error_analytic'    - visualize both intensity and pitch:
                              intensity: size of shape
                              pitch: vertical displacement
                           display intensity and pitch errors in separate ways:
                              intensity errors: color
                              pitch errors: shape
     'error_shape'       - visualize both intensity and pitch:
                              intensity: size of shape
                              pitch: vertical displacement
                           display both intensity and pitch errors with shapes
     'error_color'       - visualize both intensity and pitch:
                              intensity: size of shape
                              pitch: vertical displacement
                           display both intensity and pitch errors with colors
     'no_error_pitch_y'  - use vertical displacement for pitch visualization
                           no intensity visualization
                           no error visualization
     'no_error_pitch_r'  - use shape size for pitch visualization
                           no intensity visualization
                           no error visualization
     'no_error_intens_y' - use vertical displacement for intensity visualization
                           no pitch visualization
                           no error visualization
     'no_error_intens_r' - use shape size for intensity visualization
                           no pitch visualization
                           no error visualization
     'no_error'          - visualize both intensity and pitch:
                              intensity: size of shape
                              pitch: vertical displacement
                           no error visualization
     'static_graph'      - visualize in static graph: EST like visualization

get_valuesavailable ()
     returns true if minimum and maximum values of intensity and pitch of all 
     measure points of the audio file are calculated, otherwise false.

get_wavname ()
     returns the name of audio file of the object.

get_width ()
     returns the width in pixels of the animation.

get_xmarge ()
     returns the number of pixels of the distance between the left border of the
     animation and the center of the first shape to be drawn; this is the same
     number as the distance between the right border of the animation and the 
     center of the last shape to be drawn.

get_xmlname ()
     returns the name of the xml file that will be produced by the PRAAT program
     from the audio file.
     
get_xscale ()
     returns the distance between the centers of two consecutive shapes that
     will be drawn for the animation.

put_xmldata ()
     stores the xml data of the audio file in an array of VestPoint objects.

set_animbasename ()
     defines the name without extension of the animation file that will be
     generated, based on base name, type, color and shape of error visalization.

set_background ($bg_type, $bg_var1, $bg_var2=0)
     defines the background of the animation.
     $bg_type: type of background that will be used,
          'color'        - solid color.
          'gardient'     - vertical gradient.
          'image'        - an image.
          'static_graph' - static graph, EST like, background.
     $bg_var1: dependent on $bg_type,
          color: hexadecimal value of color of the background.
          gradient: hexadecimal value of top color of the gradient.
          image: path to the image that will be used as background.
          static_graph: hexadecimal value of color of the the first frame in the
            animation.
     $bg_var2 (default 0): dependent on $bg_type,
          color: not used.
          gradient: hexadecimal value of bottom color of the gradient.
          image: hexadecimal value of color of the the first frame in the
            animation.
          static_graph: pitch of the pitch error line.

set_basecolor ($basecolor)
     defines the base grey value of the fill color of the shapes (disc, cross, 
     star) to be drawn.
     same as these three functions together:
     set_disccol_r ($basecolor);
     set_disccol_g ($basecolor);
     set_disccol_b ($basecolor);
     $basecolor: decimal value between 0 (black) and 255 (white).

set_boldfont ($boldfont)
     defines path to the ttf font that will be used for bold text in static 
     "animation".
     $boldfont: path to a ttf font file.

set_colorerrvis ($colorerrvis)
     defines the mode of the error color visualization.
     $colorerrvis:
       'continuous' - error visualization gets stronger when error is bigger.
       'discrete' - same error visualization for all errors.

set_disccol_b ($disccol_b)
     defines the blue value of the fill color of the shapes (disc, cross, star) 
     to be drawn.
     $disccol_b: decimal value between 0 (no blue) and 255 (full blue).

set_disccol_g ($disccol_g)
     defines the green value of the fill color of the shapes (disc, cross, star) 
     to be drawn.
     $disccol_g: decimal value between 0 (no green) and 255 (full green).

set_disccol_r ($disccol_r)
     defines the red value of the fill color of the shapes (disc, cross, star) 
     to be drawn.
     $disccol_r: decimal value between 0 (no red) and 255 (full red).

set_drawguides ($guidedistance)
     defines the distance between vertical guide lines in the animation.
     $guidedistance: distance in pixels, 0 means no guide lines.

set_ffmpeg ($ffmpeg_path)
     defines the ffmpeg executable.
     $ffmpeg_path: the path of the ffmpeg executable.

set_qtfaststart ($qtfaststart_path)
     defines the qt-faststart executable.
     $qtfaststart_path: the path of the qt-faststart executable.

set_firstx ($firstx) [private]
     defines the first 'res' element in the xml file that will be drawn in the 
     animation.
     $firstx: child element 'time' of the 'res' element.

set_height ($height)
     defines the height of the animation.
     $height: height in pixels

set_heightscale ($heightscale)
     defines the ratio with which the pitch is multiplied to calculate the
     vertical position where the shape in the animation will be drawn.
     $heightscale: the ratio to be set.

set_imgformat ($imgformat)
     sets image file format by extension.
     $imgformat: "bmp", "png", "tga" etc.
     it turns out that uncompressed formats work a lot faster (bmp is approximately 10x faster than png).

set_intensityerrvis ($intensityerrvis)
     defines if intensity error visualization is on or off.
     $intensityerrvis: 'true' (on) or 'false' (off).

set_intensitylow ($intensitylow)
     defines the minimum intensity that will be drawn in a none error 
     shape/color (if error visualization is on).
     $intensitylow: intensity value that will be compared to the child element 
          'int' of the 'res' element.

set_intensitymean ($intensitymean) [private]
     defines the mean value of all intensity values in the audio file.
     $intensitymean: float mean value of all intensity values.

set_intensitymeancor ($intensitymeancor) [private]
     defines the mean value of all intensity values of measure points in the 
     audio file which have a pitch value.
     $intensitymeancor: float mean value of all intensity values with pitch.

set_intensitymedianmean ($intensitymedianmean) [private]
     defines the mean value of all intensity median values in the audio file.
     $intensitymedianmean: mean of all intensity median values.

set_intensitymedianmeancor ($intensitymedianmeancor) [private]
     defines the mean value of all intensity median values of measure points in 
     the audio file which have a pitch value.
     $intensitymedianmeancor: mean of all intensity median values with pitch.

set_intensitythreshold ($intensitythreshold)
     defines the lowest intensity that will be drawn in the animation.
     $intensitythreshold: integer value of threshold.

set_lastx ($lastx) [private]
     defines the last 'res' element in the xml file that will be drawn in the 
     animation.
     $lastx: child element 'time' of the 'res' element.
     
set_lowy ($lowy)
     defines the vertical position of the lowest shape that will be drawn.
     $lowy: vertical position in pixels from bottom.

set_maxintensitymedian ($maxintensitymedian) [private]
     defines the highest intensity median value of the audio file.
     $maxintensitymedian: highest intensity median value.

set_maxpitch ($maxpitch) [private]
     defines the highest pitch value of the audio file.
     $maxpitch: highest pitch value.

set_maxpitchmedian ($maxpitchmedian) [private]
     defines the highest pitch median value of the audio file.
     $maxpitchmedian: highest pitch median value.

set_minintensitymedian ($minintensitymedian) [private]
     defines the lowest intensity median value of the audio file.
     $minintensitymedian: lowest intensity median value.

set_minpitch ($minpitch) [private]
     defines the lowest pitch value of the audio file.
     $minpitch: lowest pitch value.

set_minpitchmedian ($minpitchmedian) [private]
     defines the lowest pitch median value of the audio file.
     $minpitchmedian: lowest pitch median value.

set_opacity ($opacity)
     defines the opacity of the drawn shapes.
     $opacity: float between 0.1 (almost completely transparent) and 1 (not transparant).

set_pitcherrvis ($pitcherrvis)
     defines if pitch error visualization is on or off.
     $pitcherrvis: 'true' (on) or 'false' (off).

set_pitchhigh ($pitchhigh)
     defines the maximum pitch that will be drawn in a none error shape/color
     (if error visualization is on).
     $pitchhigh: pitch value that will be compared to the child element 
          'pit' of the 'res' element.
     
set_pitchmean ($pitchmean) [private]
     defines the mean value of all pitch values in the audio file.
     $pitchmean: float mean value of all pitch values.

set_pitchmeancor ($pitchmeancor) [private]
     defines the mean value of all pitch values of measure points in the 
     audio file which have a pitch value.
     $pitchmeancor: float mean value of all pitch values with pitch.

set_pitchmedianmean ($pitchmedianmean) [private]
     defines the mean value of all pitch median values in the audio file.
     $pitchmedianmean: mean of all pitch median values.

set_pitchmedianmeancor ($pitchmedianmeancor) [private]
     defines the mean value of all pitch median values of measure points in 
     the audio file which have a pitch value.
     $pitchmedianmeancor: mean of all pitch median values with pitch.

set_pitchthreshold ($pitchthreshold)
     defines the value of the pitch where the shape error visualization changes
     dramatically (from octagonal shape to cross shape).
     $pitchthreshold: integer value of pitch threshold.

set_postframes ($postframes)
     defines the number of frames that will additional be drawn at the end of 
     the animation. These frames will be the same as the last generated frame.
     $postframes: number of frames.

set_praatexe ($praatexe)
     defines the path to the executable PRAAT program.
     $praatexe: path to the PRAAT executable.

set_praatscript ($praatscript)
     defines the path to the vest.praat script.
     $praatscript: path to the praat script.

set_preframes ($preframes)
     defines the number of frames that will additional be drawn at the beginning
     of the animation. These frames will only contain the background.
     $preframes: number of frames.

set_rdeviation ($rdeviation)
     defines the amount of deviation from the base color of the shape in error 
     visualization.
     $rdeviation: the amount of deviation.

set_render ($render)
     This flag is only for external use, the class itself doesn't use this flag.
     $render: 'true' or 'false'.

set_shadow ($shadow)
     defines if shadows are drawn behind the shapes.
     $shadow: 'true' (shadow are drawn) or 'false' (no shadows are drawn).
     no shadows is approximately 3x faster

set_shapeerrvis ($shapeerrvis)
     defines the mode of the error shape visualization.
     $shapeerrvis:
       'continuous' - error visualization gets stronger when error is bigger.
       'discrete' - same error visualization for all errors.

set_textfont ($textfont)
     defines path to the ttf font that will be used for plain text in static 
     "animation".
     $textfont: path to a ttf font file.

set_usrdir ($dir)
    defines unique temporary subdirectory name for temporary generated images and animations.
    if $dir is a string, the subdirectory name will be this.
    if $dir is not a string, the subdirectory name is generated based on md5 hash of time, ip and browser info.
    if $dir is an empty string, no subdirectory will be used.

set_type ($type)
     defines type of visualization.
     $type:
       'error_analytic'    - visualize both intensity and pitch:
                                intensity: size of shape
                                pitch: vertical displacement
                             display intensity and pitch errors in separate ways:
                                intensity errors: color
                                pitch errors: shape
       'error_shape'       - visualize both intensity and pitch:
                                intensity: size of shape
                                pitch: vertical displacement
                             display both intensity and pitch errors with shapes
       'error_color'       - visualize both intensity and pitch:
                                intensity: size of shape
                                pitch: vertical displacement
                             display both intensity and pitch errors with colors
       'no_error_pitch_y'  - use vertical displacement for pitch visualization
                             no intensity visualization
                             no error visualization
       'no_error_pitch_r'  - use shape size for pitch visualization
                             no intensity visualization
                             no error visualization
       'no_error_intens_y' - use vertical displacement for intensity visualization
                             no pitch visualization
                             no error visualization
       'no_error_intens_r' - use shape size for intensity visualization
                             no pitch visualization
                             no error visualization
       'no_error'          - visualize both intensity and pitch:
                                intensity: size of shape
                                pitch: vertical displacement
                             no error visualization
       'static_graph'      - visualize in static graph: EST like visualization

set_valuesavailable ($valuesavailable) [private]
     set to true when minimum and maximum values of intensity and pitch of all 
     measure points of the audio file are calculated.
     $valuesavailable: boolean.

set_width ($width)
     defines the width of the animation.
     $width: width in pixels.
     
set_xmarge ($xmarge)
     defines the distance between the left border of the
     animation and the center of the first shape to be drawn; this is the same
     number as the distance between the right border of the animation and the 
     center of the last shape to be drawn.
     $xmarge: the number of pixels of the marge.

set_xscale ($xscale) [private]
     defines the distance between the centers of two consecutive shapes that
     will be drawn for the animation.
     $xscale: distance in pixels.

The VestPoint class
===================
__construct ($time, $intensity, $pitch)
     $time: time in seconds of measure point.
     $intensity: intensity value (usualy between 0 and 100).
     $pitch: pitch value (usualy between 0 and 400).
     
get_intensity ()
     returns the intensity value of the VestPoint.

get_intensitymedian ()
     returns the intensity median value of the VestPoint, calculated from the
     point itself and one mearure point earlier and one later.

get_intensitymedian5 ()
     returns the intensity median value of the VestPoint, calculated from the
     point itself and two mearure points earlier and teo later.

get_pitch ()
     returns the pitch value of the VestPoint.

get_pitchmedian ()
     returns the pitch median value of the VestPoint, calculated from the
     point itself and one mearure point earlier and one later.

get_pitchmedian5 ()
     returns the pitch median value of the VestPoint, calculated from the
     point itself and two mearure points earlier and teo later.

get_r ()
     returns the radius of the shape (disc, cross or star) to be drawn.

get_time ()
     returns the time in seconds of the VestPoint.

get_x ()
     returns the horizontal position of the shape (disc, cross or star) to be 
     drawn.

get_y ()
     returns the vertical position of the shape (disc, cross or star) to be 
     drawn.

set_intensitymedian ($intensitymedian) [private]
     sets the intensity median value of the VestPoint, calculated from the
     point itself and one mearure point earlier and one later.
 
set_intensitymedian5 ($intensitymedian5) [private]
     sets the intensity median value of the VestPoint, calculated from the
     point itself and two mearure points earlier and teo later.

set_pitchmedian ($pitchmedian) [private]
     sets the pitch median value of the VestPoint, calculated from the
     point itself and one mearure point earlier and one later.

set_pitchmedian5  ($pitchmedian5) [private]
     sets the pitch median value of the VestPoint, calculated from the
     point itself and two mearure points earlier and teo later.

set_r ($r) [private]
     sets the radius of the shape (disc, cross or star) to be drawn.

set_x ($x) [private]
     sets the horizontal position of the shape (disc, cross or star) to be 
     drawn.

set_y ($y) [private]
     sets the vertical position of the shape (disc, cross or star) to be drawn.

The VestImagickDraw class (extends ImagickDraw)
=========================
cross ($cx, $cy, $r, $dev=0)
     draws a cross shape on position $cx,$cy with diameter $r.
     if $dev is set, a value <73 makes the cross 'rounder', higher values make 
     the cross 'sharper'.
     
star ($cx, $cy, $r, $len)
     draws a four pointed star shape on position $cx,$cy with diameter $r.
     $len defines the sharpnes of the star, where a value of 0 gives a sharp
     star shape and a value equal to diameter $r an almost round octagonal 
     shape.

*/

	class VestVisualization { //************************************************************************************

		function __construct ($basename, $praatexe, $praatscript) {
			$this->basename        = $basename;
			$this->praatexe        = $praatexe;
			$this->praatscript     = $praatscript;
			$this->pitcherrvis     = true;
			$this->intensityerrvis = true;
			$this->imgformat       = "bmp";
			$this->shadow          = false;
			$this->set_usrdir (0);
			// $this->writelog ("construct");
			// $this->type         = $type;
			// $this->color_errvis = $color_errvis;
			// $this->shape_errvis = $shape_errvis;
			// $this->animbasename = "$basename-$type-$color_errvis-$shape_errvis";
	  }

		function set_logfile                ($logfile)                {$this->logfile                = $logfile;}
		function set_imgformat              ($imgformat)              {$this->imgformat              = $imgformat;}
		function set_type                   ($type)                   {$this->type                   = $type;}
		function set_colorerrvis            ($colorerrvis)            {$this->colorerrvis            = $colorerrvis;}
		function set_shapeerrvis            ($shapeerrvis)            {$this->shapeerrvis            = $shapeerrvis;}
		function set_render                 ($render)                 {$this->render                 = $render;}
		function set_shadow                 ($shadow)                 {$this->shadow                 = $shadow;}
		function set_ffmpeg                 ($ffmpeg_path)            {$this->ffmpeg                 = $ffmpeg_path;}
		function set_qtfaststart            ($qtfaststart_path)       {$this->qtfaststart            = $qtfaststart_path;}
		function set_opacity                ($opacity)                {$this->opacity                = $opacity;}
		private function set_firstx         ($firstx)                 {$this->firstx                 = $firstx;}
		private function set_lastx          ($lastx)                  {$this->lastx                  = $lastx;}
		private function set_xscale                 ($xscale)                 {$this->xscale                 = $xscale;}
		private function set_valuesavailable ($valuesavailable)       {$this->valuesavailable        = $valuesavailable;}
		function set_width                  ($width)                  {$this->width                  = $width;}
		function set_height                 ($height)                 {$this->height                 = $height;}
		function set_xmarge                 ($xmarge)                 {$this->xmarge                 = $xmarge;}
		function set_intensitythreshold     ($intensitythreshold)     {$this->intensitythreshold     = $intensitythreshold;}
		function set_pitchthreshold         ($pitchthreshold)         {$this->pitchthreshold         = $pitchthreshold;}
		function set_praatexe               ($praatexe)               {$this->praatexe               = $praatexe;}
		function set_praatscript            ($praatscript)            {$this->praatscript            = $praatscript;}
		function set_disccol_r              ($disccol_r)              {$this->disccol_r              = $disccol_r;}              // decimal value for red
		function set_disccol_g              ($disccol_g)              {$this->disccol_g              = $disccol_g;}              // decimal value for green
		function set_disccol_b              ($disccol_b)              {$this->disccol_b              = $disccol_b;}              // decimal value for blue
		function set_rdeviation             ($rdeviation)             {$this->rdeviation             = $rdeviation;}
		private function set_pitchmean      ($pitchmean)              {$this->pitchmean              = $pitchmean;}
		private function set_intensitymean  ($intensitymean)          {$this->intensitymean          = $intensitymean;}
		private function set_pitchmedianmean ($pitchmedianmean)       {$this->pitchmedianmean        = $pitchmedianmean;}
		private function set_intensitymedianmean ($intensitymedianmean) {$this->intensitymedianmean  = $intensitymedianmean;}
		private function set_pitchmeancor   ($pitchmeancor)           {$this->pitchmeancor           = $pitchmeancor;}
		private function set_intensitymeancor ($intensitymeancor)     {$this->intensitymeancor       = $intensitymeancor;}
		private function set_pitchmedianmeancor ($pitchmedianmeancor) {$this->pitchmedianmeancor     = $pitchmedianmeancor;}
		private function set_intensitymedianmeancor ($intensitymedianmeancor) {$this->intensitymedianmeancor = $intensitymedianmeancor;}
		private function set_minpitchmedian ($minpitchmedian)         {$this->minpitchmedian         = $minpitchmedian;}
		private function set_maxpitchmedian ($maxpitchmedian)         {$this->maxpitchmedian         = $maxpitchmedian;}
		private function set_minpitch       ($minpitch)               {$this->minpitch               = $minpitch;}
		private function set_maxpitch       ($maxpitch)               {$this->maxpitch               = $maxpitch;}
		private function set_minintensitymedian ($minintensitymedian) {$this->minintensitymedian     = $minintensitymedian;}
		private function set_maxintensitymedian ($maxintensitymedian) {$this->maxintensitymedian     = $maxintensitymedian;}
		function set_pitchhigh              ($pitchhigh)              {$this->pitchhigh              = $pitchhigh;}
		function set_intensitylow           ($intensitylow)           {$this->intensitylow           = $intensitylow;}
		function set_heightscale            ($heightscale)            {$this->heightscale            = $heightscale;}
		function set_lowy                   ($lowy)                   {$this->lowy                   = $lowy;}
		function set_pitcherrvis            ($pitcherrvis)            {$this->pitcherrvis            = $pitcherrvis;}
		function set_intensityerrvis        ($intensityerrvis)        {$this->intensityerrvis        = $intensityerrvis;}
		function set_drawguides             ($guidedistance)          {$this->drawguides             = $guidedistance;}
		function set_preframes              ($preframes)              {$this->preframes              = $preframes;}
		function set_postframes             ($postframes)             {$this->postframes             = $postframes;}
		function set_textfont               ($textfont)               {$this->textfont               = $textfont;}
		function set_boldfont               ($boldfont)               {$this->boldfont               = $boldfont;}
		function get_basename           () {return $this->basename;}
		function get_logfile            () {return $this->logfile;}
		function get_imgformat          () {return $this->imgformat;}
		function get_opacity            () {return $this->opacity;}
		function get_type               () {return $this->type;}
		function get_usrdir             () {return $this->usrdir;}
		function get_colorerrvis        () {return $this->colorerrvis;}
		function get_shapeerrvis        () {return $this->shapeerrvis;}
		function get_wavname            () {return "$this->basename.wav";}
		function get_xmlname            () {return "$this->basename.xml";}
		function get_animbasename       () {return $this->animbasename;}
		function get_animname           () {return "$this->animbasename.mp4";}
		function get_pointscount        () {return count($this->points);}
		function get_points             () {return $this->points;}
		function get_firstx             () {return $this->firstx;}
		function get_lastx              () {return $this->lastx;}
		function get_xscale             () {return $this->xscale;}
		function get_render             () {return $this->render;}
		function get_valuesavailable    () {return $this->valuesavailable;}
		function get_width              () {return $this->width;}
		function get_height             () {return $this->height;}
		function get_xmarge             () {return $this->xmarge;}
		function get_intensitythreshold () {return $this->intensitythreshold;}
		function get_pitchthreshold     () {return $this->pitchthreshold;}
		function get_praatexe           () {return $this->praatexe;}
		function get_praatscript        () {return $this->praatscript;}
		function get_basecolor          () {return $this->basecolor;}
		function get_disccol_r          () {return $this->disccol_r;}
		function get_disccol_g          () {return $this->disccol_g;}
		function get_disccol_b          () {return $this->disccol_b;}
		function get_rdeviation         () {return $this->rdeviation;}
		function get_pitchhigh          () {return $this->pitchhigh;}
		function get_intensitylow       () {return $this->intensitylow;}
		function get_heightscale        () {return $this->heightscale;}
		function get_lowy               () {return $this->lowy;}
		function get_pitcherrvis        () {return $this->pitcherrvis;}
		function get_intensityerrvis    () {return $this->intensityerrvis;}
		function get_drawguides         () {return $this->drawguides;}
		function get_preframes          () {if (isset($this->preframe)) return $this->preframes;}
		function get_postframes         () {return $this->postframes;}
		function get_textfont           () {return $this->textfont;}
		function get_boldfont           () {return $this->boldfont;}

		function writelog ($msg)
		{
			$logfile = $this->get_logfile();
 			file_put_contents ($logfile, "$msg\n", FILE_APPEND);			
		}

		function set_basecolor ($basecolor) {
			// decimal value for r, g and b, so color will be grey
			$this->basecolor = $basecolor;
			$this->set_disccol_r ($basecolor);
			$this->set_disccol_g ($basecolor);
			$this->set_disccol_b ($basecolor);
		}

		function set_background ($bg_type, $bg_var1, $bg_var2=0) {
			$this->bg_type = $bg_type; // "color", "gradient"  , "image"               , "static_graph"
			$this->bg_var1 = $bg_var1; //  color ,  upper color,  img path             , animation start color
			$this->bg_var2 = $bg_var2; //  -     ,  lower color,  animation start color, highest pitch
		}

		function set_animbasename () {
			$basename     = $this->get_basename();
			$type         = $this->get_type();
			$color_errvis = $this->get_colorerrvis();
			$shape_errvis = $this->get_shapeerrvis();
			$this->animbasename = "$basename-$type-$color_errvis-$shape_errvis";
		}

		function set_usrdir ($dir) {
			if (is_string($dir)){
				//if (strlen($dir){
					$this->usrdir = $dir;
				//}
			}else{
				// print ("dir is not set<hr/>\n");
				$time  = time();
				$agent = $_SERVER ["HTTP_USER_AGENT"];
				$ip    = $_SERVER ["REMOTE_ADDR"];
				$this->usrdir = md5 ($time.$ip.$agent);
			}
			// print ("$time$ip$agent<br/>{$this->usrdir}<hr/>\n");
		}

		function create_xml () {
			// $this->writelog ("create_xml");
			// This doesn't work anymore with new versions of praat.
			// Praat 5.3.40 throws an error when executed with php within Apache, error message:
			// "terminate called after throwing an instance of 'MelderError' Aborted (core dumped)"
			// See a.o. http://uk.groups.yahoo.com/group/praat-users/message/5962
			// Old sources available in old ubuntu distributions https://launchpad.net/ubuntu/+source/praat/5.2.17-1
			// Other solution is to create xml files manually beforehand, if xml file exists, create_xml() will not be called.
			$praatexe    = $this->get_praatexe();
			$praatscript = $this->get_praatscript();
			$cwd = getcwd();
			// $this->writelog ("cwd: $cwd");
			$cmd = "$praatexe $praatscript \"{$cwd}/{$this->basename}.wav\" \"{$cwd}/{$this->basename}.xml\"";
			// $this->writelog ("cmd: $cmd\n");
			exec ($cmd);
		}

		function put_xmldata () {
			// $this->writelog ("put_xmldata()");
			// print ("this->get_xmlname(): ".$this->get_xmlname()."<br />\n");
			if (!file_exists($this->get_xmlname())){
				// print ("this->create_xml();<br />\n");
				$this->create_xml();
			}
			$tmp = $this->get_xmlname();
			// $this->writelog ("new SimpleXMLElement '$tmp'");
			$xml = new SimpleXMLElement($this->get_xmlname(), NULL, TRUE);
			// $this->writelog ("new SimpleXMLElement done");
			foreach ($xml->res as $node) {
				$this->points [] = new VestPoint ($node->time, $node->int, intval ($node->pit));
			}
			// $this->writelog ("put_xmldata() END");
		}

		function calculate_medians () {
			// $this->writelog ("calculate_medians()");
			if (!$this->get_pointscount()){
				$this->put_xmldata();
			}
			$pointscount        = $this->get_pointscount();
			$intensitythreshold = $this->get_intensitythreshold();
			$anim_width         = $this->get_width();
			$xmarge             = $this->get_xmarge();
			$points             = $this->get_points();
			foreach ($points as $nr => $point){
				// pitch //
				unset ($pm3s);
				unset ($pm5s);
				// get pitch of current point
				$pm3s[] = $pm5s[] = intval ($point->get_pitch());
				// get pitch of previous point
				if ($nr>0){
					$pm3s[] = $pm5s[] = intval ($points[$nr-1]->get_pitch());
				}else{
					$pm3s [] = $pm5s[] = 0;
				}
				// get pitch of point before previous point, needed for median on 5 points
				if ($nr>1){
					$pm5s[] = intval ($points[$nr-2]->get_pitch());
				}else{
					$pm5s[] = 0;
				}
				// get pitch of next point
				if ($nr<$pointscount-1){
					$pm3s[] = $pm5s[] = intval ($points[$nr+1]->get_pitch());
				}else{
					$pm3s [] = $pm5s[] = 0;
				}
				// get pitch of point after next point, needed for median on 5 points
				if ($nr<$pointscount-2){
					$pm5s[] = intval ($points[$nr+2]->get_pitch());
				}else{
					$pm5s[] = 0;
				}
				// sort 3 pitch values and assign middle value as median pitch of current point
				sort ($pm3s);
				$point->set_pitchmedian($pm3s[1]);
				// sort 5 pitch values and assign middle value as median5 pitch of current point
				sort ($pm5s);
				$point->set_pitchmedian5($pm5s[2]);
				// intensity //
				unset ($im3s);
				unset ($im5s);
				// get intensity of current point
				$im3s[] = $im5s[] = intval ($point->get_intensity());
				// get intensity of previous point
				if ($nr>0){
					$im3s[] = $im5s[] = intval ($points[$nr-1]->get_intensity());
				}else{
					$im3s [] = $im5s[] = 0;
				}
				// get intensity of point before previous point, needed for median on 5 points
				if ($nr>1){
					$im5s[] = intval ($points[$nr-2]->get_intensity());
				}else{
					$im5s[] = 0;
				}
				// get intensity of next point
				if ($nr<$pointscount-1){
					$im3s[] = $im5s[] = intval ($points[$nr+1]->get_intensity());
				}else{
					$im3s [] = $im5s[] = 0;
				}
				// get intensity of point after next point
				if ($nr<$pointscount-2){
					$im5s[] = intval ($points[$nr+2]->get_intensity());
				}else{
					$im5s[] = 0;
				}
				// sort 3 intensity values and assign middle value as median intensity of current point
				sort ($im3s);
				$point->set_intensitymedian($im3s[1]);
				// sort 5 intensity values and assign middle value as median5 intensity of current point
				sort ($im5s);
				$point->set_intensitymedian5($im5s[2]);
				// first & last value
				$intensityvalue = $point->get_intensity() - $intensitythreshold;
				if ($intensityvalue>0 && !$firstx){
					$firstx = $point->get_time();
					// print ("firstx {$firstx}<br/>\n");
				}
				if ($intensityvalue>0){
					$lastx  = $point->get_time();
				}
			}
			$this->set_firstx ($firstx);
			$this->set_lastx ($lastx);
			// in first version factor was 2.9 in stead of 2.2
			if ($lastx != $firstx){
				$this->set_xscale (floatval($anim_width - 2.2*$xmarge) / floatval(floatval($lastx) - floatval($firstx)));
			}else{
				$this->set_xscale (floatval($anim_width - 2.2*$xmarge));
			}
		}

		function calculate_mean () {
			// print ("calculate_mean {$this->basename}<br/>\n");
			if (!$this->get_pointscount()) $this->put_xmldata();
			if (!$this->get_xscale()) $this->calculate_medians();
			$pointscount        = $this->get_pointscount();
			$points             = $this->get_points();
			foreach ($points as $nr => $point){
				$pitch       = intval ($point->get_pitch());
				$pitchmedian = intval ($point->get_pitchmedian());
				$pitch_sum           += $pitch;
				$intensity_sum       += intval ($point->get_intensity());
				$pitchmedian_sum     += $pitchmedian;
				$intensitymedian_sum += intval ($point->get_intensitymedian());
				// _cor = corrected = omit measure points that contain no pitch (silence or consonant)
				if ($pitch){
					$pitch_sum_cor     += $pitch;
					$intensity_sum_cor += intval ($point->get_intensity());
					$pitch_pointscount++;
				}
				if ($pitchmedian){
					$pitchmedian_sum_cor     += $pitch;
					$intensitymedian_sum_cor += intval ($point->get_intensity());
					$pitchmedian_pointscount++;
				}
			}
			$pitchmean              = $pitch_sum               / $pointscount;
			$intensitymean          = $intensity_sum           / $pointscount;
			$pitchmedianmean        = $pitchmedian_sum         / $pointscount;
			$intensitymedianmean    = $intensitymedian_sum     / $pointscount;
			if ($pitch_pointscount){
				$pitchmeancor         = $pitch_sum_cor           / $pitch_pointscount;
				$intensitymeancor     = $intensity_sum_cor       / $pitch_pointscount;
			}else{
				$pitchmeancor         = 0;
				$intensitymeancor     = 0;
			}
			if ($pitchmedian_pointscount){
				$pitchmedianmeancor     = $pitchmedian_sum_cor     / $pitchmedian_pointscount;
				$intensitymedianmeancor = $intensitymedian_sum_cor / $pitchmedian_pointscount;
			}else{
				$pitchmedianmeancor     = 0;
				$intensitymedianmeancor = 0;
			}
			// print ("pitchmean {$this->basename}: $pitchmean<br/>\n");
			$this->set_pitchmean              ($pitchmean);
			$this->set_intensitymean          ($intensitymean);
			$this->set_pitchmedianmean        ($pitchmedianmean);
			$this->set_intensitymedianmean    ($intensitymedianmean);
			$this->set_pitchmeancor           ($pitchmeancor);
			$this->set_intensitymeancor       ($intensitymeancor);
			$this->set_pitchmedianmeancor     ($pitchmedianmeancor);
			$this->set_intensitymedianmeancor ($intensitymedianmeancor);
		}

		function get_pitchmean () {
			// print ("get_pitchmean {$this->basename}<br/>\n");
			if (!$this->pitchmean){
				$this->calculate_mean();
			}
			return $this->pitchmean;
		}

		function get_intensitymean () {
			if (!$this->intensitymean){
				$this->calculate_mean();
			}
			return $this->intensitymean;
		}

		function get_pitchmedianmean () {
			// print ("get_pitchmedianmean {$this->basename}<br/>\n");
			if (!$this->pitchmedianmean){
				$this->calculate_mean();
			}
			return $this->pitchmedianmean;
		}

		function get_intensitymedianmean () {
			if (!$this->intensitymedianmean){
				$this->calculate_mean();
			}
			return $this->intensitymedianmean;
		}

		function get_pitchmeancor () {
			// print ("get_pitchmeancor {$this->basename}<br/>\n");
			if (!$this->pitchmeancor){
				$this->calculate_mean();
			}
			return $this->pitchmeancor;
		}

		function get_intensitymeancor () {
			if (!$this->intensitymeancor){
				$this->calculate_mean();
			}
			return $this->intensitymeancor;
		}

		function get_pitchmedianmeancor () {
			// print ("get_pitchmedianmean {$this->basename}<br/>\n");
			if (!$this->pitchmedianmeancor){
				$this->calculate_mean();
			}
			return $this->pitchmedianmeancor;
		}

		function get_intensitymedianmeancor () {
			if (!$this->intensitymedianmeancor){
				$this->calculate_mean();
			}
			return $this->intensitymedianmeancor;
		}

		function get_minpitchmedian () {
			if (!$this->get_valuesavailable()) $this->calculate_values();
			return $this->minpitchmedian;
		}

		function get_maxpitchmedian () {
			if (!$this->get_valuesavailable()) $this->calculate_values();
			return $this->maxpitchmedian;
		}

		function get_minpitch () {
			if (!$this->get_valuesavailable()) $this->calculate_values();
			return $this->minpitch;
		}

		function get_maxpitch () {
			if (!$this->get_valuesavailable()) $this->calculate_values();
			return $this->maxpitch;
		}

		function get_minintensitymedian () {
			if (!$this->get_valuesavailable()) $this->calculate_values();
			return $this->minintensitymedian;
		}

		function get_maxintensitymedian () {
			if (!$this->get_valuesavailable()) $this->calculate_values();
			return $this->maxintensitymedian;
		}

		function calculate_values() {
			// $this->writelog ("calculate_values()");
			if (!$this->get_xscale()) $this->calculate_medians();
			// $this->writelog ("out of calculate_medians()");
			$intensitythreshold = $this->get_intensitythreshold();
			// print ("intensitythreshold: $intensitythreshold<br/>\n");
			$xmarge             = $this->get_xmarge();
			$anim_height        = $this->get_height(); // 300
			if ($height_scale   = $this->get_heightscale()){
				$low_y = $this->get_lowy();
			}
			$min_pitchmedian =  99999999;
			$max_pitchmedian = -99999999;
			$min_pitch       =  99999999;
			$max_pitch       = -99999999;
			$min_intensitymedian =  99999999;
			$max_intensitymedian = -99999999;
			$points = $this->get_points();
			foreach ($points as $nr => $point){
				$distance = floatval ($point->get_time()) - floatval ($this->get_firstx());
				$point->set_x ($xmarge + intval ($distance * $this->get_xscale()));
				$pitchmedian = $point->get_pitchmedian();
				$pitch = $point->get_pitch();
				$intensitymedian = $point->get_intensitymedian();
				$intensitymedianvalue = $intensitymedian - $intensitythreshold;
				if ($pitchmedian > 0  && $intensitymedianvalue > 0  && $nr >= 0){
					$point->set_y ($anim_height + $low_y - round($pitchmedian*$height_scale));
					// in first version factor was 1.44 in stead of 1.5
					$point->set_r (round ($intensitymedianvalue * 1.5));
					$pointscount = $this->get_pointscount();
					if ($nr>0){
						// attention: pitchmedian is used for previous and next pitch
						$pitch_prev     = intval ($points[$nr-1]->get_pitchmedian());
					}else{
						$pitch_prev     = 0;
					}
					if ($nr<$pointscount-1){
						$pitch_next     = intval ($points[$nr+1]->get_pitchmedian());
					}else{
						$pitch_next     = 0;
					}
					if ($pitch_prev && $pitch_next){
						if ($pitchmedian < $min_pitchmedian){
							$min_pitchmedian = $pitchmedian;
							// print ("min_pitchmedian = $min_pitchmedian<br/>");
						}
						if ($pitchmedian > $max_pitchmedian){
							$max_pitchmedian = $pitchmedian;
							// print ("max_pitchmedian = $max_pitchmedian<br/>");
						}
						if ($intensitymedian < $min_intensitymedian){
							$min_intensitymedian = $intensitymedian;
							// print ("min_intensitymedian = $min_intensitymedian; intensitymedianvalue: $intensitymedianvalue<br/>");
						}
						if ($intensitymedian > $max_intensitymedian){
							$max_intensitymedian = $intensitymedian;
							// print ("max_intensitymedian = $max_intensitymedian<br/>");
						}
					}

					if ($pitch < $min_pitch){
						$min_pitch = $pitch;
						// print ("min_pitch = $min_pitch<br/>");
					}
					if ($pitch > $max_pitch){
						$max_pitch = $pitch;
						//////////print ($this->get_basename().": max_pitch = $max_pitch<br/>");
					}

				}
			}
			$this->set_valuesavailable (true);
			$this->set_minpitchmedian ($min_pitchmedian);
			$this->set_maxpitchmedian ($max_pitchmedian);
			$this->set_minpitch ($min_pitch);
			$this->set_maxpitch ($max_pitch);
			$this->set_minintensitymedian ($min_intensitymedian);
			$this->set_maxintensitymedian ($max_intensitymedian);
		}

		function create_animation () {
			// $this->writelog ("create_animation");
			// return;
			if (!$this->get_valuesavailable()) $this->calculate_values();
			// $this->writelog ("out of calculate_values()");
			$anim_width  = $this->get_width();
			$anim_height = $this->get_height();
			if ($this->usrdir){
				// $cwd = getcwd();
				// $this->writelog ("cwd: $cwd");
				// $this->writelog ("mkdir (images)");
				mkdir ("images");
			}
			// $this->writelog ("this->bg_type: '{$this->bg_type}'");
			$intensitythreshold = $this->get_intensitythreshold();
			$intensity_low = $this->get_intensitylow();
			$pitch_high = $this->get_pitchhigh();
			$canvas = new Imagick();
			// $this->writelog ("new Imagick()");
			switch ($this->bg_type){
				case "image":
					$canvas->readImage($this->bg_var1);
					break;
				case "color":
					$canvas->newImage($anim_width, $anim_height, "none");
					$background = new Imagick();
					$background->newPseudoImage($anim_width, $anim_height, "canvas:#$this->bg_var1");
					$canvas->compositeImage($background, imagick::COMPOSITE_OVER, 0, 0);
					break;
				case "gradient":
					$canvas->newImage($anim_width, $anim_height, "none");
					$background = new Imagick();
					$background->newPseudoImage($anim_width, $anim_height, "gradient:#$this->bg_var1-#$this->bg_var2");
					$canvas->compositeImage($background, imagick::COMPOSITE_OVER, 0, 0);
					break;
				case "static_graph":
					$canvas->newImage($anim_width, $anim_height, "none");
 					$canvas->newImage($anim_width, $anim_height, "#$this->bg_var1");
					// $this->create_static_background ($canvas, $this->bg_var2, $this->get_textfont(), $this->get_boldfont());
					break;
				default:
					$canvas->newImage($anim_width, $anim_height, "none");
					$background = new Imagick();
					$background->newPseudoImage($anim_width, $anim_height, "gradient:#eeffff-#666688");
					$canvas->compositeImage($background, imagick::COMPOSITE_OVER, 0, 0);
					break;
			}
			$canvas->setImageFormat($this->imgformat);
			$draw = new VestImagickDraw ();
			$draw->setFillOpacity (0);
			$draw->rectangle (0, 0, $anim_width-1, $anim_height-1);
			$canvas->drawImage ($draw);
			if ($guidedistance = $this->get_drawguides()){
				$draw->setStrokeColor ("#88aaff");
				$draw->setStrokeOpacity (.5);
				$draw->setStrokeDashArray(array(1,5));
		 		for ($i=$guidedistance; $i<$anim_width; $i+=$guidedistance){
					$draw->line ($i, 0, $i, $anim_height-1);
				}
				$canvas->drawImage ($draw);
			}
			$draw->setStrokeOpacity (1);
			if ($pointscount = $this->get_pointscount()){
				$basename      = $this->get_basename();
				if (!$animbasename){
					$this->set_animbasename ();
				}
				$animbasename  = $this->get_animbasename();
				$points        = $this->get_points();
				$type          = $this->get_type();
				$color_errvis  = $this->get_colorerrvis();
				$shape_errvis  = $this->get_shapeerrvis();
				$pitch_errvis  = $this->get_pitcherrvis();
				$intens_errvis = $this->get_intensityerrvis();
				$base_color    = $this->get_basecolor();
				$disc_col_r    = $this->get_disccol_r();
				$disc_col_g    = $this->get_disccol_g();
				$disc_col_b    = $this->get_disccol_b();
				$r_deviation   = $this->get_rdeviation();
				$dev_step      = floor ((($disc_col_r+$disc_col_g+$disc_col_b)/3)/$r_deviation); // 150/20=7.5
				// $dev_step      = floor ($base_color/$r_deviation); // 150/20=7.5
				// print ("disc_col_r:     $disc_col_r<br/>\n");
				// print ("r_deviation:     $r_deviation<br/>\n");
				// print ("dev_step:     $dev_step<br/>\n");
				$p_threshold   = $anim_height - $this->get_pitchthreshold(); // 300-175=125
				$pitchmedianmeancor     = $this->get_pitchmedianmeancor();     // mean pitch     with median and zero-pitch omitting correction
				$intensitymedianmeancor = $this->get_intensitymedianmeancor(); // mean intensity with median and zero-pitch omitting correction
				if ($preframes = $this->get_preframes()){
					for ($i=0; $i<$preframes; $i++){
						// if ($this->usrdir){
						//	$framename = sprintf ("images/{$this->usrdir}/$animbasename-%05d.{$this->imgformat}", $i);
						// }else{
							$framename = sprintf ("images/$animbasename-%05d.{$this->imgformat}", $i);
						// }
						$canvas->writeImage ($framename);
					}
				}

				if ($type == "static_graph"){
					$up_thr_color  = "#2f4f4f";
					$low_thr_color = "#008b8b";
					$graph_color   = "#b22222";
					$text_color    = "#0b333c";
					// write first blanc frame:
					$background = new Imagick();
					// $background->newPseudoImage($anim_width, $anim_height, "canvas:#$this->bg_var1");
					$background->newPseudoImage($anim_width, $anim_height, "gradient:#eeffff-#666688");
					$canvas->compositeImage($background, imagick::COMPOSITE_OVER, 0, 0);
					// if ($this->usrdir){
					// 	$framename = sprintf ("images/{$this->usrdir}/$animbasename-%05d.{$this->imgformat}", 1);
					// }else{
						$framename = sprintf ("images/$animbasename-%05d.{$this->imgformat}", 1);
					// }
					$canvas->writeImage ($framename);
					$this->create_static_background ($canvas, $this->bg_var2, $this->get_textfont(), $this->get_boldfont());
					$draw->clear();
					$draw->setStrokeWidth (2);
					$draw->setFillColor ($up_thr_color);
					$draw->setStrokeColor ($up_thr_color);
					$high_max_pitch = $this->bg_var2;
					// print ("high_max_pitch: $high_max_pitch<br/>\n");
					$pit_graph_high = 20 * floor(1 + $high_max_pitch/20);
					// print ("pit_graph_high: $pit_graph_high<br/>\n");
					// print ("pitch_high: $pitch_high<hr/>\n");
					$intensity_low = $this->get_intensitylow();
					// print ("intensity_low: $intensity_low<hr/>\n");
					$pitch_scale = 190 / ($pit_graph_high-60);
					$intns_scale = 190 / 100;
					// print ("pitch_scale: $pitch_scale<br/>\n");
					$draw->line ( 50, $anim_height-(65+($pitch_high-60)*$pitch_scale), 256, $anim_height-(65+($pitch_high-60)*$pitch_scale));
					$draw->line (325, 44, 531, 44);
					$draw->setFillColor ($low_thr_color);
					$draw->setStrokeColor ($low_thr_color);
					$draw->line (50, 240, 256, 240);
					$draw->line (325, $anim_height-(65+($intensity_low)*$intns_scale), 531, $anim_height-(65+($intensity_low)*$intns_scale));
					$draw->setFillColor( new ImagickPixel( "transparent" ) );
					$draw->setStrokeColor( new ImagickPixel($graph_color) );
					$draw->setStrokeAntialias( true );
        	$this->pitchCoordinates = array();
        	$this->intnsCoordinates = array();
        	$i = 0;
        	$signal = true;
					foreach ($points as $nr => $point){
						$psy = $py = $point->get_pitch();
						$isy = $iy = $point->get_intensity();
						// print (": $py");
						// print ($point->get_time().", intensity: $iy");
						if ($py){
							$py = $anim_height-(62+($py-60)*$pitch_scale);
						  $iy = $anim_height-(62+ $iy    *$intns_scale);
							// if ($signal) print ($point->get_time().": $psy -> $py<br/>\n");
							// if ($signal) print ($point->get_time().": $isy -> $iy<br/>\n");
							// print ("-> $iy<br/>\n");
							$signal = false;
							// $this->pitchCoordinates[$i]['x'] = $x;
							$this->pitchCoordinates[$i]['y'] = $py;
							$this->intnsCoordinates[$i]['y'] = $iy;
							$i++;
						}else{
							// print ("<br/>\n");
						}
					}
					$px_count = count ($this->pitchCoordinates);
					// print ("x_count: $px_count<br/>\n");
					$ptime_scale = 206 / ($px_count-1);
					for ($i=0; $i<$px_count; $i++){
						$this->pitchCoordinates[$i]['x'] = 50 + $i * $ptime_scale;
					}
					$ix_count = count ($this->intnsCoordinates);
					// print ("x_count: $px_count<br/>\n");
					$itime_scale = 206 / ($ix_count-1);
					for ($i=0; $i<$ix_count; $i++){
						$this->intnsCoordinates[$i]['x'] = 325 + $i * $itime_scale;
					}

					$draw->polyLine( $this->pitchCoordinates );
					$draw->polyLine( $this->intnsCoordinates );
					$canvas->drawImage ($draw);
					$draw->clear();
					$draw->setStrokeWidth (1);
					if ($this->get_textfont()){
						$draw->setFont( $this->get_textfont() );
					}
					$draw->setFillColor ($text_color);
					$draw->setFontSize( 10 );
					$draw->annotation(50,  257, "gemiddelde waarde: ". round($this->get_pitchmeancor()));
					$draw->annotation(325, 257, "gemiddelde waarde: ". round($this->get_intensitymeancor()));
					$canvas->drawImage ($draw);

					foreach ($points as $nr => $point){
						// if ($this->usrdir){
						//	$framename = sprintf ("images/{$this->usrdir}/$animbasename-%05d.{$this->imgformat}", 2+$nr);
						// }else{
							$framename = sprintf ("images/$animbasename-%05d.{$this->imgformat}", 2+$nr);
						// }
						$canvas->writeImage ($framename);
					}

				}else{


					foreach ($points as $nr => $point){
						$x = $point->get_x();
						if ($x>0 && $x<$anim_width){
							$y = $point->get_y();
							$r = $point->get_r();
							$pitch     = $point->get_pitchmedian();
							// for drawing, intensitymedian is used for intensity:
							$intensity = $point->get_intensitymedian();
							$intensitymedian  = $point->get_intensitymedian();
							$intensitymedian5 = $point->get_intensitymedian5();
							// attention: pitchmedian is used for previous and next pitch and intensity
							if ($nr>0){
								$r_prev         = intval ($points[$nr-1]->get_r());
								$intensity_prev = intval ($points[$nr-1]->get_intensitymedian());
								$pitch_prev     = intval ($points[$nr-1]->get_pitchmedian());
							}else{
								$r_prev         = 0;
								$intensity_prev = 0;
								$pitch_prev     = 0;
							}
							if ($nr<$pointscount-1){
								$r_next         = intval ($points[$nr+1]->get_r());
								$intensity_next = intval ($points[$nr+1]->get_intensitymedian());
								$pitch_next     = intval ($points[$nr+1]->get_pitchmedian());
							}else{
								$r_next = 0;
								$intensity_next = 0;
								$pitch_next     = 0;
							}
							switch ($type){
								case "error_analytic":
									// if (($r<15)                     && ($r_prev>0) && ($r_next>0)){
									if    (($intensity<$intensity_low) && $pitch_prev && $pitch_next && $intens_errvis){
										if ($color_errvis == "continuous"){
											$fill_r = $disc_col_r;
											$fill_g = $disc_col_g-($r_deviation-$r)*$dev_step;
											$fill_b = $disc_col_b-($r_deviation-$r)*$dev_step;
											if ($fill_r<0) $fill_r = 0;
											if ($fill_g<0) $fill_g = 0;
											if ($fill_b<0) $fill_b = 0;
											$fillcolor   = sprintf ("#%02x%02x%02x", $fill_r, $fill_g, $fill_b);
											$strokecolor = sprintf ("#%02x%02x%02x", ($r_deviation-$r)*$dev_step, 0, 0);
										}else{
											$fill_r = $disc_col_r + 75;
											if ($fill_r>255) $fill_r = 255;
											$fillcolor   = sprintf ("#%02x%02x%02x", $fill_r, 25, 25);
											$strokecolor = sprintf ("#%02x%02x%02x", 150, 0, 0);
										}
									}else{
										$fillcolor   = sprintf ("#%02x%02x%02x", $disc_col_r, $disc_col_g, $disc_col_b);
										$strokecolor = sprintf ("#%02x%02x%02x", 0, 0, 0);
									}
									// print ("fillcolor: $fillcolor<br/>\n");
									$draw->clear();
									$draw->setFillColor   ($fillcolor);
									$draw->setStrokeColor ($strokecolor);
									$draw->setStrokeWidth (1);
									if ($this->opacity){
										$draw->setFillOpacity   ($this->opacity);
										$draw->setStrokeOpacity ($this->opacity);
									}
									// if ($y     > $p_threshold){
									if    (($pitch < $pitch_high) || !$pitch_errvis){
										$draw->ellipse ($x, $y+25, round($r*$anim_height/400), round($r*$anim_height/400), 0, 360);
									}else{
										if ($shape_errvis == "continuous"){
											// dev moet eigenlijk ook uitgedrukt worden in pitch_high en pitch?
											if (($dev = $p_threshold - $y) < 1) $dev = 1;
											$draw->cross ($x, $y+25, round($r*$anim_height/400), $dev);
										}else{
											$draw->cross ($x, $y+25, round($r*$anim_height/400), 0);
										}
									}
									break;
								case "error_shape":
									$fillcolor   = sprintf ("#%02x%02x%02x", $disc_col_r, $disc_col_g, $disc_col_b);
									$strokecolor = sprintf ("#%02x%02x%02x",   0,   0,   0);
									$draw->clear();
									$draw->setFillColor   ($fillcolor);
									$draw->setStrokeColor ($strokecolor);
									$draw->setStrokeWidth (1);
									if ($this->opacity){
										$draw->setFillOpacity   ($this->opacity);
										$draw->setStrokeOpacity ($this->opacity);
									}
									// nowhere used:
									// $prev_result = ($pitch_prev < $pitch_high)  &&  ($intensity_prev >  $intensity_low);
									// $this_result = ($pitch      < $pitch_high)  &&  ($intensity      >  $intensity_low);
									// $next_result = ($pitch_next < $pitch_high)  &&  ($intensity_next >  $intensity_low);
									if    ((($pitch < $pitch_high) || !$pitch_errvis)  &&  (($intensity >= $intensity_low) || !$intens_errvis) || !$pitch_prev || !$pitch_next){
										$draw->ellipse ($x, $y+25, round($r*$anim_height/400), round($r*$anim_height/400), 0, 360);
									}else{
										if ($shape_errvis == "continuous"){
											// dev moet eigenlijk ook uitgedrukt worden in pitch_high en pitch?
											if (($dev = $p_threshold - $y) < 1) $dev = 1;
											$draw->cross ($x, $y+25, round($r*$anim_height/400), $dev);
										}else{
											$draw->cross ($x, $y+25, round($r*$anim_height/400), 0);
										}
									}
									break;
								case "error_color":
									if (($pitch > $pitch_high) && $pitch_errvis){
										// dev_color moet eigenlijk ook uitgedrukt worden in pitch_high en pitch?
										$dev_color = ($p_threshold-$y);
										if ($dev_color<0) $dev_color = 0; // this shouldn't happen, but it does with low pitch threshold percentage
										$dev_col_r = $dev_col_g = $dev_col_b = $dev_color;
										if ($dev_color > $base_color) $dev_color = $base_color; // base_color=150
										if ($dev_col_r > $disc_col_r) $dev_col_r = $disc_col_r;
										if ($dev_col_g > $disc_col_g) $dev_col_g = $disc_col_g;
										if ($dev_col_b > $disc_col_b) $dev_col_b = $disc_col_b;
										if ($color_errvis == "continuous"){
											$fillcolor   = sprintf ("#%02x%02x%02x", $disc_col_r, $disc_col_g-$dev_col_g, $disc_col_b-$dev_col_b);
											$strokecolor = sprintf ("#%02x%02x%02x", round($dev_color*1.5), 0, 0);
										}else{
											$fill_r = $disc_col_r + 75;
											if ($fill_r>255) $fill_r = 255;
											$fillcolor   = sprintf ("#%02x%02x%02x", $fill_r, 25, 25);
											$strokecolor = sprintf ("#%02x%02x%02x", 150, 0, 0);
										}
									}elseif ((($intensity<$intensity_low) && $intens_errvis) && $pitch_prev && $pitch_next){
										switch ($color_errvis){
											case "continuous":
												$fill_r = $disc_col_r;
												$fill_g = $disc_col_g-($r_deviation-$r)*$dev_step;
												$fill_b = $disc_col_b-($r_deviation-$r)*$dev_step;
												if ($fill_r<0) $fill_r = 0;
												if ($fill_g<0) $fill_g = 0;
												if ($fill_b<0) $fill_b = 0;
												$fillcolor   = sprintf ("#%02x%02x%02x", $fill_r, $fill_g, $fill_b);
												// $fillcolor   = sprintf ("#%02x%02x%02x", $disc_col_r, $disc_col_g-($r_deviation-$r)*$dev_step, $disc_col_b-($r_deviation-$r)*$dev_step);
												$strokecolor = sprintf ("#%02x%02x%02x", ($r_deviation-$r)*$dev_step, 0, 0);
												break;
											default:
												$fill_r = $disc_col_r + 75;
												if ($fill_r>255) $fill_r = 255;
												$fillcolor   = sprintf ("#%02x%02x%02x", $fill_r, 25, 25);
												$strokecolor = sprintf ("#%02x%02x%02x", 150, 0, 0);
												break;
										}
									}else{
										$fillcolor   = sprintf ("#%02x%02x%02x", $disc_col_r, $disc_col_g, $disc_col_b);
										$strokecolor = sprintf ("#%02x%02x%02x",   0,   0,   0);
									}
									// print ("fillcolor: $fillcolor<br/>\n");
									$draw->clear();
									$draw->setFillColor   ($fillcolor);
									$draw->setStrokeColor ($strokecolor);
									$draw->setStrokeWidth (1);
									if ($this->opacity){
										$draw->setFillOpacity   ($this->opacity);
										$draw->setStrokeOpacity ($this->opacity);
									}
									$draw->ellipse ($x, $y+25, round($r*$anim_height/400), round($r*$anim_height/400), 0, 360);
									break;
								case "no_error_pitch_y":
									$fillcolor   = sprintf ("#%02x%02x%02x", $disc_col_r, $disc_col_g, $disc_col_b);
									$strokecolor = sprintf ("#%02x%02x%02x", 0, 0, 0);
									$draw->clear();
									$draw->setFillColor   ($fillcolor);
									$draw->setStrokeColor ($strokecolor);
									$draw->setStrokeWidth (1);
									if ($this->opacity){
										$draw->setFillOpacity   ($this->opacity);
										$draw->setStrokeOpacity ($this->opacity);
									}
									if ($r){
										$draw->ellipse ($x, $y+25, 18, 18, 0, 360);
									}
									break;
								case "no_error_pitch_r":
									$fillcolor   = sprintf ("#%02x%02x%02x", $disc_col_r , $disc_col_g, $disc_col_b);
									$strokecolor = sprintf ("#%02x%02x%02x", 0, 0, 0);
									$draw->clear();
									$draw->setFillColor   ($fillcolor);
									$draw->setStrokeColor ($strokecolor);
									$draw->setStrokeWidth (1);
									if ($this->opacity){
										$draw->setFillOpacity   ($this->opacity);
										$draw->setStrokeOpacity ($this->opacity);
									}
									if ($r){
										$draw->ellipse ($x, $anim_height/2, round($pitch*$anim_height/2400), round($pitch*$anim_height/2400), 0, 360);
									}
									break;
								case "no_error_intens_y":
									$fillcolor   = sprintf ("#%02x%02x%02x", $disc_col_r, $disc_col_g, $disc_col_b);
									$strokecolor = sprintf ("#%02x%02x%02x", 0, 0, 0);
									$draw->clear();
									$draw->setFillColor   ($fillcolor);
									$draw->setStrokeColor ($strokecolor);
									$draw->setStrokeWidth (1);
									if ($this->opacity){
										$draw->setFillOpacity   ($this->opacity);
										$draw->setStrokeOpacity ($this->opacity);
									}
									// median5 nemen!! , besloten om dit niet te doen 20130214, reden?
									// toch weer 5, om het gelijk te houden aan het eerste experiment 20130227
									if ($intensitymedian5 > $intensitythreshold){
										$draw->ellipse ($x, $anim_height-$intensitymedian5*7+325, 18, 18, 0, 360);
									}
									break;
								case "no_error_intens_r":
									$fillcolor   = sprintf ("#%02x%02x%02x", $disc_col_r, $disc_col_g, $disc_col_b);
									$strokecolor = sprintf ("#%02x%02x%02x", 0, 0, 0);
									$draw->clear();
									$draw->setFillColor   ($fillcolor);
									$draw->setStrokeColor ($strokecolor);
									$draw->setStrokeWidth (1);
									if ($this->opacity){
										$draw->setFillOpacity   ($this->opacity);
										$draw->setStrokeOpacity ($this->opacity);
									}
									$int = $intensitymedian - $intensitythreshold;
									if ($int>0){
										$draw->ellipse ($x, $anim_height/2, round ($int * 1.1), round ($int * 1.1), 0, 360);
									}
									break;
								case "no_error":
								default:
									$fillcolor   = sprintf ("#%02x%02x%02x", $disc_col_r, $disc_col_g, $disc_col_b);
									$strokecolor = sprintf ("#%02x%02x%02x", 0, 0, 0);
									$draw->clear();
									$draw->setFillColor   ($fillcolor);
									$draw->setStrokeColor ($strokecolor);
									$draw->setStrokeWidth (1);
									if ($this->opacity){
										$draw->setFillOpacity   ($this->opacity);
										$draw->setStrokeOpacity ($this->opacity);
									}
									$draw->ellipse ($x, $y+25, round($r*$anim_height/400), round($r*$anim_height/400), 0, 360);
									break;
							} // end switch
							$dodraw = true;
						}else{
							$dodraw = false;
						}

						// save frame
						if ($dodraw){
							if ($this->shadow){
								$shadow = new Imagick();
								$shadow->newImage(550, 300, "none");
								$shadow->drawImage ($draw);
								$shadow->setImageBackgroundColor( new ImagickPixel( '#444444' ) );
								$shadow->shadowImage( 50, 3, 1, 1 );
								$canvas->compositeImage($shadow, Imagick::COMPOSITE_OVER, -2, -2 );
				 				$shadow->destroy();
				 			}
							$canvas->drawImage ($draw);
						}
						// if ($this->usrdir){
						// 	$framename = sprintf ("images/$animbasename-%05d.{$this->imgformat}", $preframes + $nr++);
						// }else{
							$framename = sprintf ("images/$animbasename-%05d.{$this->imgformat}", $preframes + $nr++);
						// }
						// $cwd = getcwd();
						// $this->writelog ("cwd: $cwd");
						/// $this->writelog ("framename: $framename");
						$canvas->writeImage ($framename);

					} // end foreach loop

					// make animation a bit longer to be sure the audio fits into it
					if ($postframes = $this->get_postframes()){
						for ($i=0; $i<$postframes; $i++){
							if ($this->usrdir){
								$framename = sprintf ("images/$animbasename-%05d.{$this->imgformat}", $preframes + $nr++);
							}else{
								$framename = sprintf ("images/$animbasename-%05d.{$this->imgformat}", $preframes + $nr++);
							}
							$canvas->writeImage ($framename);
						}
					}

					$canvas->setImageFormat($this->jpg);
					$canvas->writeImage ("$animbasename.jpg");

				}

				// make animation and remove frames
				$frames = $nr;
				$this->writelog ("$animbasename - frames: $frames");
				$time = $point->get_time();
				$this->writelog ("$animbasename - time: $time");
				$rate = floatval($frames) / floatval($time);
				$this->writelog ("$animbasename - rate: $rate\n");
				if ($this->usrdir){
					$cmd = "{$this->ffmpeg} -f image2 -r $rate -i images/$animbasename-%05d.{$this->imgformat} -i $basename.wav -vcodec libx264 -pix_fmt yuv420p -b:v 1024k -g 150 -b:a 192k -y $animbasename.tmp.mp4 2>&1";
					// print ("$cmd<br/>\n");
					// passthru ($cmd);
					// $cwd = getcwd();
					// $this->writelog ("cwd: $cwd");
					// $this->writelog ("cmd: $cmd");
					exec ($cmd, $output1);
					$cmd = "rm images/$animbasename-?????.{$this->imgformat} 2>&1";
					exec ($cmd, $output2);
					$cmd = "rmdir images 2>&1";
					exec ($cmd, $output3);
					$cmd = "{$this->qtfaststart} $animbasename.tmp.mp4 $animbasename.mp4 2>&1";
					// print ("$cmd<br/>\n");
					exec ($cmd, $output4);
					$cmd = "rm $animbasename.tmp.mp4 2>&1";
					exec ($cmd, $output5);
				}else{
					$cmd = "{$this->ffmpeg} -f image2 -r $rate -i images/$animbasename-%05d.{$this->imgformat} -i $basename.wav -vcodec libx264 -pix_fmt yuv420p -b:v 1024k -g 150 -b:a 192k -y $animbasename.tmp.mp4 2>&1";
					// print ("$cmd<br/>\n");
					// passthru ($cmd);
					exec ($cmd, $output1);
					$cmd = "rm images/$animbasename-?????.{$this->imgformat} 2>&1";
					exec ($cmd, $output2);
					$cmd = "{$this->qtfaststart} $animbasename.tmp.mp4 $animbasename.mp4 2>&1";
					// print ("$cmd<br/>\n");
					exec ($cmd, $output3);
					$cmd = "rm $animbasename.tmp.mp4 2>&1";
					exec ($cmd, $output4);
				}
			}
		}



		private function create_static_background ($canvas, $pitch_high, $textfont, $boldfont)
		{
			$backgr_color  = "#f3f3f3";
			$up_thr_color  = "#2f4f4f";
			$low_thr_color = "#008b8b";
			$graph_color   = "#b22222";
			$rulers_color  = "#bbccdd";
			$text_color    = "#0b333c";
			$graph_bgcolor = "#ffffff";
			$shadow_color  = "#b6b6b6";
			$grid_color    = "#eeeeee";
	
			$pit_graph_low    = 60;
			$pit_graph_high   = 260;
			$pit_block_raise  = 20;
			$pit_graph_bg_x1  = 6;
			$pit_graph_bg_y1  = 35;
			$pit_graph_bg_x2  = 267;
			$pit_graph_bg_y2  = 264;
			$pit_graph_orig_x = 50;
			$pit_graph_orig_y = 241;
			$pit_graph_height = 197;
			$pit_graph_width  = 206;
	
			$int_graph_low    = 0;
			$int_graph_high   = 100;
			$int_block_raise  = 20;
			$int_graph_bg_x1  = 281;
			$int_graph_bg_y1  = 35;
			$int_graph_bg_x2  = 542;
			$int_graph_bg_y2  = 264;
			$int_graph_orig_x = 325;
			$int_graph_orig_y = 241;
			$int_graph_height = 197;
			$int_graph_width  = 206;
			$int_graph_steps  = ($int_graph_high-$int_graph_low)/$int_block_raise;
			$int_graph_height = $int_graph_height / $int_graph_steps;
	
			define("LEFT",     1);
			define("CENTER",   2);
			define("RIGHT",    3);
			define("BOLD",   900);
	
			// print ("pitch_high: $pitch_high<br/>\n");
			$pit_graph_high = $pit_block_raise * floor(1 + $pitch_high/$pit_block_raise);
			// print ("pit_graph_high: $pit_graph_high<br/>\n");
			$pit_graph_steps  = ($pit_graph_high-$pit_graph_low)/$pit_block_raise;
			$pit_graph_height = $pit_graph_height / $pit_graph_steps;
	
	
			$draw = new ImagickDraw ();
			$draw->clear();
			$draw->setStrokeAntialias( true );
			$draw->setTextAntialias( true );
			// background:
			$draw->setFillColor($backgr_color);
			$draw->rectangle (0, 0, 550, 300);
			$draw->setFillColor($shadow_color);
			$draw->rectangle ($pit_graph_bg_x1-.7, $pit_graph_bg_y1+3.7, $pit_graph_bg_x2+.7, $pit_graph_bg_y2+3.7);
			$draw->rectangle ($int_graph_bg_x1-.7, $int_graph_bg_y1+3.7, $int_graph_bg_x2+.7, $int_graph_bg_y2+3.7);
			$draw->setFillColor($graph_bgcolor);
			$draw->rectangle ($pit_graph_bg_x1, $pit_graph_bg_y1, $pit_graph_bg_x2, $pit_graph_bg_y2);
			$draw->rectangle ($int_graph_bg_x1, $int_graph_bg_y1, $int_graph_bg_x2, $int_graph_bg_y2);
			// graph info:
			// $draw->setFontWeight(BOLD); // does not work
			if ($boldfont){
				$draw->setFont($boldfont); // specific font type, optional
			}
			$draw->setFillColor($text_color);
			$draw->setFontSize( 10 );
			$draw->setTextAlignment(LEFT);
			$draw->annotation(  8,  23, "toonhoogte");
			$draw->annotation(284,  23, "intensiteit");
			$draw->annotation( 26, 286, "Bovengrens");
			$draw->annotation(137, 286, "Ondergrens");
			$draw->annotation(247, 286, "Uw resultaat");
			$draw->setFillColor($up_thr_color);
			$draw->rectangle ( 6, 275.4, 15, 289.7 );
			$draw->setFillColor($low_thr_color);
			$draw->rectangle ( 117, 275.4, 126, 289.7 );
			$draw->setFillColor($graph_color);
			$draw->rectangle ( 227, 275.4, 236, 289.7 );
			// rulers:
			$draw->setFillColor($rulers_color);
			$draw->rectangle ($pit_graph_orig_x-8, 44, $pit_graph_orig_x-1, $pit_graph_orig_y);
			$draw->rectangle ($int_graph_orig_x-8, 44, $int_graph_orig_x-1, $int_graph_orig_y);
			$draw->line ($pit_graph_orig_x, $pit_graph_orig_y, $pit_graph_orig_x+$pit_graph_width, $pit_graph_orig_y);
			$draw->line ($int_graph_orig_x, $int_graph_orig_y, $int_graph_orig_x+$int_graph_width, $int_graph_orig_y);
			// pitch grid:
			if ($textfont){
				$draw->setFont($textfont); // specific font type, optional
			}
			$draw->setTextAlignment(RIGHT);
			for ($i=0; $i<=$pit_graph_steps; $i++){
				$text = sprintf ("%d", $pit_graph_low + $i*$pit_block_raise);
				$txt_x    = $pit_graph_orig_x - 21;
				$txt_y    = $pit_graph_orig_y + 2 - $i * $pit_graph_height;
				$line1_x1 = round ($txt_x    + 5);
				$line1_x2 = round ($line1_x1 + 8);
				$line1_y1 = round ($txt_y    - 3);
				$line1_y2 = round ($line1_y1 + $pit_graph_height/2);
				// $draw->setStrokeAntialias( false );
				$draw->setFillColor($rulers_color);
				$draw->line ( $line1_x1, $line1_y1, $line1_x2, $line1_y1 );
				$draw->setFillColor($graph_bgcolor);
				$draw->line ( $line1_x1+8, $line1_y1, $line1_x2+7, $line1_y1 );
				if ($i){
					$draw->line ( $line1_x1+8, $line1_y2, $line1_x2+7, $line1_y2 );
				}
				$draw->setFillColor($grid_color);
				$draw->line ( $pit_graph_orig_x, $line1_y1, $pit_graph_orig_x+$pit_graph_width, $line1_y1 );
				$draw->setFillColor($text_color);
			 	$draw->annotation($txt_x, $txt_y, $text);
			}
			// intensity grid:
			for ($i=0; $i<=$int_graph_steps; $i++){
				$text = sprintf ("%d", $int_graph_low + $i*$int_block_raise);
				$txt_x    = $int_graph_orig_x - 21;
				$txt_y    = $int_graph_orig_y + 2 - $i * $int_graph_height;
				$line1_x1 = round ($txt_x    + 5);
				$line1_x2 = round ($line1_x1 + 8);
				$line1_y1 = round ($txt_y    - 3);
				$line1_y2 = round ($line1_y1 + $int_graph_height/2);
				// $draw->setStrokeAntialias( false );
				$draw->setFillColor($rulers_color);
				$draw->line ( $line1_x1, $line1_y1, $line1_x2, $line1_y1 );
				$draw->setFillColor($graph_bgcolor);
				$draw->line ( $line1_x1+8, $line1_y1, $line1_x2+7, $line1_y1 );
				if ($i){
					$draw->line ( $line1_x1+8, $line1_y2, $line1_x2+7, $line1_y2 );
				}
				$draw->setFillColor($grid_color);
				$draw->line ( $int_graph_orig_x, $line1_y1, $int_graph_orig_x+$int_graph_width, $line1_y1 );
				$draw->setFillColor($text_color);
			 	$draw->annotation($txt_x, $txt_y, $text);
			}
			$draw->setTextAlignment(LEFT);
			$draw->setFillColor($text_color);
			// $draw->annotation($pit_graph_orig_x, $pit_graph_orig_y + 16, "gemiddelde waarde:");
			$draw->annotation($int_graph_orig_x, $int_graph_orig_y + 16, "gemiddelde waarde:");
			$canvas->drawImage ($draw);
		}

	}


	class VestPoint { //********************************************************************************************

		function __construct ($time, $intensity, $pitch) {
			$this->time      = $time;
			$this->intensity = $intensity;
			$this->pitch     = $pitch;
		}

		function set_x                ($x)                {$this->x                = $x;}
		function set_y                ($y)                {$this->y                = $y;}
		function set_r                ($r)                {$this->r                = $r;}
		function set_pitchmedian      ($pitchmedian)      {$this->pitchmedian      = $pitchmedian;}
		function set_pitchmedian5     ($pitchmedian5)     {$this->pitchmedian5     = $pitchmedian5;}
		function set_intensitymedian  ($intensitymedian)  {$this->intensitymedian  = $intensitymedian;}
		function set_intensitymedian5 ($intensitymedian5) {$this->intensitymedian5 = $intensitymedian5;}
		function get_time             () {return $this->time;}
		function get_intensity        () {return $this->intensity;}
		function get_pitch            () {return $this->pitch;}
		function get_x                () {return $this->x;}
		function get_y                () {if (isset($this->y)) return $this->y;}
		function get_r                () {if (isset($this->r)) return $this->r;}
		function get_intensitymedian  () {return $this->intensitymedian;}
		function get_intensitymedian5 () {return $this->intensitymedian5;}
		function get_pitchmedian      () {return $this->pitchmedian;}
		function get_pitchmedian5     () {return $this->pitchmedian5;}
	}


	class VestImagickDraw extends ImagickDraw { //************************************************************************

		function cross ($cx, $cy, $r, $dev=0)
		{
			$maxdev       = 145;
			$devchange    = round ($maxdev/2);
			$compensation =  .8; // 'blown crosses' look bigger than circles
			$boost        = 1.2; // 'make smaller 'wings' on crosses more apparent

			if ($dev <       0) $dev =   0;
			if ($dev > $maxdev) $dev = $maxdev;

			if ($dev > 0){
				// print ("dev: $dev (x:$cx y:$cy r:$r)<br/>\n");
				$r  = round ($compensation * $r);
				if ($dev > $devchange){
					$ri  = round ((1 - $dev/$maxdev * $boost) * $r);
					$ro1 = round ($dev/$maxdev * $r * $boost);
				}else{
					$ri  = round ((1 - $dev/$maxdev) * $r);
					if ($dev > 4){
						$ro1 = round ($r/2);
					}else{
						$fac = (4-$dev)/4;
						$ro1 = round ($r/2 - ($fac * ($r/6)));
					}
				}
				if ($ro1 > $r) $ro1 = $r;
			}else{
				$ri  = round ($r/2);
				$ro1 = round ($r/2);
			}
			$ro2 = $r;

			$values = array(
				array ('x' => $cx + $ro1, 'y' => $cy + $ro2), // Point  1 (x, y)
				array ('x' => $cx + $ro2, 'y' => $cy + $ro1), // Point  2 (x, y)
				array ('x' => $cx + $ri,  'y' => $cy       ), // Point  3 (x, y)
				array ('x' => $cx + $ro2, 'y' => $cy - $ro1), // Point  4 (x, y)
				array ('x' => $cx + $ro1, 'y' => $cy - $ro2), // Point  5 (x, y)
				array ('x' => $cx       , 'y' => $cy - $ri ), // Point  6 (x, y)
				array ('x' => $cx - $ro1, 'y' => $cy - $ro2), // Point  7 (x, y)
				array ('x' => $cx - $ro2, 'y' => $cy - $ro1), // Point  8 (x, y)
				array ('x' => $cx - $ri , 'y' => $cy       ), // Point  9 (x, y)
				array ('x' => $cx - $ro2, 'y' => $cy + $ro1), // Point 10 (x, y)
				array ('x' => $cx - $ro1, 'y' => $cy + $ro2), // Point 11 (x, y)
				array ('x' => $cx       , 'y' => $cy + $ri )  // Point 12 (x, y)
	    );
			$this->polygon ($values);
		}

		function star ($cx, $cy, $r, $len)
		{
			$r = round ($r/2);
			if ($len >= $r) $len = $r - 1;
			if ($len < 0)   $len = 0;
			$long_ax  = $r + $len;
			$short_ax = round (sqrt (2*($r-$len)*($r-$len)));
			if ($short_ax >= round($long_ax*3/4)) $short_ax = round($long_ax*3/4);
			$values = array(
				array ('x'=>$cx,             'y'=>$cy + $long_ax ), // Point 1 (x, y)
				array ('x'=>$cx + $short_ax, 'y'=>$cy + $short_ax), // Point 2 (x, y)
				array ('x'=>$cx + $long_ax,  'y'=>$cy            ), // Point 3 (x, y)
				array ('x'=>$cx + $short_ax, 'y'=>$cy - $short_ax), // Point 4 (x, y)
				array ('x'=>$cx,             'y'=>$cy - $long_ax ), // Point 5 (x, y)
				array ('x'=>$cx - $short_ax, 'y'=>$cy - $short_ax), // Point 6 (x, y)
				array ('x'=>$cx - $long_ax,  'y'=>$cy            ), // Point 7 (x, y)
				array ('x'=>$cx - $short_ax, 'y'=>$cy + $short_ax)  // Point 8 (x, y)
	    );
			$this->polygon ($values);
		}

	}

?>