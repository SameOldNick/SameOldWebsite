<?php

namespace Database\Seeders\Initial;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::find(1);

        $article = Article::createWithPost(function (Article $article) {
            $article->fill([
                'title' => 'Welcome to Same Old Nick\'s Corner of the Internet! 🚀',
                'slug' => 'welcome',
            ]);

            $article->published_at = Carbon::now();
        }, $user);

        $revision = $article->revisions()->create([
            'content' => $this->getContent(),
        ]);

        $article->currentRevision()->associate($revision);

        $article->save();
    }

    protected function getContent(): string
    {
        return <<<'EOD'
Hey there, tech enthusiasts, curious wanderers, and everyone in between! Welcome to Same Old Nick's cozy corner of the internet, where pixels meet passion and bytes blend with boundless curiosity.

I'm Nick, the curator of this digital haven, and I'm thrilled to have you here. Whether you stumbled upon this space intentionally or found yourself pleasantly lost in the maze of the web, I'm delighted to extend a warm welcome to you.

What can you expect to find here, you might wonder? Well, let me give you a glimpse into the virtual world I've crafted with love and dedication.

First and foremost, Same Old Nick's is a platform where I share my musings, insights, and discoveries in the vast realm of technology. From the latest gadgets that set hearts racing to the intricacies of coding languages that power our digital lives, consider this your hub for all things tech.

But wait, there's more! Beyond the binary of ones and zeros, I also delve into the human side of technology. You see, behind every line of code and every innovation lies a story—a story of creativity, perseverance, and sometimes even a dash of serendipity. And it's these stories that I'm passionate about unraveling and sharing with you.

So, whether you're a seasoned tech veteran or a curious novice dipping your toes into the digital waters for the first time, there's a place for you here at Same Old Nick's. Let's embark on this journey together, exploring the ever-evolving landscape of technology and uncovering the wonders it holds.

Before you go exploring, don't forget to grab a metaphorical cup of virtual coffee and make yourself at home. Feel free to dive into the blog posts, leave your thoughts in the comments, or reach out to me directly—I'm always eager to connect with fellow tech enthusiasts and exchange ideas.

Once again, welcome to Same Old Nick's website. I'm thrilled to have you here, and I can't wait to see where our digital adventures take us!
EOD;
    }
}
