<?php
class Session {
    public static function set(string $key, $value): void { $_SESSION[$key] = $value; }
    public static function get(string $key) { return $_SESSION[$key] ?? null; }
    public static function remove(string $key): void { unset($_SESSION[$key]); }
    public static function destroy(): void { session_unset(); session_destroy(); }
    public static function isLoggedIn(): bool { return isset($_SESSION['user_id']); }

    private const UNKNOWN = 'غير محدد';
    private const GEO_USER_AGENT = 'MyStoreGeoResolver/1.0';
    private const OFFLINE_TTL = 300;

    private static function normalizeKey(string $value): string {
        $v = mb_strtolower(trim($value), 'UTF-8');
        $v = preg_replace('/[\x{0640}]/u', '', $v);
        $v = preg_replace('/[\x{064B}-\x{0652}]/u', '', $v);
        $v = preg_replace('/[\x{0622}\x{0623}\x{0625}]/u', '', $v);
        $v = preg_replace('/[ـ]/u', '', $v);
        $v = preg_replace('/^(?:محافظه|محافظة|governorate|gov\.?)\s+/u', '', $v);
        $v = preg_replace('/\s+(?:محافظه|محافظة|governorate|gov\.?)$/u', '', $v);
        $v = preg_replace('/^al[\s\-_]+/u', '', $v);
        $v = preg_replace('/[\s\-_.,]+/u', ' ', $v);
        return trim($v);
    }

    public static function governorateMap(): array {
        static $map = null;
        if ($map !== null) {
            return $map;
        }
        $map = [
            'القاهرة' => ['cairo', 'cairo gov', 'qahirah', 'qahira', 'alcairo', 'kairo', 'القاهره', 'القاهرة'],
            'الجيزة' => ['giza', 'giza gov', 'jiza', 'jizzah', 'gizeh', 'الجيزه', 'الجيزة', '6th of october', '6th october', 'sixth of october', 'october 6', '6 october', 'city of 6th october', '٦ أكتوبر', '٦ أكتوبر', 'مدينه ٦ أكتوبر'],
            'الإسكندرية' => ['alexandria', 'alexanderia', 'alex', 'alex gov', 'الاسكندريه', 'الإسكندرية'],
            'الدقهلية' => ['dakahlia', 'dakahlia gov', 'dakahlia governorate', 'mansoura', 'mansourah', 'منصورة', 'الدقهليه', 'الدقهلية'],
            'الشرقية' => ['sharqia', 'sharqia gov', 'sharqia governorate', 'zagazig', 'الزقازيق', 'الشرقيه', 'الشرقية'],
            'القليوبية' => ['qalyubia', 'qaliubia', 'qalyubia gov', 'qalyubia governorate', 'benha', 'banha', 'banna', 'بنها', 'القليوبيه', 'القليوبية'],
            'الغربية' => ['gharbia', 'gharbia gov', 'gharbia governorate', 'tanta', 'طنطا', 'الغربيه', 'الغربية'],
            'المنوفية' => ['menofia', 'menoufia', 'monofia', 'menoufia gov', 'shibin el kom', 'shibin elkom', 'shebin el kom', 'شبين الكوم', 'المنوفيه', 'المنوفية'],
            'البحيرة' => ['beheira', 'behera', 'beheira gov', 'damanhour', 'damanhur', 'دمنهور', 'البحيره', 'البحيرة'],
            'كفر الشيخ' => ['kafr el sheikh', 'kafr el shikh', 'kafr elsheikh', 'kafr sheikh', 'kafr elsheikh', 'كفر الشيخ'],
            'دمياط' => ['damietta', 'damiatta', 'damiat', 'دمياط'],
            'بورسعيد' => ['port said', 'portsaid', 'port saeed', 'بورسعيد'],
            'الإسماعيلية' => ['ismailia', 'ismailia gov', 'ismailia governorate', 'ismailia city', 'الاسماعيله', 'الإسماعيلية'],
            'السويس' => ['suez', 'suez gov', 'suez governorate', 'السويس'],
            'الفيوم' => ['faiyum', 'fayoum', 'faoum', 'faiyum gov', 'الفيوم'],
            'بني سويف' => ['beni suef', 'beni suif', 'beni suif gov', 'beni suyef', 'بنى سويف', 'بني سويف'],
            'المنيا' => ['minya', 'minya gov', 'minya governorate', 'al minya', 'المنيا', 'المنياه'],
            'أسيوط' => ['asyut', 'asuit', 'assiut', 'asyout', 'asiut', 'أسيوط'],
            'سوهاج' => ['sohag', 'sohag gov', 'sohag governorate', 'سوهاج'],
            'قنا' => ['qena', 'qena gov', 'qena governorate', 'قنا'],
            'الأقصر' => ['luxor', 'luxor gov', 'luxor governorate', 'الاقصر', 'الأقصر'],
            'أسوان' => ['aswan', 'assuan', 'aswan gov', 'اسوان', 'أسوان'],
            'البحر الأحمر' => ['red sea', 'red sea gov', 'redsea', 'bahr el ahmar', 'البحر الاحمر', 'البحر الأحمر'],
            'الوادي الجديد' => ['new valley', 'newvalley', 'wadi el nidar', 'wadi al nidar', 'الوادي الجديد'],
            'مطروح' => ['matrouh', 'matruh', 'maritut', 'matrooh', 'مطروح'],
            'شمال سيناء' => ['north sinai', 'northsinai', 'shimal sina', 'شمال سيناء'],
            'جنوب سيناء' => ['south sinai', 'southsinai', 'shomul sina', 'جنوب سيناء'],
        ];
        return $map;
    }

