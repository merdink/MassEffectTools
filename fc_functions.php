<?php

//define(MALE, 0);
//define(FEMALE, 1);
$GLOBALS['femCheck'] = 0;

function isFemale()
{
	if ( $GLOBALS['isFemale'] == 1 ) { return true; }
		return false;
}

// Searches through morphFeatures to find the value for a specific variable
function getOffset($array,$search)
{
	// Morph Features uses feature for the key, offset for the value
	foreach ($array as $key => $value) {
		$actualValue = substr($value['feature'],0,-1);
	
		//print_r($actualValue);
		if ( $actualValue === $search ) { return $value['offset']; }
	}
	
	return null;
}

// Searches through Texture/Vector Parameters to find the value for a specific variable
function getValue($array,$search)
{
	// Texture/Vector Parameters uses name for the key, value for the value
	foreach ($array as $key => $value) {
		$actualValue = substr($value['name'],0,-1);
	
		//print_r($actualValue);
		if ( $actualValue === $search ) { return $value['value']; }
	}
	
	return null;
}

////////////////////////////////////////
////////////////////////////////////////

// Returns a slider value for the first variable that is greater than zero for race
function findRace($array)
{
	// Checking for Female Shepard
	if ( isFemale() )
	{
		if ( getOffset($array,'race_yngBlk') > 0 ) {return 1;}
		if ( getOffset($array,'race_oldBlk') > 0 ) {return 2;}
		if ( getOffset($array,'race_yngAsn') > 0 ) {return 3;}
		if ( getOffset($array,'race_oldAsn') > 0 ) {return 4;}
		if ( getOffset($array,'race_yngCauc') > 0 ) {return 5;}
		if ( getOffset($array,'race_oldCauc') > 0 ) {return 6;}
		if ( getOffset($array,'race_iconic') > 0 ) {return 7;}
		if ( getOffset($array,'race_Ashley') > 0 ) {return 8;}
		if ( getOffset($array,'race_liara') > 0 ) {return 9;}
		return "[FACESTRUC_FAIL]";
	}
	
	// Assuming Male Shepard
	if ( getOffset($array,'race_yngBlk') > 0 ) {return 1;}
	if ( getOffset($array,'race_oldBlk') > 0 ) {return 2;}
	if ( getOffset($array,'race_yngAsn') > 0 ) {return 3;}
	if ( getOffset($array,'race_oldAsn') > 0 ) {return 4;}
	if ( getOffset($array,'race_yngCauc') > 0 ) {return 5;}
	if ( getOffset($array,'race_oldCauc') > 0 ) {return 6;}
	return "[FACESTRUC_FAIL]";
}

// Compares first 4 digits of the R value from SkinTone and
// returns the slider value for the matching set of numbers
function findSkinTone($skinValueOne)
{
	switch ( substr($skinValueOne, 2, 4) )
	{
		case "2120": return 1;
		case "3736": return 2;
		case "5356": return 3;
		case "7592": return 4;
		case "8671": return 5;
		case "9743": return 6;
		default: return "[SKINTONE_FAIL]";
	}
}

function cheek_Gaunt($array)
{
	$temptwo = getOffset($array,'cheek_Gaunt');

	if ($temptwo == 0) { return 1; }
	return 1 + round($temptwo / 0.026667);
}

function findEyeShape($array)
{
	// Checking for Female Shepard
	if ( isFemale() )
	{
		if ( getOffset($array,'eyeShape_droop') > 0 ) {return 1;}
		if ( getOffset($array,'eyeShape_sleepy') > 0 ) {return 2;}
		if ( getOffset($array,'eyeShape_SlantUp') > 0 ) {return 3;}
		if ( getOffset($array,'eyeShape_highInside') > 0 ) {return 4;}
		if ( getOffset($array,'eyeShape_flatTop') > 0 ) {return 5;}
		if ( getOffset($array,'eyes_SlantDown') > 0 ) {return 6;}
		if ( getOffset($array,'eyeShape_liara') > 0 ) {return 7;}
		if ( getOffset($array,'eyeShape_Ashley') > 0 ) {return 8;}
		if ( getOffset($array,'eyeShape_yngAsn') > 0 ) {return 9;}
		return "[EYESHAPE_FAIL]";
	}
	
	// Assuming Male Shepard
	if ( getOffset($array,'eyes_Shape_droop') > 0 ) {return 1;}
	if ( getOffset($array,'eyes_Shape_sleepy') > 0 ) {return 2;}
	if ( getOffset($array,'eyes_Shape_squint') > 0 ) {return 3;}
	if ( getOffset($array,'eyes_SlantUp') > 0 ) {return 4;}
	if ( getOffset($array,'eyes_SlantDown') > 0 ) {return 5;}
	if ( getOffset($array,'eyes_Shape_wide') > 0 ) {return 6;}
	if ( getOffset($array,'eyes_Shape_flatTop') > 0 ) {return 7;}
	if ( getOffset($array,'eyes_Shape_outerPoint') > 0 ) {return 8;}
	return "[EYESHAPE_FAIL]";
}

