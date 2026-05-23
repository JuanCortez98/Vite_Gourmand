<?php 

define('JWT_SECRET', '19981998PAFATj');
define('JWT_ALGO', 'HS256');

/**
 * Genera un JWT
 * @param array $payload Datos a incluir (ej: ['user_id' => 1, 'role' => 'admin'])
 * @param int $expire Tiempo de expiración en segundos
 * @return string Token JWT
 */
function generateJWT($payload, $expire = 3600) {
    $headerEncoded = base64_encode(json_encode([
        'alg' => JWT_ALGO,
        'typ' => 'JWT'
    ]));

    $payload['iat'] = time();
    $payload['exp'] = time() + $expire;

    $payloadEncoded = base64_encode(json_encode($payload));

    $signature = hash_hmac(
        'sha256',
        "$headerEncoded.$payloadEncoded",
        JWT_SECRET,
        true
    );

    $signatureEncoded = base64_encode($signature);

    return "$headerEncoded.$payloadEncoded.$signatureEncoded";
}

/**
 * Valida un JWT y devuelve el payload o false si es inválido
 * @param string $token Token JWT
 * @return array|false Payload o false
 */
function validateJWT($token) {
    if (!$token) return false;

    $parts = explode('.', $token);
    if (count($parts) !== 3) return false;

    [$header, $payloadEncoded, $signature] = $parts;

    $expectedSignature = base64_encode(
        hash_hmac(
            'sha256',
            "$header.$payloadEncoded",
            JWT_SECRET,
            true
        )
    );

    if ($signature !== $expectedSignature) return false;

    $payload = json_decode(base64_decode($payloadEncoded), true);
    if (!$payload) return false;

    if (isset($payload['exp']) && $payload['exp'] < time()) {
        return false;
    }
    
    return $payload;
}