    private static function cityToGovernorateMap(): array {
        static $map = null;
        if ($map !== null) {
            return $map;
        }
        $map = [
            'hurghada' => 'البحر الأحمر',
            'hurghada red sea' => 'البحر الأحمر',
            'الغردقه' => 'البحر الأحمر',
            'الغردقة' => 'البحر الأحمر',
            'سمرقان' => 'البحر الأحمر',
            'sharm el sheikh' => 'جنوب سيناء',
            'sharm elsheikh' => 'جنوب سيناء',
            'sharm el sheikh south sinai' => 'جنوب سيناء',
            'شرم الشيخ' => 'جنوب سيناء',
            'شرم الشيش' => 'جنوب سيناء',
            'dahab' => 'جنوب سيناء',
            'دهب' => 'جنوب سيناء',
            'nagaa hammadi' => 'البحر الأحمر',
            'نجا حمادي' => 'البحر الأحمر',
            'مرسى مراد' => 'البحر الأحمر',
            'sidi barrani' => 'مطروح',
            'مرسى مطروح' => 'مطروح',
            'marsa matruh' => 'مطروح',
            '6th of october city' => 'الجيزة',
            'sheikh zayed' => 'الجيزة',
            'الشيخ زايد' => 'الجيزة',
            'october city' => 'الجيزة',
            'مدينة نصر' => 'القاهرة',
            'مدينة ٦ أكتوبر' => 'الجيزة',
            'المحلة الكبرى' => 'الغربية',
            'smouha' => 'الإسكندرية',
            'سمكرة' => 'الإسكندرية',
            'برج العرب' => 'الإسكندرية',
            'burg el arab' => 'الإسكندرية',
        ];
        return $map;
    }

