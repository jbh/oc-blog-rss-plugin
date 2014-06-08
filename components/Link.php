<?php namespace SoBoRed\Rss\Components;

use DB;
use App;
use File;
use Request;
use DateTime;
use Cms\Classes\ComponentBase;
use SoBoRed\Rss\Models\Settings;

class Link extends ComponentBase
{
    public $feedBurnerAddress;
    public $defaultText;
    public $iconCLass;
    public $posts;
    public $file;

    public function componentDetails()
    {
        return [
            'name'        => 'Link to RSS Feed',
            'description' => 'Outputs a link to the RSS feed.'
        ];
    }

    public function defineProperties()
    {
        return [
            'feedBurnerAddress' => [
                'title'       => 'Feed Burner Address',
                'description' => 'This is the address to the feedburner feed: http://feeds.feedburner.com/feed_address. If left blank, the link will just be to your RSS file: http://yourblog.com/rss.xml',
                'default'     => '',
                'type'        => 'string'
            ],
            'defaultText' => [
                'title'       => 'Default Link Text',
                'description' => 'This is the default link text. It is used for the RSS link. For example: <a>RSS</a>.',
                'default'     => 'RSS',
                'type'        => 'string'
            ],
            'iconClass' => [
                'title'       => 'Icon Class(es)',
                'description' => 'This is the icon class(es). It is used for the RSS link. <a><i class="icon icon-rss"></i></a>. If left blank, the link will just display the default link text.',
                'default'     => '',
                'type'        => 'string'
            ]
        ];
    }

    public function onRun()
    {
        $this->posts = $this->page['posts'] = $this->loadPosts();
        $this->feedBurnerAddress = $this->page['feedBurnerAddress'] = $this->property('feedBurnerAddress');
        $this->defaultText = $this->page['defaultText'] = $this->property('defaultText');
        $this->iconClass = $this->page['iconClass'] = $this->property('iconClass');
        $this->defaultRssLink = $this->page['defaultRssLink'] = Settings::get('link') . "/rss.xml";
        $this->createRss();
    }

    protected function loadPosts()
    {
        $posts = Db::table('rainlab_blog_posts')
                     ->orderBy('published_at', 'desc')
                     ->having('published', '=', '1')
                     ->get();

        return $posts;
    }

    protected function createRss()
    {
        $fileContents = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n" .
                        "<rss version=\"2.0\">\n".
                        "\t<channel>\n".
                        "\t\t<title>" . Settings::get('title') . "</title>\n" .
                        "\t\t<link>" . Settings::get('link') . "</link>\n" .
                        "\t\t<description>" . Settings::get('description') . "</description>\n\n";


        foreach($this->loadPosts() as $post)
        {
            $published = DateTime::createFromFormat('Y-m-d H:i:s', $post->published_at);

            $fileContents .= "\t\t<item>\n" .
                             "\t\t\t<title>" . $post->title . "</title>\n" .
                             "\t\t\t<link>" . Settings::get('link') . Settings::get('postPage') . "/" . $post->slug . "</link>\n" .
                             "\t\t\t<guid>" . Settings::get('link') . Settings::get('postPage') . "/" . $post->slug . "</guid>\n" .
                             "\t\t\t<pubDate>" . $published->format('M d, Y') . "</pubDate>\n" .
                             "\t\t\t<description>" . $post->excerpt . "</description>\n" .
                             "\t\t</item>\n";

        }

        $fileContents .= "\t</channel>\n";
        $fileContents .= "</rss>\n";

        $file = File::put('rss.xml', $fileContents);

        return $file;
    }
}
