<?php
namespace wishanddell\accountnotify;

use Craft;
use yii\base\Event;
use craft\web\UrlManager;
use craft\elements\User;

class Plugin extends \craft\base\Plugin
{
    public function init()
    {
        parent::init();

        // Services
        $this->setComponents([
            'notify' => \wishanddell\accountnotify\services\Notify::class
        ]);

        // On User Save
        Event::on(
            User::class,
            User::EVENT_BEFORE_SAVE,
            function ($event) {
                try {
                    $this->notify->check($event->sender);
                } catch (\Exception $e) {}
            }
        );
    }
}