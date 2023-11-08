<?php

namespace App\Traits;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Encryption\DefuseCrypto;
// use Lcobucci\JWT\Parser;
use Carbon\CarbonImmutable;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;

trait EncryptJson
{
    // use DefuseCrypto;

    // private $this->key;

    public function __construct()
    {
        $this->key = env('JWT_SECRET');
    }

	public function encryptData($data): string
	{
		// Set the JWT token configuration
		$signer = new Sha256();
		$time = CarbonImmutable::now();

		// Build the JWT token
		$token = (new Builder())->issuedBy('YourIssuer')
								->permittedFor('YourAudience')
								->identifiedBy('YourId', true)
								->issuedAt($time)
								->expiresAt($time + 3600)
								->withClaim('data', DefuseCrypto::encrypt(json_encode($data)))
								->getToken($signer, $this->key);

		// Return the encrypted JSON data as a JWT token
		return (string) $token;
	}

    public function decryptData($token)
    {
        // Parse the JWT token
        $parsedToken = (new Parser(new JoseEncoder()))->parse($token)->claims()->all()['jti'];

        // Verify the token
        $signer = new Sha256();
        $isValid = $parsedToken->verify($signer, $this->key);

        // Get and decrypt the token data if it's valid
        if ($isValid)
        {
            $encryptedData = $parsedToken->getClaim('data');
            $decryptedData = json_decode(DefuseCrypto::decrypt($encryptedData), true);
            return $decryptedData;
        }

        // Return null if the token is not valid
        return null;
    }
}