    private static function countryTable(): array {
        static $table = null;
        if ($table !== null) {
            return $table;
        }
        $table = [
            'EG' => ['ar' => 'مصر', 'en' => 'Egypt', 'alias' => []],
            'SA' => ['ar' => 'السعودية', 'en' => 'Saudi Arabia', 'alias' => ['saudi']],
            'AE' => ['ar' => 'الإمارات', 'en' => 'United Arab Emirates', 'alias' => ['uae']],
            'KW' => ['ar' => 'الكويت', 'en' => 'Kuwait', 'alias' => []],
            'QA' => ['ar' => 'قطر', 'en' => 'Qatar', 'alias' => []],
            'BH' => ['ar' => 'البحرين', 'en' => 'Bahrain', 'alias' => []],
            'OM' => ['ar' => 'عُمان', 'en' => 'Oman', 'alias' => []],
            'JO' => ['ar' => 'الأردن', 'en' => 'Jordan', 'alias' => []],
            'LB' => ['ar' => 'لبنان', 'en' => 'Lebanon', 'alias' => []],
            'IQ' => ['ar' => 'العراق', 'en' => 'Iraq', 'alias' => []],
            'LY' => ['ar' => 'ليبيا', 'en' => 'Libya', 'alias' => []],
            'SD' => ['ar' => 'السودان', 'en' => 'Sudan', 'alias' => []],
            'SS' => ['ar' => 'جنوب السودان', 'en' => 'South Sudan', 'alias' => ['southern sudan']],
            'MA' => ['ar' => 'المغرب', 'en' => 'Morocco', 'alias' => []],
            'TN' => ['ar' => 'تونس', 'en' => 'Tunisia', 'alias' => []],
            'DZ' => ['ar' => 'الجزائر', 'en' => 'Algeria', 'alias' => []],
            'PS' => ['ar' => 'فلسطين', 'en' => 'Palestine', 'alias' => ['palestinian territory']],
            'SY' => ['ar' => 'سوريا', 'en' => 'Syria', 'alias' => ['syrian arab republic']],
            'YE' => ['ar' => 'اليمن', 'en' => 'Yemen', 'alias' => []],
            'US' => ['ar' => 'الولايات المتحدة', 'en' => 'United States', 'alias' => ['usa', 'united states of america', 'us', 'america']],
            'GB' => ['ar' => 'المملكة المتحدة', 'en' => 'United Kingdom', 'alias' => ['uk', 'great britain', 'britain', 'england']],
            'DE' => ['ar' => 'ألمانيا', 'en' => 'Germany', 'alias' => []],
            'FR' => ['ar' => 'فرنسا', 'en' => 'France', 'alias' => []],
            'TR' => ['ar' => 'تركيا', 'en' => 'Turkey', 'alias' => ['turkiye', 'türkiye']],
            'RU' => ['ar' => 'روسيا', 'en' => 'Russia', 'alias' => ['russian federation']],
            'CN' => ['ar' => 'الصين', 'en' => 'China', 'alias' => ['peoples republic of china']],
            'JP' => ['ar' => 'اليابان', 'en' => 'Japan', 'alias' => []],
            'IN' => ['ar' => 'الهند', 'en' => 'India', 'alias' => []],
            'PK' => ['ar' => 'باكستان', 'en' => 'Pakistan', 'alias' => []],
            'ID' => ['ar' => 'إندونيسيا', 'en' => 'Indonesia', 'alias' => []],
            'MY' => ['ar' => 'ماليزيا', 'en' => 'Malaysia', 'alias' => []],
            'CA' => ['ar' => 'كندا', 'en' => 'Canada', 'alias' => []],
            'AU' => ['ar' => 'أستراليا', 'en' => 'Australia', 'alias' => []],
            'NZ' => ['ar' => 'نيوزيلندا', 'en' => 'New Zealand', 'alias' => []],
            'IT' => ['ar' => 'إيطاليا', 'en' => 'Italy', 'alias' => []],
            'ES' => ['ar' => 'إسبانيا', 'en' => 'Spain', 'alias' => []],
            'NL' => ['ar' => 'هولندا', 'en' => 'Netherlands', 'alias' => ['the netherlands', 'holland']],
            'BE' => ['ar' => 'بلجيكا', 'en' => 'Belgium', 'alias' => []],
            'SE' => ['ar' => 'السويد', 'en' => 'Sweden', 'alias' => []],
            'NO' => ['ar' => 'النرويج', 'en' => 'Norway', 'alias' => []],
            'DK' => ['ar' => 'الدنمارك', 'en' => 'Denmark', 'alias' => []],
            'FI' => ['ar' => 'فنلندا', 'en' => 'Finland', 'alias' => []],
            'CH' => ['ar' => 'سويسرا', 'en' => 'Switzerland', 'alias' => []],
            'AT' => ['ar' => 'النمسا', 'en' => 'Austria', 'alias' => []],
            'IE' => ['ar' => 'أيرلندا', 'en' => 'Ireland', 'alias' => []],
            'PT' => ['ar' => 'البرتغال', 'en' => 'Portugal', 'alias' => []],
            'GR' => ['ar' => 'اليونان', 'en' => 'Greece', 'alias' => []],
            'BR' => ['ar' => 'البرازيل', 'en' => 'Brazil', 'alias' => []],
            'MX' => ['ar' => 'المكسيك', 'en' => 'Mexico', 'alias' => []],
            'ZA' => ['ar' => 'جنوب أفريقيا', 'en' => 'South Africa', 'alias' => []],
            'NG' => ['ar' => 'نيجيريا', 'en' => 'Nigeria', 'alias' => []],
            'KE' => ['ar' => 'كينيا', 'en' => 'Kenya', 'alias' => []],
            'ET' => ['ar' => 'إثيوبيا', 'en' => 'Ethiopia', 'alias' => []],
        ];
        return $table;
    }

