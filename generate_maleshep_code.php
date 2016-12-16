<?php
// Path to directory with library
define('MASSEFFECT_PATH', dirname(__FILE__).'/');

include(MASSEFFECT_PATH.'MassEffect/Save.php');
include('fc_functions.php');

// Extracting save file (can use pcsav for PC or xbsav for Xbox 360)
// xbsav by default
$save = new MassEffect_Save(dirname(__FILE__).'/example2.xbsav');

// player object
$player = $save->player->getPlayer();

// getting morphFeatures/textureParameters/vectorParameters for Face Code generation
$morphF = $player->appearance->morphHead->morphFeatures->asArray();
$textureP = $player->appearance->morphHead->textureParameters->asArray();
$vectorP = $player->appearance->morphHead->vectorParameters->asArray(); 

// seeing if this save file's shepard is female so the
// correct functions will run to give the right code
$GLOBALS['isFemale'] = $player['isFemale'];

////////////////////////////////////////
////////////////////////////////////////

$raceNum = findRace($morphF);
$skinTone = findSkinTone(getValue($vectorP,'SkinTone')['r']);
$complexion = 1; //Guide says compare and match
$scar = 1;

//$neckThickness = getSliderValue("neck_Thin", "neck_wide", 0.03667, $morphF); //0.02667 possible guide error
$neckThickness = getSliderValue("neck_Thin", "neck_wide", 0.02667, $morphF);
if ($neckThickness > 31) {$neckThickness = 31;} //Temp fix for now
$faceSize = getSliderValue("shape_skinny", "shape_chubby", 0.03667, $morphF);
$cheekWidth = getSliderValue("cheek_BonesIn", "cheek_BonesOut", 0.0533333, $morphF);
//$cheekBones = getSliderValue("cheek_DepthFront", "cheek_DepthBack", 0.0533333, $morphF);
$cheekBones = getSliderValue("cheek_DepthBack", "cheek_DepthFront", 0.0533333, $morphF); //Swapped places to fix possible error
//$cheekGaunt = cheek_Gaunt($morphHead);
$cheekGaunt = getSliderValue("mouth_cheekMass", "cheek_Gaunt", 0.06, $morphF);
$earSize = getSliderValue("ears_small", "ears_large", 0.02667, $morphF);
$earsOrientation = getSliderValue("ears_In", "ears_Out", 0.05, $morphF);
//$earsOrientation = getSliderValue("ears_Out", "ears_In", 0.05, $morphF); //Swapped places to fix possible error

$eyeShape = findEyeShape($morphF);
$eyeHeight = getSliderValue("eyes_PosDown","eyes_PosUp",0.02,$morphF);
$eyeWidth = getSliderValue("eyes_narrow","eyes_Wide",0.02667,$morphF);
$eyeDepth = getSliderValue("eyes_Back","eyes_Forward",0.04,$morphF);
$browDepth = getSliderValue("eyes_browBack","eyes_browForward",0.06667,$morphF);
$browHeight = getSliderValue("eyes_browDown","eyes_browUp",0.033333,$morphF);
$irisColor = 1; //Guide says ME2 eyes have lower saturation.

$chinHeight = getSliderValue("jaw_chinUp","jaw_chinDown",0.02667,$morphF);//Flipped variables because of guide
$chinDepth = getSliderValue("jaw_chinIn","jaw_chinOut",0.02667,$morphF);
$chinWidth = getSliderValue("jaw_chinWide","jaw_chinThin",0.04,$morphF);
$jawWidth = getSliderValue("jaw_narrow","Jaw_Width",0.06667,$morphF); //Used to be jaw_wide, seems to now be Jaw_Width

$mouthShape = mouth_Shape($morphHead);
$mouthDepth = getSliderValue("mouth_Back","mouth_Forward",0.03667,$morphF);
$mouthWidth = getSliderValue("mouth_Narrow","mouth_Wide",0.02,$morphF);
$mouthLipSize = getSliderValue("mouth_lipsThin","mouth_lipsFat",0.02667,$morphF);
$mouthHeight = getSliderValue("mouth_Down","mouth_Up",0.03667,$morphF);

$noseShape = 1; //Guide says compare and match
$noseHeight = getSliderValue("nose_Down","nose_Up",0.0433,$morphF);
$noseDepth = getSliderValue("nose_BottomIn","nose_BottomOut",0.0433,$morphF);

$hair = 1; //Guide says compare and match
$browColor = 1; //Guide said nothing, so I dunno (THIS IS SUPPOSED TO BE BEARD)
$brow = findBrow($textureP);
$hairColor = findHairColor(getValue($vectorP,'HED_Hair_Colour_Vector')['r']);

$facialHairColor = 1; //Guide says compare and match
////////////////////////////////////////
////////////////////////////////////////

