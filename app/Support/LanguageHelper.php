<?php

namespace App\Support;

class LanguageHelper
{
    private static array $languages = [
        'aa' => ['nativeName' => 'Afar', 'flag' => '🇪🇷'],
        'ab' => ['nativeName' => 'Аҧсуа', 'flag' => '🇬🇪'],
        'ae' => ['nativeName' => 'Avesta', 'flag' => '🌐'],
        'af' => ['nativeName' => 'Afrikaans', 'flag' => '🇿🇦'],
        'ak' => ['nativeName' => 'Akan', 'flag' => '🇬🇭'],
        'am' => ['nativeName' => 'አማርኛ', 'flag' => '🇪🇹'],
        'an' => ['nativeName' => 'Aragonés', 'flag' => '🇪🇸'],
        'ar' => ['nativeName' => 'العربية', 'flag' => '🇸🇦'],
        'as' => ['nativeName' => 'অসমীয়া', 'flag' => '🇮🇳'],
        'av' => ['nativeName' => 'Авар', 'flag' => '🇷🇺'],
        'ay' => ['nativeName' => 'Aymar', 'flag' => '🇧🇴'],
        'az' => ['nativeName' => 'Azərbaycan', 'flag' => '🇦🇿'],
        'ba' => ['nativeName' => 'Башҡорт', 'flag' => '🇷🇺'],
        'be' => ['nativeName' => 'Беларуская', 'flag' => '🇧🇾'],
        'bg' => ['nativeName' => 'Български', 'flag' => '🇧🇬'],
        'bh' => ['nativeName' => 'भोजपुरी', 'flag' => '🇮🇳'],
        'bi' => ['nativeName' => 'Bislama', 'flag' => '🇻🇺'],
        'bm' => ['nativeName' => 'Bamanankan', 'flag' => '🇲🇱'],
        'bn' => ['nativeName' => 'বাংলা', 'flag' => '🇧🇩'],
        'bo' => ['nativeName' => 'བོད་ཡིག', 'flag' => '🇨🇳'],
        'br' => ['nativeName' => 'Brezhoneg', 'flag' => '🇫🇷'],
        'bs' => ['nativeName' => 'Bosanski', 'flag' => '🇧🇦'],
        'ca' => ['nativeName' => 'Català', 'flag' => '🇪🇸'],
        'ce' => ['nativeName' => 'Нохчийн', 'flag' => '🇷🇺'],
        'ch' => ['nativeName' => 'Chamoru', 'flag' => '🇬🇺'],
        'co' => ['nativeName' => 'Corsu', 'flag' => '🇫🇷'],
        'cr' => ['nativeName' => 'ᓀᐦᐃᔭᐍᐏᐣ', 'flag' => '🇨🇦'],
        'cs' => ['nativeName' => 'Čeština', 'flag' => '🇨🇿'],
        'cu' => ['nativeName' => 'Словѣньскъ', 'flag' => '🌐'],
        'cv' => ['nativeName' => 'Чӑваш', 'flag' => '🇷🇺'],
        'cy' => ['nativeName' => 'Cymraeg', 'flag' => '🇬🇧'],
        'da' => ['nativeName' => 'Dansk', 'flag' => '🇩🇰'],
        'de' => ['nativeName' => 'Deutsch', 'flag' => '🇩🇪'],
        'dv' => ['nativeName' => 'ދިވެހި', 'flag' => '🇲🇻'],
        'dz' => ['nativeName' => 'རྫོང་ཁ', 'flag' => '🇧🇹'],
        'ee' => ['nativeName' => 'Eʋegbe', 'flag' => '🇬🇭'],
        'el' => ['nativeName' => 'Ελληνικά', 'flag' => '🇬🇷'],
        'en' => ['nativeName' => 'English', 'flag' => '🇬🇧'],
        'eo' => ['nativeName' => 'Esperanto', 'flag' => '🌐'],
        'es' => ['nativeName' => 'Español', 'flag' => '🇪🇸'],
        'et' => ['nativeName' => 'Eesti', 'flag' => '🇪🇪'],
        'eu' => ['nativeName' => 'Euskara', 'flag' => '🇪🇸'],
        'fa' => ['nativeName' => 'فارسی', 'flag' => '🇮🇷'],
        'ff' => ['nativeName' => 'Fulfulde', 'flag' => '🇸🇳'],
        'fi' => ['nativeName' => 'Suomi', 'flag' => '🇫🇮'],
        'fj' => ['nativeName' => 'Vosa Vakaviti', 'flag' => '🇫🇯'],
        'fo' => ['nativeName' => 'Føroyskt', 'flag' => '🇫🇴'],
        'fr' => ['nativeName' => 'Français', 'flag' => '🇫🇷'],
        'fy' => ['nativeName' => 'Frysk', 'flag' => '🇳🇱'],
        'ga' => ['nativeName' => 'Gaeilge', 'flag' => '🇮🇪'],
        'gd' => ['nativeName' => 'Gàidhlig', 'flag' => '🇬🇧'],
        'gl' => ['nativeName' => 'Galego', 'flag' => '🇪🇸'],
        'gn' => ['nativeName' => 'Avañe\'ẽ', 'flag' => '🇵🇾'],
        'gu' => ['nativeName' => 'ગુજરાતી', 'flag' => '🇮🇳'],
        'gv' => ['nativeName' => 'Gaelg', 'flag' => '🇮🇲'],
        'ha' => ['nativeName' => 'Hausa', 'flag' => '🇳🇬'],
        'he' => ['nativeName' => 'עברית', 'flag' => '🇮🇱'],
        'hi' => ['nativeName' => 'हिन्दी', 'flag' => '🇮🇳'],
        'ho' => ['nativeName' => 'Hiri Motu', 'flag' => '🇵🇬'],
        'hr' => ['nativeName' => 'Hrvatski', 'flag' => '🇭🇷'],
        'ht' => ['nativeName' => 'Kreyòl ayisyen', 'flag' => '🇭🇹'],
        'hu' => ['nativeName' => 'Magyar', 'flag' => '🇭🇺'],
        'hy' => ['nativeName' => 'Հայերեն', 'flag' => '🇦🇲'],
        'hz' => ['nativeName' => 'Otjiherero', 'flag' => '🇳🇦'],
        'ia' => ['nativeName' => 'Interlingua', 'flag' => '🌐'],
        'id' => ['nativeName' => 'Bahasa Indonesia', 'flag' => '🇮🇩'],
        'ie' => ['nativeName' => 'Interlingue', 'flag' => '🌐'],
        'ig' => ['nativeName' => 'Igbo', 'flag' => '🇳🇬'],
        'ii' => ['nativeName' => 'ꆇꉙ', 'flag' => '🇨🇳'],
        'ik' => ['nativeName' => 'Iñupiaq', 'flag' => '🇺🇸'],
        'io' => ['nativeName' => 'Ido', 'flag' => '🌐'],
        'is' => ['nativeName' => 'Íslenska', 'flag' => '🇮🇸'],
        'it' => ['nativeName' => 'Italiano', 'flag' => '🇮🇹'],
        'iu' => ['nativeName' => 'ᐃᓄᒃᑎᑐᑦ', 'flag' => '🇨🇦'],
        'ja' => ['nativeName' => '日本語', 'flag' => '🇯🇵'],
        'jv' => ['nativeName' => 'Basa Jawa', 'flag' => '🇮🇩'],
        'ka' => ['nativeName' => 'ქართული', 'flag' => '🇬🇪'],
        'kg' => ['nativeName' => 'Kikongo', 'flag' => '🇨🇩'],
        'ki' => ['nativeName' => 'Gĩkũyũ', 'flag' => '🇰🇪'],
        'kj' => ['nativeName' => 'Kuanyama', 'flag' => '🇳🇦'],
        'kk' => ['nativeName' => 'Қазақ', 'flag' => '🇰🇿'],
        'kl' => ['nativeName' => 'Kalaallisut', 'flag' => '🇬🇱'],
        'km' => ['nativeName' => 'ភាសាខ្មែរ', 'flag' => '🇰🇭'],
        'kn' => ['nativeName' => 'ಕನ್ನಡ', 'flag' => '🇮🇳'],
        'ko' => ['nativeName' => '한국어', 'flag' => '🇰🇷'],
        'kr' => ['nativeName' => 'Kanuri', 'flag' => '🇳🇪'],
        'ks' => ['nativeName' => 'कॉशुर', 'flag' => '🇮🇳'],
        'ku' => ['nativeName' => 'Kurdî', 'flag' => '🇮🇶'],
        'kv' => ['nativeName' => 'Коми', 'flag' => '🇷🇺'],
        'kw' => ['nativeName' => 'Kernewek', 'flag' => '🇬🇧'],
        'ky' => ['nativeName' => 'Кыргызча', 'flag' => '🇰🇬'],
        'la' => ['nativeName' => 'Latina', 'flag' => '🇻🇦'],
        'lb' => ['nativeName' => 'Lëtzebuergesch', 'flag' => '🇱🇺'],
        'lg' => ['nativeName' => 'Luganda', 'flag' => '🇺🇬'],
        'li' => ['nativeName' => 'Limburgs', 'flag' => '🇳🇱'],
        'ln' => ['nativeName' => 'Lingála', 'flag' => '🇨🇩'],
        'lo' => ['nativeName' => 'ລາວ', 'flag' => '🇱🇦'],
        'lt' => ['nativeName' => 'Lietuvių', 'flag' => '🇱🇹'],
        'lu' => ['nativeName' => 'Tshiluba', 'flag' => '🇨🇩'],
        'lv' => ['nativeName' => 'Latviešu', 'flag' => '🇱🇻'],
        'mg' => ['nativeName' => 'Malagasy', 'flag' => '🇲🇬'],
        'mh' => ['nativeName' => 'Kajin M̧ajeļ', 'flag' => '🇲🇭'],
        'mi' => ['nativeName' => 'Te Reo Māori', 'flag' => '🇳🇿'],
        'mk' => ['nativeName' => 'Македонски', 'flag' => '🇲🇰'],
        'ml' => ['nativeName' => 'മലയാളം', 'flag' => '🇮🇳'],
        'mn' => ['nativeName' => 'Монгол', 'flag' => '🇲🇳'],
        'mr' => ['nativeName' => 'मराठी', 'flag' => '🇮🇳'],
        'ms' => ['nativeName' => 'Bahasa Melayu', 'flag' => '🇲🇾'],
        'mt' => ['nativeName' => 'Malti', 'flag' => '🇲🇹'],
        'my' => ['nativeName' => 'မြန်မာ', 'flag' => '🇲🇲'],
        'na' => ['nativeName' => 'Dorerin Naoero', 'flag' => '🇳🇷'],
        'nb' => ['nativeName' => 'Norsk Bokmål', 'flag' => '🇳🇴'],
        'nd' => ['nativeName' => 'isiNdebele', 'flag' => '🇿🇼'],
        'ne' => ['nativeName' => 'नेपाली', 'flag' => '🇳🇵'],
        'ng' => ['nativeName' => 'Owambo', 'flag' => '🇳🇦'],
        'nl' => ['nativeName' => 'Nederlands', 'flag' => '🇳🇱'],
        'nn' => ['nativeName' => 'Norsk Nynorsk', 'flag' => '🇳🇴'],
        'no' => ['nativeName' => 'Norsk', 'flag' => '🇳🇴'],
        'nr' => ['nativeName' => 'isiNdebele', 'flag' => '🇿🇦'],
        'nv' => ['nativeName' => 'Diné bizaad', 'flag' => '🇺🇸'],
        'ny' => ['nativeName' => 'Chichewa', 'flag' => '🇲🇼'],
        'oc' => ['nativeName' => 'Occitan', 'flag' => '🇫🇷'],
        'oj' => ['nativeName' => 'ᐊᓂᔑᓈᐯᒧᐎᓐ', 'flag' => '🇨🇦'],
        'om' => ['nativeName' => 'Oromoo', 'flag' => '🇪🇹'],
        'or' => ['nativeName' => 'ଓଡ଼ିଆ', 'flag' => '🇮🇳'],
        'os' => ['nativeName' => 'Ирон', 'flag' => '🇷🇺'],
        'pa' => ['nativeName' => 'ਪੰਜਾਬੀ', 'flag' => '🇮🇳'],
        'pi' => ['nativeName' => 'पालि', 'flag' => '🌐'],
        'pl' => ['nativeName' => 'Polski', 'flag' => '🇵🇱'],
        'ps' => ['nativeName' => 'پښتو', 'flag' => '🇦🇫'],
        'pt' => ['nativeName' => 'Português', 'flag' => '🇵🇹'],
        'qu' => ['nativeName' => 'Runa Simi', 'flag' => '🇵🇪'],
        'rm' => ['nativeName' => 'Rumantsch', 'flag' => '🇨🇭'],
        'rn' => ['nativeName' => 'Ikirundi', 'flag' => '🇧🇮'],
        'ro' => ['nativeName' => 'Română', 'flag' => '🇷🇴'],
        'ru' => ['nativeName' => 'Русский', 'flag' => '🇷🇺'],
        'rw' => ['nativeName' => 'Ikinyarwanda', 'flag' => '🇷🇼'],
        'sa' => ['nativeName' => 'संस्कृतम्', 'flag' => '🇮🇳'],
        'sc' => ['nativeName' => 'Sardu', 'flag' => '🇮🇹'],
        'sd' => ['nativeName' => 'سنڌي', 'flag' => '🇵🇰'],
        'se' => ['nativeName' => 'Davvisámegiella', 'flag' => '🇳🇴'],
        'sg' => ['nativeName' => 'Sängö', 'flag' => '🇨🇫'],
        'si' => ['nativeName' => 'සිංහල', 'flag' => '🇱🇰'],
        'sk' => ['nativeName' => 'Slovenčina', 'flag' => '🇸🇰'],
        'sl' => ['nativeName' => 'Slovenščina', 'flag' => '🇸🇮'],
        'sm' => ['nativeName' => 'Gagana Samoa', 'flag' => '🇼🇸'],
        'sn' => ['nativeName' => 'chiShona', 'flag' => '🇿🇼'],
        'so' => ['nativeName' => 'Soomaali', 'flag' => '🇸🇴'],
        'sq' => ['nativeName' => 'Shqip', 'flag' => '🇦🇱'],
        'sr' => ['nativeName' => 'Српски', 'flag' => '🇷🇸'],
        'ss' => ['nativeName' => 'SiSwati', 'flag' => '🇸🇿'],
        'st' => ['nativeName' => 'Sesotho', 'flag' => '🇱🇸'],
        'su' => ['nativeName' => 'Basa Sunda', 'flag' => '🇮🇩'],
        'sv' => ['nativeName' => 'Svenska', 'flag' => '🇸🇪'],
        'sw' => ['nativeName' => 'Kiswahili', 'flag' => '🇹🇿'],
        'ta' => ['nativeName' => 'தமிழ்', 'flag' => '🇱🇰'],
        'te' => ['nativeName' => 'తెలుగు', 'flag' => '🇮🇳'],
        'tg' => ['nativeName' => 'Тоҷикӣ', 'flag' => '🇹🇯'],
        'th' => ['nativeName' => 'ไทย', 'flag' => '🇹🇭'],
        'ti' => ['nativeName' => 'ትግርኛ', 'flag' => '🇪🇷'],
        'tk' => ['nativeName' => 'Türkmen', 'flag' => '🇹🇲'],
        'tl' => ['nativeName' => 'Tagalog', 'flag' => '🇵🇭'],
        'tn' => ['nativeName' => 'Setswana', 'flag' => '🇧🇼'],
        'to' => ['nativeName' => 'Lea faka-Tonga', 'flag' => '🇹🇴'],
        'tr' => ['nativeName' => 'Türkçe', 'flag' => '🇹🇷'],
        'ts' => ['nativeName' => 'Xitsonga', 'flag' => '🇿🇦'],
        'tt' => ['nativeName' => 'Татар', 'flag' => '🇷🇺'],
        'tw' => ['nativeName' => 'Twi', 'flag' => '🇬🇭'],
        'ty' => ['nativeName' => 'Reo Tahiti', 'flag' => '🇵🇫'],
        'ug' => ['nativeName' => 'ئۇيغۇرچە', 'flag' => '🇨🇳'],
        'uk' => ['nativeName' => 'Українська', 'flag' => '🇺🇦'],
        'ur' => ['nativeName' => 'اردو', 'flag' => '🇵🇰'],
        'uz' => ['nativeName' => 'Oʻzbek', 'flag' => '🇺🇿'],
        've' => ['nativeName' => 'Tshivenḓa', 'flag' => '🇿🇦'],
        'vi' => ['nativeName' => 'Tiếng Việt', 'flag' => '🇻🇳'],
        'vo' => ['nativeName' => 'Volapük', 'flag' => '🌐'],
        'wa' => ['nativeName' => 'Walon', 'flag' => '🇧🇪'],
        'wo' => ['nativeName' => 'Wolof', 'flag' => '🇸🇳'],
        'xh' => ['nativeName' => 'isiXhosa', 'flag' => '🇿🇦'],
        'yi' => ['nativeName' => 'ייִדיש', 'flag' => '🇮🇱'],
        'yo' => ['nativeName' => 'Yorùbá', 'flag' => '🇳🇬'],
        'za' => ['nativeName' => 'Vahcuengh', 'flag' => '🇨🇳'],
        'zh' => ['nativeName' => '中文', 'flag' => '🇨🇳'],
        'zu' => ['nativeName' => 'isiZulu', 'flag' => '🇿🇦'],
    ];