    private static function resolveCountryCode(string $name): string {
        static $index = null;
        if ($index === null) {
            $index = [];
            foreach (self::countryTable() as $code => $entry) {
                $index[self::normalizeKey((string)$entry['ar'])] = $code;
                $index[self::normalizeKey((string)$entry['en'])] = $code;
                $index[strtolower($code)] = $code;
                foreach ((array)$entry['alias'] as $alias) {
                    $index[self::normalizeKey((string)$alias)] = $code;
                }
            }
        }
        $key = self::normalizeKey($name);
        if ($key === '') {
            return '';
        }
        return $index[$key] ?? '';
    }

    public static function countryNameMap(): array {
        static $map = null;
        if ($map !== null) {
            return $map;
        }
        $map = [];
        foreach (self::countryTable() as $code => $entry) {
            $map[$code] = (string)$entry['ar'];
        }
        return $map;
    }

    public static function normalizeLocation(string $country, string $countryCode, string $region, string $city): array {
        $countryCode = strtoupper(trim($countryCode));
        $country = trim($country);
        $region = trim($region);
        $city = trim($city);

        $lowCountry = mb_strtolower($country, 'UTF-8');
        $nameCode = self::resolveCountryCode($country);
        $isEgypt = ($countryCode === 'EG')
            || ($nameCode === 'EG')
            || in_array($lowCountry, ['egypt', 'مصر'], true)
            || in_array(self::normalizeKey($country), ['egypt', 'مصر'], true);

        if ($isEgypt) {
            $governorate = self::resolveGovernorate($region !== '' ? $region : $city);
            $resolvedCity = self::resolveGovernorate($city);
            if ($resolvedCity === '' || $resolvedCity === $governorate || self::normalizeKey($resolvedCity) === self::normalizeKey($governorate)) {
                $resolvedCity = '';
            }
            return [
                'country' => 'مصر',
                'country_code' => 'EG',
                'region' => $governorate !== '' ? $governorate : self::UNKNOWN,
                'city' => $resolvedCity !== '' ? $resolvedCity : ($governorate !== '' ? $governorate : self::UNKNOWN),
            ];
        }

        $names = self::countryNameMap();

        if ($countryCode === '' || !isset($names[$countryCode])) {
            if ($nameCode !== '') {
                $countryCode = $nameCode;
            }
        }

        if ($country === '' || self::normalizeKey($country) === self::normalizeKey(self::UNKNOWN)) {
            $country = isset($names[$countryCode]) ? $names[$countryCode] : ($countryCode !== '' ? $countryCode : self::UNKNOWN);
        } elseif ($nameCode !== '' && isset($names[$nameCode])) {
            $country = $names[$nameCode];
        }

        $resolvedRegion = trim($region);
        if ($resolvedRegion === '' || self::normalizeKey($resolvedRegion) === self::normalizeKey(self::UNKNOWN)) {
            $resolvedRegion = trim($city);
        }
        if ($resolvedRegion === '' || self::normalizeKey($resolvedRegion) === self::normalizeKey(self::UNKNOWN)) {
            $resolvedRegion = self::UNKNOWN;
        }

        $resolvedCity = trim($city);
        if ($resolvedCity === '' || self::normalizeKey($resolvedCity) === self::normalizeKey($resolvedRegion)) {
            $resolvedCity = $resolvedRegion;
        }

        return [
            'country' => $country,
            'country_code' => $countryCode,
            'region' => $resolvedRegion,
            'city' => $resolvedCity,
        ];
    }

