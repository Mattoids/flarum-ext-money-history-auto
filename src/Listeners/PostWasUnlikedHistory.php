<?php

namespace Mattoid\MoneyHistoryAuto\Listeners;

use Flarum\Likes\Event\PostWasUnliked;
use Flarum\Locale\Translator;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Mattoid\MoneyHistory\Event\MoneyHistoryEvent;

class PostWasUnlikedHistory
{
    protected $source = "POSTWASUNLIKED";
    protected $sourceDesc = "";

    private $events;
    private $settings;
    private $autoremove;

    public function __construct(Dispatcher $events, SettingsRepositoryInterface $settings, Translator $translator)
    {
        $this->events = $events;
        $this->settings = $settings;

        $this->sourceDesc = $translator->trans("mattoid-money-history-auto.forum.source-desc");
        $this->autoremove = (int)$this->settings->get('antoinefr-money.autoremove', 1);
    }


    public function handle(PostWasUnliked $event) {
        $money = (float)$this->settings->get('antoinefr-money.moneyforlike', 0);

        $this->events->dispatch(new MoneyHistoryEvent($event->post->user, -$money, $this->source, $this->sourceDesc));
    }
}