    public static function getLanguageName(string $code, ?string $locale = null, bool $showFlag = false): string
    {
        $code = strtolower($code);
        $locale = $locale ?? app()->getLocale();

        if (!isset(self::$languages[$code])) {
            return ucfirst($code);
        }

        $name = __("languages.{$code}", [], $locale) ?: ucfirst($code);
        $flag = $showFlag ? self::$languages[$code]['flag'] . ' ' : '';

        return $name . ' ' . $flag;
    }

    public static function getLanguageNameWithTranslation(string $code, ?string $locale = null, bool $showFlag = false): string
    {
        $code = strtolower($code);
        $locale = $locale ?? app()->getLocale();

        if (!isset(self::$languages[$code])) {
            return ucfirst($code);
        }

        $nativeName = self::$languages[$code]['nativeName'];
        $translation = __("languages.{$code}", [], $locale);
        $flag = $showFlag ? self::$languages[$code]['flag'] . ' ' : '';

        if ($code === $locale) {
            return $nativeName . ' ' . $flag;
        }

        return "{$nativeName} {$flag} ({$translation})";
    }

    public static function getAllLanguages(?string $locale = null, bool $showFlag = false): array
    {
        $locale = $locale ?? app()->getLocale();
        $languages = [];

        foreach (self::$languages as $code => $language) {
            $name = __("languages.{$code}", [], $locale) ?: ucfirst($code);
            $flag = $showFlag ? $language['flag'] . ' ' : '';
            $languages[$code] = $name . ' ' . $flag;
        }

        return $languages;
    }

    public static function getAllLanguagesWithTranslation(?string $locale = null, bool $showFlag = false): array
    {
        $locale = $locale ?? app()->getLocale();
        $languages = [];

        foreach (self::$languages as $code => $language) {
            $nativeName = $language['nativeName'];
            $translation = __("languages.{$code}", [], $locale);
            $flag = $showFlag ? $language['flag'] . ' ' : '';

            if ($code === $locale) {
                $languages[$code] =  $nativeName . ' ' . $flag;
            } else {
                $languages[$code] = "{$nativeName} {$flag} ({$translation})";
            }
        }

        return $languages;
    }

    public static function isValidLanguageCode(string $code): bool
    {
        return isset(self::$languages[strtolower($code)]);
    }
}

