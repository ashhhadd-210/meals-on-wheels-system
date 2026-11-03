<?php
function validateEmail($email)
{
    $pattern = "/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/";
    if (!preg_match($pattern, $email)) {
        return false;
    }

    list($user, $domain) = explode("@", $email);
    if (!checkdnsrr($domain, "MX")) {
        return false;
    }

    return true;
}
function cleanNumbers($string){
    return preg_replace('/[^0-9]/', '', $string);
}

function extractNumber($string) {
    if (preg_match('/\d+/', $string, $matches)) {
        return (int) $matches[0];
    }
    return 0;
}

function formatFloat($float){
    if (floor($float) == $float) {
        return number_format($float, 0, '.', '');
    } else {
        return number_format($float, 2, '.', '');
    }
}

function formatPhone($phone){
    $digits = preg_replace('/\D/', '', $phone);
    if (strpos($digits, '1') === 0) {
        return preg_replace('/(\d{1})(\d{3})(\d{3})(\d{4})/', '+1($2)-$3-$4', $digits);
    } else {
        return preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1)-$2-$3', $digits);
    }
}
function getUserIP()
{
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip); // just to be safe

                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
}
function generateRandomKey($length,$duel = false)
{
    $keyTerms = '1234567890abcdefghijklmnopqrstuvwxyz';
    $key = '';

    if($duel){
        for ($i = 0; $i < $length; $i++) {
            $key .= $keyTerms[mt_rand(0, 35)];
        }
    } else {
        for ($i = 0; $i < $length; $i++) {
            $key .= mt_rand(0, 9);
        }
    }
    return $key;
}
function makeShareKey($db){
    $key = generateRandomKey(50,true);
    $query = $db->select('SELECT * FROM applications WHERE appKey = ?',[$key]);
    if($query == false){
        return $key;
    } else {
        return makeShareKey($db);
    }
}
function hashPassword($password){
    return password_hash($password, PASSWORD_ARGON2ID, [PASSWORD_ARGON2_DEFAULT_MEMORY_COST]);
}

function isLightColor($hexColor)
{
    // Remove the '#' if present
    $hexColor = ltrim($hexColor, '#');

    // Handle 3-digit hex codes (expand to 6 digits)
    if (strlen($hexColor) === 3) {
        $hexColor = str_repeat($hexColor[0], 2) .
            str_repeat($hexColor[1], 2) .
            str_repeat($hexColor[2], 2);
    }

    // Validate the hex code (6 or 8 characters long)
    if (!preg_match('/^[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/', $hexColor)) {
        throw new InvalidArgumentException('Invalid hex color code');
    }

    // Extract RGB components
    $r = hexdec(substr($hexColor, 0, 2)); // Red component
    $g = hexdec(substr($hexColor, 2, 2)); // Green component
    $b = hexdec(substr($hexColor, 4, 2)); // Blue component

    // Optional: Handle 8-digit hex codes (ignore alpha)
    // Alpha (opacity) is in the last 2 characters (if present)
    // $alpha = strlen($hexColor) === 8 ? hexdec(substr($hexColor, 6, 2)) : 255;

    // Calculate luminance (weighted average method)
    $luminance = (0.299 * $r) + (0.587 * $g) + (0.114 * $b);

    // Return true if light, false if dark
    return $luminance > 128;
}