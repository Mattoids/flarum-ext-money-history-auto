<?php

namespace Mattoid\MoneyHistoryAuto\Listeners;

use AntoineFr\Money\Listeners\AutoRemoveEnum;
use Flarum\Locale\Translator;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Discussion\Event\Restored as DiscussionRestored;
use Illuminate\Contracts\Events\Dispatcher;
use Mattoid\MoneyHistory\Event\MoneyHistoryEvent;

class DiscussionWasRestoredHistory
{
    private $source = "DISCUSSIONWASRESTORED";
    private $sourceKey;
    private $sourceDesc = "";

    private $events;
    private $settings;
    private $autoremove;

    public function __construct(Dispatcher $events, SettingsRepositoryInterface $settings, Translator $translator)
    {
        $this->events = $events;
        $this->settings = $settings;

        $this->sourceKey = "mattoid-money-history-auto.forum.source-desc";
        $this->sourceDesc = $translator->trans("mattoid-money-history-auto.forum.source-desc");
        $this->autoremove = (int)$this->settings->get('antoinefr-money.autoremove', 1);
    }

    public function handle(DiscussionRestored $event) {
        if ($this->autoremove == AutoRemoveEnum::HIDDEN) {
            $money = (float)$this->settings->get('antoinefr-money.moneyfordiscussion', 0);

            $this->events->dispatch(new MoneyHistoryEvent($event->discussion->user, $money, $this->source, $this->sourceDesc));
        }
    }
}
