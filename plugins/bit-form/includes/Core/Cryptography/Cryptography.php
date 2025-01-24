<?php

namespace BitCode\BitForm\Core\Cryptography;

class Cryptography
{
  public static $sodiumCompat;

  public static function getSodiumCompat()
  {
    if (!self::$sodiumCompat) {
      self::$sodiumCompat = new SodiumCompat();
    }
    return self::$sodiumCompat;
  }

  public static function encrypt($message, $key)
  {
    try {
      if (32 !== strlen($key)) {
        $key = hash('sha256', $key, true); // Generate a 32-byte raw binary key
      }
      return base64_encode(self::getSodiumCompat()->compatEncrypt($message, $key));
    } catch (Exception $e) {
      // Handle the exception (e.g., log it, rethrow it, or return a meaningful error message)
      error_log('Encryption failed: ' . $e->getMessage());
      return null; // Or throw new Exception('Encryption failed');
    }
  }

  public static function decrypt($message, $key)
  {
    try {
      if (32 !== strlen($key)) {
        $key = hash('sha256', $key, true); // Generate a 32-byte raw binary key
      }
      return self::getSodiumCompat()->compatDecrypt(base64_decode($message), $key);
    } catch (Exception $e) {
      // Handle the exception
      error_log('Decryption failed: ' . $e->getMessage());
      return null; // Or throw new Exception('Decryption failed');
    }
  }
}
