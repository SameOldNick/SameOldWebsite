<?php

namespace Database\Seeders\Setup;

use App\Models\Page;
use App\Models\PageMetaData;
use Illuminate\Database\Seeder;

class CommentSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            'user_authentication' => 'registered',
            'comment_moderation' => 'auto',
            'use_captcha' => 'disabled',
            'moderators' => ['profanity', 'email', 'language', 'link'],
        ];

        $models = collect($defaults)->map(fn ($value, $key) => new PageMetaData(['key' => $key, 'value' => $value]));

        Page::firstWhere(['page' => 'blog'])->metaData()->saveMany($models);
    }
}
