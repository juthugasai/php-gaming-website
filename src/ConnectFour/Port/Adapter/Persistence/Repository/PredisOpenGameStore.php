<?php
declare(strict_types=1);

namespace Gaming\ConnectFour\Port\Adapter\Persistence\Repository;

use Gaming\ConnectFour\Application\Game\Query\Model\OpenGames\OpenGame;
use Gaming\ConnectFour\Application\Game\Query\Model\OpenGames\OpenGames;
use Gaming\ConnectFour\Application\Game\Query\Model\OpenGames\OpenGameStore;
use Predis\Client;

final class PredisOpenGameStore implements OpenGameStore
{
    private const STORAGE_KEY = 'open-games';

    /**
     * @var Client
     */
    private Client $predis;

    /**
     * PredisOpenGameStore constructor.
     *
     * @param Client $predis
     */
    public function __construct(Client $predis)
    {
        $this->predis = $predis;
    }

    /**
     * @inheritdoc
     */
    public function save(OpenGame $openGame): void
    {
        $this->predis->hset(
            self::STORAGE_KEY,
            $openGame->gameId(),
            json_encode(
                [
                    'gameId'   => $openGame->gameId(),
                    'playerId' => $openGame->playerId()
                ]
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function remove(string $gameId): void
    {
        $this->predis->hdel(self::STORAGE_KEY, [$gameId]);
    }

    /**
     * @inheritdoc
     */
    public function all(): OpenGames
    {
        return new OpenGames(
            array_values(
                array_map(
                    static function ($value) {
                        $payload = json_decode($value, true);
                        return new OpenGame($payload['gameId'], $payload['playerId']);
                    },
                    $this->predis->hgetall(self::STORAGE_KEY)
                )
            )
        );
    }
}
