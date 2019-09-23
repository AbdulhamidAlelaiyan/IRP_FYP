<?php

namespace Seed;

use Core\Model;

class FakerSeeder extends Model
{
    public function seedUsers()
    {
        $db = static::getDB();
        $faker = \Faker\Factory::create();
        for($i = 0; $i < 100; $i++)
        {
            $name = $faker->name;
            $pass = password_hash($faker->password, PASSWORD_DEFAULT);
            $email = $faker->email;
            if($i % 2 == 0 )
            {
                $type = 'reader';
            }
            elseif($i % 3 == 0)
            {
                $type = 'author';
            }
            else
            {
                $type = 'admin';
            }
            $db->query("INSERT INTO users (name, email, password_hash, activation_hash, is_active, type) 
                    VALUES ('$name', '$email', '$pass', null, 1, '$type')");
            echo 'Done Users ()' . $email . '<br>';
        }
    }

    public function seedPosts()
    {
        $db = static::getDB();
        $faker = \Faker\Factory::create();
        $j = 0;
        for($i = 107; $i <= 147; $i++)
        {
            $title = $faker->sentence;
            $body = $faker->text;
            $db->query("INSERT INTO posts (isbn, title, body, user_id) 
                    VALUES ('{$this->isbn[$j]}', '$title', '$body', $i)");
            echo 'Done Users ()' . $title . '<br>';
            ++$j;
        }
    }

    public function seedBooks()
    {
        $db = static::getDB();
        $faker = \Faker\Factory::create();
        for($i = 0; $i < 40; $i++)
        {
            $isbn = $faker->isbn10;
            $this->isbn[] = $isbn;
            $title = $faker->sentence;
            $publication_date = $faker->date('Y-m-d');
            if($i % 2 == 0 )
            {
                $edition = '1st edition';
            }
            elseif($i % 3 == 0)
            {
                $edition = '2nd edition';
            }
            else
            {
                $edition = '3rd edition';
            }
            $authors = $faker->name;
            $db->query("INSERT INTO books_information (isbn, title, publication_date, edition, authors) 
                    VALUES ('$isbn', '$title', '$publication_date', '$edition', '$authors')");
            echo 'Done Books (' . $title . ')<br>';
        }
    }

    public function seedBooksContent()
    {

    }
}

$seeder = new FakerSeeder();
//$seeder->seedUsers();
//$seeder->seedBooks();
//$seeder->seedPosts();


