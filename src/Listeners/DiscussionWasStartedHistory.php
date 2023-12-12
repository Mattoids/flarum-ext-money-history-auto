<?php

namespace Mattoid\MoneyHistoryAuto\Listeners;

use Flarum\Locale\Translator;
use Flarum\Discussion\Event\Started;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Mattoid\MoneyHistory\Event\MoneyHistoryEvent;

class DiscussionWasStartedHistory
{
    protected $source = "DISCUSSIONWASSTARTED";
    protected $sourceDesc = "发帖奖励";

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

    public function handle(Started $event) {
        $money = (float)$this->settings->get('antoinefr-money.moneyfordiscussion', 0);

        $this->events->dispatch(new MoneyHistoryEvent($event->actor, $money, $this->source, $this->sourceDesc));
    }
}
