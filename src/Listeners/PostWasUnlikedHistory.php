<?php

namespace Mattoid\MoneyHistoryAuto\Listeners;

use Flarum\Likes\Event\PostWasUnliked;
use Flarum\Locale\Translator;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Mattoid\MoneyHistory\Event\MoneyHistoryEvent;

class PostWasUnlikedHistory
{
    private $source = "POSTWASUNLIKED";
    private $sourceKey;
    private $sourceDesc = "取消点赞";

    private $events;
    private $settings;
    private $autoremove;

    public function __construct(Dispatcher $events, SettingsRepositoryInterface $settings, Translator $translator)
    {
        $this->events = $events;
        $this->settings = $settings;

        $this->sourceKey = "mattoid-money-history-auto.forum.post-was-unliked";
        $this->sourceDesc = $translator->trans("mattoid-money-history-auto.forum.post-was-unliked");
        $this->autoremove = (int)$this->settings->get('antoinefr-money.autoremove', 1);
    }


    public function handle(PostWasUnliked $event) {
        $money = (float)$this->settings->get('antoinefr-money.moneyforlike', 0);

        $rewarded = $this->settings->get("mattoid-money-history-auto.privateChatsAreNotRewarded", 0);
        if ($rewarded && $event->post->discussion->is_private) {
            $user = $event->post->user;
            $user->money += $money;
            $user->save();
        } else {
            $this->events->dispatch(new MoneyHistoryEvent($event->post->user, -$money, $this->source, $this->sourceDesc, $this->sourceKey));
        }
    }
}
