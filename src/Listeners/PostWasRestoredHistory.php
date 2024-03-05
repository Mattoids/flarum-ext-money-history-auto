<?php

namespace Mattoid\MoneyHistoryAuto\Listeners;

use AntoineFr\Money\Listeners\AutoRemoveEnum;
use Flarum\Locale\Translator;
use Flarum\Post\Event\Restored as PostRestored;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Mattoid\MoneyHistory\Event\MoneyHistoryEvent;

class PostWasRestoredHistory
{
    private $source = "POSTWASRESTORED";
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

    public function handle(PostRestored $event) {
        if ($this->autoremove == AutoRemoveEnum::HIDDEN) {
            $minimumLength = (int)$this->settings->get('antoinefr-money.postminimumlength', 0);

            if (strlen($event->post->content) >= $minimumLength) {
                $money = (float)$this->settings->get('antoinefr-money.moneyforpost', 0);

                $this->events->dispatch(new MoneyHistoryEvent($event->post->user, $money, $this->source, $this->sourceDesc, $this->sourceKey));
            }
        }
    }
}