echo 'ME1 Import Face Code Generator [MALESHEP] - For ME2';
echo '<div class="contentTitle"><h1>FACIAL STRUCTURE</h1></div>';
echo '<div class="contentText">';
echo 'Facial Structure ' . $raceNum;
echo '<br>Skin Tone ' . $skinTone;
echo '<br>Complexion ' . $complexion . '<b>****</b>';
//echo '<br>Scar ' . $scar . ' [Default: 1. Choose Any.]';
echo '</div>';

echo '<div class="contentTitle"><h1>HEAD</h1></div>';
echo '<div class="contentText">';
echo 'Neck Thickness ' . $neckThickness;
echo '<br>Face Size ' . $faceSize;
echo '<br>Cheek Width ' . $cheekWidth;
echo '<br>Cheek Bones ' . $cheekBones;
echo '<br>Cheek Gaunt ' . $cheekGaunt;
echo '<br>Ears Size ' . $earSize;
echo '<br>Ears Orientation ' . $earsOrientation;
echo '</div>';

echo '<div class="contentTitle"><h1>EYES</h1></div>';
echo '<div class="contentText">';
echo 'Eye Shape ' . $eyeShape;
echo '<br>Eye Height ' . $eyeHeight;
echo '<br>Eye Width ' . $eyeWidth;
echo '<br>Eye Depth ' . $eyeDepth;
echo '<br>Brow Depth ' . $browDepth;
echo '<br>Brow Height ' . $browHeight;
echo '<br>Iris Color ' . $irisColor . '<b>****</b>';
echo '</div>';

echo '<div class="contentTitle"><h1>JAW</h1></div>';
echo '<div class="contentText">';
echo 'Chin Height ' . $chinHeight;
echo '<br>Chin Depth ' . $chinDepth;
echo '<br>Chin Width ' . $chinWidth;
echo '<br>Jaw Width ' . $jawWidth;
echo '</div>';

echo '<div class="contentTitle"><h1>MOUTH</h1></div>';
echo '<div class="contentText">';
echo 'Mouth Shape ' . $mouthShape;
if ($mouthShape == 6) echo ' [NOTE: You can choose 6 or 9. They are identical.]';
echo '<br>Mouth Depth ' . $mouthDepth;
echo '<br>Mouth Width ' . $mouthWidth;
echo '<br>Mouth Lip Size ' . $mouthLipSize;
echo '<br>Mouth Height ' . $mouthHeight;
echo '</div>';

echo '<div class="contentTitle"><h1>NOSE</h1></div>';
echo '<div class="contentText">';
echo 'Nose Shape ' . $noseShape . '<b>****</b>';
echo '<br>Nose Height ' . $noseHeight;
echo '<br>Nose Depth ' . $noseDepth;
echo '</div>';

echo '<div class="contentTitle"><h1>HAIR</h1></div>';
echo '<div class="contentText">';
echo 'Hair ' . $hair . '<b>****</b>';
echo '<br>Beard ' . $browColor . '<b>****</b>';
echo '<br>Brow ' . $brow;
echo '<br>Hair Color ' . $hairColor;
echo '<br>Facial Hair Color ' . $facialHairColor . '<b>****</b>';
echo '</div>';

echo '<div class="contentTitle"><h1>ATTENTION!</h1></div>';
echo '<div class="contentText">';
echo '<b>****This value was not calculated, please choose the number that matches your Shepard.</b>';
echo '</div>';

echo '<div class="contentTitle"><h1>Mass Effect 2 FaceCode</h1></div>';
echo '<div class="contentText">';
echo faceCodeKey($raceNum) . faceCodeKey($skinTone) . faceCodeKey($complexion) . '.';
echo faceCodeKey($neckThickness) . faceCodeKey($faceSize) . faceCodeKey($cheekWidth) . '.';
echo faceCodeKey($cheekBones) . faceCodeKey($cheekGaunt) . faceCodeKey($earSize) . '.';
echo faceCodeKey($earsOrientation) . faceCodeKey($eyeShape) . faceCodeKey($eyeHeight) . '.';
echo faceCodeKey($eyeWidth) . faceCodeKey($eyeDepth) . faceCodeKey($browDepth) . '.';
echo faceCodeKey($browHeight) . faceCodeKey($irisColor) . faceCodeKey($chinHeight) . '.';
echo faceCodeKey($chinDepth) . faceCodeKey($chinWidth) . faceCodeKey($jawWidth) . '.';
echo faceCodeKey($mouthShape) . faceCodeKey($mouthDepth) . faceCodeKey($mouthWidth) . '.';
echo faceCodeKey($mouthLipSize) . faceCodeKey($mouthHeight) . faceCodeKey($noseShape) . '.';
echo faceCodeKey($noseHeight) . faceCodeKey($noseDepth) . faceCodeKey($hair) . '.';
echo faceCodeKey($browColor) . faceCodeKey($brow) . faceCodeKey($hairColor) . '.';
echo faceCodeKey($facialHairColor);
echo '</div>';
?>