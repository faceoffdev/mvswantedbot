<?php

namespace App\Models;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Yadakhov\InsertOnDuplicateKey;

/**
 * Class Person
 *
 * @property int    $id
 * @property string $ovd
 * @property string $category
 * @property string $first_name_u
 * @property string $last_name_u
 * @property string $middle_name_u
 * @property string $first_name_r
 * @property string $last_name_r
 * @property string $middle_name_r
 * @property string $first_name_e
 * @property string $last_name_e
 * @property string $birth_date
 * @property string $sex
 * @property string $lost_date
 * @property string $lost_place
 * @property string $article_crim
 * @property string $restraint
 * @property string $contact
 * @property string $photo_url
 * @method static Builder|Person whereId($value)
 * @method static Builder|Person whereFirstNameU($value)
 * @method static Builder|Person whereLastNameU($value)
 * @method static Builder|Person whereMiddleNameU($value)
 * @method static Builder|Person whereFirstNameR($value)
 * @method static Builder|Person whereLastNameR($value)
 * @method static Builder|Person whereMiddleNameR($value)
 * @method static Builder|Person whereFirstNameE($value)
 * @method static Builder|Person whereLastNameE($value)
 * @mixin \Eloquent
 */
class Person extends Model
{
    use InsertOnDuplicateKey;

    const PHOTO_URL = 'https://wanted.mvs.gov.ua/getphoto/person/?id=';

    public $timestamps = false;

    protected $table = 'persons';

    public static function findByFullName(string $fullName, ?string $birthDate): ?Person
    {
        list($firstName, $lastName, $middleName) = self::parseFullName($fullName);

        if (preg_match('/[a-z]/i', $fullName)) {
            $person = Person::whereFirstNameE($firstName)->whereLastNameE($lastName);
        } else {
            $person = Person::where(function ($query) use ($firstName, $lastName, $middleName) {
                $query->whereFirstNameR($firstName)->whereLastNameR($lastName)->whereMiddleNameR($middleName);
            })
                ->orWhere(function ($query) use ($firstName, $lastName, $middleName) {
                    $query->whereFirstNameU($firstName)->whereLastNameU($lastName)->whereMiddleNameU($middleName);
                });
        }

        $person = $person->when($birthDate, function ($query) use ($birthDate) {
            return $query->whereDate('birth_date', new Carbon($birthDate));
        })
            ->first();

        if ($person) {
            $person->photo_url = self::PHOTO_URL . $person->id;
        }

        return $person;
    }

    public function __toString(): string
    {
        $string = sprintf('ПIБ: %s %s %s', $this->first_name_u, $this->last_name_u, $this->middle_name_u) . PHP_EOL;
        $string .= 'Категорія: ' . $this->category . PHP_EOL;
        $string .= 'Дата народження: ' . Carbon::parse($this->birth_date)->format('d.m.Y') . PHP_EOL;
        $string .= 'Обмежувач: ' . $this->restraint . PHP_EOL;
        $string .= 'Контакти: ' . $this->contact . PHP_EOL;

        return $string;
    }

    private static function parseFullName(string $fullName): array
    {
        $result = explode(' ', $fullName);

        return [mb_strtoupper($result[0]), mb_strtoupper($result[1] ?? ''), mb_strtoupper($result[2] ?? '')];
    }
}
