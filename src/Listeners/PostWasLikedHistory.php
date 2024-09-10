<?php

namespace Mattoid\MoneyHistoryAuto\Listeners;

use Flarum\Likes\Event\PostWasLiked;
use Flarum\Locale\Translator;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Mattoid\MoneyHistory\Event\MoneyHistoryEvent;

class PostWasLikedHistory
{
    private $source = "POSTWASLIKED";
    private $sourceKey;
    private $sourceDesc = "收到点赞获得奖励";

    private $events;
    private $settings;
    private $autoremove;

    public function __construct(Dispatcher $events, SettingsRepositoryInterface $settings, Translator $translator)
    {
        $this->events = $events;
        $this->settings = $settings;

        $this->sourceKey = "mattoid-money-history-auto.forum.post-was-liked";
        $this->sourceDesc = $translator->trans("mattoid-money-history-auto.forum.post-was-liked");
        $this->autoremove = (int)$this->settings->get('antoinefr-money.autoremove', 1);
    }

    public function handle(PostWasLiked $event) {
        $money = (float)$this->settings->get('antoinefr-money.moneyforlike', 0);

        $event->post->user->create_user_id = $event->user_id;
        $this->events->dispatch(new MoneyHistoryEvent($event->post->user, $money, $this->source, $this->sourceDesc, $this->sourceKey));
    }
}