    private static function resolveGovernorate(string $value): string {
        $key = self::normalizeKey($value);
        if ($key === '' || self::normalizeKey($key) === self::normalizeKey(self::UNKNOWN)) {
            return '';
        }
        $cityMap = self::cityToGovernorateMap();
        if (isset($cityMap[$key])) {
            return $cityMap[$key];
        }
        foreach (self::governorateMap() as $canonical => $variants) {
            if (self::normalizeKey($canonical) === $key) {
                return $canonical;
            }
            foreach ($variants as $variant) {
                if (self::normalizeKey($variant) === $key) {
                    return $canonical;
                }
            }
        }
        return '';
    }

    private static function isPublicIp(string $ip): bool {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    }

    private static function isTrackableRequest(): bool {
        $uri = (string)($_SERVER['REQUEST_URI'] ?? '/');
        $path = (string)(parse_url($uri, PHP_URL_PATH) ?: $uri);

        $method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        if ($method === 'HEAD' || $method === 'OPTIONS') {
            return false;
        }

        $extension = strtolower((string)pathinfo($path, PATHINFO_EXTENSION));
        $staticExtensions = [
            'css', 'js', 'mjs', 'map', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'bmp', 'svg', 'ico',
            'webmanifest', 'xml', 'txt', 'woff', 'woff2', 'ttf', 'otf', 'eot', 'mp3', 'mp4',
            'webm', 'ogg', 'wav', 'avi', 'mov', 'pdf', 'zip', 'rar', '7z', 'json', 'csv',
        ];
        if ($extension !== '' && in_array($extension, $staticExtensions, true)) {
            return false;
        }

        $agent = (string)($_SERVER['HTTP_USER_AGENT'] ?? '');
        if ($agent !== '' && preg_match('/render|uptimerobot|pingdom|statuscake|gtmetrix|googlehc|kube-probe|go-http-client|curl|wget|python-requests|node-fetch|headlesschrome|libwww-perl|java\/|okhttp|axios/i', $agent)) {
            return false;
        }

        if (empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) && $path === '/') {
            return false;
        }

