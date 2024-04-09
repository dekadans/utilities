<?php

namespace tthe\UtilTool;

use GenPhrase\Password;
use joshtronic\LoremIpsum;
use Ramsey\Uuid\Uuid;

readonly class Utilities
{
    private const PHRASE_ENTROPY = 70;

    private \DateTimeImmutable $datetime;

    private bool $bool;
    private string $bytes;

    public function __construct()
    {
        $this->datetime = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $this->bool = (bool) random_int(0, 1);
        $this->bytes = random_bytes(32);
    }

    public function getBool(): bool
    {
        return $this->bool;
    }

    public function getUuid(): string
    {
        return Uuid::uuid4()->toString();
    }

    public function getPhrase(): string
    {
        $pass = new Password();
        $pass->removeWordlist('default');
        $pass->addWordlist('diceware.lst', 'diceware');
        $pass->disableSeparators(true);
        $pass->disableWordModifier(true);
        return $pass->generate(self::PHRASE_ENTROPY);
    }

    public function getPassword(): string
    {
        $length = 20;
        $uppercase = range('A', 'Z');
        $lowercase = range('a', 'z');
        $numbers = range(0, 9);
        $symbols = ['!','@','#','$','%','^','&','*'];
        $all = array_merge($uppercase, $lowercase, $numbers, $symbols);

        while (true) {
            $result = [];
            while (count($result) < $length) {
                $i = random_int(0, count($all)-1);
                $result[] = $all[$i];
            }

            if (
                count(array_intersect($symbols, $result)) == 0 ||
                count(array_intersect($numbers, $result)) == 0
            ) {
                continue;
            }

            return implode('', $result);
        }
    }

    public function getSentences(): array
    {
        return (new LoremIpsum())->sentences(3, false, true);
    }

    public function getBytesHex(): string
    {
        return bin2hex($this->bytes);
    }


    public function getBytesInt()
    {
        return array_map(ord(...), str_split($this->bytes));
    }

    public function getTimeIso(): string
    {
        return $this->datetime->format('c');
    }

    public function getTimeHttp(): string
    {
        return $this->datetime->format(\DateTimeInterface::RFC7231);
    }

    public function getTimeUnix(): string
    {
        return $this->datetime->format('U');
    }

    public function getWorldTime(): array
    {
        $tz = [
            'Pacific/Honolulu',
            'America/Los_Angeles',
            'America/Mexico_City',
            'America/Chicago',
            'America/New_York',
            'America/Sao_Paulo',
            'Africa/Lagos',
            'Africa/Johannesburg',
            'Europe/London',
            'Europe/Paris',
            'Europe/Kyiv',
            'Europe/Istanbul',
            'Asia/Tehran',
            'Asia/Dubai',
            'Asia/Karachi',
            'Asia/Kolkata',
            'Asia/Dhaka',
            'Asia/Jakarta',
            'Asia/Shanghai',
            'Asia/Tokyo',
            'Australia/Sydney',
            'Pacific/Auckland'
        ];

        $times = [];

        foreach ($tz as $zone) {
            $dt = $this->datetime->setTimezone(new \DateTimeZone($zone));
            $times[$zone] = [
                'time' => $dt->format('H:i:s'),
                'offset' => $dt->format('P')
            ];
        }

        return $times;
    }
}