function mouth_Shape($array)
{
	// Checking for Female Shepard
	if ( isFemale() )
	{
		if ( getOffset($array,'mouthShape_iconic') > 0 ) {return 1;}
		if ( getOffset($array,'mouthShape_liara') > 0 ) {return 3;}
		if ( getOffset($array,'mouthShape_ashley') > 0 ) {return 2;}
		if ( getOffset($array,'mouthShape_oldBlk') > 0 ) {return 5;}
		if ( getOffset($array,'mouthShape_yngBlk') > 0 ) {return 6;}
		if ( getOffset($array,'mouthShape_yngCauc') > 0 ) {return 7;}
		if ( getOffset($array,'mouthShape_yngAsn') > 0 ) {return 9;}
		return 4;
	}
	
	// Assuming Male Shepard
	if ( getOffset($array,'mouthShape_centerKleft') > 0 ) {return 1;}
	if ( getOffset($array,'MouthShape_Diddy') > 0 ) {return 2;}
	if ( getOffset($array,'mouthShape_overBite') > 0 ) {return 3;}
	if ( getOffset($array,'mouthShape_underBite') > 0 ) {return 4;}
	if ( getOffset($array,'mouthShape_thin') > 0 ) {return 5;}
	if ( getOffset($array,'mouthShape_pinchedSides') > 0 ) {return 7;}
	if ( getOffset($array,'mouthShape_Philtrum') > 0 ) {return 8;}
	return 6;
}

// Compares first 4 digits of the R value from HED_Hair_Colour_Vector
// and returns the slider value for the matching set of numbers
function findHairColor($hairColorValueOne)
{
	switch ( substr($hairColorValueOne, 2, 4) )
	{
		case "9573": return 1;
		case "1572": return 2;
		case "0891": return 3;
		case "2157": return 4;
		case "1165": return 5;
		case "0477": return 6;
		case "0370": return 7;
		default: return "[HAIRCOLOR_FAIL]";
	}
}

function findBrow($array)
{
	$value = getValue($array,'HED_Brow');
	
	// Checking for Female Shepard
	if ( isFemale() )
	{
		if ( stristr($value,'AngularBrow') ) {return 2;}
		if ( stristr($value,'ArchedHighBrow') ) {return 3;}
		if ( stristr($value,'AssymBrow') ) {return 4;}
		if ( stristr($value,'GroomedBrow') ) {return 5;}
		if ( stristr($value,'PluckArchBrow') ) {return 6;}
		if ( stristr($value,'ShortArchBrow') ) {return 7;}
		if ( stristr($value,'SoftArchedBrow') ) {return 8;}
		if ( stristr($value,'ThickBushyBrow') ) {return 9;}
		if ( stristr($value,'ThickWispyBrow') ) {return 10;}
		if ( stristr($value,'ThinArchedBrow') ) {return 11;}
		if ( stristr($value,'ThinBrow') ) {return 12;}
		if ( stristr($value,'WispyBrow') ) {return 13;}
		if ( stristr($value,'FlatDiamondBrow') ) {return 14;}
		if ( stristr($value,'ThinnerBrow') ) {return 15;}
		if ( stristr($value,'SquareBrow') ) {return 16;}
		return 1;
	}
	
	// Assuming Male Shepard
	if ( stristr($value,'ArchedBrow') ) {return 2;}
	if ( stristr($value,'BushyBrow') ) {return 3;}
	if ( stristr($value,'FatBrow') ) {return 4;}
	if ( stristr($value,'FuzzyBrow') ) {return 5;}
	if ( stristr($value,'ThickBrow') ) {return 6;}
	if ( stristr($value,'UniBrow') ) {return 7;}
	return 1;
}

////////////////////////////////////////
////////////////////////////////////////

function doTheMath($leftHalf,$rightHalf,$divideMe)
{
	if ($leftHalf > 0) { return 16 - round($leftHalf / $divideMe); }
	if ($rightHalf > 0) { return 16 + round($rightHalf / $divideMe); }
	return 16;
}

function getSliderValue($varOne,$varTwo,$divisor,$array)
{
	$tempOne = getOffset($array, $varOne);
	$tempTwo = getOffset($array, $varTwo);
	return doTheMath($tempOne,$tempTwo,$divisor);
}

function faceCodeKey($value)
{
	if ( $value > 9 )
	{
		switch ($value)
		{
			case 10: return "A";
			case 11: return "B";
			case 12: return "C";
			case 13: return "D";
			case 14: return "E";
			case 15: return "F";
			case 16: return "G";
			case 17: return "H";
			case 18: return "I";
			case 19: return "J";
			case 20: return "K";
			case 21: return "L";
			case 22: return "M";
			case 23: return "N";
			case 24: return "P";
			case 25: return "Q";
			case 26: return "R";
			case 27: return "S";
			case 28: return "T";
			case 29: return "U";
			case 30: return "V";
			case 31: return "W";
			default: return "[KEYCODE_FAIL]";
		}
	}
	return $value;
}

?>