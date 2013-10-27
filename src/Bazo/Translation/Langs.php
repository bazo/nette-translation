<?php
namespace Bazo\Translation;
/**
 * Plurals
 *
 * @author martin.bazik
 */
class Langs
{
	private static 
		$plurals = array(
			'ach' => 'nplurals=2; plural=(n > 1);', 
			'af' => 'nplurals=2; plural=(n != 1);', 
			'ak' => 'nplurals=2; plural=(n > 1);', 
			'am' => 'nplurals=2; plural=(n > 1);', 
			'an' => 'nplurals=2; plural=(n != 1);', 
			'ar' => 'nplurals=6; plural= n==0 ? 0 : n==1 ? 1 : n==2 ? 2 : n%100>=3 && n%100<=10 ? 3 : n%100>=11 ? 4 : 5;', 
			'arn' => 'nplurals=2; plural=(n > 1);', 
			'ast' => 'nplurals=2; plural=(n != 1);', 
			'ay' => 'nplurals=1; plural=0;', 
			'az' => 'nplurals=2; plural=(n != 1);', 
			'be' => 'nplurals=3; plural=((n%10==1 && n%100!=11) ? 0 : (n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2));', 
			'bg' => 'nplurals=2; plural=(n != 1);', 
			'bn' => 'nplurals=2; plural=(n != 1);', 
			'bo' => 'nplurals=1; plural=0;', 
			'br' => 'nplurals=2; plural=(n > 1);', 
			'bs' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);', 
			'ca' => 'nplurals=2; plural=(n != 1);', 
			'cgg' => 'nplurals=1; plural=0;', 
			'cs' => 'nplurals=3; plural=(n==1) ? 0 : ((n>=2 && n<=4) ? 1 : 2);', 
			'csb' => 'nplurals=3; n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2;', 
			'cy' => 'nplurals=4; plural= (n==1) ? 0 : (n==2) ? 1 : (n != 8 && n != 11) ? 2 : 3;', 
			'da' => 'nplurals=2; plural=(n != 1);', 
			'de' => 'nplurals=2; plural=(n != 1);', 
			'dz' => 'nplurals=1; plural=0;', 
			'el' => 'nplurals=2; plural=(n != 1);', 
			'en' => 'nplurals=2; plural=(n != 1);', 
			'eo' => 'nplurals=2; plural=(n != 1);', 
			'es' => 'nplurals=2; plural=(n != 1);', 
			'es_AR' => 'nplurals=2; plural=(n != 1);', 
			'et' => 'nplurals=2; plural=(n != 1);', 
			'eu' => 'nplurals=2; plural=(n != 1);', 
			'fa' => 'nplurals=1; plural=0;', 
			'fi' => 'nplurals=2; plural=(n != 1);', 
			'fil' => 'nplurals=2; plural=n > 1;', 
			'fo' => 'nplurals=2; plural=(n != 1);', 
			'fr' => 'nplurals=2; plural=(n > 1);', 
			'fur' => 'nplurals=2; plural=(n != 1);', 
			'fy' => 'nplurals=2; plural=(n != 1);', 
			'ga' => 'nplurals=5; plural=n==1 ? 0 : n==2 ? 1 : n<7 ? 2 : n<11 ? 3 : 4;', 
			'gd' => 'nplurals=4; plural=(n==1 || n==11) ? 0 : (n==2 || n==12) ? 1 : (n > 2 && n < 20) ? 2 : 3;', 
			'gl' => 'nplurals=2; plural=(n != 1);', 
			'gu' => 'nplurals=2; plural=(n != 1);', 
			'gun' => 'nplurals=2; plural = (n > 1);', 
			'ha' => 'nplurals=2; plural=(n != 1);', 
			'he' => 'nplurals=2; plural=(n != 1);', 
			'hi' => 'nplurals=2; plural=(n != 1);', 
			'hy' => 'nplurals=1; plural=0;', 
			'hr' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);', 
			'hu' => 'nplurals=2; plural=(n != 1);', 
			'ia' => 'nplurals=2; plural=(n != 1);', 
			'id' => 'nplurals=1; plural=0;', 
			'is' => 'nplurals=2; plural=(n%10!=1 || n%100==11);', 
			'it' => 'nplurals=2; plural=(n != 1);', 
			'ja' => 'nplurals=1; plural=0;', 
			'jv' => 'nplurals=2; plural=n!=0;', 
			'ka' => 'nplurals=1; plural=0;', 
			'kk' => 'nplurals=1; plural=0;', 
			'km' => 'nplurals=1; plural=0;', 
			'kn' => 'nplurals=2; plural=(n!=1);', 
			'ko' => 'nplurals=1; plural=0;', 
			'ku' => 'nplurals=2; plural=(n!= 1);', 
			'kw' => 'nplurals=4; plural= (n==1) ? 0 : (n==2) ? 1 : (n == 3) ? 2 : 3;', 
			'ky' => 'nplurals=1; plural=0;', 
			'lb' => 'nplurals=2; plural=(n != 1);', 
			'ln' => 'nplurals=2; plural=n>1;', 
			'lo' => 'nplurals=1; plural=0;', 
			'lt' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && (n%100<10 or n%100>=20) ? 1 : 2);', 
			'lv' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n != 0 ? 1 : 2);', 
			'mai' => 'nplurals=2; plural=(n != 1);', 
			'mfe' => 'nplurals=2; plural=(n > 1);', 
			'mg' => 'nplurals=2; plural=(n > 1);', 
			'mi' => 'nplurals=2; plural=(n > 1);', 
			'mk' => 'nplurals=2; plural= n==1 || n%10==1 ? 0 : 1;', 
			'ml' => 'nplurals=2; plural=(n != 1);', 
			'mn' => 'nplurals=2; plural=(n != 1);', 
			'mr' => 'nplurals=2; plural=(n != 1);', 
			'ms' => 'nplurals=1; plural=0;', 
			'mt' => 'nplurals=4; plural=(n==1 ? 0 : n==0 || ( n%100>1 && n%100<11) ? 1 : (n%100>10 && n%100<20 ) ? 2 : 3);', 
			'nah' => 'nplurals=2; plural=(n != 1);', 
			'nap' => 'nplurals=2; plural=(n != 1);', 
			'nb' => 'nplurals=2; plural=(n != 1);', 
			'ne' => 'nplurals=2; plural=(n != 1);', 
			'nl' => 'nplurals=2; plural=(n != 1);', 
			'se' => 'nplurals=2; plural=(n != 1);', 
			'nn' => 'nplurals=2; plural=(n != 1);', 
			'no' => 'nplurals=2; plural=(n != 1);', 
			'nso' => 'nplurals=2; plural=(n != 1);', 
			'oc' => 'nplurals=2; plural=(n > 1);', 
			'or' => 'nplurals=2; plural=(n != 1);', 
			'ps' => 'nplurals=2; plural=(n != 1);', 
			'pa' => 'nplurals=2; plural=(n != 1);', 
			'pap' => 'nplurals=2; plural=(n != 1);', 
			'pl' => 'nplurals=3; plural=(n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);', 
			'pms' => 'nplurals=2; plural=(n != 1);', 
			'pt' => 'nplurals=2; plural=(n != 1);', 
			'pt_BR' => 'nplurals=2; plural=(n > 1);', 
			'rm' => 'nplurals=2; plural=(n!=1);', 
			'ro' => 'nplurals=3; plural=(n==1 ? 0 : (n==0 || (n%100 > 0 && n%100 < 20)) ? 1 : 2);', 
			'ru' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);', 
			'sco' => 'nplurals=2; plural=(n != 1);', 
			'si' => 'nplurals=2; plural=(n != 1);', 
			'sk' => 'nplurals=3; plural=(n==1) ? 0 : ((n>=2 && n<=4) ? 1 : 2);', 
			'sl' => 'nplurals=4; plural=(n%100==1 ? 1 : n%100==2 ? 2 : n%100==3 || n%100==4 ? 3 : 0);', 
			'so' => 'nplurals=2; plural=n != 1;', 
			'son' => 'nplurals=2; plural=(n != 1);', 
			'sq' => 'nplurals=2; plural=(n != 1);', 
			'sr' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);', 
			'su' => 'nplurals=1; plural=0;', 
			'sw' => 'nplurals=2; plural=(n != 1);', 
			'sv' => 'nplurals=2; plural=(n != 1);', 
			'ta' => 'nplurals=2; plural=(n != 1);', 
			'te' => 'nplurals=2; plural=(n != 1);', 
			'tg' => 'nplurals=1; plural=0;', 
			'ti' => 'nplurals=2; plural=n > 1;', 
			'th' => 'nplurals=1; plural=0;', 
			'tk' => 'nplurals=2; plural=(n != 1);', 
			'tr' => 'nplurals=2; plural=(n>1);', 
			'tt' => 'nplurals=1; plural=0;', 
			'ug' => 'nplurals=1; plural=0;', 
			'uk' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);', 
			'ur' => 'nplurals=2; plural=(n != 1);', 
			'uz' => 'nplurals=1; plural=0;', 
			'vi' => 'nplurals=1; plural=0;', 
			'wa' => 'nplurals=2; plural=(n > 1);', 
			'yo' => 'nplurals=2; plural=(n != 1);', 
			'zh' => 'nplurals=1; plural=0'
		);
	
	public static function getPluralRule($iso)
	{
		return self::$plurals[$iso];
	}
	
	public static function getPluralsCount($iso)
	{
		$rule = self::$plurals[$iso];
		$tmp = preg_replace('/([a-z]+)/', '$$1', "n=1;" . $rule);
		eval($tmp);
		
		return $nplurals;
	}
	
	public static function verifyLang($iso)
	{
		return array_key_exists($iso, self::$plurals);
	}
	
	public static function getLangs()
	{
		return self::$plurals;
	}
}