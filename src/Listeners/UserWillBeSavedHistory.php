<?php

namespace Mattoid\MoneyHistoryAuto\Listeners;

use Flarum\Locale\Translator;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Flarum\User\Event\Saving;
use Flarum\Settings\SettingsRepositoryInterface;
use Mattoid\MoneyHistory\Event\MoneyHistoryEvent;

class UserWillBeSavedHistory
{
    protected $source = "USERWILLBESAVED";
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

    public function handle(Saving $event) {
        $attributes = Arr::get($event->data, 'attributes', []);

        if (array_key_exists('money', $attributes)) {
            $this->events->dispatch(new MoneyHistoryEvent($event->user, (float)$attributes['money'], $this->source, $this->sourceDesc));
        }
    }
}