        return true;
    }

    private static function extractClientIp(): string {
        $candidates = [];
        foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_TRUE_CLIENT_IP', 'HTTP_X_REAL_IP'] as $header) {
            if (!empty($_SERVER[$header])) {
                $candidates[] = trim((string)$_SERVER[$header]);
            }
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            foreach (explode(',', (string)$_SERVER['HTTP_X_FORWARDED_FOR']) as $part) {
                $candidates[] = trim($part);
            }
        }
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            $candidates[] = trim((string)$_SERVER['REMOTE_ADDR']);
        }
        $fallback = '';
        foreach ($candidates as $ip) {
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                continue;
            }
            if (self::isPublicIp($ip)) {
                return $ip;
            }
            if ($fallback === '') {
                $fallback = $ip;
            }
        }
        return $fallback;
    }

    private static function curlGet(string $url): ?string {
        if (!function_exists('curl_init')) {
            return null;
        }
        $handle = curl_init($url);
        if ($handle === false) {
            return null;
        }
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT_MS, 1200);
        curl_setopt($handle, CURLOPT_TIMEOUT_MS, 1800);
        curl_setopt($handle, CURLOPT_USERAGENT, self::GEO_USER_AGENT);
        curl_setopt($handle, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($handle, CURLOPT_ACCEPT_ENCODING, '');
        $body = curl_exec($handle);
        $error = curl_error($handle);
        curl_close($handle);
        if ($body === false || $body === '' || $error !== '') {
            return null;
        }
        return (string)$body;
    }

    private static function fetchTierOne(string $ip): ?array {
        $url = 'http://ip-api.com/json/' . rawurlencode($ip) . '?fields=status,country,countryCode,regionName,city&lang=ar';
        $raw = self::curlGet($url);
        if ($raw === null) {
            return null;
        }
        $data = json_decode($raw, true);
        if (!is_array($data) || ($data['status'] ?? '') !== 'success') {
            return null;
        }
        return self::normalizeLocation(
            (string)($data['country'] ?? ''),
            (string)($data['countryCode'] ?? ''),
            (string)($data['regionName'] ?? ''),
            (string)($data['city'] ?? '')
        );
    }

    private static function fetchTierTwo(string $ip): ?array {
        $raw = self::curlGet('https://ipwho.is/' . rawurlencode($ip));
        if ($raw === null) {
            return null;
        }
        $data = json_decode($raw, true);
        if (!is_array($data) || ($data['success'] ?? false) === false) {
            return null;
        }
        return self::normalizeLocation(
            (string)($data['country'] ?? ''),
            (string)($data['country_code'] ?? ''),
            (string)($data['region'] ?? ''),
            (string)($data['city'] ?? '')
        );
    }

    private static function fetchTierThree(string $ip): array {
        $headerCode = '';
        foreach (['HTTP_CF_IPCOUNTRY', 'HTTP_X_COUNTRY_CODE'] as $header) {
            $value = strtoupper(trim((string)($_SERVER[$header] ?? '')));
            if ($value !== '' && $value !== 'XX' && $value !== 'T1') {
                $headerCode = $value;
                break;
            }
        }
        return self::normalizeLocation('', $headerCode, '', '');
    }

    private static function lookupCachedLocation(string $ip): ?array {
        if ($ip === '') {
            return null;
        }
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT country, country_code, region, city FROM online_users WHERE ip_address = ? AND country IS NOT NULL AND country != ? AND region IS NOT NULL AND region != '' LIMIT 1");
            $stmt->execute([$ip, self::UNKNOWN]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row || empty($row['country']) || empty($row['region'])) {
                return null;
            }
            $cached = self::normalizeLocation(
                (string)($row['country'] ?? ''),
                (string)($row['country_code'] ?? ''),
                (string)($row['region'] ?? ''),
                (string)($row['city'] ?? '')
            );
            return $cached;
        } catch (Throwable $e) {
            return null;
        }
    }

    private static function resolveCloudflareLocation(): ?array {
        $country = strtoupper(trim((string)($_SERVER['HTTP_CF_IPCOUNTRY'] ?? '')));
        if ($country === '' || $country === 'XX' || $country === 'T1' || $country === 'UNKNOWN') {
            return null;
        }
        $region = trim((string)($_SERVER['HTTP_CF_REGION'] ?? ''));
        $city = trim((string)($_SERVER['HTTP_CF_IPCITY'] ?? ''));
        return self::normalizeLocation('', $country, $region, $city);
    }

    private static function getVisitorLocation(): array {
        $ip = self::extractClientIp();
        $ipKey = $ip !== '' ? $ip : 'local';

        $cached = $_SESSION['visitor_location'] ?? null;
        if (is_array($cached) && ($cached['ip'] ?? '') === $ipKey) {
            $cachedCountry = trim((string)($cached['country'] ?? ''));
            $cachedRegion = trim((string)($cached['region'] ?? ''));
            $isPrecise = !empty($cached['precise']);
            $isResolved = $cachedCountry !== '' && $cachedCountry !== self::UNKNOWN && $cachedRegion !== '' && $cachedRegion !== self::UNKNOWN;
            if ($isPrecise || $isResolved) {
                return $cached;
            }
        }

        if ($ip === '') {
            $resolved = self::normalizeLocation(self::UNKNOWN, '', '', '');
        } else {
            $resolved = self::resolveCloudflareLocation();

            if ($resolved === null) {
                $resolved = self::lookupCachedLocation($ip);
            }

            if ($resolved === null && !self::isPublicIp($ip)) {
                $resolved = self::normalizeLocation(self::UNKNOWN, '', '', '');
            }

            if ($resolved === null) {
                $resolved = self::fetchTierOne($ip);
            }

            if ($resolved === null) {
                $resolved = self::fetchTierTwo($ip);
            }

            if ($resolved === null) {
                $resolved = self::fetchTierThree($ip);
            }
        }

        if (!is_array($resolved)) {
            $resolved = self::normalizeLocation(self::UNKNOWN, '', '', '');
        }

        $resolved['ip'] = $ipKey;
        if (empty($resolved['precise'])) {
            $resolved['precise'] = false;
        }
        $_SESSION['visitor_location'] = $resolved;

        return $resolved;
    }

    private static function ensureOnlineSchema(): void {
        if (!empty($_SESSION['online_schema_ok'])) {
            return;
        }
        try {
            $db = Database::getInstance()->getConnection();
            $db->exec("CREATE TABLE IF NOT EXISTS online_users (
                session_id VARCHAR(255) NOT NULL,
                user_id INT NULL,
                user_name VARCHAR(255) NULL,
                last_activity INT NOT NULL,
                ip_address VARCHAR(64) NULL,
                country VARCHAR(120) NULL,
                country_code VARCHAR(10) NULL,
                region VARCHAR(120) NULL,
                city VARCHAR(120) NULL,
                current_page VARCHAR(255) NULL,
                device_type VARCHAR(30) NULL,
                PRIMARY KEY (session_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

            $existing = [];
            $columns = $db->query("SHOW COLUMNS FROM online_users");
            if ($columns !== false) {
                foreach ($columns->fetchAll(PDO::FETCH_ASSOC) as $column) {
                    $existing[strtolower((string)($column['Field'] ?? ''))] = true;
                }
            }
            $wanted = [
                'user_id' => 'INT NULL',
                'user_name' => 'VARCHAR(255) NULL',
                'ip_address' => 'VARCHAR(64) NULL',
                'country' => 'VARCHAR(120) NULL',
                'country_code' => 'VARCHAR(10) NULL',
                'region' => 'VARCHAR(120) NULL',
                'city' => 'VARCHAR(120) NULL',
                'current_page' => 'VARCHAR(255) NULL',
                'device_type' => 'VARCHAR(30) NULL',
            ];
            foreach ($wanted as $column => $definition) {
                if (!isset($existing[$column])) {
                    $db->exec('ALTER TABLE online_users ADD COLUMN `' . $column . '` ' . $definition);
                }
            }
            $_SESSION['online_schema_ok'] = true;
        } catch (Throwable $e) {
        }
    }

    public static function trackOnline(): void {
        if (!self::isTrackableRequest()) {
            return;
        }
        self::ensureOnlineSchema();

        $sessionId = session_id();
        if (!$sessionId) {
            return;
        }

        $ip = self::extractClientIp();
        if ($ip === '' || !filter_var($ip, FILTER_VALIDATE_IP)) {
            return;
        }
        if (!self::isPublicIp($ip)) {
            return;
        }

        try {
            $db = Database::getInstance()->getConnection();
            $time = time();

            $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            $userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;

            $location = self::getVisitorLocation();

            $deviceType = preg_match('/Mobile|Android|iPhone|iPad/i', (string)($_SERVER['HTTP_USER_AGENT'] ?? '')) ? 'Mobile' : 'Desktop';
            $currentPage = (string)(parse_url((string)($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/');
            if ($currentPage === '') {
                $currentPage = '/';
            }
            if (strlen($currentPage) > 255) {
                $currentPage = substr($currentPage, 0, 255);
            }

            $stmt = $db->prepare("
                INSERT INTO online_users (session_id, user_id, user_name, last_activity, ip_address, country, country_code, region, city, current_page, device_type)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                user_id = VALUES(user_id),
                user_name = VALUES(user_name),
                last_activity = VALUES(last_activity),
                ip_address = VALUES(ip_address),
                country = VALUES(country),
                country_code = VALUES(country_code),
                region = VALUES(region),
                city = VALUES(city),
                current_page = VALUES(current_page),
                device_type = VALUES(device_type)
            ");
            $stmt->execute([
                $sessionId,
                $userId,
                $userName,
                $time,
                $ip,
                $location['country'] ?? self::UNKNOWN,
                $location['country_code'] ?? '',
                $location['region'] ?? self::UNKNOWN,
                $location['city'] ?? self::UNKNOWN,
                $currentPage,
                $deviceType,
            ]);

            if ($userId !== null) {
                $dedupe = $db->prepare("DELETE FROM online_users WHERE session_id != ? AND (user_id = ? OR (user_id IS NULL AND ip_address = ?))");
                $dedupe->execute([$sessionId, $userId, $ip]);
            } else {
                $dedupe = $db->prepare("DELETE FROM online_users WHERE session_id != ? AND user_id IS NULL AND ip_address = ? AND device_type = ?");
                $dedupe->execute([$sessionId, $ip, $deviceType]);
            }

            $db->prepare("DELETE FROM online_users WHERE last_activity < ?")->execute([$time - self::OFFLINE_TTL]);
        } catch (Throwable $e) {
        }
    }

    public static function updatePreciseLocation(): void {
        header('Content-Type: application/json; charset=utf-8');

        $payload = json_decode((string)@file_get_contents('php://input'), true);
        if (!is_array($payload)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'invalid_payload']);
            return;
        }

        $country = trim((string)($payload['country'] ?? ''));
        $countryCode = trim((string)($payload['country_code'] ?? ''));
        $region = trim((string)($payload['region'] ?? ''));
        $city = trim((string)($payload['city'] ?? ''));

        if ($country === '' && $countryCode === '' && $region === '' && $city === '') {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'empty_payload']);
            return;
        }

        $clamp = static function (string $value): string {
            return mb_substr(trim($value), 0, 120);
        };

        $norm = self::normalizeLocation($clamp($country), $clamp($countryCode), $clamp($region), $clamp($city));

        $ip = self::extractClientIp();
        if ($ip === '' || !filter_var($ip, FILTER_VALIDATE_IP)) {
            $ip = 'local';
        }

        $stored = $_SESSION['visitor_location'] ?? [];
        if (!is_array($stored)) {
            $stored = [];
        }

        $stored['ip'] = $ip;
        $stored['country'] = $norm['country'];
        $stored['country_code'] = $norm['country_code'];
        $stored['region'] = $norm['region'];
        $stored['city'] = $norm['city'];
        $stored['precise'] = true;
        $_SESSION['visitor_location'] = $stored;

        try {
            self::ensureOnlineSchema();
            $db = Database::getInstance()->getConnection();
            $update = $db->prepare("UPDATE online_users SET country = ?, country_code = ?, region = ?, city = ? WHERE session_id = ? OR ip_address = ?");
            $update->execute([
                $norm['country'],
                $norm['country_code'],
                $norm['region'],
                $norm['city'],
                (string)session_id(),
                $ip,
            ]);
        } catch (Throwable $e) {
        }

        echo json_encode(['status' => 'ok']);
    }

    public static function getOnlineCount(): int {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT COUNT(*) FROM online_users WHERE last_activity >= ?");
            $stmt->execute([time() - self::OFFLINE_TTL]);
            return (int)$stmt->fetchColumn();
        } catch (Throwable $e) {
            return 0;
        }
    }
}
