<?php

namespace App\Classes;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * In-house discord client cause no good libraries exist...
 *
 * @author kolbyd
 */
class DiscordClient
{
    private Client $http;
    private string $token;

    private const AUDIT_CHANNEL = 695849973585149962;
    private const WINNIPEG_GUILD = 598023748741758976;

    function __construct(string $token) {
        $this->token = $token;
        $this->http = new Client([
            'base_uri' => 'https://discord.com/api/v10/',
            'headers' => [
                'Authorization' => "Bot " . $this->token,
                'User-Agent' => 'DiscordBot (winnipegfir.ca, v1.0.0)',
                'Accept' => 'application/json'
            ]
        ]);
    }

    function AddGuildMember(int $user_id, string $access_token, string $nickname, array $roles) : void {
        try {
            $this->http->put("guilds/" . self::WINNIPEG_GUILD . "/members/$user_id", [
                'body' => [
                    'access_token' => $access_token,
                    'nick' => $nickname,
                    'roles' => $roles
                ]
            ]);
        } catch (GuzzleException $e) {
            Log::error($e->getMessage());
        }
    }

    function GetGuildMember(int $user_id) : object|null {
        try {
            $response = $this->http->get("guilds/" . self::WINNIPEG_GUILD . "/members/$user_id");

            if ($response->getStatusCode() != 200)
                return null;

            return json_decode($response->getBody());
        } catch (GuzzleException $e) {
            Log::error($e->getMessage());
        }

        return null;
    }

    function RemoveGuildMember(int $user_id) : void {
        try {
            $this->http->delete("guilds/" . self::WINNIPEG_GUILD . "/members/$user_id", [
                'headers' => [
                    'X-Audit-Log-Reason' => "User disconnected via website."
                ]
            ]);
        } catch (GuzzleException $e) {
            Log::error($e->getMessage());
        }
    }

    function GetDiscordUser(int $user_id): object|null {
        try {
            $response = $this->http->get("users/$user_id");

            if ($response->getStatusCode() !== 200)
                throw new Exception("Couldn't get user $user_id.");

            return json_decode($response->getBody()->getContents());
        } catch (Exception | GuzzleException $e) {
            Log::error($e->getMessage());
        }

        // No user found
        return null;
    }

    function SendAuditMessage(string $content): void {
        try {
            $this->http->post("channels/" . self::AUDIT_CHANNEL . "/messages", [
                'body' => [ 'content' => $content ]
            ]);
        } catch (GuzzleException $e) {
            Log::error($e->getMessage());
        }
    }


}
