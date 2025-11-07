<?php

declare(strict_types=1);

namespace AqwSocketClient\Services;

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;

class AuthService
{
    private const LOGIN_URL = 'https://game.aq.com/game/api/login/now';

    /**
     * Performs authentication via POST request and returns the token synchronously.
     *
     * @return string The authentication token (sToken).
     * @throws RuntimeException If token retrieval fails.
     */
    public static function getAuthToken(string $username, string $password): string
    {
        try {
            $response = new GuzzleHttpClient()->post(self::LOGIN_URL, [
                'form_params' => [
                    'user' => $username,
                    'pass' => $password,
                    'option' => 1
                ],
                'timeout' => 5.0, 
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            
            if (!isset($body['login']['sToken'])) {
                throw new RuntimeException(
                    "Failed to retrieve account auth token for user: {$username}"
                );
            }

            return $body['login']['sToken'];

        } catch (GuzzleException $e) {
            throw new RuntimeException("HTTP Authentication Error: " . $e->getMessage(), 0, $e);
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}